<?php

namespace App\Services;

use App\Models\Word;

class QuizQuestionBuilder
{
    // Same random selection GameController::quiz() always did - extracted so
    // battle creation can persist the exact word IDs a battle will use.
    public function selectWordIds(int $lessonId, int $count): array
    {
        return Word::where('lesson_id', $lessonId)
            ->where('status', 1)
            ->inRandomOrder()
            ->limit($count)
            ->pluck('id')
            ->toArray();
    }

    // Rebuilds the question/options payload from a fixed list of word IDs
    // (order preserved) so every caller sees the identical question set.
    public function buildQuestionsFromWordIds(array $wordIds): array
    {
        if (empty($wordIds)) {
            return [];
        }

        $wordsById = Word::whereIn('id', $wordIds)->get()->keyBy('id');
        $words = collect($wordIds)->map(fn($id) => $wordsById->get($id))->filter()->values();

        if ($words->isEmpty()) {
            return [];
        }

        $allMeanings = Word::where('status', 1)
            ->whereNotIn('id', $words->pluck('id'))
            ->inRandomOrder()
            ->limit(30)
            ->pluck('meaning')
            ->toArray();

        return $words->map(function ($word) use ($allMeanings) {
            $wrongOptions = array_slice(array_diff($allMeanings, [$word->meaning]), 0, 3);
            while (count($wrongOptions) < 3) {
                $wrongOptions[] = 'N/A';
            }

            $options = array_merge([$word->meaning], $wrongOptions);
            shuffle($options);

            $correctIndex = array_search($word->meaning, $options);

            return [
                'question'      => $word->word,
                'correct_index' => $correctIndex,
                'options'       => array_values($options),
                'explanation'   => $word->synonyms ?? '',
            ];
        })->values()->toArray();
    }
}
