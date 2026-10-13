<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Exam tags on chapters, so the app can offer a "my exam" filter on the
 * vocabulary list (HSC / SSC / JSC / BCS / Bank / Medical / Admission & Job).
 *
 * Additive and nullable: untagged chapters stay visible under every filter, and
 * old app builds ignore the new field. This runs on every deploy via
 * `php artisan migrate --force` in the start command, so it is guarded
 * (hasTable/hasColumn) and the seed only touches rows that are still untagged
 * - an admin's own edits are never overwritten.
 */
return new class extends Migration
{
    // title LIKE pattern => tag. Tags are lowercase slugs, comma separated.
    private const SEED = [
        ['Hsc english first paper%', 'hsc'],
        ['SSC english first paper%', 'ssc'],
        ['JSC english first paper%', 'jsc'],
        ['%BCS English%',            'bcs'],
        ['%Bank English%',           'bank'],
        ['%Medical English%',        'medical'],
        ['%Admission & Job English%', 'admission_job'],
    ];

    public function up(): void
    {
        if (!Schema::hasTable('chapters')) {
            return;
        }

        if (!Schema::hasColumn('chapters', 'exam_tags')) {
            Schema::table('chapters', function (Blueprint $table) {
                $table->string('exam_tags', 255)->nullable()->after('type');
            });
        }

        foreach (self::SEED as [$pattern, $tag]) {
            DB::table('chapters')
                ->where('type', 'vocabulary')
                ->where('title', 'like', $pattern)
                ->whereNull('exam_tags')
                ->update(['exam_tags' => $tag]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('chapters') && Schema::hasColumn('chapters', 'exam_tags')) {
            Schema::table('chapters', function (Blueprint $table) {
                $table->dropColumn('exam_tags');
            });
        }
    }
};
