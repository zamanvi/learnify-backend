<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * books, book_chapters, and book_items were missing their PRIMARY KEY /
     * AUTO_INCREMENT (and book_chapters.book_id / book_items.chapter_id were
     * missing their FOREIGN KEY) despite the original create-table
     * migrations specifying them via $table->id() / ->constrained(...).
     * Without a unique constraint, every row had been silently duplicated
     * exactly once (confirmed via direct inspection: duplicate pairs have
     * byte-identical content - id, title, slug, chapter_id all match).
     *
     * This removes the extra duplicate copy of every affected row (keeping
     * exactly one), then adds the missing keys so this cannot recur.
     */
    public function up(): void
    {
        foreach (['books', 'book_chapters', 'book_items'] as $table) {
            $duplicates = DB::select("SELECT id, COUNT(*) as cnt FROM {$table} GROUP BY id HAVING COUNT(*) > 1");
            foreach ($duplicates as $dup) {
                $extra = $dup->cnt - 1;
                for ($i = 0; $i < $extra; $i++) {
                    DB::delete("DELETE FROM {$table} WHERE id = ? LIMIT 1", [$dup->id]);
                }
            }
        }

        DB::statement('ALTER TABLE books MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
        DB::statement('ALTER TABLE book_chapters MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
        DB::statement('ALTER TABLE book_items MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');

        DB::statement('ALTER TABLE book_chapters ADD CONSTRAINT book_chapters_book_id_foreign FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE');
        DB::statement('ALTER TABLE book_items ADD CONSTRAINT book_items_chapter_id_foreign FOREIGN KEY (chapter_id) REFERENCES book_chapters (id) ON DELETE CASCADE');
    }

    /**
     * Reverse the migrations.
     *
     * Only reverses the schema change (drops the keys this migration
     * added). Does not restore the deleted duplicate rows - they were
     * byte-identical copies of rows that remain in the table, so nothing
     * of substance is lost, and re-duplicating them would defeat the
     * purpose of this fix.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE book_items DROP FOREIGN KEY book_items_chapter_id_foreign');
        DB::statement('ALTER TABLE book_chapters DROP FOREIGN KEY book_chapters_book_id_foreign');

        DB::statement('ALTER TABLE books DROP PRIMARY KEY, MODIFY id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE book_chapters DROP PRIMARY KEY, MODIFY id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE book_items DROP PRIMARY KEY, MODIFY id BIGINT UNSIGNED NOT NULL');
    }
};
