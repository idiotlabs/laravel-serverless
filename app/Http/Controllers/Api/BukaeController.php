<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BukaePost;
use App\Models\BukaeReaction;
use App\Models\BukaeReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BukaeController extends Controller
{
    // 고정 이모지 키(클라가 🤣💀🫠🥲🤨🔥로 표시). DB엔 키만 저장.
    private const EMOJIS = ['bang', 'death', 'melt', 'sad', 'what', 'fire'];
    private const HIDE_THRESHOLD = 5; // 신고 5회 → 자동 숨김

    /** POST /api/bukae/publish — 캐릭터당 1회(중복이면 기존 반환) */
    public function publish(Request $r)
    {
        $d = $r->validate([
            'device_id' => 'required|string|max:64',
            'fc' => 'required|integer',
            'fs' => 'required|integer|min:0|max:255',
            'e' => 'required|integer|min:0|max:255',
            'm' => 'required|integer|min:0|max:255',
            'h' => 'required|integer|min:0|max:255',
            'archetype' => 'required|string|max:64',
            'tagline' => 'required|string|max:160',
        ]);
        $post = BukaePost::firstOrCreate(
            ['device_id' => $d['device_id'], 'fc' => $d['fc'], 'fs' => $d['fs'], 'e' => $d['e'], 'm' => $d['m'], 'h' => $d['h']],
            ['archetype' => $d['archetype'], 'tagline' => $d['tagline']]
        );
        return response()->json(['post' => $this->serialize($post, $d['device_id'])]);
    }

    /** GET /api/bukae/timeline?device_id=&cursor= */
    public function timeline(Request $r)
    {
        $deviceId = (string) $r->query('device_id', '');
        $cursor = (int) $r->query('cursor', 0);
        $q = BukaePost::where('hidden', false);
        if ($cursor > 0) {
            $q->where('id', '<', $cursor);
        }
        $posts = $q->orderByDesc('id')->limit(30)->get();
        return response()->json([
            'posts' => $posts->map(fn ($p) => $this->serialize($p, $deviceId)),
            'next' => $posts->count() ? $posts->last()->id : null,
        ]);
    }

    /** POST /api/bukae/react {device_id, post_id, emoji} — 토글 */
    public function react(Request $r)
    {
        $d = $r->validate([
            'device_id' => 'required|string|max:64',
            'post_id' => 'required|integer',
            'emoji' => 'required|string|in:' . implode(',', self::EMOJIS),
        ]);
        $existing = BukaeReaction::where($d)->first();
        if ($existing) {
            $existing->delete();
        } else {
            BukaeReaction::create($d);
        }
        return response()->json([
            'counts' => $this->reactionCounts($d['post_id']),
            'my_reactions' => $this->myReactions($d['post_id'], $d['device_id']),
        ]);
    }

    /** POST /api/bukae/report {device_id, post_id} — 5회면 자동 숨김 */
    public function report(Request $r)
    {
        $d = $r->validate([
            'device_id' => 'required|string|max:64',
            'post_id' => 'required|integer',
        ]);
        BukaeReport::firstOrCreate($d);
        $post = BukaePost::find($d['post_id']);
        if ($post) {
            $post->report_count = BukaeReport::where('post_id', $post->id)->count();
            if ($post->report_count >= self::HIDE_THRESHOLD) {
                $post->hidden = true;
            }
            $post->save();
        }
        return response()->json(['ok' => true]);
    }

    /** GET /api/bukae/best?period=week&device_id= — distinct 리액터 상위(자추 방어) */
    public function best(Request $r)
    {
        $deviceId = (string) $r->query('device_id', '');
        $days = $r->query('period') === 'month' ? 30 : 7;
        $since = now()->subDays($days);
        $ids = DB::table('bukae_posts as p')
            ->join('bukae_reactions as rx', 'rx.post_id', '=', 'p.id')
            ->where('p.hidden', false)
            ->where('rx.created_at', '>=', $since)
            ->groupBy('p.id')
            ->orderByRaw('COUNT(DISTINCT rx.device_id) DESC')
            ->limit(10)
            ->pluck('p.id')
            ->all();
        $posts = BukaePost::whereIn('id', $ids)->get()
            ->sortBy(fn ($p) => array_search($p->id, $ids))->values();
        return response()->json(['posts' => $posts->map(fn ($p) => $this->serialize($p, $deviceId))]);
    }

    // ---- helpers ----
    private function serialize(BukaePost $p, string $deviceId): array
    {
        return [
            'id' => $p->id,
            'fc' => $p->fc, 'fs' => $p->fs, 'e' => $p->e, 'm' => $p->m, 'h' => $p->h,
            'archetype' => $p->archetype, 'tagline' => $p->tagline,
            'mine' => $deviceId !== '' && $p->device_id === $deviceId,
            'counts' => $this->reactionCounts($p->id),
            'my_reactions' => $deviceId !== '' ? $this->myReactions($p->id, $deviceId) : [],
        ];
    }

    private function reactionCounts(int $postId): array
    {
        $rows = BukaeReaction::where('post_id', $postId)
            ->select('emoji', DB::raw('count(*) as c'))->groupBy('emoji')->pluck('c', 'emoji');
        $out = [];
        foreach (self::EMOJIS as $e) {
            $out[$e] = (int) ($rows[$e] ?? 0);
        }
        return $out;
    }

    private function myReactions(int $postId, string $deviceId): array
    {
        return BukaeReaction::where('post_id', $postId)->where('device_id', $deviceId)
            ->pluck('emoji')->all();
    }
}
