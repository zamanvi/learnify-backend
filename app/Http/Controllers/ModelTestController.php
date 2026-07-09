<?php
namespace App\Http\Controllers;
use App\Models\AllClass;
use App\Models\ModelQuestion;
use App\Models\ModelSyllabus;
use App\Models\ModelTestAll;
use App\Models\ModelTestResult;
use App\Traits\HttpWebResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class ModelTestController extends Controller
{
    use HttpWebResponse;

    public function modeltest_show($id)
    {
        $modeltest = ModelTestAll::find($id);
        $modeltestlist = ModelTestAll::paginate(20);
        return view('admin.model_test.sindex', [
            'modeltestlist' => $modeltestlist,
            'modeltest' => $modeltest,
        ]);
    }
    public function modeltest_edit($id)
    {
        $modeltest = ModelTestAll::find($id);
        $classlist = AllClass::get();
        $modeltestlist = ModelTestAll::paginate(20);
        return view('admin.model_test.eindex', [
            'modeltestlist' => $modeltestlist,
            'modeltest' => $modeltest,
            'classlist' => $classlist,
        ]);
    }
    public function modeltest_delete($id)
    {
        $modeltest = ModelTestAll::find($id);
        $modeltest->delete();
        $this->notification(Auth::user()->id, '"' . Auth::user()->name . '" delete Model test "' . $modeltest->name . '" successfully.!',  'modeltest', '1');
        return redirect('/modeltestcreate')->with('warning', 'Model Test deleted successful.!');
    }
    public function modeltest_index()
    {
        $classlist = AllClass::get();
        $modeltestlist = ModelTestAll::paginate(20);
        return view('admin.model_test.index', [
            'modeltestlist' => $modeltestlist,
            'classlist' => $classlist,
        ]);
    }
    public function modeltest_create()
    {
        $classlist = AllClass::get();
        $modeltestlist = ModelTestAll::paginate(20);
        return view('admin.model_test.create', [
            'modeltestlist' => $modeltestlist,
            'classlist' => $classlist,
        ]);
    }
    public function modeltest_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'class_id' => 'required',
            'subject' => 'required',
        ]);
        ModelTestAll::create([
            'name' => $request['name'],
            'class_id' => $request['class_id'],
            'subject' => $request['subject'],
            'type' => 'off',
        ]);
        $this->notification(Auth::user()->id, '"' . Auth::user()->name . '" create Model test "' . $request['name'] . '" successfully.!',  'modeltest', '1');
        return back()->with('success', 'Model test created successfull.!');
    }
    public function modeltest_update(Request $request, $id)
    {
        ModelTestAll::where('id', $id)->update([
            'name' => $request['name'],
            'class_id' => $request['class_id'],
            'subject' => $request['subject'],
            'type' => $request['type'],
        ]);
        $this->notification(Auth::user()->id, '"' . Auth::user()->name . '" update Model test "' . $request['name'] . '" successfully.!',  'modeltest', '1');
        return back()->with('success', 'Model test update successfull.!');
    }
    public function m_syllabus_create($id)
    {
        $modelTestAll = ModelTestAll::find($id);
        $modelSyllabuslist = ModelSyllabus::where('modeltest_id', $id)->paginate(10);
        return view('admin.model_test.msyllabus', [
            'modelSyllabuslist' => $modelSyllabuslist,
            'modelTestAll' => $modelTestAll,
        ]);
    }

    public function msyllabus_edit($id)
    {
        $modelTestAll = ModelTestAll::get();
        $modelSyllabus = ModelSyllabus::find($id);
        $modelSyllabuslist = ModelSyllabus::paginate(10);
        return view('admin.model_test.e-syllabus', [
            'modelSyllabuslist' => $modelSyllabuslist,
            'modelSyllabus' => $modelSyllabus,
            'modelTestAll' => $modelTestAll,
        ]);
    }
    public function msyllabus_show($id)
    {
        $modelSyllabus = ModelSyllabus::find($id);
        $modelSyllabuslist = ModelSyllabus::paginate(10);
        return view('admin.model_test.s-syllabus', [
            'modelSyllabuslist' => $modelSyllabuslist,
            'modelSyllabus' => $modelSyllabus,
        ]);
    }
    public function msyllabus_store(Request $request)
    {
        $request->validate([
            'modeltest_id' => 'required',
            'name' => 'required',
            'description' => 'required',
        ]);
        ModelSyllabus::create([
            'name' => $request->input('name'),
            'modeltest_id' => $request->input('modeltest_id'),
            'description' => $request->input('description'),
        ]);
        $this->notification(Auth::user()->id, '"' . Auth::user()->name . '" create Modeltest Syllabus "' . $request->input('name') . '" successfully.!',  'modelsyllabus', '1');
        return back()->with('success', 'Model test syllabus created successfull.!');
    }
    public function msyllabus_update(Request $request, $id)
    {
        ModelSyllabus::where('id', $id)->update([
            'name' => $request->input('name'),
            'modeltest_id' => $request->input('modeltest_id'),
            'description' => $request->input('description'),
        ]);
        $this->notification(Auth::user()->id, '"' . Auth::user()->name . '" update Modeltest Syllabus "' . $request->input('name') . '" successfully.!',  'modelsyllabus', '1');
        return back()->with('success', 'Model test syllabus update successfull.!');
    }
    public function msyllabus_delete($id)
    {
        $modeltestSyllabus = ModelSyllabus::find($id);
        $modeltestSyllabus->delete();
        $this->notification(Auth::user()->id, '"' . Auth::user()->name . '" delete Modeltest Syllabus "' . $modeltestSyllabus->name . '" successfully.!',  'modelsyllabus', '1');
        return redirect()->route('modeltest.index')->with('success', 'Model test syllabus created successfull.!');
    }
    public function mquestion_create($id)
    {
        $modeltest = ModelTestAll::find($id);
        $modelquestionlist = ModelQuestion::paginate(20);
        return view('admin.model_test.mquestion', [
            'modelquestionlist' => $modelquestionlist,
            'modeltest' => $modeltest,
        ]);
    }
    public function mquestion_index()
    {
        $modeltestlist = ModelTestAll::get();
        $modelquestionlist = ModelQuestion::paginate(20);
        return view('admin.model_test.question', [
            'modelquestionlist' => $modelquestionlist,
            'modeltestlist' => $modeltestlist,
        ]);
    }
    public function mquestion_store(Request $request)
    {
        $request->validate([
            'modeltest_id' => 'required',
            'name' => 'required',
            'option1' => 'required',
            'option2' => 'required',
            'option3' => 'required',
            'option4' => 'required',
            'option5' => 'required',
        ]);
        ModelQuestion::create([
            'modeltest_id' => $request['modeltest_id'],
            'name' => $request['name'],
            'option1' => $request['option1'],
            'option2' => $request['option2'],
            'option3' => $request['option3'],
            'option4' => $request['option4'],
            'option5' => $request['option5'],
        ]);
        $this->notification(Auth::user()->id, '"' . $request['name'] . '" Model Question Created successful.!',  'question', '1');
        return back()->with('success', $request['name'] . ' - added new question.!');
    }
    public function mquestion_delete($id)
    {
        $modeltestQuestion = ModelQuestion::find($id);
        $modeltestQuestion->delete();
        $this->notification(Auth::user()->id, '"' . Auth::user()->name . '" delete Modeltest Question "' . $modeltestQuestion->name . '" successfully.!',  'modelquestion', '1');
        return back()->with('success', 'Model test syllabus created successfull.!');
    }
}
