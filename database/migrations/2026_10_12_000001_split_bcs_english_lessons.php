<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Split the two giant lessons of the "BCS English" vocabulary chapter
 * (220 "came" words + 240 "probable" words) into small themed lessons, the
 * same size the rest of the app uses (~25-35 words).
 *
 *   existing lesson A (220 words, tagged BCS-40..43)
 *       -> 8 lessons: BCS-40/41/42/43, two parts each
 *   existing lesson B (240 words, 8 theme blocks in word order)
 *       -> 8 themed lessons
 *
 * SAFETY (this file runs automatically on every Railway deploy):
 *  - Nothing is created or deleted from scratch: words are only re-pointed
 *    (UPDATE words.lesson_id), so every word id, every mistake-notebook entry
 *    and every word-exposure row stays valid. No lesson is deleted
 *    (words.lesson_id is ON DELETE CASCADE - deleting a lesson deletes its words).
 *  - The two existing lessons are re-used (ids stay, order by id stays):
 *    A -> "BCS-40 (1)", B -> "BCS-40 (2)". New lessons get larger ids, so the
 *    app (which orders by id) shows them after, in the order created here.
 *  - It only runs when the chapter looks EXACTLY like the audited data
 *    (2 lessons, 220 + 240 words, BCS-40..43 tags, expected first word of every
 *    block). Anything else -> logs a warning and changes nothing.
 *  - All data changes happen in one transaction and are verified before commit;
 *    any problem rolls everything back. Exceptions are caught and logged so a
 *    problem here can never stop the server from booting.
 *  - A backup table (bcs_lesson_split_backup) stores the original lesson_id of
 *    every word, the original lesson rows and the progress rows that are reset,
 *    so down() restores the exact previous state.
 */
