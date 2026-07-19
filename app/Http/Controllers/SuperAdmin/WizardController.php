<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\WizardChapter;
use App\Models\WizardStory;
use Illuminate\Http\Request;

class WizardController extends Controller
{
    public function chapter_index()
    {
        $chapters = WizardChapter::latest()->paginate(10);
        return view('admin.wizard.chapter', compact('chapters'));
    }

    public function chapter_store(Request $request)
    {
        $request->validate([
            'title' => 'required',
        ]);
        WizardChapter::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'order_by' => $request->order_by ?? 0,
            'status' => true,
        ]);
        return back()->with('success', $request->title . ' - added as a new Wizard chapter...!');
    }

    public function chapter_edit($id)
    {
        $chapters = WizardChapter::latest()->paginate(10);
        $chapterf = WizardChapter::findOrFail($id);
        return view('admin.wizard.chapter', compact('chapters', 'chapterf'));
    }

    public function chapter_update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
        ]);
        WizardChapter::where('id', $id)->update([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'order_by' => $request->order_by ?? 0,
            'status' => $request->has('status'),
        ]);
        return redirect(route('wizard.chapter.index'))->with('success', 'Wizard chapter updated...!');
    }

    public function chapter_delete($id)
    {
        WizardStory::where('chapter_id', $id)->delete();
        WizardChapter::find($id)->delete();
        return redirect(route('wizard.chapter.index'))->with('success', 'Wizard chapter deleted...!');
    }

    public function story_index($chapterId)
    {
        $chapter = WizardChapter::findOrFail($chapterId);
        $stories = WizardStory::where('chapter_id', $chapterId)->orderBy('order_by')->paginate(10);
        return view('admin.wizard.story', compact('chapter', 'stories'));
    }

    public function story_store(Request $request)
    {
        $request->validate([
            'chapter_id' => 'required',
            'hook_title' => 'required',
            'bangla_title' => 'required',
            'english_content' => 'required',
            'bangla_content' => 'required',
        ]);

        WizardStory::create([
            'chapter_id' => $request->chapter_id,
            'hook_title' => $request->hook_title,
            'meta' => $request->meta,
            'english_paragraphs' => $this->splitParagraphs($request->english_content),
            'bangla_title' => $request->bangla_title,
            'bangla_paragraphs' => $this->splitParagraphs($request->bangla_content),
            'grammar_notes' => $this->parseGrammarNotes($request->grammar_notes),
            'vocabulary' => $this->parseVocabulary($request->vocabulary),
            'order_by' => $request->order_by ?? 0,
            'status' => true,
        ]);

        return redirect(route('wizard.story.index', $request->chapter_id))
            ->with('success', $request->hook_title . ' - added as a new Wizard story...!');
    }

    public function story_edit($id)
    {
        $story = WizardStory::findOrFail($id);
        $chapter = WizardChapter::findOrFail($story->chapter_id);
        $stories = WizardStory::where('chapter_id', $story->chapter_id)->orderBy('order_by')->paginate(10);
        return view('admin.wizard.story', compact('chapter', 'stories', 'story'));
    }

    public function story_update(Request $request, $id)
    {
        $request->validate([
            'hook_title' => 'required',
            'bangla_title' => 'required',
            'english_content' => 'required',
            'bangla_content' => 'required',
        ]);

        $story = WizardStory::findOrFail($id);
        $story->update([
            'hook_title' => $request->hook_title,
            'meta' => $request->meta,
            'english_paragraphs' => $this->splitParagraphs($request->english_content),
            'bangla_title' => $request->bangla_title,
            'bangla_paragraphs' => $this->splitParagraphs($request->bangla_content),
            'grammar_notes' => $this->parseGrammarNotes($request->grammar_notes),
            'vocabulary' => $this->parseVocabulary($request->vocabulary),
            'order_by' => $request->order_by ?? 0,
            'status' => $request->has('status'),
        ]);

        return redirect(route('wizard.story.index', $story->chapter_id))->with('success', 'Wizard story updated...!');
    }

    public function story_delete($id)
    {
        $story = WizardStory::findOrFail($id);
        $chapterId = $story->chapter_id;
        $story->delete();
        return redirect(route('wizard.story.index', $chapterId))->with('success', 'Wizard story deleted...!');
    }

    // Paragraphs are entered as plain text separated by a blank line.
    private function splitParagraphs($text)
    {
        $parts = preg_split('/\n\s*\n/', trim($text));
        return array_values(array_filter(array_map('trim', $parts), fn ($p) => $p !== ''));
    }

    // Grammar notes are entered one per line as "Label :: note text".
    private function parseGrammarNotes($text)
    {
        if (!$text) return [];
        $notes = [];
        foreach (explode("\n", $text) as $line) {
            $line = trim($line);
            if ($line === '') continue;
            $parts = explode('::', $line, 2);
            if (count($parts) === 2) {
                $notes[] = ['label' => trim($parts[0]), 'text' => trim($parts[1])];
            }
        }
        return $notes;
    }

    // Vocabulary entries are entered one per line as "word :: phonetic :: meaning :: pos".
    private function parseVocabulary($text)
    {
        if (!$text) return [];
        $entries = [];
        foreach (explode("\n", $text) as $line) {
            $line = trim($line);
            if ($line === '') continue;
            $parts = array_map('trim', explode('::', $line, 4));
            if (count($parts) === 4) {
                $entries[] = [
                    'word' => $parts[0],
                    'phonetic' => $parts[1],
                    'meaning' => $parts[2],
                    'pos' => $parts[3],
                ];
            }
        }
        return $entries;
    }
}
