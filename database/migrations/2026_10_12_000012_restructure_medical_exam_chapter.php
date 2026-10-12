<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Restructure the Medical vocabulary chapter
 * "🏥 Medical English — MBBS ভর্তি পরীক্ষার প্রস্তুতি" the same way the BCS and
 * the Admission & Job chapters were restructured:
 *
 *   - lesson "✅ MBBS-তে আসা শব্দ" (182 rows) -> 6 lessons, one per MBBS year tag
 *   - lesson "🔮 আসতে পারে" (114 words)       -> new chapter, 4 themed lessons
 *   - chapter titles: "… — পরীক্ষায় এসেছে" / "… — ভবিষ্যৎ পরীক্ষায় আসতে পারে"
 *
 * Data problems fixed on the way (backed up, restored by down()):
 *   - the "📌 দ্রষ্টব্য" disclaimer was stored as a word row (it appeared in quizzes)
 *   - "Dormant" was in BOTH lessons; the "may come" copy is removed
 *
 * SAFETY (runs automatically on every Railway deploy):
 *   - words are only re-pointed (UPDATE words.lesson_id); the two removed rows
 *     are copied to the backup table first (full row, same id)
 *   - the two existing lessons are re-used, no lesson is deleted
 *   - it only runs when the chapter looks EXACTLY like the audited data
 *     (2 lessons, 182 + 114 rows, year-tag sizes, the exact 113 "may come" words)
 *   - full-table snapshots bak_med_<date>_* before the first change
 *   - one transaction + verification; exceptions are logged, never thrown
 */
