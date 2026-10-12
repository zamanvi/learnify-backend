<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Restructure the exam-vocabulary chapter
 * "📝 পরীক্ষার English — ভর্তি ও চাকরির প্রস্তুতি" the same way the BCS
 * chapter was restructured:
 *
 *   - lesson "✅ …came…" (261 words, BCS + DU + RU + Medical + banks/jobs mixed)
 *       -> 11 lessons grouped by the exam tag already stored on each word
 *   - lesson "🔮 …may come…" (186 words) -> new chapter, 6 alphabetical lessons
 *   - chapter titles: "… — পরীক্ষায় এসেছে" / "… — ভবিষ্যৎ পরীক্ষায় আসতে পারে"
 *
 * Data problems fixed on the way (all backed up, see down()):
 *   - the "📌 দ্রষ্টব্য" disclaimer was stored as a word row (it appeared in quizzes)
 *   - "Ntrca" is not a word (meaning সৎ, synonym honest)
 *   - 4 words (Capitulate, Denounce, Enervate, Garrulous) were in BOTH the
 *     "came" and the "may come" lesson; the "may come" copy is removed
 *
 * SAFETY (runs automatically on every Railway deploy):
 *   - words are only re-pointed (UPDATE words.lesson_id); the six removed rows
 *     are copied to the backup table first (full row, same id) and restored by down()
 *   - the two existing lessons are re-used, no lesson is deleted
 *   - it only runs when the chapter looks EXACTLY like the audited data
 *     (2 lessons, 261 + 186 words, the junk rows, tag group sizes, block starts);
 *     anything else -> warning, nothing changed
 *   - full-table snapshots bak_adm_<date>_* are taken before the first change
 *   - one transaction + verification; exceptions are logged, never thrown, so
 *     this can never stop the server from booting
 */
