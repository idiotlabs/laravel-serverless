<?php

namespace App\Services;

use App\Models\YoutubeTrend;
use App\Models\YoutubeTrendKeyword;
use App\Models\YoutubeTrendNotification;
use App\Models\YoutubeTrendUser;
use Carbon\Carbon;
use Google\Client;
use Google\Service\YouTube;

class YoutubeService
{
    /**
     * @return void
     * @throws \Google\Service\Exception
     */
    public function getTrendList()
    {
        $client = new Client();
        $client->setApplicationName("youtube-trend-410111");
        $client->setDeveloperKey("AIzaSyCBwx-U_csEy11RQV5g4b8EXIdNhryvbRk");

        $youtube = new YouTube($client);

        $response = $youtube->videos->listVideos('snippet,contentDetails,statistics', [
            'chart' => 'mostPopular',
            'regionCode' => 'kr',
            'maxResults' => '50',
        ]);

        $count = YoutubeTrend::max('count') + 1;
        $rank = 1;

        foreach ($response->items as $item) {
            YoutubeTrend::create([
                'title' =>  $item?->snippet?->title,
                'video_id' => $item?->id,
                'published_at' => $item?->snippet?->publishedAt,
                'channel_title' => $item?->snippet?->channelTitle,
                'video_views' => $item?->statistics?->viewCount,
                'video_duration' => $item?->contentDetails?->duration,
                'count' => $count,
                'rank' => $rank++,
            ]);
        }

        $this->sendNotificaiton();
    }

    /**
     * @return void
     */
    public function sendNotificaiton()
    {
        $youtubeTrend = YoutubeTrend::query()
            ->select(['id', 'title', 'video_id'])
            ->orderBy('id', 'desc')
            ->take(50)
            ->get();

        $keywords = YoutubeTrendKeyword::query()
            ->select(['user_keyword'])
            ->groupBy('user_keyword')
            ->get();

        // 유저의 키워드와 매칭되는 youtube video 가져오기
        $data = [];

        foreach ($keywords as $keyword) {
            foreach ($youtubeTrend as $trend) {
                if (stripos($trend->title, $keyword->user_keyword) !== false) {
                    $data[] = [
                        'youtube_id' => $trend->id,
                        'title' => $trend->title,
                        'video_id' => $trend->video_id,
                        'keyword' => $keyword->user_keyword
                    ];
                }
            }
        }

        // 여기서 유저 정보를 가져오면서 보낸 이력을 체크하고 제거한다.
        $noti_list = [];

        foreach ($data as $value) {
            $keyword = $value['keyword'];
            if (empty($keyword)) continue;

            $users = YoutubeTrendKeyword::where('user_keyword', $keyword)->get();

            foreach ($users as $user) {
                $youtubeTrendNotificationExist = YoutubeTrendNotification::query()
                    ->where('user_id', $user->user_id)
                    ->where('video_id', $value['video_id'])
                    ->exists();

                if (empty($youtubeTrendNotificationExist)) {
                    $noti_list[] = [
                        'user_id' => $user->user_id,
                        'video_id' => $value['video_id'],
                        'title' => $value['title'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                }
            }
        }

        // log
        YoutubeTrendNotification::insert($noti_list);

        // notify
        foreach ($noti_list as $noti) {
            (YoutubeTrendUser::find($noti['user_id']))
                ->notify(new \App\Notifications\YoutubeTrendAlarmByKeyword($noti['title']));
        }
    }
}
