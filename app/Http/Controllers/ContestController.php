<?php

namespace App\Http\Controllers;

use App\Models\Contest;
use App\Models\ContestEnroll;
use App\Models\ContestQuestion;
use App\Models\Result;
use App\Traits\HttpWebResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ContestController extends Controller
{
    use HttpWebResponse;
    function contest_index()
    {
        return view('admin.contest.index');
    }
    function contest_create()
    {
        return view('admin.contest.create');
    }
    function contest_store(Request $request)
    {
        $data = [];
        $fillableFields = ['name', 'date', 'time', 'price', 'duration'];
        foreach ($fillableFields as $field) {
            if ($request->filled($field)) {
                $data[$field] = $request->input($field);
            }
        }
        $rules = ['name' => 'required', 'date' => 'required', 'time' => 'required', 'price' => 'required', 'duration' => 'required'];
        $messages = [
            'name.required' => 'The name field is required.',
            'date.required' => 'The date field is required.',
            'time.required' => 'The time field is required.',
            'price.required' => 'The price field is required.',
            'duration.required' => 'The duration field is required.',
        ];
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $user = Auth::user();
        $this->notification($user->id, '"' . $user->name . '" create new contest "' . $request['name'] . '" successful.!', 'contest', '1');
        $contest = Contest::createStore($request);
        if ($contest) {
            return back()->with('success', 'New contest "' . $request['name'] . '" created successfull.!');
        } else {
            return back()->with('warning', 'Error check all data again and submit again...!');
        }
    }
    function contest_show($slug)
    {
        $contest = Contest::where('slug', $slug)->first();
        return view('admin.contest.show', compact('contest'));
    }
    function contest_edit($slug)
    {
        $contest = Contest::where('slug', $slug)->first();
        return view('admin.contest.edit', compact('contest'));
    }
    function contest_update(Request $request, $id)
    {
        $user = Auth::user();
        $this->notification($user->id, '"' . $user->name . '" update contest "' . $request['name'] . '" successful.!', 'contest', '1');
        $contest = Contest::updateStore($request, $id);
        if ($contest) {
            return redirect(route('contest.index'))->with('success', 'Contest "' . $request['name'] . '" Update successfull.!');
        } else {
            return back()->with('warning', 'Error check all data again and submit again...!');
        }
    }
    function contest_delete(Request $request, $id)
    {
        $contest = Contest::find($id);
        $contest->delete();
        $user = Auth::user();
        $this->notification($user->id, '"' . $user->name . '" delete contest "' . $contest->name . '" successful.!', 'contest', '1');
        return redirect(route('contest.index'))->with('success', 'Contest "' . $contest->name . '" Update successfull.!');
    }
    function contest_result($slug)
    {
        $contest = Contest::where('slug', $slug)->first();
        $results = Result::where('contest_id', $contest->id)->orderby('total_mark', 'desc')->paginate(20);
        return view('admin.contest.result', compact('results'));
    }
    function contest_participant($slug)
    {
        $contest = Contest::where('slug', $slug)->first();
        $contestEnrolls = ContestEnroll::latest()->where('contest_id', $contest->id)->paginate(20);
        return view('admin.contest.participant', compact('contestEnrolls'));
    }
    function contest_question_index()
    {
        return view('admin.question.index');
    }
    function contest_question_create($slug)
    {
        $contest = Contest::where('slug', $slug)->first();
        return view('admin.contest.question', compact('contest'));
    }
    function contest_question_store(Request $request)
    {
        $user = Auth::user();
        $this->notification($user->id, '"' . $user->name . '" create new question "' . $request['name'] . '" successful.!', 'contest', '1');
        $contestQuestion = ContestQuestion::createStore($request);
        if ($contestQuestion) {
            return back()->with('success', 'New question "' . $request['name'] . '" created successfull.!');
        } else {
            return back()->with('warning', 'Error check all data again and submit again...!');
        }
    }
    function contest_question_show($id)
    {
        $contestQuestion = ContestQuestion::find($id);
        return view('admin.question.show', compact('contestQuestion'));
    }
    function contest_question_edit($id)
    {
        $contestQuestion = ContestQuestion::find($id);
        return view('admin.question.edit', compact('contestQuestion'));
    }
    function contest_question_update(Request $request, $id)
    {
        $user = Auth::user();
        $this->notification($user->id, '"' . $user->name . '" Update question "' . $request['name'] . '" successful.!', 'contest', '1');
        $contestQuestion = ContestQuestion::updateStore($request, $id);
        if ($contestQuestion) {
            return back()->with('success', 'Question update"' . $request['name'] . '" successfull.!');
        } else {
            return back()->with('warning', 'Error check all data again and submit again...!');
        }
    }
    function contest_question_delete($id)
    {
        $contestQuestion = ContestQuestion::find($id);
        $contestQuestion->delete();
        $user = Auth::user();
        $this->notification($user->id, '"' . $user->name . '" delete contest question "' . $contestQuestion->name . '" successful.!', 'contest', '1');
        return redirect(route('contest.index'))->with('success', 'Contest question "' . $contestQuestion->name . '" delete successfull.!');
    }
}
