<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\YoutubeTrend;
use App\Models\YoutubeTrendKeyword;
use App\Models\YoutubeTrendUser;
use App\Notifications\YoutubeTrendAlarmByKeyword;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\Fcm\FcmMessage;

class YoutubeController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        $token = $request->input('token');
        $device_id = $request->input('deviceId');
        $platform = $request->input('platform');
        $fcm_token = $request->input('fcmToken');

        $youtubeTrendUser = YoutubeTrendUser::where('user_token', $token)->first();

        if (empty($youtubeTrendUser)) {
            $token = 'user_' . date('Ymd') . time();

            $youtubeTrendUser = YoutubeTrendUser::create([
                'user_token' => $token,
            ]);
        }

        // update platform, device_id
        if ($youtubeTrendUser->device_id !== $device_id) {
            $youtubeTrendUser->platform = $platform;
            $youtubeTrendUser->device_id = $device_id;
            $youtubeTrendUser->save();
        }

        // update token
        if ($youtubeTrendUser->fcm_token !== $fcm_token) {
            $youtubeTrendUser->fcm_token = $fcm_token;
            $youtubeTrendUser->save();
        }

        return response()->json(['token' => $token]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function trend(Request $request)
    {
        // index는 가장 최근이 0, 한단계씩 과거로 가면 1씩 증가하는 형태
        $index = $request->input('index', 0);

        $page = 50;
        $offset = $index * $page;

        $youtubeTrend = YoutubeTrend::orderBy('id', 'desc')
            ->skip($offset)
            ->take($page)
            ->get()
            ->reverse();

        $info = [];
        $response = [];

        foreach ($youtubeTrend as $data) {
            $views = floor($data->video_views / 10000);

            $di = new \DateInterval($data->video_duration);

            $date = Carbon::parse($data->published_at);
            $diffInDays = $date->diffInDays();

            $response[] = [
                'title' => $data->title,
                'channel_title' => $data->channel_title,
                'video_id' => $data->video_id,
                'video_views' => $views >= 1 ? $views . '만' : $data->video_views,
                'video_duration' => ($di->h > 0 ? $di->h . ':' : '') . $di->i . ':' . $di->s,
                'video_data' => $diffInDays > 0 ? $diffInDays . '일 전' : '오늘',
            ];
        }

        $info = [
            'index' => $index,
            'youtube_count' => $data->count,
            'youtube_date' => Carbon::parse($data->created_at)->format('Y-m-d H:i'),
        ];

        return response()->json(['info' => $info, 'data' => $response]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function trendId(Request $request)
    {
        $video_id = $request->input('video_id');

        $youtubeTrend = YoutubeTrend::query()
            ->select(['rank', 'created_at'])
            ->where('video_id', $video_id)->get();

        $response = [];

        foreach ($youtubeTrend as $trend) {
            $response[] = [
                'date' => Carbon::parse($trend->created_at)->format('Y-m-d H:i'),
                'rank' => $trend->rank,
            ];
        }

        return response()->json(['data' => $response]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function keywordList(Request $request)
    {
        $token = $request->input('token');

        $youtubeTrendUser = YoutubeTrendUser::where('user_token', $token)->first();

        $youtubeTrendKeyword = [];

        if ($youtubeTrendUser) {
            $youtubeTrendKeyword = YoutubeTrendKeyword::query()
                ->where('user_id', $youtubeTrendUser->id)
                ->select('id', 'user_keyword')
                ->get();
        }

//        $youtubeTrendKeyword = YoutubeTrendKeyword::query()
//            ->select('id', 'user_keyword')
//            ->get();

        return response()->json(['data' => $youtubeTrendKeyword]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function keywordAdd(Request $request)
    {
        $token = $request->input('token');
        $keyword = $request->input('keyword');

        $user_id = YoutubeTrendUser::where('user_token', $token)->first()?->id;

        $youtubeTrendKeyword = YoutubeTrendKeyword::create([
            'user_id' => $user_id,
            'user_keyword' => $keyword,
        ]);

        return response()->json(['data' => $youtubeTrendKeyword]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function keywordRemove(Request $request)
    {
        $token = $request->input('token');
        $keyword_id = $request->input('id');

        $user_id = YoutubeTrendUser::where('user_token', $token)->first()?->id;

        $youtubeTrendKeyword = YoutubeTrendKeyword::query()
            ->where('user_id', $user_id)
            ->where('id', $keyword_id)
            ->delete();

        return response()->json(['data' => $youtubeTrendKeyword]);
    }
}
