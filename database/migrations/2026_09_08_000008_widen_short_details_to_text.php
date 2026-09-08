<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Root cause of the brief outage: short_details was created as a VARCHAR(255)
// (the earlier migration's Schema::table()->string()), but real BookItem
// short_details values are full explanatory paragraphs, sometimes well over
// 255 characters - the backfill migration's UPDATE failed with "Data too
// long for column 'short_details'" (SQLSTATE 22001), which crashed
// `php artisan migrate --force` and took the whole app down until reverted.
//
// Checked BookItem's own migration (database/migrations/*_create_book_items_
// table.php): short_details and keyword are longText there, link is text -
// none are varchar(255). Matching those exactly here (rather than just
// fixing short_details) so link/keyword can't hit the same failure on a
// longer value later. No doctrine/dbal dependency in this project, so this
// uses raw SQL (MODIFY) instead of Schema::table()->change().
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE web_lessons MODIFY short_details LONGTEXT NULL');
        DB::statement('ALTER TABLE web_lessons MODIFY link TEXT NULL');
        DB::statement('ALTER TABLE web_lessons MODIFY keyword LONGTEXT NULL');
    }

    public function down(): void
    {
        // Not shrinking back to varchar(255) - would risk truncating data
        // written after this migration ran.
    }
};
