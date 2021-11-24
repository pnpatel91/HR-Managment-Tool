<?php

namespace App\Http\Controllers\Admin;

use App\Holiday;
use App\Branch;

use App\Http\Controllers\Controller;
use App\Http\Requests\HolidayStoreRequest;
use App\Http\Requests\HolidayUpdateRequest;
use App\Traits\UploadTrait;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    use UploadTrait;
    function __construct()
    {
        $this->middleware('can:create holiday', ['only' => ['create', 'store']]);
        $this->middleware('can:edit holiday', ['only' => ['edit', 'update']]);
        $this->middleware('can:delete holiday', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branch_id = auth()->user()->getBranchIdsAttribute();
        $holidays = Holiday::select([
                'id',
                'name',
                'holiday_date',
                'created_by',
                'updated_by',
                'created_at',
                'updated_at',
            ])->with(['branches' => function($query) use ($branch_id) {
                                    $query->whereIn('branch_id', $branch_id);
                                }])
                            ->whereHas('branches', function($q) use ($branch_id) { $q->whereIn('branch_id', $branch_id); })->orderBy('holiday_date', 'ASC')->get();
        return view('admin.holiday.index',compact('holidays'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Where condition on Role and Branch, If role super admin then show all records, other than only user branch records show.
        if(!auth()->user()->hasRole('superadmin')){
            $branch_id = auth()->user()->getBranchIdsAttribute();
            $branches = Branch::whereIn('id',$branch_id)->get();
        }else{
            $branches = Branch::all();
        }

        return view('admin.holiday.create', compact("branches"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(HolidayStoreRequest $request)
    {
        try {

            $holiday = new Holiday();
            $holiday->name = $request->name;
            $holiday->holiday_date = $request->holiday_date;
            $holiday->created_by = auth()->user()->id;
            $holiday->updated_by = auth()->user()->id;
            $holiday->save();

            $holiday->branches()->attach($request->branch_id);
            //Session::flash('success', 'holiday was created successfully.');
            //return redirect()->route('holiday.index');

            return response()->json([
                'success' => 'holiday was created successfully.' // for status 200
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
     * @param  \App\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function show(Holiday $holiday)
    {
        return view('admin.holiday.show', compact('holiday'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function edit(Holiday $holiday)
    {
        // Where condition on Role and Branch, If role super admin then show all records, other than only user branch records show.
        if(!auth()->user()->hasRole('superadmin')){
            $branch_id = auth()->user()->getBranchIdsAttribute();
            $branches = Branch::whereIn('id',$branch_id)->get();
        }else{
            $branches = Branch::all();
        }
        $holidayBranches = $holiday->branches->pluck('id')->toArray();
        return view('admin.holiday.edit', compact('holiday', 'branches', 'holidayBranches'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\HolidayUpdateRequest  $request
     * @param  \App\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function update(HolidayUpdateRequest $request, Holiday $holiday)
    {
        try {

            if (empty($holiday)) {
                //Session::flash('failed', 'holiday Update Denied');
                //return redirect()->back();
                return response()->json([
                    'error' => 'holiday update denied.' // for status 200
                ]);   
            }

            $holiday->name = $request->name;
            $holiday->holiday_date = $request->holiday_date;
            $holiday->updated_by = auth()->user()->id;
            $holiday->save();

            $holiday->branches()->sync($request->branch_id);
            //Session::flash('success', 'A holiday updated successfully.');
            //return redirect('admin/holiday');

            return response()->json([
                'success' => 'holiday update successfully.' // for status 200
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
     * @param  \App\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function destroy(Holiday $holiday)
    {
        $holiday->branches()->detach();

        // delete holiday
        $holiday->delete();

        //return redirect('admin/holiday')->with('delete', 'holiday deleted successfully.');
        return response()->json([
            'delete' => 'holiday deleted successfully.' // for status 200
        ]);
    }
}
