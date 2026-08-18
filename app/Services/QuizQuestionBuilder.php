<?php

namespace App\Services;

use App\Models\Word;
use Illuminate\Support\Facades\DB;

class QuizQuestionBuilder
{
    // Same random selection GameController::quiz() always did - extracted so
    // battle creation can persist the exact word IDs a battle will use.
    // Pure random, no memory of what a user was shown before - deliberately
    // left untouched so BattleController and the legacy quiz() endpoint keep
    // their existing behavior. See selectWordIdsForUser() below for the
    // round-based quiz's anti-repeat version.
    public function selectWordIds(int $lessonId, int $count): array
    {
        return Word::where('lesson_id', $lessonId)
            ->where('status', 1)
            ->inRandomOrder()
            ->limit($count)
            ->pluck('id')
            ->toArray();
    }

    // Round-based Quick Quiz version of selectWordIds() - favors words this
    // user hasn't seen recently (or has never seen) instead of pure
    // ORDER BY RAND(), so a lesson's whole word pool rotates through before
    // anything repeats for that user. Ties among equally-stale words are
    // still randomized. Call markWordsShown() after building the question
    // set so the next call sees updated exposure. $userId is nullable -
    // roundQuiz()/quiz() are reachable without login (bearer optional), and
    // a guest falls back to plain random selection (no exposure to track).
    public function selectWordIdsForUser(?int $userId, int $lessonId, int $count): array
    {
        if ($userId === null) {
            return $this->selectWordIds($lessonId, $count);
        }

        return Word::where('words.lesson_id', $lessonId)
            ->where('words.status', 1)
            ->leftJoin('user_word_exposures', function ($join) use ($userId) {
                $join->on('user_word_exposures.word_id', '=', 'words.id')
                    ->where('user_word_exposures.user_id', '=', $userId);
            })
            ->orderByRaw("COALESCE(user_word_exposures.last_shown_at, '1970-01-01') ASC")
            ->orderByRaw('RAND()') // tiebreak among equally-stale (e.g. never-shown) words
            ->limit($count)
            ->pluck('words.id')
            ->toArray();
    }

