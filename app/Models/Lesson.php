<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'type', 'chapter_id', 'status', 'is_premium', 'pattern',
        'col1_label', 'col2_label', 'col3_label', 'col4_label',
    ];
    protected $casts = ['status' => 'bool', 'is_premium' => 'bool'];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    /**
     * Generate hint text dynamically based on lesson pattern.
     * This ensures the hint matches actual available fields in the pattern.
     */
    public function getHintTextAttribute(): string
    {
        return match($this->pattern) {
            'standard' => 'শব্দ | অর্থ',                    // Word | Meaning
            'exam' => 'অর্থ | পরীক্ষা %',                   // Meaning | Exam %
            'medical' => 'অর্থ | উৎস / %',                  // Meaning | Source / %
            default => 'শব্দ | অর্থ'                        // Default fallback
        };
    }

    /**
     * Resolves the 4 Word-form columns (word/meaning/synonyms/antonyms DB
     * fields) to {label, placeholder, show} for this lesson.
     *
     * Two modes, chosen automatically per lesson - never mixed:
     *
     * 1. CUSTOM (col1_label..col4_label - any one of them set on this
     *    lesson): admin has opted this specific lesson into free-text column
     *    naming via the Create/Edit Lesson form. Only the slots that were
     *    actually typed are shown; an empty slot is hidden entirely (no
     *    generic fallback name) in both the admin Word form and the app.
     *
     * 2. LEGACY (none of col1_label..col4_label set - true for every lesson
     *    that existed before this feature, and for any new lesson the admin
     *    doesn't bother customizing): falls back byte-for-byte to the
     *    type/pattern-based labels this app has always shown. This is what
     *    keeps every existing lesson's admin form and live app screen
     *    unchanged - they never touch the new columns, so they never leave
     *    legacy mode.
     *
     * Used directly by the admin Word Create/Edit blade forms (both modes).
     * The API (WordController) only forwards this to the app when
     * hasCustomColumnLabels() is true - see the comment there for why.
     */
    public function hasCustomColumnLabels(): bool
    {
        return (bool) ($this->col1_label || $this->col2_label || $this->col3_label || $this->col4_label);
    }

    public function getColumnLabelsAttribute(): array
    {
        if ($this->hasCustomColumnLabels()) {
            $slot = fn (?string $label) => $label
                ? ['label' => $label, 'placeholder' => $label, 'show' => true]
                : ['label' => null, 'placeholder' => null, 'show' => false];

            return [
                // `words.word` is NOT NULL in the DB - unlike the other 3
                // slots, this one can never actually hide, or submitting the
                // Word form would throw a SQL error. Falls back to a generic
                // name instead of hiding when left blank.
                'word'     => $this->col1_label
                    ? ['label' => $this->col1_label, 'placeholder' => $this->col1_label, 'show' => true]
                    : ['label' => 'Word', 'placeholder' => 'Word', 'show' => true],
                'meaning'  => $slot($this->col2_label),
                'synonyms' => $slot($this->col3_label),
                'antonyms' => $slot($this->col4_label),
            ];
        }

        if ($this->type === 'verb') {
            return [
                'word'     => ['label' => 'Verb 1', 'placeholder' => 'Verb 1 (base form)', 'show' => true],
                'meaning'  => ['label' => 'Verb meaning', 'placeholder' => 'Verb meaning', 'show' => true],
                'synonyms' => ['label' => 'Verb 2', 'placeholder' => 'Verb 2 (past tense)', 'show' => true],
                'antonyms' => ['label' => 'Verb 3', 'placeholder' => 'Verb 3 (past participle)', 'show' => true],
            ];
        }

        return match ($this->pattern) {
            'exam' => [
                'word'     => ['label' => 'Word', 'placeholder' => 'Word', 'show' => true],
                'meaning'  => ['label' => 'Meaning', 'placeholder' => 'Meaning', 'show' => true],
                'synonyms' => ['label' => 'Exam Type', 'placeholder' => 'e.g. HSC, SSC, Admission', 'show' => true],
                'antonyms' => ['label' => 'Exam %', 'placeholder' => 'e.g. 12%', 'show' => true],
            ],
            'medical' => [
                'word'     => ['label' => 'Word', 'placeholder' => 'Word', 'show' => true],
                'meaning'  => ['label' => 'Meaning', 'placeholder' => 'Meaning', 'show' => true],
                'synonyms' => ['label' => 'Source', 'placeholder' => 'e.g. Robbins Pathology', 'show' => true],
                'antonyms' => ['label' => '%', 'placeholder' => 'e.g. 8%', 'show' => true],
            ],
            default => [
                'word'     => ['label' => 'Word', 'placeholder' => 'Word', 'show' => true],
                'meaning'  => ['label' => 'Word meaning', 'placeholder' => 'Word meaning', 'show' => true],
                'synonyms' => ['label' => 'Word synonyms', 'placeholder' => 'Word synonyms', 'show' => true],
                'antonyms' => ['label' => 'Word antonyms', 'placeholder' => 'Word antonyms', 'show' => true],
            ],
        };
    }
}
