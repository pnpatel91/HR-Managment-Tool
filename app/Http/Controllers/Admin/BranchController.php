<?php

namespace App\Http\Controllers\Admin;

use App\Branch;
use App\Company;
use App\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\BranchStoreRequest;
use App\Traits\UploadTrait;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;


class BranchController extends Controller
{
    use UploadTrait;

    function __construct()
    {
        $this->middleware('can:create branch', ['only' => ['create', 'store']]);
        $this->middleware('can:edit branch', ['only' => ['edit', 'update']]);
        $this->middleware('can:delete branch', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.branch.index');
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

            //$model = Branch::with('company');//All Branches Show [Old Code]

            // Where condition on Role and Branch, If role super admin then show all records, other than only user branch records show.
            $model = Branch::with('company');
            if(!auth()->user()->hasRole('superadmin')){
                $branch_id = auth()->user()->getBranchIdsAttribute();
                $model->whereIn('id',$branch_id);
            }

            return Datatables::eloquent($model)
                    ->addColumn('action', function (Branch $data) {
                        $html='';
                        if (auth()->user()->can('edit branch')){
                            $html.= '<a href="'.  route('admin.branch.edit', ['branch' => $data->id]) .'" class="btn btn-success btn-sm float-left mr-3"  id="popup-modal-button"><span tooltip="Edit" flow="left"><i class="fas fa-edit"></i></span></a>';
                        }

                        if (auth()->user()->can('delete branch')){
                            $html.= '<form method="post" class="float-left delete-form" action="'.  route('admin.branch.destroy', ['branch' => $data->id ]) .'"><input type="hidden" name="_token" value="'. Session::token() .'"><input type="hidden" name="_method" value="delete"><button type="submit" class="btn btn-danger btn-sm"><span tooltip="Delete" flow="up"><i class="fas fa-trash"></i></span></button></form>';
                        }

                        return $html; 
                    })
                    ->editColumn('radius', '{{intval($radius)}} M')
                    ->addColumn('fulladdress', function (Branch $data) {
                        return $data->address .', '. $data->city .', '. $data->state .', '. $data->postcode .', '. $data->country;
                    })

                    ->addColumn('company', function (Branch $data) {
                        return $data->company->name;
                    })

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
        if(!auth()->user()->hasRole('superadmin')){
            $branch_id = auth()->user()->getBranchIdsAttribute();
            $users = User::select('id', 'name')->whereHas('branches', function($q) use ($branch_id) { $q->where('branch_id', $branch_id); })->get();
            $company = Company::select('id', 'name')->whereHas('branch', function($q) use ($branch_id) { $q->where('id', $branch_id); })->get();
        }else{
            $company = Company::all('id', 'name');
            $users = User::all('id', 'name');
        }
        return view('admin.branch.create', compact("company","users"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BranchStoreRequest $request)
    {
        try {

            $branch = new Branch();
            $branch->name = $request->name;
            $branch->address = $request->address;
            $branch->city = $request->city;
            $branch->state = $request->state;
            $branch->postcode = $request->postcode;
            $branch->country = $request->country;
            $branch->latitude = $request->latitude;
            $branch->longitude = $request->longitude;
            $branch->radius = $request->radius;
            $branch->company_id = $request->company_id;
            $branch->created_by = auth()->user()->id;
            $branch->updated_by = auth()->user()->id;
            $branch->save();

            $branch->users()->attach($request->user_id);

            //Session::flash('success', 'branch was created successfully.');
            //return redirect()->route('branch.index');

            return response()->json([
                'success' => 'branch was created successfully.' // for status 200
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
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function show(Branch $branch)
    {
        return view('admin.branch.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function edit(Branch $branch)
    {
        if(!auth()->user()->hasRole('superadmin')){
            $branch_id = auth()->user()->getBranchIdsAttribute();
            $users = User::select('id', 'name')->whereHas('branches', function($q) use ($branch_id) { $q->where('branch_id', $branch_id); })->get();
            $company = Company::select('id', 'name')->whereHas('branch', function($q) use ($branch_id) { $q->where('id', $branch_id); })->get();
        }else{
            $company = Company::all('id', 'name');
            $users = User::all('id', 'name');
        }
        
        $branchUsers = $branch->users->pluck('id')->toArray();
        return view('admin.branch.edit', compact('branch', 'company', 'users', 'branchUsers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function update(BranchStoreRequest $request, Branch $branch)
    {

        try {

            if (empty($branch)) {
                //Session::flash('failed', 'branch Update Denied');
                //return redirect()->back();
                return response()->json([
                    'error' => 'branch update denied.' // for status 200
                ]);   
            }

            $branch->name = $request->name;
            $branch->address = $request->address;
            $branch->city = $request->city;
            $branch->state = $request->state;
            $branch->postcode = $request->postcode;
            $branch->country = $request->country;
            $branch->latitude = $request->latitude;
            $branch->longitude = $request->longitude;
            $branch->radius = $request->radius;
            $branch->company_id = $request->company_id;
            $branch->updated_by = auth()->user()->id;
            $branch->save();

            $branch->users()->sync($request->user_id);

            //Session::flash('success', 'A branch updated successfully.');
            //return redirect('admin/branch');

            return response()->json([
                'success' => 'branch update successfully.' // for status 200
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
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function destroy(Branch $branch)
    {
        $branch->users()->detach();

        // delete branch
        $branch->delete();

        //return redirect('admin/branch')->with('success', 'branch deleted successfully.');
        return response()->json([
            'success' => 'branch deleted successfully.' // for status 200
        ]);
    }
}
