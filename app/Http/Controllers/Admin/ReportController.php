<?php

namespace App\Http\Controllers\Admin;

use App\Attendance;
use App\Branch;
use App\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceStoreRequest;
use App\Http\Requests\AttendanceUpdateRequest;
use App\Traits\UploadTrait;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use JeroenDesloovere\Distance\Distance;
use Carbon\Carbon;

class ReportController extends Controller
{
    use UploadTrait;

    function __construct()
    {
        $this->middleware('can:create attendance', ['only' => ['create', 'store']]);
        $this->middleware('can:edit attendance', ['only' => ['edit', 'update']]);
        $this->middleware('can:delete attendance', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function employee_daily_summary()
    {
        if(auth()->user()->hasRole('superadmin')){
            $branches = Branch::all();
            $users = User::all();
        }elseif(auth()->user()->hasRole('Team Leader')){
            $branch_id = auth()->user()->getBranchIdsAttribute();
            $branches = Branch::whereIn('id',$branch_id)->get();
            $child_id = auth()->user()->children()->pluck('id')->toArray();
            $users = User::whereIn('id', $child_id)->get();
        }else{
            $branch_id = auth()->user()->getBranchIdsAttribute();
            $branches = Branch::whereIn('id',$branch_id)->get();
            $users = User::whereHas('branches', function($q) use ($branch_id) { $q->whereIn('branch_id', $branch_id); })->get();
        }
        return view('admin.report.employee_daily_summary', compact("users","branches"));
    }

    public function datatables(Request $request)
    {

        if ($request->ajax() == true) {

            $daterange = $request->daterange;
            if($daterange!=null || $daterange!='' ){
                $dates = explode(" - ", $daterange);
                $startDate = Carbon::createFromFormat('d/m/Y', Carbon::parse($dates[0])->format('d/m/Y'));
                $endDate = Carbon::createFromFormat('d/m/Y', Carbon::parse($dates[1])->format('d/m/Y'));
                
                //dd($endDate);
                if(auth()->user()->hasRole('superadmin')){
                    $model = User::with(['attendance_creator' => function($query) use ($startDate,$endDate) {
                                        $query->where('status', 'punch_in')->whereBetween('attendance_at', [$startDate, $endDate]);
                                    },'branches','attendance_editor']);
                }elseif(auth()->user()->hasRole('Team Leader')){
                    $child_id = auth()->user()->children()->pluck('id')->toArray();
                    $model = User::whereIn('id',$child_id)->with(['attendance_creator' => function($query) use ($startDate,$endDate)  {
                                                            $query->where('status', 'punch_in')->whereBetween('attendance_at', [$startDate, $endDate]);
                                                        },'branches','attendance_editor']);
                    //dd($model[0]->attendance_creator);
                }else{
                    $branch_id = auth()->user()->getBranchIdsAttribute();
                    $model = User::whereIn('id',$child_id)
                                    ->with(['attendance_creator' => function($query) use ($startDate,$endDate)  {
                                            $query->where('status', 'punch_in')->whereBetween('attendance_at', [$startDate, $endDate]);
                                        },'branches','attendance_editor'])
                                    ->whereHas('branches', function($q) use ($branch_id) { 
                                                $q->whereIn('branch_id', $branch_id); });
                }
            }else{
                if(auth()->user()->hasRole('superadmin')){
                    $model = User::with(['attendance_creator' => function($query) {
                                                                            $query->where('status', 'punch_in');
                                                                        },'branches','attendance_editor']);
                }elseif(auth()->user()->hasRole('Team Leader')){
                    $child_id = auth()->user()->children()->pluck('id')->toArray();
                    $model = User::whereIn('id',$child_id)->with(['attendance_creator' => function($query) {
                                                                            $query->where('status', 'punch_in');
                                                                        },'branches','attendance_editor']);
                }else{
                    $branch_id = auth()->user()->getBranchIdsAttribute();
                    $model = User::whereIn('id',$child_id)
                                    ->with(['attendance_creator' => function($query) {
                                                                            $query->where('status', 'punch_in');
                                                                        },'branches','attendance_editor'])
                                    ->whereHas('branches', function($q) use ($branch_id) { 
                                                $q->whereIn('branch_id', $branch_id); });
                }
            }
            
            return Datatables::eloquent($model)

                    ->addColumn('username', function (User $data) {
                        return '<img src="'.$data->getImageUrlAttribute($data->id).'" alt="user_id_'.$data->id.'" class="profile-user-img-small img-circle"> '. $data->name;
                    })
                    
                    ->addColumn('branch', function (User $data) {
                        $branches='';
                        foreach($data->branches as $branch){
                            $branches .= '<span class="selection_choice">'.$branch->company->name.' - '.$branch->name.'</span>';
                        }
                        return $branches;
                    })

                    ->addColumn('search_username', function (User $data) {
                        return 'user_id_'.$data->id;
                    })

                    ->addColumn('total_hrs', function (User $data) {

                        $totalDuration = 0;
                        foreach ($data->attendance_creator as $attendance_creator) {
                            if($attendance_creator->punch_out!=null){
                                $startTime = Carbon::parse($attendance_creator->attendance_at);
                                $endTime = Carbon::parse($attendance_creator->punch_out->attendance_at);
                                $mins            = $endTime->diffInMinutes($startTime, true)/60;
                                $totalDuration =  $totalDuration + $mins;
                            }
                        }
                        
                        return number_format($totalDuration,2,".","");
                        
                    })
                    
                    
                    ->rawColumns(['total_hrs', 'username', 'branch', 'search_username'])

                    ->make(true);
        }
    }

}
