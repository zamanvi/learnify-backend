<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * The eight older "future exam" lessons of the chapter
 * "BCS English — ভবিষ্যৎ পরীক্ষায় আসতে পারে" (Governance, Law, Economy, Intellect,
 * Philosophy, Language, Attitude, Verbs) show the literal text "Probable" in the
 * third column of every word. That is a leftover tag, not information.
 *
 * This replaces it with a short English synonym / gloss (like the 24 newer
 * lessons already have) and relabels column 3 "English Synonym".
 *
 * Safety:
 *  - only lessons of that chapter whose words ALL still carry exactly "Probable"
 *    are considered; a lesson is changed only if EVERY one of its words has a
 *    mapped synonym, otherwise the lesson is skipped whole (never half-done)
 *  - only words.synonyms and lessons.col3_label change: word text, meaning,
 *    importance % (antonyms), order, ids, progress rows are untouched
 *  - one transaction + verification, old values kept in bcs_probable_syn_backup
 *    (down() restores them), exceptions are logged and never thrown so the
 *    deploy/boot cannot be blocked
 */
return new class extends Migration
{
    private const BACKUP        = 'bcs_probable_syn_backup';
    private const CHAPTER_TITLE = 'BCS English — ভবিষ্যৎ পরীক্ষায় আসতে পারে';
    private const TAG           = 'Probable';
    private const NEW_LABEL     = '🔤 English Synonym';

    public function up(): void
    {
        try {
            $this->apply();
        } catch (\Throwable $e) {
            Log::error('[bcs-probable-syn] aborted, nothing was changed: ' . $e->getMessage());
        }
    }

    private function apply(): void
    {
        foreach (['chapters', 'lessons', 'words'] as $t) {
            if (!Schema::hasTable($t)) { return; }
        }
        if (!Schema::hasColumn('lessons', 'col3_label') || !Schema::hasColumn('words', 'synonyms')) { return; }
        if (Schema::hasTable(self::BACKUP)) { Log::info('[bcs-probable-syn] already applied'); return; }

        $map = json_decode(self::mapping(), true);
        if (!is_array($map) || count($map) < 200) { Log::error('[bcs-probable-syn] mapping unreadable'); return; }

        $chapter = DB::table('chapters')->where('title', self::CHAPTER_TITLE)->orderBy('id')->first();
        if (!$chapter) { Log::warning('[bcs-probable-syn] chapter not found, nothing changed'); return; }

        $wordUpdates = [];   // word id => [old, new]
        $lessonIds   = [];
        foreach (DB::table('lessons')->where('chapter_id', $chapter->id)->orderBy('id')->get() as $lesson) {
            $words = DB::table('words')->where('lesson_id', $lesson->id)->orderBy('id')->get(['id', 'word', 'synonyms']);
            if ($words->isEmpty()) { continue; }

            $allTagged = true;
            foreach ($words as $w) {
                if (trim((string) $w->synonyms) !== self::TAG) { $allTagged = false; break; }
            }
            if (!$allTagged) { continue; }

            $plan = [];
            $ok = true;
            foreach ($words as $w) {
                $key = mb_strtolower(trim((string) $w->word), 'UTF-8');
                if (!isset($map[$key]) || trim((string) $map[$key]) === '') { $ok = false; break; }
                $plan[$w->id] = [(string) $w->synonyms, trim((string) $map[$key])];
            }
            if (!$ok) {
                Log::warning('[bcs-probable-syn] lesson ' . $lesson->id . ' skipped: a word has no mapped synonym');
                continue;
            }
            foreach ($plan as $id => $pair) { $wordUpdates[$id] = $pair; }
            $lessonIds[] = $lesson->id;
        }

        if (!$wordUpdates) { Log::info('[bcs-probable-syn] nothing to change'); return; }

        Schema::create(self::BACKUP, function ($t) {
            $t->string('kind', 10);
            $t->unsignedBigInteger('ref_id');
            $t->text('old_value')->nullable();
            $t->primary(['kind', 'ref_id']);
        });

        try {
            DB::transaction(function () use ($wordUpdates, $lessonIds) {
                foreach ($lessonIds as $lid) {
                    $old = DB::table('lessons')->where('id', $lid)->value('col3_label');
                    DB::table(self::BACKUP)->insert(['kind' => 'lesson', 'ref_id' => $lid, 'old_value' => $old]);
                }
                foreach ($wordUpdates as $id => $pair) {
                    DB::table(self::BACKUP)->insert(['kind' => 'word', 'ref_id' => $id, 'old_value' => $pair[0]]);
                    $n = DB::table('words')->where('id', $id)->where('synonyms', $pair[0])
                        ->update(['synonyms' => $pair[1], 'updated_at' => now()]);
                    if ($n !== 1) { throw new \RuntimeException('word ' . $id . ' changed concurrently'); }
                }
                DB::table('lessons')->whereIn('id', $lessonIds)
                    ->update(['col3_label' => self::NEW_LABEL, 'updated_at' => now()]);

                // verification: nothing left tagged in the touched lessons, counts match
                $left = DB::table('words')->whereIn('lesson_id', $lessonIds)->where('synonyms', self::TAG)->count();
                $done = DB::table('words')->whereIn('lesson_id', $lessonIds)->count();
                if ($left !== 0 || $done !== count($wordUpdates)) {
                    throw new \RuntimeException("verification failed (left=$left, words=$done, expected=" . count($wordUpdates) . ')');
                }
            });
        } catch (\Throwable $e) {
            Schema::dropIfExists(self::BACKUP);
            throw $e;
        }

        Log::info('[bcs-probable-syn] ' . count($wordUpdates) . ' words in lessons ' . implode(',', $lessonIds) . ' now carry English synonyms');
    }

    public function down(): void
    {
        if (!Schema::hasTable(self::BACKUP)) {
            return;
        }
        DB::transaction(function () {
            foreach (DB::table(self::BACKUP)->get() as $r) {
                if ($r->kind === 'word') {
                    DB::table('words')->where('id', $r->ref_id)->update(['synonyms' => $r->old_value]);
                } else {
                    DB::table('lessons')->where('id', $r->ref_id)->update(['col3_label' => $r->old_value]);
                }
            }
        });
        Schema::dropIfExists(self::BACKUP);
    }

    private static function mapping(): string
    {
        return <<<'NOWDOC_END'
{
 "abrogate": "repeal; abolish",
 "absolutism": "unlimited authority",
 "accelerate": "speed up; hasten",
 "accountability": "answerability; responsibility",
 "acquisition": "takeover; purchase",
 "acquittal": "clearance; discharge",
 "acumen": "sharpness; keen insight",
 "adjudicate": "judge; rule on",
 "affinity": "attraction; kinship",
 "aggregate": "total; combine",
 "agnosticism": "doubt about God",
 "alleviate": "relieve; ease",
 "altruism": "selflessness",
 "ambiguous": "unclear; vague",
 "ameliorate": "improve; better",
 "amnesty": "general pardon",
 "analyze": "examine; study",
 "anarchy": "lawlessness; chaos",
 "animosity": "hostility; hatred",
 "animus": "ill will; hostility",
 "antipathy": "dislike; hostility",
 "appeal": "plea; petition",
 "arbitration": "mediation; settlement",
 "archaism": "obsolete word",
 "aristocracy": "nobility; upper class",
 "articulate": "well-spoken",
 "asceticism": "self-denial; austerity",
 "assiduous": "persistent; tireless",
 "astute": "shrewd; clever",
 "atheism": "disbelief in God",
 "autonomy": "self-rule; independence",
 "aversion": "dislike; repugnance",
 "bail": "temporary release; surety",
 "bankruptcy": "insolvency; ruin",
 "benevolence": "kindness; goodwill",
 "benevolent": "kind; charitable",
 "boom": "upswing; rapid growth",
 "bureaucracy": "red tape; officialdom",
 "bust": "collapse; crash",
 "camaraderie": "fellowship",
 "capitalism": "free-market system",
 "cartel": "price-fixing group",
 "centralization": "concentration of power",
 "chastity": "purity; modesty",
 "clemency": "mercy; leniency",
 "colloquial": "informal; conversational",
 "colonialism": "colonization; foreign rule",
 "communism": "classless-state ideology",
 "compatibilism": "free will with determinism",
 "compliance": "conformity; obedience",
 "concise": "brief; succinct",
 "conscientious": "scrupulous; dutiful",
 "consequentialism": "outcome-based ethics",
 "conservatism": "traditionalism",
 "contempt": "disrespect; disdain",
 "decelerate": "slow down",
 "decentralization": "devolution",
 "decompose": "rot; break down",
 "defendant": "accused; respondent",
 "deflation": "falling prices",
 "deism": "God without revelation",
 "demand side": "consumers' side",
 "deontology": "duty-based ethics",
 "depression": "severe slump",
 "deteriorate": "worsen; decline",
 "determinism": "fixed-cause belief",
 "devaluation": "currency value cut",
 "diction": "choice of words",
 "diligent": "hardworking; industrious",
 "diminish": "lessen; decrease",
 "dividend": "profit share; payout",
 "dualism": "two-part nature",
 "due process": "fair legal procedure",
 "egalitarianism": "belief in equality",
 "egoism": "self-interest; selfishness",
 "elocution": "speaking skill; delivery",
 "eloquent": "fluent; articulate",
 "embargoe": "trade ban",
 "equilibrate": "balance; stabilize",
 "eradicate": "eliminate; wipe out",
 "erudite": "scholarly; learned",
 "etymology": "word origin",
 "exacerbate": "worsen; aggravate",
 "export": "sell abroad",
 "fanaticism": "extremism; zealotry",
 "fascism": "authoritarian nationalism",
 "fastidious": "fussy; particular",
 "fatalism": "belief in fate",
 "fiscal policy": "tax and spending policy",
 "flourish": "thrive; prosper",
 "fortitude": "courage; endurance",
 "free trade": "barrier-free trade",
 "free will": "freedom of choice",
 "gdp": "gross domestic product",
 "gerontocracy": "rule by elders",
 "gnp": "gross national product",
 "habeas corpus": "anti-detention writ",
 "hedonism": "pleasure-seeking",
 "hegemony": "dominance; supremacy",
 "hierarchy": "ranking; pecking order",
 "holism": "whole-system view",
 "humility": "modesty",
 "idealism": "visionary thinking",
 "idiomatic": "natural-sounding",
 "immunity": "exemption; protection",
 "imperialism": "empire-building",
 "implementation": "execution; enforcement",
 "import": "bring in from abroad",
 "inflation": "rising prices",
 "inflation targeting": "price-stability policy",
 "injunction": "court order; ban",
 "integrate": "combine; unify",
 "integrity": "honesty; uprightness",
 "investment": "capital outlay",
 "judicious": "sensible; wise",
 "jurisdiction": "legal authority",
 "jurisprudence": "philosophy of law",
 "kleptocracy": "rule by thieves",
 "lenient": "merciful; tolerant",
 "lexicon": "vocabulary; dictionary",
 "liberalism": "freedom-based ideology",
 "libertarianism": "belief in liberty",
 "liquidity": "cash availability",
 "lucid": "clear; transparent",
 "magnanimity": "generosity; nobility",
 "malevolent": "malicious; evil",
 "mandate": "authority; order",
 "materialism": "matter-first belief",
 "matriarchy": "female-led rule",
 "merger": "amalgamation; union",
 "meritocracy": "rule by the talented",
 "meticulous": "careful; precise",
 "mitigate": "lessen; ease",
 "mobocracy": "mob rule",
 "monasticism": "monk's way of life",
 "monetary policy": "money-supply policy",
 "monism": "single-reality belief",
 "monopoly": "sole control",
 "morphology": "word structure",
 "mysticism": "spiritual-union belief",
 "nationalism": "patriotism; national pride",
 "neologism": "new word; coinage",
 "nihilism": "belief in nothing",
 "nominalism": "names-only theory",
 "nullify": "cancel; void",
 "objectivism": "mind-independent reality",
 "obscure": "unclear; little-known",
 "obstinate": "stubborn; headstrong",
 "ochlocracy": "mob rule",
 "oligopoly": "control by few sellers",
 "oration": "formal speech",
 "oratory": "public speaking",
 "oscillate": "swing; fluctuate",
 "oversight": "supervision; monitoring",
 "parole": "conditional release",
 "patriarchy": "male dominance",
 "pedantic": "overly scholarly; nitpicking",
 "penchant": "liking; fondness",
 "perjury": "lying under oath",
 "perpetuate": "continue; prolong",
 "persist": "continue; endure",
 "perspicacious": "shrewd; insightful",
 "pervasive": "widespread; prevalent",
 "philanthropy": "charity; humanitarianism",
 "phonetics": "study of speech sounds",
 "phonology": "sound system",
 "plaintiff": "complainant; claimant",
 "pluralism": "diversity; multiplicity",
 "plutocracy": "rule by the wealthy",
 "pragmatics": "language in context",
 "pragmatism": "practicality",
 "preamble": "introduction; prologue",
 "precedent": "earlier ruling; example",
 "prescient": "far-sighted",
 "probity": "honesty; integrity",
 "proclivity": "tendency; leaning",
 "profit margin": "earnings ratio",
 "proliferate": "multiply; spread",
 "propensity": "tendency; inclination",
 "protectionism": "trade-barrier policy",
 "prudent": "wise; cautious",
 "quota": "fixed share; allotment",
 "ratify": "approve formally",
 "realism": "factual view",
 "recession": "downturn; slump",
 "rectitude": "righteousness",
 "reductionism": "breaking into parts",
 "regulatory": "governing; supervisory",
 "rejuvenate": "refresh; renew",
 "relativism": "no absolute truth",
 "retaliation": "payback; reprisal",
 "retribution": "revenge; punishment",
 "revaluation": "currency value rise",
 "revoke": "withdraw; repeal",
 "rhetoric": "persuasive speech",
 "rigorous": "strict; thorough",
 "sagacious": "wise; judicious",
 "secularism": "state-religion separation",
 "sedulous": "diligent; persevering",
 "segregate": "separate; isolate",
 "semantics": "study of meaning",
 "sentence": "punishment; penalty",
 "socialism": "public ownership system",
 "solidarity": "unity; togetherness",
 "solipsism": "only-self-exists belief",
 "solvency": "ability to pay debts",
 "stagflation": "stagnation with inflation",
 "stagnate": "stand still; idle",
 "statutory": "legislated; legal",
 "stringent": "strict; severe",
 "subjectivism": "personal-view theory",
 "subpoena": "court summons",
 "subsidy": "grant; financial aid",
 "supply side": "producers' side",
 "syntax": "sentence structure",
 "synthesize": "combine; produce",
 "tariff": "customs duty",
 "temperance": "moderation; self-restraint",
 "tenacious": "persistent; stubborn",
 "theism": "belief in God",
 "theocracy": "rule by religious leaders",
 "totalitarianism": "absolute rule; dictatorship",
 "transparency": "openness; clarity",
 "tribunal": "court; judicial body",
 "ubiquitous": "omnipresent; everywhere",
 "utilitarianism": "greatest good principle",
 "vacillate": "waver; hesitate",
 "validate": "confirm; authorize",
 "veracity": "truthfulness",
 "verbose": "wordy; long-winded",
 "verdict": "judgment; decision",
 "vernacular": "native language; dialect",
 "vigilant": "watchful; alert",
 "vindictive": "vengeful; spiteful",
 "virtue": "goodness; morality",
 "virtue ethics": "character-based ethics",
 "wither": "shrivel; fade",
 "zealotry": "extreme zeal"
}
NOWDOC_END;
    }
};