    // Records that these words were just shown to this user, so the next
    // selectWordIdsForUser() call for them favors the rest of the lesson.
    // No-ops for guests (no userId) since there's nothing to track.
    public function markWordsShown(?int $userId, array $wordIds): void
    {
        if ($userId === null || empty($wordIds)) return;

        $now = now();
        $rows = array_map(fn($wordId) => [
            'user_id'       => $userId,
            'word_id'       => $wordId,
            'last_shown_at' => $now,
        ], $wordIds);

        DB::table('user_word_exposures')->upsert($rows, ['user_id', 'word_id'], ['last_shown_at']);
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
            ->limit(60)
            ->pluck('meaning')
            ->toArray();

        return $words->map(function ($word) use ($allMeanings) {
            // Reshuffle per question - without this, array_diff() always
            // leaves the pool in the same fixed order, so array_slice(0, 3)
            // picks the same 2-3 wrong options for nearly every question in
            // the round (confirmed live: one wrong meaning showed up in 6 of
            // 10 questions). Shuffling per question fixes that.
            $wrongPool = array_diff($allMeanings, [$word->meaning]);
            shuffle($wrongPool);
            $wrongOptions = array_slice($wrongPool, 0, 3);
            while (count($wrongOptions) < 3) {
                $wrongOptions[] = 'N/A';
            }

            $options = array_merge([$word->meaning], $wrongOptions);
            shuffle($options);

            $correctIndex = array_search($word->meaning, $options);

            return [
                'word_id'       => $word->id,
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
    public function buildReadingQuestions(?int $userId, int $lessonId, int $count = 20): array
    {
        $wordIds = $this->selectWordIdsForUser($userId, $lessonId, $count);
        if (empty($wordIds)) {
            return [];
        }

        $words = Word::whereIn('id', $wordIds)->get();
        $half = (int) ceil($words->count() / 2);

        $bnToEn = $words->slice(0, $half);
        $enToBn = $words->slice($half);

        $allWords    = Word::where('status', 1)->whereNotIn('id', $words->pluck('id'))->inRandomOrder()->limit(60)->pluck('word')->toArray();
        $allMeanings = Word::where('status', 1)->whereNotIn('id', $words->pluck('id'))->inRandomOrder()->limit(60)->pluck('meaning')->toArray();

        $bnToEnQuestions = $bnToEn->map(function ($word) use ($allWords) {
            return $this->buildOptionQuestion($word->meaning, $word->word, $allWords, 'bn_to_en', $word->id);
        });

        $enToBnQuestions = $enToBn->map(function ($word) use ($allMeanings) {
            return $this->buildOptionQuestion($word->word, $word->meaning, $allMeanings, 'en_to_bn', $word->id);
        });

        return $bnToEnQuestions->concat($enToBnQuestions)->values()->toArray();
    }

    // Round 4 (Listening) - reuses the client's existing TTS helper. First
    // half: client speaks `speak_text` (the English word), user picks the
    // Bangla meaning. Second half: Bangla meaning shown as text, user picks
    // (and hears) the matching English word among the options.
    public function buildListeningQuestions(?int $userId, int $lessonId, int $count = 10): array
    {
        $wordIds = $this->selectWordIdsForUser($userId, $lessonId, $count);
        if (empty($wordIds)) {
            return [];
        }

        $words = Word::whereIn('id', $wordIds)->get();
        $half = (int) ceil($words->count() / 2);

        $listenForMeaning = $words->slice(0, $half);
        $meaningForListen = $words->slice($half);

        $allMeanings = Word::where('status', 1)->whereNotIn('id', $words->pluck('id'))->inRandomOrder()->limit(60)->pluck('meaning')->toArray();
        $allWords    = Word::where('status', 1)->whereNotIn('id', $words->pluck('id'))->inRandomOrder()->limit(60)->pluck('word')->toArray();

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

    // Round 5 (Writing) - typed input, checked client-side with fuzzy
    // matching against `expected_answer` (same client-trust model the MCQ
    // endpoint already uses by shipping `correct_index`). Word-level only
    // for now, same reason as buildReadingQuestions - no sentence data yet.
    public function buildWritingQuestions(?int $userId, int $lessonId, int $count = 10): array
    {
        $wordIds = $this->selectWordIdsForUser($userId, $lessonId, $count);
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

    // Round 3 (Picture) - shows the word's image, user picks the matching
    // English word among 4 options (complements Round 1, which goes
    // word -> meaning; this goes picture -> word). Only words that already
    // have an uploaded image are eligible - if a lesson has fewer than 4
    // such words, returns [] and the caller's existing empty-questions 404
    // handles it the same way it already does for any word-less lesson.
    public function buildPictureQuestions(?int $userId, int $lessonId, int $count = 10): array
    {
        $eligibleIds = Word::where('lesson_id', $lessonId)
            ->where('status', 1)
            ->whereNotNull('image')
            ->pluck('id')
            ->toArray();

        if (count($eligibleIds) < 4) {
            return [];
        }

        $wordIds = $userId === null
            ? collect($eligibleIds)->shuffle()->take($count)->toArray()
            : Word::whereIn('words.id', $eligibleIds)
                ->leftJoin('user_word_exposures', function ($join) use ($userId) {
                    $join->on('user_word_exposures.word_id', '=', 'words.id')
                        ->where('user_word_exposures.user_id', '=', $userId);
                })
                ->orderByRaw("COALESCE(user_word_exposures.last_shown_at, '1970-01-01') ASC")
                ->orderByRaw('RAND()')
                ->limit($count)
                ->pluck('words.id')
                ->toArray();

        $words = Word::whereIn('id', $wordIds)->get();

        // Distractor words for the 3 wrong options - prefer other words in
        // the same lesson first (more plausible near-miss options), topped
        // up from the wider pool if the lesson itself is small.
        $distractorPool = Word::where('status', 1)
            ->whereNotIn('id', $words->pluck('id'))
            ->inRandomOrder()
            ->limit(60)
            ->pluck('word')
            ->toArray();

        return $words->map(function ($word) use ($distractorPool) {
            $wrongPool = array_diff($distractorPool, [$word->word]);
            shuffle($wrongPool);
            $wrongOptions = array_slice($wrongPool, 0, 3);
            while (count($wrongOptions) < 3) {
                $wrongOptions[] = 'N/A';
            }

            $options = array_merge([$word->word], $wrongOptions);
            shuffle($options);

            return [
                'word_id'       => $word->id,
                'image_url'     => $word->image_url,
                'correct_index' => array_search($word->word, $options),
                'options'       => array_values($options),
            ];
        })->values()->toArray();
    }

    // Shared 4-option builder used by the reading/listening round methods.
    private function buildOptionQuestion(?string $question, string $correctAnswer, array $pool, string $direction, int $wordId): array
    {
        // Same per-question reshuffle fix as buildQuestionsFromWordIds() -
        // see comment there. $pool's order is otherwise fixed across every
        // question built from it in one round, so array_slice(0, 3) alone
        // would repeat the same wrong options question after question.
        $wrongPool = array_diff($pool, [$correctAnswer]);
        shuffle($wrongPool);
        $wrongOptions = array_slice($wrongPool, 0, 3);
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
