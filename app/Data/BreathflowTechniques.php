<?php

namespace App\Data;

/**
 * Static dataset for BreathFlow programmatic-SEO pages.
 * 12 breathing techniques + 1 Panic SOS variant = 13 entries.
 *
 * The source of truth: cc-project-10-breathflow/breathflow_app/lib/home/domain/models/breathing_technique.dart
 */
class BreathflowTechniques
{
    public const CATEGORY_LABELS = [
        'panic' => 'Emergency · Panic SOS',
        'stress' => 'Stress relief',
        'sleep' => 'Sleep',
        'focus' => 'Focus',
        'energy' => 'Energy',
    ];

    /** @return array<int, array<string, mixed>> */
    public static function all(): array
    {
        return [
            [
                'slug' => 'panic-sos-breathing',
                'name' => 'Panic SOS Breathing',
                'tagline' => 'Instant calm in a panic attack',
                'category' => 'panic',
                'pattern' => ['inhale' => 2, 'hold_in' => 0, 'exhale' => 2, 'hold_out' => 0],
                'cycles' => 20,
                'intro' => 'Panic SOS starts at your panicked breathing rate — fast inhales and exhales — and gradually slows you down over five minutes. Most breathing apps assume you are already calm. This one is built for the moment you are not.',
                'science' => 'When you are in panic, asking you to inhale for four seconds feels impossible. Research on respiratory entrainment shows the autonomic nervous system follows breath rhythm. By matching your panic speed first and decelerating in tiny increments, the SOS pattern coaxes the parasympathetic system back online without resistance.',
                'steps' => [
                    'Open BreathFlow and tap the SOS button on the home screen.',
                    'Breathe along with the haptic pulse — short inhale, short exhale.',
                    'Do not focus on the screen. Let the haptic feedback guide your phone or palm.',
                    'Notice the rhythm slowing naturally as you continue.',
                    'After 3–5 minutes, you will be breathing at a calm 4 seconds in, 7 seconds out without effort.',
                ],
                'use_cases' => [
                    'Mid-panic attack when slow breathing feels impossible',
                    'Hyperventilation episodes',
                    'Public situations where you cannot leave',
                    'Recurring 3am wake-ups with racing heart',
                ],
                'related' => ['physiological-sigh', '4-7-8-breathing', 'extended-exhale'],
                'seo_title' => 'Panic Attack SOS Breathing — Free One-Tap Calm Down',
                'seo_description' => 'Panic SOS breathing meets you at your panic speed and gradually slows you down. Free, no subscription. Used by people with panic disorder, anxiety, PTSD.',
            ],
            [
                'slug' => 'box-breathing',
                'name' => 'Box Breathing',
                'tagline' => 'Navy SEAL calm under pressure',
                'category' => 'stress',
                'pattern' => ['inhale' => 4, 'hold_in' => 4, 'exhale' => 4, 'hold_out' => 4],
                'cycles' => 8,
                'intro' => 'Box breathing — also called square breathing or 4-4-4-4 — is a four-phase pattern of equal length: inhale, hold, exhale, hold. It is the breathing technique taught in Navy SEAL training for staying calm under high-stress conditions.',
                'science' => 'The equal-length cycle stabilizes heart rate variability and signals the vagus nerve that you are safe. The hold phases, in particular, raise CO₂ tolerance slightly, which dampens the fight-or-flight response. Studies in occupational stress contexts show measurable cortisol reduction within a single session.',
                'steps' => [
                    'Sit upright with feet flat. You can do this anywhere — desk, car (parked), bathroom stall.',
                    'Exhale fully to empty your lungs.',
                    'Inhale through your nose for 4 seconds.',
                    'Hold the breath in for 4 seconds.',
                    'Exhale through your mouth for 4 seconds.',
                    'Hold empty for 4 seconds. Repeat 8 cycles (~2 minutes).',
                ],
                'use_cases' => [
                    'Before a high-stakes meeting or presentation',
                    'Right after a stressful email or call',
                    'Workout warm-up for nervous system regulation',
                    'When you cannot leave your desk but need to reset',
                ],
                'related' => ['coherent-breathing', 'triangle-breathing', 'focus-breath'],
                'seo_title' => 'Box Breathing — 4-4-4-4 Pattern Used by Navy SEALs',
                'seo_description' => 'Box breathing (square breathing) is a 4-4-4-4 pattern Navy SEALs use to stay calm under pressure. Free guided sessions in BreathFlow.',
            ],
            [
                'slug' => '4-7-8-breathing',
                'name' => '4-7-8 Breathing',
                'tagline' => "Dr. Andrew Weil's natural tranquilizer",
                'category' => 'sleep',
                'pattern' => ['inhale' => 4, 'hold_in' => 7, 'exhale' => 8, 'hold_out' => 0],
                'cycles' => 4,
                'intro' => 'The 4-7-8 technique is a pranayama-derived pattern popularized by Dr. Andrew Weil. Inhale for 4, hold for 7, exhale for 8. He calls it a natural tranquilizer for the nervous system.',
                'science' => 'The 1:1.75:2 ratio biases the cycle heavily toward exhalation. Long exhales preferentially activate the parasympathetic branch of the autonomic nervous system via the vagus nerve. The 7-second hold also creates a mild oxygen-carbon dioxide rebalancing that shifts blood chemistry toward calm.',
                'steps' => [
                    'Lie on your back or sit upright. Place the tip of your tongue behind your upper front teeth.',
                    'Exhale completely through your mouth, making a soft whoosh sound.',
                    'Close your mouth. Inhale quietly through your nose for 4 seconds.',
                    'Hold the breath for 7 seconds.',
                    'Exhale through your mouth for 8 seconds, making the whoosh sound.',
                    'Repeat 4 cycles. Most people fall asleep before finishing.',
                ],
                'use_cases' => [
                    'Trouble falling asleep (insomnia)',
                    'Middle-of-the-night anxiety',
                    'Acute stress response (replaces a cigarette or drink)',
                    'Pre-flight nerves',
                ],
                'related' => ['2-1-relaxing', 'sleep-wave-breathing', 'extended-exhale'],
                'seo_title' => "4-7-8 Breathing — Andrew Weil's Sleep Technique (Free Guide)",
                'seo_description' => '4-7-8 breathing technique by Dr. Andrew Weil. A natural tranquilizer for falling asleep faster and calming anxiety. Free guided sessions in BreathFlow.',
            ],
            [
                'slug' => 'physiological-sigh',
                'name' => 'Physiological Sigh',
                'tagline' => "Stanford's fastest way to calm down",
                'category' => 'stress',
                'pattern' => ['inhale' => 2, 'hold_in' => 1, 'exhale' => 6, 'hold_out' => 0],
                'cycles' => 6,
                'intro' => 'The physiological sigh — researched at Stanford by Andrew Huberman — is a double inhale followed by a long exhale. Your body already does this involuntarily during sobbing and right before falling asleep. Done deliberately, it is the fastest known way to drop physiological stress.',
                'science' => 'The second short inhale reinflates alveoli that collapse during stressed shallow breathing. The long exhale offloads accumulated CO₂. Studies at Stanford and elsewhere show measurable mood and anxiety improvement after just five minutes a day. Faster onset than meditation, yoga, or visual mindfulness for acute stress.',
                'steps' => [
                    'Inhale deeply through your nose to fill your lungs.',
                    'Without exhaling, take a second, sharper inhale through your nose to top off your lungs.',
                    'Exhale slowly and completely through your mouth — twice as long as the inhale.',
                    'Repeat 1–3 times for in-the-moment relief, or for 5 minutes for deeper effect.',
                ],
                'use_cases' => [
                    'Acute stress spike (bad news, conflict, performance pressure)',
                    'Between meetings to reset',
                    'When you only have 30 seconds',
                    'Pre-sleep wind down',
                ],
                'related' => ['panic-sos-breathing', 'extended-exhale', 'deep-calm-breathing'],
                'seo_title' => "Physiological Sigh — Stanford's Fastest Calm-Down Breathing",
                'seo_description' => 'The physiological sigh (Stanford / Andrew Huberman) is the fastest researched way to reduce stress. Double inhale, long exhale. Free guided sessions in BreathFlow.',
            ],
            [
                'slug' => 'deep-calm-breathing',
                'name' => 'Deep Calm Breathing',
                'tagline' => 'Activate the parasympathetic system',
                'category' => 'stress',
                'pattern' => ['inhale' => 5, 'hold_in' => 0, 'exhale' => 7, 'hold_out' => 0],
                'cycles' => 10,
                'intro' => 'Deep calm is the simplest stress-relief pattern: a slow 5-second inhale followed by a longer 7-second exhale, repeated for 10 cycles. No holds, no counting beyond two numbers.',
                'science' => 'Exhales longer than inhales shift the autonomic balance toward the parasympathetic branch. Heart rate slows on exhale (respiratory sinus arrhythmia) — the longer the exhale, the more pronounced the drop. Two minutes of 5-in / 7-out can produce measurable calm in heart rate variability biofeedback studies.',
                'steps' => [
                    'Sit or lie comfortably. Close your eyes if you can.',
                    'Inhale slowly through your nose for 5 seconds, expanding your belly first, then chest.',
                    'Exhale slowly through your mouth or nose for 7 seconds, fully emptying your lungs.',
                    'Repeat for 10 cycles (~2 minutes). Notice your shoulders dropping.',
                ],
                'use_cases' => [
                    'Anxiety that does not require urgent SOS',
                    'Daily morning or evening calming practice',
                    'Post-exercise wind down',
                    "Beginner's first breathing exercise",
                ],
                'related' => ['extended-exhale', 'coherent-breathing', '2-1-relaxing'],
                'seo_title' => 'Deep Calm Breathing — Slow Exhale Anxiety Relief (Free)',
                'seo_description' => 'Deep calm breathing (5-in, 7-out) activates the parasympathetic nervous system to relieve anxiety. Free guided sessions in BreathFlow.',
            ],
            [
                'slug' => 'extended-exhale',
                'name' => 'Extended Exhale',
                'tagline' => 'Vagus nerve stimulation through long exhales',
                'category' => 'stress',
                'pattern' => ['inhale' => 3, 'hold_in' => 0, 'exhale' => 9, 'hold_out' => 0],
                'cycles' => 8,
                'intro' => 'Extended exhale pushes the inhale-to-exhale ratio further than deep calm — a 3-second inhale matched with a 9-second exhale. The 1:3 ratio aggressively engages the vagus nerve.',
                'science' => 'The vagus nerve runs from your brainstem to your gut and is the primary highway of the parasympathetic system. Long exhales mechanically stretch the diaphragm and stimulate vagal afferents. Vagal tone improvements are linked to lower anxiety, better digestion, improved heart rate variability, and depression resilience.',
                'steps' => [
                    'Sit upright. Place one hand on your belly.',
                    'Inhale through your nose for 3 seconds, feeling your belly expand.',
                    'Exhale through pursed lips for 9 seconds, slow and controlled.',
                    'Allow a small natural pause before the next inhale.',
                    'Repeat 8 cycles. If 9 seconds is too long, start with 6 and build up.',
                ],
                'use_cases' => [
                    'Vagus nerve toning practice',
                    'Chronic anxiety management',
                    'Acid reflux or digestion issues (post-meal)',
                    'Pre-public-speaking nervous system reset',
                ],
                'related' => ['deep-calm-breathing', 'physiological-sigh', '2-1-relaxing'],
                'seo_title' => 'Extended Exhale Breathing — Vagus Nerve Stimulation',
                'seo_description' => 'Extended exhale breathing (1:3 ratio) stimulates the vagus nerve for deep anxiety relief and improved heart rate variability. Free in BreathFlow.',
            ],
            [
                'slug' => '2-1-relaxing',
                'name' => '2:1 Relaxing Breath',
                'tagline' => 'Exhale twice as long as inhale',
                'category' => 'sleep',
                'pattern' => ['inhale' => 4, 'hold_in' => 0, 'exhale' => 8, 'hold_out' => 0],
                'cycles' => 8,
                'intro' => 'The 2:1 relaxing breath is the simplest sleep-oriented pattern. Inhale for any length you like — 4 seconds is typical — then exhale for exactly twice that. No holds, no fuss.',
                'science' => 'Doubling the exhale is sufficient to tip autonomic balance toward parasympathetic dominance. Cardiology studies show measurable resting heart rate reduction within 4–8 cycles. It is also the easiest breathing pattern to remember when you are sleep-deprived and cannot count.',
                'steps' => [
                    'Lie on your back or curl in your preferred sleep position.',
                    'Inhale through your nose for 4 seconds.',
                    'Exhale through your nose or pursed lips for 8 seconds.',
                    'If 4–8 is too long, try 3–6 or even 2–4. The ratio matters more than the count.',
                    'Continue 8 cycles. Often people fall asleep before completing.',
                ],
                'use_cases' => [
                    'Falling asleep',
                    'Returning to sleep after waking at night',
                    'Quick anxiety relief in the evening',
                    "Children's bedtime routine (simplified count)",
                ],
                'related' => ['4-7-8-breathing', 'sleep-wave-breathing', 'deep-calm-breathing'],
                'seo_title' => '2:1 Breathing Technique — Sleep Faster Free Guide',
                'seo_description' => '2:1 relaxing breath (exhale twice as long as inhale) is a simple sleep-onset technique. No counting marathon. Free in BreathFlow.',
            ],
            [
                'slug' => 'sleep-wave-breathing',
                'name' => 'Sleep Wave Breathing',
                'tagline' => 'Gradually slowing pattern for bedtime',
                'category' => 'sleep',
                'pattern' => ['inhale' => 4, 'hold_in' => 3, 'exhale' => 7, 'hold_out' => 2],
                'cycles' => 6,
                'intro' => 'Sleep wave is a four-phase descending pattern that gradually lengthens the exhale and inserts a brief empty-lung pause — emulating the natural breath rhythm that occurs just before falling asleep.',
                'science' => 'Sleep-onset EEG studies show that breathing naturally lengthens its exhale and develops a small empty-lung pause as you transition through the hypnagogic state. Imitating this rhythm consciously can entrain the brain into the same state — researchers call it physiological mimicry.',
                'steps' => [
                    'Get into your sleeping position. Lights off, phone face down.',
                    'Inhale 4 seconds, hold 3 seconds, exhale 7 seconds, pause 2 seconds.',
                    'On each cycle, focus on the empty-lung pause — that quiet space between exhale and next inhale.',
                    'Repeat 6 cycles. By cycle 4 you should feel pre-sleep heaviness.',
                ],
                'use_cases' => [
                    'Bedtime routine ritual',
                    'Difficulty transitioning from wakefulness to sleep',
                    'Mind racing at night',
                    'Restless sleep onset',
                ],
                'related' => ['4-7-8-breathing', '2-1-relaxing', 'deep-calm-breathing'],
                'seo_title' => 'Sleep Wave Breathing — Natural Sleep-Onset Breath Pattern',
                'seo_description' => 'Sleep wave breathing mimics the natural rhythm of falling asleep — gradually slowing with empty-lung pauses. Free guided sessions in BreathFlow.',
            ],
            [
                'slug' => 'coherent-breathing',
                'name' => 'Coherent Breathing',
                'tagline' => '5.5 breaths per minute for optimal HRV',
                'category' => 'focus',
                'pattern' => ['inhale' => 5.5, 'hold_in' => 0, 'exhale' => 5.5, 'hold_out' => 0],
                'cycles' => 10,
                'intro' => 'Coherent breathing — also called resonance breathing — is the practice of breathing at approximately 5.5 breaths per minute (5.5-second inhale, 5.5-second exhale). It is the rate at which most adults\' heart rate variability peaks.',
                'science' => 'Each person has a resonance frequency for their cardiovascular system, usually between 4.5 and 7 breaths per minute. Breathing at this rate produces the largest possible swings in heart rate per breath — the gold standard for HRV biofeedback. Research links this practice to improved mood, lower blood pressure, and improved sleep.',
                'steps' => [
                    'Sit upright in a chair, feet flat. No slouching — diaphragm needs room.',
                    'Inhale through your nose for 5.5 seconds (round to 6 if easier).',
                    'Exhale through your nose for 5.5 seconds.',
                    'No holds. Smooth transition between inhale and exhale.',
                    'Continue for 10 minutes for measurable HRV improvement.',
                ],
                'use_cases' => [
                    'Daily HRV training',
                    'Pre-work focus warm-up',
                    'Recovery from intense workouts',
                    'Anxiety with attention difficulty',
                ],
                'related' => ['box-breathing', 'triangle-breathing', 'focus-breath'],
                'seo_title' => 'Coherent Breathing — 5.5 Breaths Per Minute HRV Training',
                'seo_description' => 'Coherent breathing (resonance breathing, 5.5 bpm) optimizes heart rate variability. Free guided sessions in BreathFlow.',
            ],
            [
                'slug' => 'triangle-breathing',
                'name' => 'Triangle Breathing',
                'tagline' => 'Three-phase balanced breath',
                'category' => 'focus',
                'pattern' => ['inhale' => 4, 'hold_in' => 4, 'exhale' => 4, 'hold_out' => 0],
                'cycles' => 10,
                'intro' => 'Triangle breathing is a three-phase pattern: inhale, hold, exhale. Same length on each side — the geometric equivalent of an equilateral triangle. Simpler than box breathing because there is no second hold.',
                'science' => "The single hold phase increases CO₂ tolerance modestly, which dampens the panic reflex (the brain's CO₂ alarm is one of the strongest panic triggers). Without the second hold, the pattern is gentler and easier for beginners and people with respiratory conditions.",
                'steps' => [
                    'Sit comfortably. Visualize a triangle in your mind.',
                    'Inhale for 4 seconds (climb up one side).',
                    'Hold for 4 seconds (across the top).',
                    'Exhale for 4 seconds (climb down the other side).',
                    'Repeat 10 cycles (~2 minutes).',
                ],
                'use_cases' => [
                    'Beginner alternative to box breathing',
                    'Pre-meditation warm-up',
                    'Test or interview anxiety',
                    'People with mild respiratory conditions (asthma) who find double holds difficult',
                ],
                'related' => ['box-breathing', 'coherent-breathing', 'focus-breath'],
                'seo_title' => 'Triangle Breathing — Three-Phase Calm Breath Technique',
                'seo_description' => 'Triangle breathing (inhale-hold-exhale, equal length) is a gentle alternative to box breathing. Free guided sessions in BreathFlow.',
            ],
            [
                'slug' => 'focus-breath',
                'name' => 'Focus Breath',
                'tagline' => 'Short holds for mental clarity',
                'category' => 'focus',
                'pattern' => ['inhale' => 4, 'hold_in' => 2, 'exhale' => 4, 'hold_out' => 2],
                'cycles' => 10,
                'intro' => 'Focus breath uses short 2-second holds between every phase — long enough to add structure, short enough to keep the rhythm energizing rather than calming. The result is a balanced state ideal for sustained mental work.',
                'science' => 'Brief holds elevate CO₂ slightly, which increases cerebral blood flow within healthy limits. Short controlled patterns also engage the prefrontal cortex through deliberate attention to breath count. Many flow-state researchers (Csíkszentmihályi, Kotler) recommend pre-work breathing as a gateway into focus.',
                'steps' => [
                    'Sit at your work station. Eyes closed or soft gaze.',
                    'Inhale 4, hold 2, exhale 4, hold 2.',
                    'Keep the rhythm metronomic — equal counts.',
                    'Continue 10 cycles before starting work. Notice the difference in concentration.',
                ],
                'use_cases' => [
                    'Before a 90-minute focus block',
                    'Pre-coding or pre-writing warm-up',
                    'Study sessions',
                    'Reset between meetings',
                ],
                'related' => ['box-breathing', 'coherent-breathing', 'triangle-breathing'],
                'seo_title' => 'Focus Breath — Pre-Work Concentration Breathing',
                'seo_description' => 'Focus breath (4-2-4-2) is a balanced breathing pattern for sustained mental clarity. Free guided sessions in BreathFlow.',
            ],
            [
                'slug' => 'energizing-breath',
                'name' => 'Energizing Breath',
                'tagline' => 'Quick inhale to boost alertness',
                'category' => 'energy',
                'pattern' => ['inhale' => 3, 'hold_in' => 3, 'exhale' => 2, 'hold_out' => 0],
                'cycles' => 12,
                'intro' => 'Energizing breath uses a longer inhale than exhale — the opposite of calming patterns. The result is mild sympathetic activation, which is what you want when you need to wake up, push through afternoon fatigue, or pre-game an important task.',
                'science' => 'Inhale-biased patterns increase oxygen uptake faster than they unload CO₂, producing brief mild hyperoxia. Heart rate accelerates on the inhale (respiratory sinus arrhythmia), so an inhale-emphasized pattern keeps heart rate slightly elevated — alertness without anxiety.',
                'steps' => [
                    'Sit or stand upright. Open your chest.',
                    'Inhale through your nose for 3 seconds, expanding fully.',
                    'Hold for 3 seconds at the top.',
                    'Exhale through your mouth for 2 seconds, sharper than your inhale.',
                    'Repeat 12 cycles. Stop if you feel lightheaded.',
                ],
                'use_cases' => [
                    'Morning wake-up (alternative to caffeine)',
                    '3pm energy slump',
                    'Pre-workout activation',
                    'Before a creative push',
                ],
                'related' => ['power-breath', 'focus-breath', 'physiological-sigh'],
                'seo_title' => 'Energizing Breath — Wake Up Without Caffeine',
                'seo_description' => 'Energizing breath uses inhale-biased patterns for alertness without anxiety. Free guided sessions in BreathFlow.',
            ],
            [
                'slug' => 'power-breath',
                'name' => 'Power Breath',
                'tagline' => 'Fast-paced breathing for energy spikes',
                'category' => 'energy',
                'pattern' => ['inhale' => 2, 'hold_in' => 2, 'exhale' => 2, 'hold_out' => 0],
                'cycles' => 15,
                'intro' => 'Power breath is short and fast — 2-2-2 — repeated 15 times. It is loosely modeled on kapalabhati (skull-shining breath) from yoga, modernized into a faster pre-workout activation tool.',
                'science' => 'Rapid breathing temporarily lowers blood CO₂, mildly alkalizing the blood. This produces a stimulating effect — a small natural adrenaline release, similar to a cold shower without the cold. Do not perform while driving or operating machinery. Stop immediately if you feel lightheaded.',
                'steps' => [
                    'Sit upright in a stable position (not standing — in case of lightheadedness).',
                    'Inhale sharply for 2 seconds.',
                    'Hold for 2 seconds.',
                    'Exhale forcefully for 2 seconds.',
                    'Repeat 15 cycles, then breathe normally for 30 seconds before standing.',
                ],
                'use_cases' => [
                    'Pre-workout pump (when caffeine is too much)',
                    'Mid-afternoon slump (alternative to second coffee)',
                    'Cold morning shower wake-up booster',
                    'Pre-competition activation',
                ],
                'related' => ['energizing-breath', 'focus-breath', 'physiological-sigh'],
                'seo_title' => 'Power Breath — Fast Pre-Workout Breathing Technique',
                'seo_description' => 'Power breath is a fast 2-2-2 pattern for pre-workout and high-energy moments. Free guided sessions in BreathFlow.',
            ],
        ];
    }

    public static function bySlug(string $slug): ?array
    {
        foreach (self::all() as $t) {
            if ($t['slug'] === $slug) return $t;
        }
        return null;
    }

    /** @return array<int, array<string, mixed>> */
    public static function related(string $slug): array
    {
        $t = self::bySlug($slug);
        if (!$t) return [];
        $bySlug = [];
        foreach (self::all() as $row) $bySlug[$row['slug']] = $row;
        $out = [];
        foreach ($t['related'] as $s) {
            if (isset($bySlug[$s])) $out[] = $bySlug[$s];
        }
        return $out;
    }

    public static function patternToString(array $pattern): string
    {
        $parts = [];
        $parts[] = self::fmt($pattern['inhale']) . 's in';
        if ($pattern['hold_in']) $parts[] = self::fmt($pattern['hold_in']) . 's hold';
        $parts[] = self::fmt($pattern['exhale']) . 's out';
        if ($pattern['hold_out']) $parts[] = self::fmt($pattern['hold_out']) . 's hold';
        return implode(' · ', $parts);
    }

    private static function fmt(float|int $n): string
    {
        return rtrim(rtrim(number_format((float)$n, 1, '.', ''), '0'), '.');
    }
}
