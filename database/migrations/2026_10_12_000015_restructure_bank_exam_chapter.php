<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Restructure the Bank vocabulary chapter
 * "🏦 Bank English — আর্থিক প্রতিষ্ঠানের সম্পূর্ণ প্রস্তুতি" like the BCS,
 * Admission & Job and Medical chapters:
 *
 *   - "✅ ব্যাংকে এসেছে শব্দ" (54, tag Sonali-2022)        -> chapter "… — পরীক্ষায় এসেছে", 2 lessons
 *   - the three "may come / topic" lessons (54 + 48 + 44)  -> new chapter "… — ভবিষ্যৎ পরীক্ষায় আসতে পারে",
 *                                                              6 lessons of ~20-26 words
 *
 * 13 rows are removed because the same word already exists in the same
 * chapter (8 copies of words that "came" were repeated in the topic lessons,
 * 4 repeats inside the topic lessons, and "Check" which is the spelling
 * variant of "Cheque"). Each removed row is copied to the backup table first.
 *
 * SAFETY (runs automatically on every Railway deploy):
 *   - words are only re-pointed (UPDATE words.lesson_id) except the 13 rows above
 *   - the four existing lessons are re-used, none is deleted
 *   - it only runs when the chapter looks EXACTLY like the audited data
 *     (4 lessons with the audited titles, 54/54/48/44 words, tags, and every
 *     removed row has the expected word text and a surviving twin)
 *   - full-table snapshots bak_bank_<date>_* before the first change
 *   - one transaction + verification; exceptions are logged, never thrown
 */
