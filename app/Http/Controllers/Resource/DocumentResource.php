<?php namespace App\Http\Controllers\Resource;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use DB;
use Exception;
use Setting;

use App\Document;
use App\Helpers\Helper;

class DocumentResource extends Controller
{

    public function __construct()
    {
        $this->middleware('demo', ['only' => ['store' ,'update', 'destroy']]);
    }

    public function index()
    {
        $documents = Document::orderBy('created_at' , 'desc')->get();
        return view('admin.document.index', compact('documents'));
    }

    public function create()
    {
        return view('admin.document.create');
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'name' => 'required|max:255',
            'type' => 'required|in:VEHICLE,DRIVER',
            'is_mandatory' => 'required',
        ]);

        try{

            Document::create($request->all());
            return redirect()->route('admin.document.index')->with('flash_success',trans('admin.document_msgs.document_saved'));

        } 

        catch (Exception $e) {
            return back()->with('flash_error', trans('admin.document_msgs.document_not_found'));
        }
    }

    public function show($id)
    {
        try {
            return Document::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return $e;
        }
    }

    public function edit($id)
    {
        try {
            $document = Document::findOrFail($id);
            return view('admin.document.edit',compact('document'));
        } catch (ModelNotFoundException $e) {
            return $e;
        }
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'type' => 'required|in:VEHICLE,DRIVER',
            'is_mandatory' => 'required',
        ]);

        try {
            Document::where('id',$id)->update([
                    'name' => $request->name,
                    'type' => $request->type,
                    'is_mandatory' => $request->is_mandatory,
                ]);
            return redirect()->route('admin.document.index')->with('flash_success', trans('admin.document_msgs.document_update'));    
        } 

        catch (Exception $e) {
            return back()->with('flash_error', trans('admin.document_msgs.document_not_found'));
        }
    }

    public function destroy($id)
    {
        try {
            Document::find($id)->delete();
            return back()->with('flash_success', trans('admin.document_msgs.document_delete'));
        } 
        catch (Exception $e) {
            return back()->with('flash_error', trans('admin.document_msgs.document_not_found'));
        }
    }
}
