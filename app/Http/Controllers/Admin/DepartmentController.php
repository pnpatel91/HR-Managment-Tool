<?php

namespace App\Http\Controllers\Admin;

use App\Department;
use App\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentStoreRequest;
use App\Traits\UploadTrait;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{

    function __construct()
    {
        $this->middleware('can:create department', ['only' => ['create', 'store']]);
        $this->middleware('can:edit department', ['only' => ['edit', 'update']]);
        $this->middleware('can:delete department', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.department.index');
    }

    /**
     * Datatables Ajax Data
     *
     * @return mixed
     * @throws \Exception
     */
    public function datatables(Request $request)
    {

        if ($request->ajax() == true) {

            $data = Department::select([
                'id',
                'name',
                'created_at',
                'updated_at',
            ])->with('users');

            return Datatables::eloquent($data)
                ->addColumn('action', function ($data) {
                    
                    $html='';
                    if (auth()->user()->can('edit department')){
                        $html.= '<a href="'.  route('admin.department.edit', ['department' => $data->id]) .'" class="btn btn-success btn-sm float-left mr-3"  id="popup-modal-button"><span tooltip="Edit" flow="left"><i class="fas fa-edit"></i></span></a>';
                    }

                    if (auth()->user()->can('delete department')){
                        $html.= '<form method="post" class="float-left delete-form" action="'.  route('admin.department.destroy', ['department' => $data->id ]) .'"><input type="hidden" name="_token" value="'. Session::token() .'"><input type="hidden" name="_method" value="delete"><button type="submit" class="btn btn-danger btn-sm"><span tooltip="Delete" flow="up"><i class="fas fa-trash"></i></span></button></form>';
                    }

                    return $html; 
                })

                ->addColumn('users_avatars', function ($data) {
                    $users='<div class="avatars_overlapping">';
  
                    foreach ($data->users as $key => $value) {
                       $users.='<span class="avatar_overlapping"><p tooltip="'.$value->name.'" flow="up"><img src="'.$value->getImageUrlAttribute($value->id).'" width="50" height="50" /></p></span>';
                    }

                    return $users.='</div>';
                })

                ->rawColumns(['users_avatars', 'action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::all('id', 'name');
        return view('admin.department.create', compact("users"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Request\DepartmentStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DepartmentStoreRequest $request)
    {
        try {

            $department = new Department();
            $department->name = $request->name;
            $department->created_by = auth()->user()->id;
            $department->updated_by = auth()->user()->id;
            $department->save();

            $department->users()->attach($request->user_id);
            //Session::flash('success', 'department was created successfully.');
            //return redirect()->route('department.index');

            return response()->json([
                'success' => 'department was created successfully.' // for status 200
            ]);

        } catch (\Exception $exception) {

            DB::rollBack();

            //Session::flash('failed', $exception->getMessage() . ' ' . $exception->getLine());
            /*return redirect()->back()->withInput($request->all());*/

            return response()->json([
                'error' => $exception->getMessage() . ' ' . $exception->getLine() // for status 200
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function show(Department $department)
    {
        return view('admin.department.show', compact('department'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function edit(Department $department)
    {
        $users = User::all('id', 'name');
        $departmentUsers = $department->users->pluck('id')->toArray();
        return view('admin.department.edit', compact('department', 'users', 'departmentUsers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Request\DepartmentStoreRequest  $request
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function update(DepartmentStoreRequest $request, Department $department)
    {
        try {

            if (empty($department)) {
                //Session::flash('failed', 'department Update Denied');
                //return redirect()->back();
                return response()->json([
                    'error' => 'department update denied.' // for status 200
                ]);   
            }

            $department->name = $request->name;
            $department->updated_by = auth()->user()->id;
            $department->save();

            $department->users()->sync($request->user_id);

            //Session::flash('success', 'A department updated successfully.');
            //return redirect('admin/department');

            return response()->json([
                'success' => 'department update successfully.' // for status 200
            ]);

        } catch (\Exception $exception) {

            DB::rollBack();

            //Session::flash('failed', $exception->getMessage() . ' ' . $exception->getLine());
            /*return redirect()->back()->withInput($request->all());*/

            return response()->json([
                'error' => $exception->getMessage() . ' ' . $exception->getLine() // for status 200
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function destroy(Department $department)
    {
        // delete related users   
        $department->users()->delete();

        // delete department
        $department->delete();

        //return redirect('admin/department')->with('success', 'department deleted successfully.');
        return response()->json([
            'success' => 'department & users deleted successfully.' // for status 200
        ]);
    }
}