return new class extends Migration
{
    private const BACKUP         = 'bank_restructure_backup';
    private const OLD_TITLE      = '🏦 Bank English — আর্থিক প্রতিষ্ঠানের সম্পূর্ণ প্রস্তুতি';
    private const CAME_TITLE     = '🏦 Bank English — পরীক্ষায় এসেছে';
    private const PROBABLE_TITLE = '🏦 Bank English — ভবিষ্যৎ পরীক্ষায় আসতে পারে';

    /** audited lesson titles in order, with word count and tag */
    private const LESSONS = [
        ['ব্যাংকে এসেছে শব্দ',                  54, 'Sonali-2022'],
        ['আসতে পারে — সম্ভাব্য ব্যাংক শব্দ',     54, 'Probable'],
        ['আর্থিক বিষয় — Banking Excellence',   48, 'Finance'],
        ['ডিজিটাল ব্যাংকিং — Modern Finance',   44, 'Fintech'],
    ];

    /** word id => expected word text (lower-case) of the rows that are removed */
    private const REMOVE = [
        6354 => 'dividend', 6369 => 'equity', 6399 => 'commission', 6408 => 'budget', 6409 => 'forecast',
        6415 => 'receipt', 6416 => 'voucher', 6418 => 'check', 6419 => 'draft', 6421 => 'mortgage',
        6431 => 'payment', 6432 => 'transaction', 6445 => 'security',
    ];

    public function up(): void
    {
        try {
            $this->restructure();
        } catch (\Throwable $e) {
            Log::error('[bank-restructure] aborted, nothing was changed: ' . $e->getMessage());
        }
    }

    private function skip(string $why): void
    {
        Log::warning('[bank-restructure] skipped, nothing was changed: ' . $why);
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
        if ($lessons->count() !== 4) { $this->skip('chapter has ' . $lessons->count() . ' lessons, expected 4'); return; }

        $words = [];   // lesson index => word rows
        foreach (self::LESSONS as $i => [$title, $count, $tag]) {
            if (trim((string) $lessons[$i]->title) !== $title) { $this->skip("lesson #{$i} title differs"); return; }
            $rows = DB::table('words')->where('lesson_id', $lessons[$i]->id)->orderBy('id')->get()->all();
            if (count($rows) !== $count) { $this->skip("lesson #{$i} has " . count($rows) . " words, expected {$count}"); return; }
            foreach ($rows as $w) {
                if (trim((string) $w->synonyms) !== $tag) { $this->skip("word {$w->id} has tag '{$w->synonyms}', expected '{$tag}'"); return; }
            }
            $words[$i] = $rows;
        }

        // the 13 rows to remove: exact id + word text + a surviving twin
        $all = [];
        foreach ($words as $rows) { foreach ($rows as $w) { $all[$w->id] = $w; } }
        $twins = [];
        foreach ($all as $id => $w) { $twins[self::norm((string) $w->word)][] = $id; }
        $twinAlias = ['check' => 'cheque'];
        foreach (self::REMOVE as $id => $expected) {
            if (!isset($all[$id]) || strtolower(trim((string) $all[$id]->word)) !== $expected) {
                $this->skip("row {$id} is not '{$expected}'"); return;
            }
            $key = $twinAlias[$expected] ?? $expected;
            $other = array_filter($twins[$key] ?? [], fn ($x) => !isset(self::REMOVE[$x]));
            if (!$other) { $this->skip("'{$expected}' has no surviving twin"); return; }
        }
        $removeIds = array_keys(self::REMOVE);

        $keep = [];
        foreach ($words as $i => $rows) {
            $keep[$i] = array_values(array_filter($rows, fn ($w) => !in_array($w->id, $removeIds, true)));
        }
        $sizes = array_map('count', $keep);
        if ($sizes !== [54, 52, 40, 41]) { $this->skip('kept sizes are [' . implode(',', $sizes) . '], expected [54,52,40,41]'); return; }

        $ids = fn (array $rows) => array_map(fn ($w) => $w->id, $rows);
        $half = fn (array $rows) => [array_slice($rows, 0, (int) ceil(count($rows) / 2)), array_slice($rows, (int) ceil(count($rows) / 2))];
        [$c1, $c2] = $half($keep[0]);
        [$p1a, $p1b] = $half($keep[1]);
        [$p2a, $p2b] = $half($keep[2]);
        [$p3a, $p3b] = $half($keep[3]);

        // plan: [title, word ids]; came lessons first, then the may-come lessons in display order
        $camePlan = [
            ['✅ ব্যাংকে এসেছে শব্দ — Sonali ২০২২ (১)', $ids($c1)],
            ['✅ ব্যাংকে এসেছে শব্দ — Sonali ২০২২ (২)', $ids($c2)],
        ];
        $mayPlan = [
            ['🔮 ব্যাংকিং ঝুঁকি, বিনিয়োগ ও বাজার (১)', $ids($p1a)],
            ['🔮 ব্যাংকিং ঝুঁকি, বিনিয়োগ ও বাজার (২)', $ids($p1b)],
            ['🔮 অর্থনীতি, বাণিজ্য ও হিসাববিজ্ঞান (১)', $ids($p2a)],
            ['🔮 অর্থনীতি, বাণিজ্য ও হিসাববিজ্ঞান (২)', $ids($p2b)],
            ['🔮 ডিজিটাল ব্যাংকিং ও ফিনটেক (১)', $ids($p3a)],
            ['🔮 ডিজিটাল ব্যাংকিং ও ফিনটেক (২)', $ids($p3b)],
        ];
        $keptWordIds = [];
        foreach (array_merge($camePlan, $mayPlan) as [, $x]) { $keptWordIds = array_merge($keptWordIds, $x); }
        sort($keptWordIds);
        if (count($keptWordIds) !== 187) { $this->skip('kept word count is ' . count($keptWordIds) . ', expected 187'); return; }

        // ── full-table snapshots ────────────────────────────────────────────
        $tag = 'bak_bank_' . date('Ymd');
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
        $lessonIds = $lessons->pluck('id')->all();

        try {
            DB::transaction(function () use ($chapter, $lessons, $lessonIds, $all, $removeIds, $camePlan, $mayPlan, $keptWordIds, $hasLayout) {
                $now = now();

                // 1. backups
                $rows = [];
                foreach ($all as $id => $w) {
                    if (in_array($id, $removeIds, true)) {
                        $rows[] = ['kind' => 'deleted_word', 'ref_id' => $id, 'payload' => json_encode((array) $w, JSON_UNESCAPED_UNICODE)];
                    } else {
                        $rows[] = ['kind' => 'word', 'ref_id' => $id, 'payload' => json_encode(['lesson_id' => $w->lesson_id])];
                    }
                }
                foreach ($lessons as $l) {
                    $rows[] = ['kind' => 'lesson', 'ref_id' => $l->id, 'payload' => json_encode((array) $l, JSON_UNESCAPED_UNICODE)];
                }
                $rows[] = ['kind' => 'chapter', 'ref_id' => $chapter->id, 'payload' => json_encode((array) $chapter, JSON_UNESCAPED_UNICODE)];
                if (Schema::hasTable('user_lesson_round_progress')) {
                    foreach (DB::table('user_lesson_round_progress')->whereIn('lesson_id', $lessonIds)->get() as $p) {
                        $rows[] = ['kind' => 'progress', 'ref_id' => $p->id, 'payload' => json_encode((array) $p)];
                    }
                }
                foreach (array_chunk($rows, 300) as $chunk) {
                    DB::table(self::BACKUP)->insert($chunk);
                }

                // 2. remove the 13 repeated rows (copied above)
                DB::table('words')->whereIn('id', $removeIds)->delete();

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

                // 4. "came": lesson #0 re-used for part 1, new lesson for part 2
                foreach ($camePlan as $i => [$title, $ids]) {
                    if ($i === 0) {
                        $upd = ['title' => $title, 'updated_at' => $now];
                        if ($hasLayout) { $upd['layout'] = 'stacked'; }
                        DB::table('lessons')->where('id', $lessons[0]->id)->update($upd);
                        $lessonId = $lessons[0]->id;
                    } else {
                        $lessonId = DB::table('lessons')->insertGetId($template($lessons[0], $title, $chapter->id));
                        DB::table(self::BACKUP)->insert(['kind' => 'new_lesson', 'ref_id' => $lessonId, 'payload' => '{}']);
                    }
                    $assign[$lessonId] = $ids;
                }

                // 5. "may come": lessons #1,#2,#3 re-used for the first three parts (moved to the new chapter)
                foreach ($mayPlan as $i => [$title, $ids]) {
                    if ($i < 3) {
                        $src = $lessons[$i + 1];
                        $upd = ['title' => $title, 'chapter_id' => $newChapterId, 'updated_at' => $now];
                        if ($hasLayout) { $upd['layout'] = 'stacked'; }
                        DB::table('lessons')->where('id', $src->id)->update($upd);
                        $lessonId = $src->id;
                    } else {
                        $lessonId = DB::table('lessons')->insertGetId($template($lessons[1], $title, $newChapterId));
                        DB::table(self::BACKUP)->insert(['kind' => 'new_lesson', 'ref_id' => $lessonId, 'payload' => '{}']);
                    }
                    $assign[$lessonId] = $ids;
                }

                // 6. re-point words (ids unchanged)
                foreach ($assign as $lessonId => $ids) {
                    foreach (array_chunk($ids, 200) as $chunk) {
                        DB::table('words')->whereIn('id', $chunk)->update(['lesson_id' => $lessonId, 'updated_at' => $now]);
                    }
                }

                // 7. old stars no longer match the content of the re-used lessons
                if (Schema::hasTable('user_lesson_round_progress')) {
                    DB::table('user_lesson_round_progress')->whereIn('lesson_id', $lessonIds)->delete();
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
                if ($c1 !== 2 || $c2 !== 6) { throw new \RuntimeException("lesson counts {$c1}/{$c2}, expected 2/6"); }
            });
        } catch (\Throwable $e) {
            Schema::dropIfExists(self::BACKUP);   // allow a clean re-run after a failed attempt
            throw $e;
        }

        Log::info('[bank-restructure] done: chapter ' . $chapter->id . ' -> 2 "came" lessons, new chapter with 6 "may come" lessons, 13 repeated rows removed');
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
