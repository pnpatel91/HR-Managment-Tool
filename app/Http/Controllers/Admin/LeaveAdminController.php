<?php

namespace App\Http\Controllers\Admin;

use App\Leave;
use App\User;
use App\Branch;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeaveStoreRequest;
use App\Http\Requests\LeaveUpdateRequest;
use App\Traits\UploadTrait;
use App\Notifications\leavesNotification;
use App\Mail\LeavesNotificationMail;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use JeroenDesloovere\Distance\Distance;
use Carbon\Carbon;
use Notification;
use Mail;

class LeaveAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!auth()->user()->hasRole('superadmin')){
            $branch_id = auth()->user()->getBranchIdsAttribute();
            $branches = Branch::whereIn('id',$branch_id)->get();
            $users = User::whereHas('branches', function($q) use ($branch_id) { $q->whereIn('branch_id', $branch_id); })->get();
        }else{
            $branches = Branch::all();
            $users = User::all();
        }

        return view('admin.leave.index', compact("users","branches"));
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

            
            if(!auth()->user()->hasRole('superadmin')){
                $branch_id = auth()->user()->getBranchIdsAttribute();
                $model = Leave::whereIn('branch_id', $branch_id)->with('employee','leave_approved_by', 'branch');
            }else{
                $model = Leave::with('employee','leave_approved_by', 'branch');
            }
            
            return Datatables::eloquent($model)
                    ->addColumn('action', function (Leave $data) {
                        $html='';
                        if (auth()->user()->can('edit leave - admin')){
                            $html.= '<a href="'.  route('admin.leave.edit', ['leave' => $data->id]) .'" class="btn btn-success btn-sm float-left mr-3"  id="popup-modal-button"><span tooltip="Edit" flow="left"><i class="fas fa-edit"></i></span></a>';
                        }

                        if (auth()->user()->can('delete leave - admin')){
                            $html.= '<form method="post" class="float-left delete-form" action="'.  route('admin.leave.destroy', ['leave' => $data->id ]) .'"><input type="hidden" name="_token" value="'. Session::token() .'"><input type="hidden" name="_method" value="delete"><button type="submit" class="btn btn-danger btn-sm"><span tooltip="Delete" flow="up"><i class="fas fa-trash"></i></span></button></form>';
                        }

                        return $html; 
                    })

                    ->addColumn('branch', function (Leave $data) {
                        return $data->branch->company->name.' - '.$data->branch->name;
                    })

                    ->addColumn('employee', function (Leave $data) {
                        return '<img src="'.$data->employee->getImageUrlAttribute($data->employee->id).'" alt="user_id_'.$data->employee->id.'" class="profile-user-img-small img-circle"> '. $data->employee->name;
                    })

                    ->addColumn('leave_approved_by', function (Leave $data) {
                        return '<img src="'.$data->leave_approved_by->getImageUrlAttribute($data->leave_approved_by->id).'" alt="Admin" class="profile-user-img-small img-circle"> '. $data->leave_approved_by->name;
                    })

                    ->addColumn('leave_status', function (Leave $data) {
                        if($data->status=='New'){ $class= 'text-purple';}elseif ($data->status=='Approved') { $class ='text-success'; }else{$class ='text-danger';}
                        return '<a class="btn btn-white btn-sm btn-rounded" href="javascript:void(0);">
                                                        <i class="fa fa-dot-circle-o '.$class.'"></i> '.$data->status.' </a>';
                    })

                    
                    ->addColumn('search_username', function (Leave $data) {
                        return 'user_id_'.$data->employee->id;
                    })
                    
                    ->rawColumns(['employee', 'leave_approved_by', 'action', 'leave_status'])

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
            $branches = Branch::whereIn('id',$branch_id)->get();
            $approvers = User::whereHas("roles", function($q){ $q->where("name", "superadmin")->orWhere("name", "admin"); })->get();
            $users = User::select('id', 'name')->whereHas('branches', function($q) use ($branch_id) { $q->where('branch_id', $branch_id); })->get();
        }else{
            $branches = Branch::all();
            $approvers = User::whereHas("roles", function($q){ $q->where("name", "superadmin")->orWhere("name", "admin"); })->get();
            $users = User::all('id', 'name');
        }

        $leave_types = ['Annual', 'Sick', 'Hospitalisation', 'Maternity', 'Paternity', 'LOP'];
        $half_day = ['Full Day', 'Half Day'];
        $status = ['New', 'Approved', 'Declined'];
        return view('admin.leave.create', compact("branches", "approvers", "users", "leave_types", "half_day"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Request\LeaveStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LeaveStoreRequest $request)
    {
        try {
            
                $leave_date = explode(' - ', $request->leave_date);

                $leave = new Leave();
                $leave->employee_id = $request->employee_id;
                $leave->approved_by = $request->approved_by;
                $leave->branch_id = $request->branch_id;
                $leave->start_at = Carbon::parse($leave_date[0]);
                $leave->end_at = Carbon::parse($leave_date[1]);
                $leave->leave_days = $request->days;
                $leave->leave_type = $request->leave_type;
                $leave->reason = $request->reason;
                $leave->description = $request->description;
                $leave->half_day = $request->half_day;
                $leave->status = 'New';
                $leave->save();

                /*NOTIFICATION CREATE [START]*/
                $sender = User::find($leave->employee_id);
                $receiver = User::find($leave->approved_by);
                $leaveData = [
                    'name' => 'leave' ,
                    'subject' => 'Leave Notification' ,
                    'body' => 'You received a leave.',
                    'thanks' => 'Thank you',
                    'leaveUrl' => url('admin/leave'),
                    'leave_id' => $leave->id,
                    'employee_id' => $sender->id,
                    'employee_name' => $sender->name,
                    'text' => 'added new'
                ];

                Notification::send($receiver, new leavesNotification($leaveData));
                /*NOTIFICATION CREATE [END]*/

                $email = 'parth.onfuro@gmail.com';
          
                Mail::to($email)->send(new LeavesNotificationMail($leaveData));
                

                //Session::flash('success', 'Your leave has been confirmed successfully.');
                //return redirect()->back();

                return response()->json([
                    'success' => 'leave was created successfully.' // for status 200
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
     * @param  \App\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function show(Leave $leave)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function edit(Leave $leave)
    {
        $leave_branch_id = $leave->branch_id;
        if(!auth()->user()->hasRole('superadmin')){
            $branch_id = auth()->user()->getBranchIdsAttribute();
            $branches = Branch::whereIn('id',$branch_id)->get();
            $approvers = User::whereHas("roles", function($q){ $q->where("name", "superadmin")->orWhere("name", "admin"); })->get();
            $users = User::select('id', 'name')->whereHas('branches', function($q) use ($leave_branch_id) { $q->where('branch_id', $leave_branch_id); })->get();
        }else{
            $branches = Branch::all();
            $approvers = User::whereHas("roles", function($q){ $q->where("name", "superadmin")->orWhere("name", "admin"); })->get();
            $users = User::select('id', 'name')->whereHas('branches', function($q) use ($leave_branch_id) { $q->where('branch_id', $leave_branch_id); })->get();
        }

        $leave_types = ['Annual', 'Sick', 'Hospitalisation', 'Maternity', 'Paternity', 'LOP'];
        $half_day = ['Full Day', 'Half Day'];
        $status = ['New', 'Approved', 'Declined'];
        return view('admin.leave.edit', compact("leave", "branches", "approvers", "users", "leave_types", "half_day", "status"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\LeaveUpdateRequest  $request
     * @param  \App\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function update(LeaveUpdateRequest $request, Leave $leave)
    {
        try {

            if (empty($leave)) {
                //Session::flash('failed', 'branch Update Denied');
                //return redirect()->back();
                return response()->json([
                    'error' => 'leave update denied.' // for status 200
                ]);   
            }

            $old_status = $leave->status;
            $leave_date = explode(' - ', $request->leave_date);
            $leave->employee_id = $request->employee_id;
            $leave->approved_by = $request->approved_by;
            $leave->branch_id = $request->branch_id;
            $leave->start_at = Carbon::parse($leave_date[0]);
            $leave->end_at = Carbon::parse($leave_date[1]);
            $leave->leave_days = $request->days;
            $leave->leave_type = $request->leave_type;
            $leave->reason = $request->reason;
            $leave->description = $request->description;
            $leave->half_day = $request->half_day;
            $leave->status = $request->status;
            $leave->save();

            /*NOTIFICATION CREATE [START]*/
            if($old_status != $leave->status){
                $sender = User::find($leave->approved_by);
                $receiver = User::find($leave->employee_id);
                if($leave->status=='New'){$leave->status='on hold';}
                $leaveData = [
                    'name' =>  Str::lower($leave->status),
                    'body' => 'You received a leave.',
                    'thanks' => 'Thank you',
                    'leaveUrl' => url('admin/leave-employee'),
                    'leave_id' => $leave->id,
                    'employee_id' => $sender->id,
                    'employee_name' => $sender->name,
                    'text' => 'your leave has been'
                ];

                Notification::send($receiver, new leavesNotification($leaveData));
            }
            /*NOTIFICATION CREATE [END]*/

            //Session::flash('success', 'A branch updated successfully.');
            //return redirect('admin/branch');

            return response()->json([
                'success' => 'leave update successfully.' // for status 200
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
     * @param  \App\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function destroy(Leave $leave)
    {
        // delete branch
        $leave->delete();

        //return redirect('admin/branch')->with('success', 'branch deleted successfully.');
        return response()->json([
            'success' => 'leave deleted successfully.' // for status 200
        ]);
    }
}
