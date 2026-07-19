@extends('admin.index')
@section('title')
    Wizard Stories
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('wizard.chapter.index') }}">Wizard</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $chapter->title }}</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Stories in "{{ $chapter->title }}"</h3>
                        <table class="table table-striped table-bordered mt-1">
                            <thead>
                                <tr>
                                    <th>Hook Title</th>
                                    <th width="20%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stories as $s)
                                    <tr>
                                        <td>{{ $s->hook_title }}</td>
                                        <td>
                                            <a class="btn btn-sm btn-primary" href="{{ route('wizard.story.edit', $s->id) }}">Edit</a>
                                            <a class="btn btn-sm btn-danger" href="{{ route('wizard.story.delete', $s->id) }}"
                                                onclick="return confirm('Delete this story?');">Delete</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div>{{ $stories->links() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">{{ isset($story) ? 'Edit Story' : 'Create Story' }}</h3>
                        <form method="POST"
                            action="{{ isset($story) ? route('wizard.story.update', $story->id) : route('wizard.story.store') }}">
                            @csrf
                            @if (isset($story))
                                @method('PUT')
                            @endif
                            <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">

                            <div class="form-group">
                                <label>Hook Title (English)</label>
                                <input required type="text" name="hook_title" class="form-control"
                                    value="{{ $story->hook_title ?? '' }}" placeholder="The Wave That Was Sweeter Than Water">
                            </div>
                            <div class="form-group">
                                <label>Meta (place, year)</label>
                                <input type="text" name="meta" class="form-control"
                                    value="{{ $story->meta ?? '' }}" placeholder="Boston, 1919">
                            </div>
                            <div class="form-group">
                                <label>English Story — separate paragraphs with a blank line</label>
                                <textarea required name="english_content" class="form-control" rows="6"
                                    placeholder="First paragraph...&#10;&#10;Second paragraph...">{{ isset($story) ? implode("\n\n", $story->english_paragraphs) : '' }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Bangla Title</label>
                                <input required type="text" name="bangla_title" class="form-control"
                                    value="{{ $story->bangla_title ?? '' }}" placeholder="যে ঢেউ পানির চেয়েও মিষ্টি ছিল">
                            </div>
                            <div class="form-group">
                                <label>Bangla Translation — separate paragraphs with a blank line</label>
                                <textarea required name="bangla_content" class="form-control" rows="6"
                                    placeholder="প্রথম অনুচ্ছেদ...&#10;&#10;দ্বিতীয় অনুচ্ছেদ...">{{ isset($story) ? implode("\n\n", $story->bangla_paragraphs) : '' }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Grammar Notes — one per line, as "Label :: note text"</label>
                                <textarea name="grammar_notes" class="form-control" rows="4"
                                    placeholder="গ্রামার নোট ১ :: ব্যাখ্যা এখানে লিখুন">{{ isset($story) ? collect($story->grammar_notes)->map(fn($n) => $n['label'] . ' :: ' . $n['text'])->implode("\n") : '' }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Vocabulary — one per line, as "word :: phonetic :: meaning :: pos"</label>
                                <textarea name="vocabulary" class="form-control" rows="5"
                                    placeholder="molasses :: মোলাসিস :: গুড়ের রস, চিটাগুড় :: N">{{ isset($story) ? collect($story->vocabulary)->map(fn($v) => $v['word'] . ' :: ' . $v['phonetic'] . ' :: ' . $v['meaning'] . ' :: ' . $v['pos'])->implode("\n") : '' }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Order</label>
                                <input type="number" name="order_by" class="form-control"
                                    value="{{ $story->order_by ?? 0 }}">
                            </div>
                            @if (isset($story))
                                <div class="form-group form-check">
                                    <input type="checkbox" name="status" class="form-check-input" id="status"
                                        @checked($story->status)>
                                    <label class="form-check-label" for="status">Active</label>
                                </div>
                            @endif
                            <input type="submit" class="btn btn-primary" value="Submit">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
