<?php

namespace App\Http\Controllers\admin;

use App\wikiCategories;
use App\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\WikiCategoryStoreRequest;
use App\Http\Requests\WikiCategoryUpdateRequest;
use App\Traits\UploadTrait;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;


class WikiCategoriesController extends Controller
{

    use UploadTrait;
    function __construct()
    {
        $this->middleware('can:create Wiki Category', ['only' => ['create', 'store']]);
        $this->middleware('can:edit Wiki Category', ['only' => ['edit', 'update']]);
        $this->middleware('can:delete Wiki Category', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $wikiCategories = wikiCategories::select('*')->with('users')->get();
        return view('admin.wikiCategory.index',compact('wikiCategories'));
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
            $users = User::select('id', 'name')->whereHas('branches', function($q) use ($branch_id) { $q->whereIn('branch_id', $branch_id); })->get();
        }else{
            $users = User::all('id', 'name');
        }

        return view('admin.wikiCategory.create',compact("users"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(WikiCategoryStoreRequest $request)
    {
        try {

            $wikiCategories = new wikiCategories();
            $wikiCategories->name = $request->name;
            $wikiCategories->status = 'Active';
            $wikiCategories->created_by = auth()->user()->id;
            $wikiCategories->updated_by = auth()->user()->id;
            $wikiCategories->save();

            $wikiCategories->users()->attach($request->user_id);
            //Session::flash('success', 'wiki Categories was created successfully.');
            //return redirect()->route('wikiCategories.index');

            return response()->json([
                'success' => 'Wiki Categories was created successfully.' // for status 200
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
     * @param  \App\wikiCategories  $wikiCategories
     * @return \Illuminate\Http\Response
     */
    public function show(wikiCategories $wikiCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\wikiCategories  $wikiCategories
     * @return \Illuminate\Http\Response
     */
    public function edit(wikiCategories $wikiCategory)
    {
        if(!auth()->user()->hasRole('superadmin')){
            $branch_id = auth()->user()->getBranchIdsAttribute();
            $users = User::select('id', 'name')->whereHas('branches', function($q) use ($branch_id) { $q->whereIn('branch_id', $branch_id); })->get();
        }else{
            $users = User::all('id', 'name');
        }
        $wikiCategoryUsers = $wikiCategory->users->pluck('id')->toArray();

        return view('admin.wikiCategory.edit', compact('wikiCategory','users','wikiCategoryUsers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\wikiCategories  $wikiCategories
     * @return \Illuminate\Http\Response
     */
    public function update(WikiCategoryUpdateRequest $request, wikiCategories $wikiCategory)
    {
        try {

            if (empty($wikiCategory)) {
                //Session::flash('failed', 'Wiki Category Update Denied');
                //return redirect()->back();
                return response()->json([
                    'error' => 'Wiki Category update denied.' // for status 200
                ]);   
            }

            $wikiCategory->name = $request->name;
            $wikiCategory->status = $request->status;
            $wikiCategory->updated_by = auth()->user()->id;
            $wikiCategory->save();

            $wikiCategory->users()->sync($request->user_id);
            //Session::flash('success', 'A Wiki Category updated successfully.');
            //return redirect('admin/wikiCategory');

            return response()->json([
                'success' => 'Wiki Category update successfully.' // for status 200
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
     * @param  \App\wikiCategories  $wikiCategories
     * @return \Illuminate\Http\Response
     */
    public function destroy(wikiCategories $wikiCategory)
    {
        // delete related blog   
        $wikiCategory->wikiBlogs()->delete();

        // delete Wiki Category
        $wikiCategory->delete();

        //return redirect('admin/wikiCategory')->with('delete', 'Wiki Category deleted successfully.');
        return response()->json([
            'delete' => 'Wiki Category deleted successfully.' // for status 200
        ]);
    }

    /**
     * Datatables Ajax Data
     *
     * @return mixed
     * @throws \Exception
     */
    public function change_status(Request $request)
    {
        try {

            $wikiCategory = wikiCategories::find($request->id);
            if (empty($wikiCategory)) {
                //Session::flash('failed', 'Wiki Category Update Denied');
                //return redirect()->back();
                return response()->json([
                    'error' => 'Wiki Category update denied.' // for status 200
                ]);   
            }

            if($request->status==0){
                $status='Inactive';
            }else{
                $status='Active';
            }
            $wikiCategory->status = $status;
            $wikiCategory->save();

            //Session::flash('success', 'A Wiki Category updated successfully.');
            //return redirect('admin/wikiCategory');

            return response()->json([
                'success' => 'Wiki Category update successfully.' // for status 200
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
}
