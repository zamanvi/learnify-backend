<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Splits the single "BCS English" chapter (8 "came" lessons + 32 "probable"
 * lessons) into two chapters:
 *
 *   BCS English — পরীক্ষায় এসেছে                 (the 8 lessons starting with ✅)
 *   BCS English — ভবিষ্যৎ পরীক্ষায় আসতে পারে      (the 32 lessons starting with 🔮)
 *
 * The empty "comming soon" placeholder chapter (0 lessons) is turned into the
 * second chapter, so the app order (by id) stays right and the placeholder is
 * gone. If no such empty placeholder exists a new chapter row is inserted.
 *
 * SAFETY (runs on every Railway deploy):
 *  - Only lessons.chapter_id and two chapter titles change. No word, lesson id,
 *    lesson title, progress or unlock row is touched or deleted.
 *  - Runs only when the BCS chapter looks exactly as audited (40 lessons: 8 ✅,
 *    32 🔮); otherwise it logs a warning and changes nothing.
 *  - One transaction + verification (lesson and word counts) before commit;
 *    exceptions are logged, never thrown, so it cannot block boot.
 *  - bcs_chapter_split_backup keeps what is needed; down() restores it exactly.
 */
return new class extends Migration
{
    private const BACKUP = 'bcs_chapter_split_backup';
    private const CAME_TITLE     = 'BCS English — পরীক্ষায় এসেছে';
    private const PROBABLE_TITLE = 'BCS English — ভবিষ্যৎ পরীক্ষায় আসতে পারে';

    public function up(): void
    {
        try {
            $this->split();
        } catch (\Throwable $e) {
            Log::error('[bcs-chapter-split] aborted, nothing was changed: ' . $e->getMessage());
        }
    }

    private function skip(string $why): void
    {
        Log::warning('[bcs-chapter-split] skipped, nothing was changed: ' . $why);
    }

    private function split(): void
    {
        foreach (['chapters', 'lessons', 'words'] as $t) {
            if (!Schema::hasTable($t)) { $this->skip("table {$t} missing"); return; }
        }
        if (Schema::hasTable(self::BACKUP)) { $this->skip('already applied (backup table exists)'); return; }
        if (DB::table('chapters')->where('title', self::PROBABLE_TITLE)->exists()) {
            $this->skip('probable chapter already exists'); return;
        }

        $came = DB::table('chapters')->where('title', 'like', 'BCS English%')->orderBy('id')->first();
        if (!$came) { $this->skip('BCS English chapter not found'); return; }

        $lessons  = DB::table('lessons')->where('chapter_id', $came->id)->orderBy('id')->get();
        $cameIds  = [];
        $probIds  = [];
        foreach ($lessons as $l) {
            if (str_starts_with($l->title, '✅')) { $cameIds[] = $l->id; }
            elseif (str_starts_with($l->title, '🔮')) { $probIds[] = $l->id; }
        }
        if ($lessons->count() !== 40 || count($cameIds) !== 8 || count($probIds) !== 32) {
            $this->skip('chapter has ' . $lessons->count() . ' lessons (' . count($cameIds) . ' came / ' . count($probIds) . ' probable), expected 40 (8/32)');
            return;
        }

        $wordsCame = DB::table('words')->whereIn('lesson_id', $cameIds)->count();
        $wordsProb = DB::table('words')->whereIn('lesson_id', $probIds)->count();
        $wordsAll  = DB::table('words')->whereIn('lesson_id', $lessons->pluck('id')->all())->count();
        if ($wordsCame + $wordsProb !== $wordsAll || $wordsAll < 1000) {
            $this->skip("unexpected word counts came={$wordsCame} probable={$wordsProb} all={$wordsAll}"); return;
        }

        // the empty placeholder chapter ("comming soon"), if it really is empty
        $placeholder = null;
        foreach (DB::table('chapters')->where('id', '>', $came->id)->orderBy('id')->get() as $c) {
            $norm = strtolower(trim((string) $c->title));
            if (in_array($norm, ['comming soon', 'coming soon'], true)
                && !DB::table('lessons')->where('chapter_id', $c->id)->exists()) {
                $placeholder = $c;
                break;
            }
        }

        Schema::create(self::BACKUP, function ($t) {
            $t->id();
            $t->string('kind', 20)->index();   // chapter | placeholder | new_chapter | lesson
            $t->unsignedBigInteger('ref_id');
            $t->longText('payload')->nullable();
        });

        try {
            DB::transaction(function () use ($came, $cameIds, $probIds, $placeholder, $wordsCame, $wordsProb) {
                $now = now();

                DB::table(self::BACKUP)->insert(['kind' => 'chapter', 'ref_id' => $came->id,
                    'payload' => json_encode((array) $came, JSON_UNESCAPED_UNICODE)]);
                foreach ($probIds as $lid) {
                    DB::table(self::BACKUP)->insert(['kind' => 'lesson', 'ref_id' => $lid,
                        'payload' => json_encode(['chapter_id' => $came->id])]);
                }

                DB::table('chapters')->where('id', $came->id)
                    ->update(['title' => self::CAME_TITLE, 'updated_at' => $now]);

                if ($placeholder) {
                    DB::table(self::BACKUP)->insert(['kind' => 'placeholder', 'ref_id' => $placeholder->id,
                        'payload' => json_encode((array) $placeholder, JSON_UNESCAPED_UNICODE)]);
                    DB::table('chapters')->where('id', $placeholder->id)
                        ->update(['title' => self::PROBABLE_TITLE, 'updated_at' => $now]);
                    $probChapterId = $placeholder->id;
                } else {
                    $row = (array) $came;
                    unset($row['id']);
                    $row['title']      = self::PROBABLE_TITLE;
                    $row['created_at'] = $now;
                    $row['updated_at'] = $now;
                    $probChapterId = DB::table('chapters')->insertGetId($row);
                    DB::table(self::BACKUP)->insert(['kind' => 'new_chapter', 'ref_id' => $probChapterId, 'payload' => '{}']);
                }

                foreach (array_chunk($probIds, 100) as $chunk) {
                    DB::table('lessons')->whereIn('id', $chunk)->where('chapter_id', $came->id)
                        ->update(['chapter_id' => $probChapterId, 'updated_at' => $now]);
                }

                // verify before commit
                $c1 = DB::table('lessons')->where('chapter_id', $came->id)->count();
                $c2 = DB::table('lessons')->where('chapter_id', $probChapterId)->count();
                if ($c1 !== 8 || $c2 !== 32) {
                    throw new \RuntimeException("lesson counts after move: {$c1}/{$c2}, expected 8/32");
                }
                $w1 = DB::table('words')->whereIn('lesson_id',
                    DB::table('lessons')->where('chapter_id', $came->id)->pluck('id'))->count();
                $w2 = DB::table('words')->whereIn('lesson_id',
                    DB::table('lessons')->where('chapter_id', $probChapterId)->pluck('id'))->count();
                if ($w1 !== $wordsCame || $w2 !== $wordsProb) {
                    throw new \RuntimeException("word counts after move: {$w1}/{$w2}, expected {$wordsCame}/{$wordsProb}");
                }
            });
        } catch (\Throwable $e) {
            Schema::dropIfExists(self::BACKUP);
            throw $e;
        }

        Log::info('[bcs-chapter-split] done: ' . count($cameIds) . ' came lessons stay, ' . count($probIds)
            . ' probable lessons moved to ' . ($placeholder ? 'former placeholder chapter ' . $placeholder->id : 'a new chapter'));
    }

    public function down(): void
    {
        if (!Schema::hasTable(self::BACKUP)) {
            return;
        }
        DB::transaction(function () {
            $rows = DB::table(self::BACKUP)->get();
            // lessons first (they point at the chapter that may be deleted below)
            foreach ($rows->where('kind', 'lesson') as $r) {
                DB::table('lessons')->where('id', $r->ref_id)->update(['chapter_id' => json_decode($r->payload)->chapter_id]);
            }
            foreach ($rows->whereIn('kind', ['chapter', 'placeholder']) as $r) {
                $row = (array) json_decode($r->payload, true);
                unset($row['id']);
                DB::table('chapters')->where('id', $r->ref_id)->update($row);
            }
            foreach ($rows->where('kind', 'new_chapter') as $r) {
                if (!DB::table('lessons')->where('chapter_id', $r->ref_id)->exists()) {
                    DB::table('chapters')->where('id', $r->ref_id)->delete();
                }
            }
        });
        Schema::dropIfExists(self::BACKUP);
    }
};
