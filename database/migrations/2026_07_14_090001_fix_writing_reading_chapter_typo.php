<?php

use App\Models\BookChapter;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    // The "Reading & Writing" book chapter's title was seeded with a typo
    // ("Redding" instead of "Reading") — found during an on-device app
    // walkthrough. Title-only content fix, slug is left as-is since it's
    // not user-visible and changing it isn't worth the churn.
    public function up(): void
    {
        BookChapter::where('title', 'Writing & Redding')->update([
            'title' => 'Writing & Reading',
        ]);
    }

    public function down(): void
    {
        BookChapter::where('title', 'Writing & Reading')->update([
            'title' => 'Writing & Redding',
        ]);
    }
};