return new class extends Migration
{
    private const BACKUP         = 'adm_restructure_backup';
    private const OLD_TITLE      = '📝 পরীক্ষার English — ভর্তি ও চাকরির প্রস্তুতি';
    private const CAME_TITLE     = '📝 Admission & Job English — পরীক্ষায় এসেছে';
    private const PROBABLE_TITLE = '📝 Admission & Job English — ভবিষ্যৎ পরীক্ষায় আসতে পারে';

    /** group key => [expected size, lesson title] in display order */
    private const CAME_GROUPS = [
        'BCS38-40' => [42, '✅ ৩৮–৪০তম BCS — যে শব্দগুলো এসেছিল'],
        'BCS41'    => [27, '✅ ৪১তম BCS — যে শব্দগুলো এসেছিল'],
        'BCS42'    => [26, '✅ ৪২তম BCS — যে শব্দগুলো এসেছিল'],
        'BCS43'    => [29, '✅ ৪৩তম BCS — যে শব্দগুলো এসেছিল'],
        'BCS44'    => [27, '✅ ৪৪তম BCS — যে শব্দগুলো এসেছিল'],
        'BCS45'    => [24, '✅ ৪৫তম BCS — যে শব্দগুলো এসেছিল'],
        'DU2018'   => [18, '✅ DU ভর্তি পরীক্ষা ২০১৮ — যে শব্দগুলো এসেছিল'],
        'DU2019'   => [20, '✅ DU ভর্তি পরীক্ষা ২০১৯ — যে শব্দগুলো এসেছিল'],
        'DU2020'   => [19, '✅ DU ভর্তি পরীক্ষা ২০২০ — যে শব্দগুলো এসেছিল'],
        'OTHER'    => [27, '✅ RU, Medical ও ব্যাংক/চাকরির পরীক্ষা — যে শব্দগুলো এসেছিল'],
    ];

    /** [first word of block, block size, lesson title] for the 182 "may come" words */
    private const MAY_BLOCKS = [
        ['Aberrant',  30, '🔮 আসতে পারে (১) — Ab থেকে Ca'],
        ['Caveat',    30, '🔮 আসতে পারে (২) — Ca থেকে Di'],
        ['Disparage', 30, '🔮 আসতে পারে (৩) — Di থেকে Ha'],
        ['Hapless',   30, '🔮 আসতে পারে (৪) — Ha থেকে Na'],
        ['Nebulous',  31, '🔮 আসতে পারে (৫) — Ne থেকে Sa'],
        ['Salient',   31, '🔮 আসতে পারে (৬) — Sa থেকে Ze'],
    ];

    private const EXPECTED_BOTH = ['capitulate', 'denounce', 'enervate', 'garrulous'];

    public function up(): void
    {
        try {
            $this->restructure();
        } catch (\Throwable $e) {
            Log::error('[adm-restructure] aborted, nothing was changed: ' . $e->getMessage());
        }
    }

    private function skip(string $why): void
    {
        Log::warning('[adm-restructure] skipped, nothing was changed: ' . $why);
    }

    private static function norm(string $w): string
    {
        return preg_replace('/[^a-z]/', '', strtolower($w));
    }

    private static function groupOf(string $tag): string
    {
        if (preg_match('/^BCS-(\d+)/', $tag, $m)) {
            $n = (int) $m[1];
            return $n <= 40 ? 'BCS38-40' : 'BCS' . $n;
        }
        if (preg_match('/^DU Admission-(\d{4})/', $tag, $m)) {
            return 'DU' . $m[1];
        }
        return 'OTHER';
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
        if (count($cameWords) !== 261 || count($probWords) !== 186) {
            $this->skip('word counts are ' . count($cameWords) . '/' . count($probWords) . ', expected 261/186'); return;
        }

        // junk rows in the "came" lesson
        $junk = [];
        foreach ($cameWords as $w) {
            $n = self::norm((string) $w->word);
            if (str_starts_with((string) $w->word, '📌')) { $junk['disclaimer'][] = $w->id; }
            elseif ($n === 'ntrca' && trim((string) $w->synonyms) === 'NTRCA') { $junk['ntrca'][] = $w->id; }
        }
        if (count($junk['disclaimer'] ?? []) !== 1 || count($junk['ntrca'] ?? []) !== 1) {
            $this->skip('junk rows are not exactly the audited ones'); return;
        }
        $junkIds = array_merge($junk['disclaimer'], $junk['ntrca']);

        // words present in both lessons
        $cameNorm = [];
        foreach ($cameWords as $w) { $cameNorm[self::norm((string) $w->word)] = true; }
        $bothIds = [];
        $bothNames = [];
        foreach ($probWords as $w) {
            $n = self::norm((string) $w->word);
            if (isset($cameNorm[$n])) { $bothIds[] = $w->id; $bothNames[] = $n; }
        }
        sort($bothNames);
        $expectedBoth = self::EXPECTED_BOTH;
        sort($expectedBoth);
        if ($bothNames !== $expectedBoth) {
            $this->skip('words in both lessons are [' . implode(',', $bothNames) . ']'); return;
        }

        // "came" groups
        $groups = [];
        foreach ($cameWords as $w) {
            if (in_array($w->id, $junkIds, true)) { continue; }
            $groups[self::groupOf((string) $w->synonyms)][] = $w->id;
        }
        foreach (self::CAME_GROUPS as $key => [$size]) {
            if (count($groups[$key] ?? []) !== $size) {
                $this->skip("group {$key} has " . count($groups[$key] ?? []) . " words, expected {$size}"); return;
            }
        }
        if (count($groups) !== count(self::CAME_GROUPS)) { $this->skip('unexpected extra exam groups'); return; }

        // "may come" blocks
        $mayWords = array_values(array_filter($probWords, fn ($w) => !in_array($w->id, $bothIds, true)));
        $blocks = [];
        $offset = 0;
        foreach (self::MAY_BLOCKS as [$first, $size, $title]) {
            if (!isset($mayWords[$offset]) || strcasecmp(trim((string) $mayWords[$offset]->word), $first) !== 0) {
                $this->skip("block at {$offset} does not start with '{$first}'"); return;
            }
            $blocks[] = ['title' => $title, 'ids' => array_map(fn ($w) => $w->id, array_slice($mayWords, $offset, $size))];
            $offset += $size;
        }
        if ($offset !== count($mayWords) || $offset !== 182) { $this->skip('block sizes do not add up to 182'); return; }

        // came plan: ordered list of [title, ids]
        $camePlan = [];
        foreach (self::CAME_GROUPS as $key => [$size, $title]) {
            $ids = $groups[$key];
            if ($key === 'BCS38-40') {
                $half = intdiv(count($ids), 2);
                $camePlan[] = [$title . ' (১)', array_slice($ids, 0, $half)];
                $camePlan[] = [$title . ' (২)', array_slice($ids, $half)];
            } else {
                $camePlan[] = [$title, $ids];
            }
        }
        $keptWordIds = [];
        foreach ($camePlan as [, $ids]) { $keptWordIds = array_merge($keptWordIds, $ids); }
        foreach ($blocks as $b) { $keptWordIds = array_merge($keptWordIds, $b['ids']); }
        sort($keptWordIds);
        if (count($keptWordIds) !== 441) { $this->skip('kept word count is ' . count($keptWordIds) . ', expected 441'); return; }

        // ── full-table snapshots ────────────────────────────────────────────
        $tag = 'bak_adm_' . date('Ymd');
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

                // 2. remove the junk / duplicate rows (copied above)
                foreach (array_chunk($deleteIds, 100) as $chunk) {
                    DB::table('words')->whereIn('id', $chunk)->delete();
                }

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

                // 4. "came" lessons: first one re-uses the old lesson, rest are new
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
                if ($c1 !== 11 || $c2 !== 6) { throw new \RuntimeException("lesson counts {$c1}/{$c2}, expected 11/6"); }
            });
        } catch (\Throwable $e) {
            Schema::dropIfExists(self::BACKUP);   // allow a clean re-run after a failed attempt
            throw $e;
        }

        Log::info('[adm-restructure] done: chapter ' . $chapter->id . ' -> 11 "came" lessons, new chapter with 6 "may come" lessons, 6 junk/duplicate rows removed');
    }

    public function down(): void
    {
        if (!Schema::hasTable(self::BACKUP)) {
            return;
        }
        DB::transaction(function () {
            $rows = DB::table(self::BACKUP)->get();

            // lessons first, so deleted words can point at them again
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