return new class extends Migration
{
    private const BACKUP = 'bcs_lesson_split_backup';

    private const YEAR_BN = ['40' => '৪০', '41' => '৪১', '42' => '৪২', '43' => '৪৩'];

    /** [first word of block, block size, new lesson title] - order of the old probable lesson */
    private const PROBABLE_BLOCKS = [
        ['Bureaucracy',  35, '🔮 শাসন ও রাষ্ট্রচিন্তা'],
        ['Jurisprudence', 30, '🔮 আইন ও বিচারব্যবস্থা'],
        ['Fiscal policy', 35, '🔮 অর্থনীতি ও ব্যবসা'],
        ['Perspicacious', 30, '🔮 বুদ্ধি ও চরিত্রের গুণ'],
        ['Pragmatism',    30, '🔮 দর্শন ও মতবাদ'],
        ['Rhetoric',      25, '🔮 ভাষা ও বাগ্মিতা'],
        ['Vindictive',    30, '🔮 মনোভাব ও আচরণ'],
        ['Perpetuate',    25, '🔮 গুরুত্বপূর্ণ Verbs'],
    ];

    public function up(): void
    {
        try {
            $this->split();
        } catch (\Throwable $e) {
            // Never let a content migration take the whole backend down.
            Log::error('[bcs-split] aborted, nothing was changed: ' . $e->getMessage());
        }
    }

    private function split(): void
    {
        foreach (['chapters', 'lessons', 'words'] as $t) {
            if (!Schema::hasTable($t)) { $this->skip("table {$t} missing"); return; }
        }
        if (Schema::hasTable(self::BACKUP)) { $this->skip('already applied (backup table exists)'); return; }

        $chapter = DB::table('chapters')->where('title', 'like', 'BCS English%')->orderBy('id')->first();
        if (!$chapter) { $this->skip('BCS English chapter not found'); return; }

        $lessons = DB::table('lessons')->where('chapter_id', $chapter->id)->orderBy('id')->get();
        if ($lessons->count() !== 2) { $this->skip('chapter has ' . $lessons->count() . ' lessons, expected 2'); return; }

        $came = $lessons[0];   // "বিসিএস-এ এসেছে শব্দ"
        $prob = $lessons[1];   // "আসতে পারে — BCS ... Most Important Words"

        $cameWords = DB::table('words')->where('lesson_id', $came->id)->orderBy('id')->get()->all();
        $probWords = DB::table('words')->where('lesson_id', $prob->id)->orderBy('id')->get()->all();
        if (count($cameWords) !== 220 || count($probWords) !== 240) {
            $this->skip('word counts are ' . count($cameWords) . '/' . count($probWords) . ', expected 220/240');
            return;
        }

        // ── group "came" words by BCS year tag, then halve each year ────────
        $byYear = [];
        foreach ($cameWords as $w) {
            if (!preg_match('/BCS[-\s]?(\d{2})/i', (string) $w->synonyms, $m)) {
                $this->skip('word id ' . $w->id . ' has no BCS year tag'); return;
            }
            $byYear[$m[1]][] = $w->id;
        }
        ksort($byYear);
        if (array_keys($byYear) !== array_keys(self::YEAR_BN)) {
            $this->skip('year tags are [' . implode(',', array_keys($byYear)) . '], expected 40..43'); return;
        }

        // plan: ordered list of [lessonId|null(new), title, templateRow, [wordIds]]
        $plan = [];
        foreach (self::YEAR_BN as $yr => $bn) {
            $ids  = $byYear[$yr];
            $half = intdiv(count($ids), 2);
            $plan[] = ['year' => $yr, 'part' => 1, 'ids' => array_slice($ids, 0, $half)];
            $plan[] = ['year' => $yr, 'part' => 2, 'ids' => array_slice($ids, $half)];
        }

        // ── probable words: verify the 8 theme blocks start where expected ──
        $blocks = [];
        $offset = 0;
        foreach (self::PROBABLE_BLOCKS as [$first, $size, $title]) {
            if (strcasecmp(trim((string) $probWords[$offset]->word), $first) !== 0) {
                $this->skip("block starting at {$offset} begins with '{$probWords[$offset]->word}', expected '{$first}'");
                return;
            }
            $blocks[] = ['title' => $title, 'ids' => array_map(fn ($w) => $w->id, array_slice($probWords, $offset, $size))];
            $offset += $size;
        }
        if ($offset !== 240) { $this->skip('block sizes do not add up to 240'); return; }

        $originalWordIds = array_merge(array_map(fn ($w) => $w->id, $cameWords), array_map(fn ($w) => $w->id, $probWords));
        sort($originalWordIds);

        // ── backup table (DDL commits implicitly, so it is created BEFORE the transaction) ──
        Schema::create(self::BACKUP, function ($t) {
            $t->id();
            $t->string('kind', 20)->index();   // word | lesson | new_lesson | progress
            $t->unsignedBigInteger('ref_id');
            $t->longText('payload')->nullable();
        });

        try {
            DB::transaction(function () use ($came, $prob, $plan, $blocks, $originalWordIds) {
                $now = now();

                // 1. backup everything we are about to change
                $backup = [];
                foreach ([$came->id, $prob->id] as $lid) {
                    foreach (DB::table('words')->where('lesson_id', $lid)->pluck('id') as $wid) {
                        $backup[] = ['kind' => 'word', 'ref_id' => $wid, 'payload' => json_encode(['lesson_id' => $lid])];
                    }
                }
                $backup[] = ['kind' => 'lesson', 'ref_id' => $came->id, 'payload' => json_encode((array) $came, JSON_UNESCAPED_UNICODE)];
                $backup[] = ['kind' => 'lesson', 'ref_id' => $prob->id, 'payload' => json_encode((array) $prob, JSON_UNESCAPED_UNICODE)];
                if (Schema::hasTable('user_lesson_round_progress')) {
                    foreach (DB::table('user_lesson_round_progress')->whereIn('lesson_id', [$came->id, $prob->id])->get() as $p) {
                        $backup[] = ['kind' => 'progress', 'ref_id' => $p->id, 'payload' => json_encode((array) $p)];
                    }
                }
                foreach (array_chunk($backup, 500) as $chunk) {
                    DB::table(self::BACKUP)->insert($chunk);
                }

                // 2. lesson row template (copy of the source lesson, minus id)
                $template = function ($src, string $title) use ($now) {
                    $row = (array) $src;
                    unset($row['id']);
                    $row['title']      = $title;
                    $row['created_at'] = $now;
                    $row['updated_at'] = $now;
                    return $row;
                };

                $titleOf = fn ($p) => '✅ ' . self::YEAR_BN[$p['year']] . 'তম BCS — যে শব্দগুলো এসেছিল ('
                    . ($p['part'] === 1 ? '১' : '২') . ')';

                // 3. new lessons, created in display order: BCS-41..43 parts, then the 8 themes
                $assign = [];   // [lessonId => [wordIds]]
                foreach ($plan as $i => $p) {
                    if ($i === 0 || $i === 1) continue;   // BCS-40 (1)/(2) live in the re-used lessons
                    $newId = DB::table('lessons')->insertGetId($template($came, $titleOf($p)));
                    DB::table(self::BACKUP)->insert(['kind' => 'new_lesson', 'ref_id' => $newId, 'payload' => '{}']);
                    $assign[$newId] = $p['ids'];
                }
                foreach ($blocks as $b) {
                    $newId = DB::table('lessons')->insertGetId($template($prob, $b['title']));
                    DB::table(self::BACKUP)->insert(['kind' => 'new_lesson', 'ref_id' => $newId, 'payload' => '{}']);
                    $assign[$newId] = $b['ids'];
                }

                // 4. re-point words (ids unchanged)
                foreach ($assign as $lessonId => $ids) {
                    foreach (array_chunk($ids, 200) as $chunk) {
                        DB::table('words')->whereIn('id', $chunk)->update(['lesson_id' => $lessonId, 'updated_at' => $now]);
                    }
                }
                // BCS-40 part 2 moves from the "came" lesson into the re-used second lesson
                foreach (array_chunk($plan[1]['ids'], 200) as $chunk) {
                    DB::table('words')->whereIn('id', $chunk)->update(['lesson_id' => $prob->id, 'updated_at' => $now]);
                }
                $assign[$came->id] = $plan[0]['ids'];
                $assign[$prob->id] = $plan[1]['ids'];

                // 5. re-title the two re-used lessons (second one becomes a "came" lesson)
                DB::table('lessons')->where('id', $came->id)->update(['title' => $titleOf($plan[0]), 'updated_at' => $now]);
                DB::table('lessons')->where('id', $prob->id)->update([
                    'title' => $titleOf($plan[1]), 'type' => $came->type, 'updated_at' => $now,
                ]);

                // 6. their old stars/progress no longer match the content
                if (Schema::hasTable('user_lesson_round_progress')) {
                    DB::table('user_lesson_round_progress')->whereIn('lesson_id', [$came->id, $prob->id])->delete();
                }

                // 7. verify before commit
                $seen = [];
                foreach ($assign as $lessonId => $ids) {
                    $actual = DB::table('words')->where('lesson_id', $lessonId)->pluck('id')->all();
                    sort($actual);
                    $expected = $ids;
                    sort($expected);
                    if ($actual !== $expected) {
                        throw new \RuntimeException("lesson {$lessonId}: word set mismatch");
                    }
                    $seen = array_merge($seen, $actual);
                }
                sort($seen);
                if ($seen !== $originalWordIds) {
                    throw new \RuntimeException('word id set changed');
                }
            });
        } catch (\Throwable $e) {
            Schema::dropIfExists(self::BACKUP);   // allow a clean re-run after a failed attempt
            throw $e;
        }

        Log::info('[bcs-split] done: chapter ' . $chapter->id . ' now has 16 lessons from the original 2');
    }

    private function skip(string $why): void
    {
        Log::warning('[bcs-split] skipped, nothing was changed: ' . $why);
    }

    public function down(): void
    {
        if (!Schema::hasTable(self::BACKUP)) {
            return;
        }
        DB::transaction(function () {
            foreach (DB::table(self::BACKUP)->where('kind', 'word')->get() as $r) {
                DB::table('words')->where('id', $r->ref_id)->update(['lesson_id' => json_decode($r->payload)->lesson_id]);
            }
            foreach (DB::table(self::BACKUP)->where('kind', 'lesson')->get() as $r) {
                $row = (array) json_decode($r->payload, true);
                unset($row['id']);
                DB::table('lessons')->where('id', $r->ref_id)->update($row);
            }
            foreach (DB::table(self::BACKUP)->where('kind', 'new_lesson')->get() as $r) {
                if (!DB::table('words')->where('lesson_id', $r->ref_id)->exists()) {
                    DB::table('lessons')->where('id', $r->ref_id)->delete();
                }
            }
            if (Schema::hasTable('user_lesson_round_progress')) {
                foreach (DB::table(self::BACKUP)->where('kind', 'progress')->get() as $r) {
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
