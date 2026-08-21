<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HanalMeditation;
use App\Models\HanalReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * 한알(cc-project-24) 반응 집계.
 *
 * PRD §4.2: "다른 사람들도 이 말씀에 은혜받았다"는 공동체감이 핵심 가치 →
 * 익명(device_id) 반응의 전체 집계를 노출한다. 계정/로그인 없음.
 * 앱은 실패 시 로컬 반응만 표시(graceful degradation)하므로 여기선 단순하게 유지.
 */
class HanalController extends Controller
{
    private const REACTIONS = ['grace', 'thanks', 'pierced', 'peace', 'joy'];

    /**
     * GET /api/hanal/today[?date=YYYY-MM-DD]
     * 오늘의 말씀 반환. 없으면 204 → 앱이 번들 폴백 (graceful degradation).
     */
    public function today(Request $r)
    {
        $date = $r->query('date', now('Asia/Seoul')->toDateString());
        $m = HanalMeditation::where('date', $date)->first();
        if (! $m) {
            return response()->json(null, 204);
        }
        return response()->json(['date' => (string) $date] + $m->toAppJson());
    }

    /** GET /api/hanal/reactions?meditation_id=...&device_id=... */
    public function reactions(Request $r)
    {
        $d = $r->validate([
            'meditation_id' => 'required|string|max:64',
            'device_id' => 'nullable|string|max:64',
        ]);

        return response()->json([
            'counts' => $this->counts($d['meditation_id']),
            'my_reactions' => $this->mine($d['meditation_id'], $d['device_id'] ?? null),
        ]);
    }

    /** POST /api/hanal/react {meditation_id, device_id, reaction} — 토글 */
    public function react(Request $r)
    {
        $d = $r->validate([
            'meditation_id' => 'required|string|max:64',
            'device_id' => 'required|string|max:64',
            'reaction' => 'required|string|in:' . implode(',', self::REACTIONS),
        ]);

        $existing = HanalReaction::where($d)->first();
        if ($existing) {
            $existing->delete();
        } else {
            // 동시 탭 중복 방지 (unique 인덱스 위반은 무시)
            try {
                HanalReaction::create($d);
            } catch (\Illuminate\Database\QueryException $e) {
                if (! str_contains($e->getMessage(), 'uniq_hanal_reaction')) {
                    throw $e;
                }
            }
        }

        return response()->json([
            'counts' => $this->counts($d['meditation_id']),
            'my_reactions' => $this->mine($d['meditation_id'], $d['device_id']),
        ]);
    }

    private function counts(string $meditationId): array
    {
        $rows = HanalReaction::where('meditation_id', $meditationId)
            ->select('reaction', DB::raw('count(*) as c'))
            ->groupBy('reaction')
            ->pluck('c', 'reaction');

        $out = [];
        foreach (self::REACTIONS as $k) {
            $out[$k] = (int) ($rows[$k] ?? 0);
        }

        return $out;
    }

    private function mine(string $meditationId, ?string $deviceId): array
    {
        if (! $deviceId) {
            return [];
        }

        return HanalReaction::where('meditation_id', $meditationId)
            ->where('device_id', $deviceId)
            ->pluck('reaction')
            ->all();
    }
}
