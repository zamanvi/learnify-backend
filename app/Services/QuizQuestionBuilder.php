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

    // Round 2 (Reading) - translation-select. First half asks "which English
    // word means this Bangla meaning" (bn_to_en), second half the reverse
    // (en_to_bn). No dedicated example-sentence data exists on `words` yet,
    // so this works at word/phrase level, not full sentences - flagged to
    // the caller via the round description, not something to fake here.
    public function buildReadingQuestions(int $lessonId, int $count = 20): array
    {
        $wordIds = $this->selectWordIds($lessonId, $count);
        if (empty($wordIds)) {
            return [];
        }

        $words = Word::whereIn('id', $wordIds)->get();
        $half = (int) ceil($words->count() / 2);

        $bnToEn = $words->slice(0, $half);
        $enToBn = $words->slice($half);

        $allWords    = Word::where('status', 1)->whereNotIn('id', $words->pluck('id'))->inRandomOrder()->limit(30)->pluck('word')->toArray();
        $allMeanings = Word::where('status', 1)->whereNotIn('id', $words->pluck('id'))->inRandomOrder()->limit(30)->pluck('meaning')->toArray();

        $bnToEnQuestions = $bnToEn->map(function ($word) use ($allWords) {
            return $this->buildOptionQuestion($word->meaning, $word->word, $allWords, 'bn_to_en', $word->id);
        });

        $enToBnQuestions = $enToBn->map(function ($word) use ($allMeanings) {
            return $this->buildOptionQuestion($word->word, $word->meaning, $allMeanings, 'en_to_bn', $word->id);
        });

        return $bnToEnQuestions->concat($enToBnQuestions)->values()->toArray();
    }

    // Round 3 (Listening) - reuses the client's existing TTS helper. First
    // half: client speaks `speak_text` (the English word), user picks the
    // Bangla meaning. Second half: Bangla meaning shown as text, user picks
    // (and hears) the matching English word among the options.
    public function buildListeningQuestions(int $lessonId, int $count = 10): array
    {
        $wordIds = $this->selectWordIds($lessonId, $count);
        if (empty($wordIds)) {
            return [];
        }

        $words = Word::whereIn('id', $wordIds)->get();
        $half = (int) ceil($words->count() / 2);

        $listenForMeaning = $words->slice(0, $half);
        $meaningForListen = $words->slice($half);

        $allMeanings = Word::where('status', 1)->whereNotIn('id', $words->pluck('id'))->inRandomOrder()->limit(30)->pluck('meaning')->toArray();
        $allWords    = Word::where('status', 1)->whereNotIn('id', $words->pluck('id'))->inRandomOrder()->limit(30)->pluck('word')->toArray();

        $listenQuestions = $listenForMeaning->map(function ($word) use ($allMeanings) {
            $q = $this->buildOptionQuestion($word->word, $word->meaning, $allMeanings, 'listen_to_meaning', $word->id);
            $q['speak_text'] = $word->word;
            $q['question']   = null; // client shows a speaker icon instead of text
            return $q;
        });

        $reverseQuestions = $meaningForListen->map(function ($word) use ($allWords) {
            $q = $this->buildOptionQuestion($word->meaning, $word->word, $allWords, 'meaning_to_listen', $word->id);
            $q['speak_text'] = $word->word; // client speaks each option on tap
            return $q;
        });

        return $listenQuestions->concat($reverseQuestions)->values()->toArray();
    }

    // Round 4 (Writing) - typed input, checked client-side with fuzzy
    // matching against `expected_answer` (same client-trust model the MCQ
    // endpoint already uses by shipping `correct_index`). Word-level only
    // for now, same reason as buildReadingQuestions - no sentence data yet.
    public function buildWritingQuestions(int $lessonId, int $count = 10): array
    {
        $wordIds = $this->selectWordIds($lessonId, $count);
        if (empty($wordIds)) {
            return [];
        }

        $words = Word::whereIn('id', $wordIds)->get();
        $half = (int) ceil($words->count() / 2);

        $bnToEn = $words->slice(0, $half);
        $enToBn = $words->slice($half);

        $bnToEnQuestions = $bnToEn->map(fn($word) => [
            'word_id'         => $word->id,
            'prompt'          => $word->meaning,
            'expected_answer' => $word->word,
            'direction'       => 'bn_to_en',
        ]);

        $enToBnQuestions = $enToBn->map(fn($word) => [
            'word_id'         => $word->id,
            'prompt'          => $word->word,
            'expected_answer' => $word->meaning,
            'direction'       => 'en_to_bn',
        ]);

        return $bnToEnQuestions->concat($enToBnQuestions)->values()->toArray();
    }

    // Shared 4-option builder used by the reading/listening round methods.
    private function buildOptionQuestion(?string $question, string $correctAnswer, array $pool, string $direction, int $wordId): array
    {
        $wrongOptions = array_slice(array_diff($pool, [$correctAnswer]), 0, 3);
        while (count($wrongOptions) < 3) {
            $wrongOptions[] = 'N/A';
        }

        $options = array_merge([$correctAnswer], $wrongOptions);
        shuffle($options);

        return [
            'word_id'       => $wordId,
            'question'      => $question,
            'correct_index' => array_search($correctAnswer, $options),
            'options'       => array_values($options),
            'direction'     => $direction,
        ];
    }
}
