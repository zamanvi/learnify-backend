<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// The frontend's lesson page uses short_details (meta description),
// link (YouTube embed), and keyword (SEO meta keywords) - fields that
// existed on BookItem but weren't carried over when copying into
// web_lessons. Purely additive to the already-independent table.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_lessons', function (Blueprint $table) {
            $table->string('short_details')->nullable()->after('content');
            $table->string('link')->nullable()->after('short_details');
            $table->string('keyword')->nullable()->after('link');
        });
    }

    public function down(): void
    {
        Schema::table('web_lessons', function (Blueprint $table) {
            $table->dropColumn(['short_details', 'link', 'keyword']);
        });
    }
};
