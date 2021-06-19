<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Company;
use App\Image;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyStoreRequest;
use App\Traits\UploadTrait;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    use UploadTrait;

    function __construct()
    {
        $this->middleware('can:create company', ['only' => ['create', 'store']]);
        $this->middleware('can:edit company', ['only' => ['edit', 'update']]);
        $this->middleware('can:delete company', ['only' => ['destroy']]);
    }
    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.company.index');  
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
            $data = Company::select([
                'id',
                'name',
                'email',
                'website',
                'created_at',
                'updated_at',
            ]);

            // Where condition on Role and Branch, If role super admin then show all records, other than only user branch records show.
            if(!auth()->user()->hasRole('superadmin')){
                $branch_id = auth()->user()->getBranchIdsAttribute();
                $data->whereHas('branch', function($query) use ($branch_id) {
                        $query->whereIn('id', $branch_id);
                    })->get();
            }

            return Datatables::eloquent($data)
                ->addColumn('action', function ($data) {
                    
                    $html='';
                    if (auth()->user()->can('edit company')){
                        $html.= '<a href="'.  route('admin.company.edit', ['company' => $data->id]) .'" class="btn btn-success btn-sm float-left mr-3"  id="popup-modal-button"><span tooltip="Edit" flow="left"><i class="fas fa-edit"></i></span></a>';
                    }

                    if (auth()->user()->can('delete company')){
                        $html.= '<form method="post" class="float-left delete-form" action="'.  route('admin.company.destroy', ['company' => $data->id ]) .'"><input type="hidden" name="_token" value="'. Session::token() .'"><input type="hidden" name="_method" value="delete"><button type="submit" class="btn btn-danger btn-sm"><span tooltip="Delete" flow="right"><i class="fas fa-trash"></i></span></button></form>';
                    }

                    return $html; 
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
        return view('admin.company.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CompanyStoreRequest $request)
    {
        try {

            $company = new Company();
            $company->name = $request->name;
            $company->email = $request->email;
            $company->website = $request->website;
            $company->created_by = auth()->user()->id;;
            $company->updated_by = auth()->user()->id;;
            $company->save();

            //Session::flash('success', 'company was created successfully.');
            //return redirect()->route('company.index');

            return response()->json([
                'success' => 'company was created successfully.' // for status 200
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
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
         return view('admin.company.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function edit(Company $company)
    {
        return view('admin.company.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(CompanyStoreRequest $request, Company $company)
    {
        try {

            if (empty($company)) {
                //Session::flash('failed', 'company Update Denied');
                //return redirect()->back();
                return response()->json([
                    'error' => 'company update denied.' // for status 200
                ]);   
            }

            $company->name = $request->name;
            $company->email = $request->email;
            $company->website = $request->website;
            $company->updated_by = auth()->user()->id;
            $company->save();

            //Session::flash('success', 'A company updated successfully.');
            //return redirect('admin/company');

            return response()->json([
                'success' => 'company update successfully.' // for status 200
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
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        // delete related branch   
        $company->branchs()->delete();

        // delete company
        $company->delete();

        //return redirect('admin/company')->with('success', 'company deleted successfully.');
        return response()->json([
            'success' => 'company deleted successfully.' // for status 200
        ]);
    }
}