return new class extends Migration
{
    private const BACKUP         = 'med_restructure_backup';
    private const OLD_TITLE      = '🏥 Medical English — MBBS ভর্তি পরীক্ষার প্রস্তুতি';
    private const CAME_TITLE     = '🏥 Medical English — পরীক্ষায় এসেছে';
    private const PROBABLE_TITLE = '🏥 Medical English — ভবিষ্যৎ পরীক্ষায় আসতে পারে';

    /** MBBS year => [expected size, lesson title] */
    private const CAME_GROUPS = [
        '2018' => [31, '✅ MBBS ২০১৮ — যে শব্দগুলো এসেছিল'],
        '2019' => [30, '✅ MBBS ২০১৯ — যে শব্দগুলো এসেছিল'],
        '2020' => [30, '✅ MBBS ২০২০ — যে শব্দগুলো এসেছিল'],
        '2021' => [30, '✅ MBBS ২০২১ — যে শব্দগুলো এসেছিল'],
        '2022' => [30, '✅ MBBS ২০২২ — যে শব্দগুলো এসেছিল'],
        '2023' => [30, '✅ MBBS ২০২৩ — যে শব্দগুলো এসেছিল'],
    ];

    /** themed blocks of the 113 "may come" words (case-insensitive, word text) */
    private function mayBlocks(): array
    {
        return [
            ['🔮 আসতে পারে (১) — রোগ ও লক্ষণ', explode(' ',
                'Abrasion Afebrile Amnesia Arrhythmia Asthma Calcification Cataracts Cirrhosis Coma Dermatitis Diabetes '
                . 'Dysfunction Embolism Gangrene Hypertrophy Hypoglycemia Hypoxia Lesion Lymphoma Malaria Malnourished '
                . 'Menopause Osteoporosis Palpitation Sclerosis Tachycardia Tuberculosis Zoonosis Pathogenic Carcinogen')],
            ['🔮 আসতে পারে (২) — ওষুধ ও চিকিৎসা-পদ্ধতি', explode(' ',
                'Analgesic Antibiotic Anticoagulant Antifungal Antipyretic Antiseptic Antiviral Catheter Contraindication '
                . 'Dialysis Dissect Diuretic Endoscopy Incision Insulin Intubation Mastectomy Phlebotomy Rehabilitation '
                . 'Sterilize Suture Transfusion Vasodilator Echocardiogram')],
            ['🔮 আসতে পারে (৩) — শরীরের অঙ্গ ও অংশ', explode(' ',
                'Adrenal Articular Bile Capillary Cell Cerebral Cornea Cortex Epidermis Fibrous Gallbladder '
                . 'Gastrointestinal Liver Lumbar Placenta Plasma Pulmonary Respiratory Retina Spleen Thyroid Trachea '
                . 'Uterus Ventricle Vertebra Visceral Somatic Serum')],
            ['🔮 আসতে পারে (৪) — জৈবরসায়ন ও চিকিৎসাবিজ্ঞানের শাখা', explode(' ',
                'Acuity Allergen Antigen Electrolyte Enzyme Estrogen Excrete Genome Hormone Metabolism Mutation '
                . 'Neurotransmitter Peptic Receptor Anatomy Cytology Hematology Immunology Microbiology Morphology '
                . 'Nephrology Obstetrician Ophthalmology Orthopedics Pediatrics Pharmacology Psychiatry Radiology '
                . 'Rheumatology Toxicology Virology')],
        ];
    }

    public function up(): void
    {
        try {
            $this->restructure();
        } catch (\Throwable $e) {
            Log::error('[med-restructure] aborted, nothing was changed: ' . $e->getMessage());
        }
    }

    private function skip(string $why): void
    {
        Log::warning('[med-restructure] skipped, nothing was changed: ' . $why);
    }

    private static function norm(string $w): string
    {
        return preg_replace('/[^a-z]/', '', strtolower($w));
    }

    private function restructure(): void
    {
        foreach (['chapters', 'lessons', 'words'] as $t) {
            if (!Schema::hasTable($t)) { $this->skip("table {$t} missing"); return; }
        }
        if (Schema::hasTable(self::BACKUP)) { $this->skip('already applied (backup table exists)'); return; }

        $chapter = DB::table('chapters')->where('title', self::OLD_TITLE)->orderBy('id')->first();
        if (!$chapter) { $this->skip('chapter not found under its original title'); return; }
        if (DB::table('chapters')->where('title', self::PROBABLE_TITLE)->exists()) {
            $this->skip('target chapter already exists'); return;
        }

        $lessons = DB::table('lessons')->where('chapter_id', $chapter->id)->orderBy('id')->get();
        if ($lessons->count() !== 2) { $this->skip('chapter has ' . $lessons->count() . ' lessons, expected 2'); return; }
        $came = $lessons[0];
        $prob = $lessons[1];
        if (!str_starts_with($came->title, '✅') || !str_starts_with($prob->title, '🔮')) {
            $this->skip('lesson titles are not ✅/🔮'); return;
        }

        $cameWords = DB::table('words')->where('lesson_id', $came->id)->orderBy('id')->get()->all();
        $probWords = DB::table('words')->where('lesson_id', $prob->id)->orderBy('id')->get()->all();
        if (count($cameWords) !== 182 || count($probWords) !== 114) {
            $this->skip('word counts are ' . count($cameWords) . '/' . count($probWords) . ', expected 182/114'); return;
        }

        // junk row: the disclaimer
        $junkIds = [];
        foreach ($cameWords as $w) {
            if (str_starts_with((string) $w->word, '📌')) { $junkIds[] = $w->id; }
        }
        if (count($junkIds) !== 1) { $this->skip('disclaimer row not found exactly once'); return; }

        // words present in both lessons
        $cameNorm = [];
        foreach ($cameWords as $w) { $cameNorm[self::norm((string) $w->word)] = true; }
        $bothIds = [];
        $bothNames = [];
        foreach ($probWords as $w) {
            $n = self::norm((string) $w->word);
            if (isset($cameNorm[$n])) { $bothIds[] = $w->id; $bothNames[] = $n; }
        }
        if ($bothNames !== ['dormant']) {
            $this->skip('words in both lessons are [' . implode(',', $bothNames) . ']'); return;
        }

        // "came" groups by year tag
        $groups = [];
        foreach ($cameWords as $w) {
            if (in_array($w->id, $junkIds, true)) { continue; }
            if (!preg_match('/^MBBS-(\d{4})$/', trim((string) $w->synonyms), $m)) {
                $this->skip('word ' . $w->id . ' has no MBBS year tag'); return;
            }
            $groups[$m[1]][] = $w->id;
        }
        foreach (self::CAME_GROUPS as $year => [$size]) {
            if (count($groups[$year] ?? []) !== $size) {
                $this->skip("year {$year} has " . count($groups[$year] ?? []) . " words, expected {$size}"); return;
            }
        }
        if (count($groups) !== count(self::CAME_GROUPS)) { $this->skip('unexpected extra year tags'); return; }

        // "may come" blocks: the themed lists must cover the 113 remaining words exactly
        $mayWords = array_values(array_filter($probWords, fn ($w) => !in_array($w->id, $bothIds, true)));
        $byName = [];
        foreach ($mayWords as $w) { $byName[strtolower(trim((string) $w->word))] = $w->id; }
        $blocks = [];
        $assigned = [];
        foreach ($this->mayBlocks() as [$title, $names]) {
            $ids = [];
            foreach ($names as $name) {
                $key = strtolower($name);
                if (!isset($byName[$key]) || isset($assigned[$key])) {
                    $this->skip("'may come' word '{$name}' not found or listed twice"); return;
                }
                $assigned[$key] = true;
                $ids[] = $byName[$key];
            }
            sort($ids);
            $blocks[] = ['title' => $title, 'ids' => $ids];
        }
        if (count($assigned) !== 113 || count($byName) !== 113) { $this->skip('themed lists do not cover the 113 words'); return; }

        $camePlan = [];
        foreach (self::CAME_GROUPS as $year => [$size, $title]) {
            $camePlan[] = [$title, $groups[$year]];
        }
        $keptWordIds = [];
        foreach ($camePlan as [, $ids]) { $keptWordIds = array_merge($keptWordIds, $ids); }
        foreach ($blocks as $b) { $keptWordIds = array_merge($keptWordIds, $b['ids']); }
        sort($keptWordIds);
        if (count($keptWordIds) !== 294) { $this->skip('kept word count is ' . count($keptWordIds) . ', expected 294'); return; }

        // ── full-table snapshots ────────────────────────────────────────────
        $tag = 'bak_med_' . date('Ymd');
        foreach (['chapters', 'lessons', 'words', 'user_lesson_round_progress', 'unlocked_lessons'] as $t) {
            $copy = "{$tag}_{$t}";
            if (!Schema::hasTable($t) || Schema::hasTable($copy)) { continue; }
            DB::statement("CREATE TABLE `{$copy}` AS SELECT * FROM `{$t}`");
            if (DB::table($t)->count() !== DB::table($copy)->count()) {
                throw new \RuntimeException("snapshot of {$t} incomplete");
            }
        }

        Schema::create(self::BACKUP, function ($t) {
            $t->id();
            $t->string('kind', 20)->index();   // word | deleted_word | lesson | new_lesson | chapter | new_chapter | progress
            $t->unsignedBigInteger('ref_id');
            $t->longText('payload')->nullable();
        });

        $hasLayout = Schema::hasColumn('lessons', 'layout');
        $deleteIds = array_merge($junkIds, $bothIds);

        try {
            DB::transaction(function () use ($chapter, $came, $prob, $cameWords, $probWords, $camePlan, $blocks, $deleteIds, $keptWordIds, $hasLayout) {
                $now = now();

                // 1. backups
                $rows = [];
                foreach (array_merge($cameWords, $probWords) as $w) {
                    if (in_array($w->id, $deleteIds, true)) {
                        $rows[] = ['kind' => 'deleted_word', 'ref_id' => $w->id, 'payload' => json_encode((array) $w, JSON_UNESCAPED_UNICODE)];
                    } else {
                        $rows[] = ['kind' => 'word', 'ref_id' => $w->id, 'payload' => json_encode(['lesson_id' => $w->lesson_id])];
                    }
                }
                $rows[] = ['kind' => 'lesson', 'ref_id' => $came->id, 'payload' => json_encode((array) $came, JSON_UNESCAPED_UNICODE)];
                $rows[] = ['kind' => 'lesson', 'ref_id' => $prob->id, 'payload' => json_encode((array) $prob, JSON_UNESCAPED_UNICODE)];
                $rows[] = ['kind' => 'chapter', 'ref_id' => $chapter->id, 'payload' => json_encode((array) $chapter, JSON_UNESCAPED_UNICODE)];
                if (Schema::hasTable('user_lesson_round_progress')) {
                    foreach (DB::table('user_lesson_round_progress')->whereIn('lesson_id', [$came->id, $prob->id])->get() as $p) {
                        $rows[] = ['kind' => 'progress', 'ref_id' => $p->id, 'payload' => json_encode((array) $p)];
                    }
                }
                foreach (array_chunk($rows, 300) as $chunk) {
                    DB::table(self::BACKUP)->insert($chunk);
                }

                // 2. remove the disclaimer row and the duplicate (copied above)
                DB::table('words')->whereIn('id', $deleteIds)->delete();

                // 3. new "may come" chapter
                $chRow = (array) $chapter;
                unset($chRow['id']);
                $chRow['title']      = self::PROBABLE_TITLE;
                $chRow['created_at'] = $now;
                $chRow['updated_at'] = $now;
                $newChapterId = DB::table('chapters')->insertGetId($chRow);
                DB::table(self::BACKUP)->insert(['kind' => 'new_chapter', 'ref_id' => $newChapterId, 'payload' => '{}']);

                $template = function ($src, string $title, int $chapterId) use ($now, $hasLayout) {
                    $row = (array) $src;
                    unset($row['id']);
                    $row['title']      = $title;
                    $row['chapter_id'] = $chapterId;
                    $row['created_at'] = $now;
                    $row['updated_at'] = $now;
                    if ($hasLayout) { $row['layout'] = 'stacked'; }
                    return $row;
                };

                $assign = [];   // lessonId => [wordIds]

                // 4. "came" lessons: first one re-uses the old lesson
                foreach ($camePlan as $i => [$title, $ids]) {
                    if ($i === 0) {
                        $upd = ['title' => $title, 'updated_at' => $now];
                        if ($hasLayout) { $upd['layout'] = 'stacked'; }
                        DB::table('lessons')->where('id', $came->id)->update($upd);
                        $lessonId = $came->id;
                    } else {
                        $lessonId = DB::table('lessons')->insertGetId($template($came, $title, $chapter->id));
                        DB::table(self::BACKUP)->insert(['kind' => 'new_lesson', 'ref_id' => $lessonId, 'payload' => '{}']);
                    }
                    $assign[$lessonId] = $ids;
                }

                // 5. "may come" lessons: first re-uses the old 🔮 lesson (moved to the new chapter)
                foreach ($blocks as $i => $b) {
                    if ($i === 0) {
                        $upd = ['title' => $b['title'], 'chapter_id' => $newChapterId, 'updated_at' => $now];
                        if ($hasLayout) { $upd['layout'] = 'stacked'; }
                        DB::table('lessons')->where('id', $prob->id)->update($upd);
                        $lessonId = $prob->id;
                    } else {
                        $lessonId = DB::table('lessons')->insertGetId($template($prob, $b['title'], $newChapterId));
                        DB::table(self::BACKUP)->insert(['kind' => 'new_lesson', 'ref_id' => $lessonId, 'payload' => '{}']);
                    }
                    $assign[$lessonId] = $b['ids'];
                }

                // 6. re-point words (ids unchanged)
                foreach ($assign as $lessonId => $ids) {
                    foreach (array_chunk($ids, 200) as $chunk) {
                        DB::table('words')->whereIn('id', $chunk)->update(['lesson_id' => $lessonId, 'updated_at' => $now]);
                    }
                }

                // 7. old stars no longer match the content of the two re-used lessons
                if (Schema::hasTable('user_lesson_round_progress')) {
                    DB::table('user_lesson_round_progress')->whereIn('lesson_id', [$came->id, $prob->id])->delete();
                }

                // 8. chapter title
                DB::table('chapters')->where('id', $chapter->id)->update(['title' => self::CAME_TITLE, 'updated_at' => $now]);

                // 9. verify before commit
                $seen = [];
                foreach ($assign as $lessonId => $ids) {
                    $actual = DB::table('words')->where('lesson_id', $lessonId)->pluck('id')->all();
                    sort($actual);
                    $expected = $ids;
                    sort($expected);
                    if ($actual !== $expected) { throw new \RuntimeException("lesson {$lessonId}: word set mismatch"); }
                    $seen = array_merge($seen, $actual);
                }
                sort($seen);
                if ($seen !== $keptWordIds) { throw new \RuntimeException('kept word set changed'); }
                $c1 = DB::table('lessons')->where('chapter_id', $chapter->id)->count();
                $c2 = DB::table('lessons')->where('chapter_id', $newChapterId)->count();
                if ($c1 !== 6 || $c2 !== 4) { throw new \RuntimeException("lesson counts {$c1}/{$c2}, expected 6/4"); }
            });
        } catch (\Throwable $e) {
            Schema::dropIfExists(self::BACKUP);   // allow a clean re-run after a failed attempt
            throw $e;
        }

        Log::info('[med-restructure] done: chapter ' . $chapter->id . ' -> 6 "came" lessons, new chapter with 4 "may come" lessons, 2 junk/duplicate rows removed');
    }

    public function down(): void
    {
        if (!Schema::hasTable(self::BACKUP)) {
            return;
        }
        DB::transaction(function () {
            $rows = DB::table(self::BACKUP)->get();

            foreach ($rows->where('kind', 'lesson') as $r) {
                $row = (array) json_decode($r->payload, true);
                unset($row['id']);
                DB::table('lessons')->where('id', $r->ref_id)->update($row);
            }
            foreach ($rows->where('kind', 'word') as $r) {
                DB::table('words')->where('id', $r->ref_id)->update(['lesson_id' => json_decode($r->payload)->lesson_id]);
            }
            foreach ($rows->where('kind', 'deleted_word') as $r) {
                $row = (array) json_decode($r->payload, true);
                if (!DB::table('words')->where('id', $row['id'])->exists()) {
                    DB::table('words')->insert($row);
                }
            }
            foreach ($rows->where('kind', 'new_lesson') as $r) {
                if (!DB::table('words')->where('lesson_id', $r->ref_id)->exists()) {
                    DB::table('lessons')->where('id', $r->ref_id)->delete();
                }
            }
            foreach ($rows->where('kind', 'new_chapter') as $r) {
                if (!DB::table('lessons')->where('chapter_id', $r->ref_id)->exists()) {
                    DB::table('chapters')->where('id', $r->ref_id)->delete();
                }
            }
            foreach ($rows->where('kind', 'chapter') as $r) {
                $row = (array) json_decode($r->payload, true);
                unset($row['id']);
                DB::table('chapters')->where('id', $r->ref_id)->update($row);
            }
            if (Schema::hasTable('user_lesson_round_progress')) {
                foreach ($rows->where('kind', 'progress') as $r) {
                    $row = (array) json_decode($r->payload, true);
                    if (!DB::table('user_lesson_round_progress')->where('id', $row['id'])->exists()) {
                        DB::table('user_lesson_round_progress')->insert($row);
                    }
                }
            }
        });
        Schema::dropIfExists(self::BACKUP);
    }
};
