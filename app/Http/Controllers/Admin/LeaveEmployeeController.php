<?php

namespace App\Http\Controllers\Admin;

use App\Leave;
use App\User;
use App\Branch;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeaveEmployeeStoreRequest;
use App\Http\Requests\LeaveEmployeeUpdateRequest;
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

class LeaveEmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.leave.index-employee');
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

            $model = Leave::where('employee_id', auth()->user()->id)->with('employee','leave_approved_by', 'branch');
            
            return Datatables::eloquent($model)
                    ->addColumn('action', function (Leave $data) {
                        $html='';
                        if (auth()->user()->can('edit leave - admin')){
                            $html.= '<a href="'.  route('admin.leave-employee.edit', ['leave_employee' => $data->id]) .'" class="btn btn-success btn-sm float-left mr-3"  id="popup-modal-button"><span tooltip="Edit" flow="left"><i class="fas fa-edit"></i></span></a>';
                        }

                        if (auth()->user()->can('delete leave - admin')){
                            $html.= '<form method="post" class="float-left delete-form" action="'.  route('admin.leave-employee.destroy', ['leave_employee' => $data->id ]) .'"><input type="hidden" name="_token" value="'. Session::token() .'"><input type="hidden" name="_method" value="delete"><button type="submit" class="btn btn-danger btn-sm"><span tooltip="Delete" flow="up"><i class="fas fa-trash"></i></span></button></form>';
                        }

                        return $html; 
                    })

                    ->addColumn('branch', function (Leave $data) {
                        return $data->branch->company->name.' - '.$data->branch->name;
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
            $approvers = User::whereHas("roles", function($q){ $q->where("name", "superadmin")->orWhere("name", "developer"); })->orWhere("id", auth()->user()->parent_id)->get();
        }else{
            $branches = Branch::all();
            $approvers = User::whereHas("roles", function($q){ $q->where("name", "superadmin")->orWhere("name", "developer"); })->orWhere("id", auth()->user()->parent_id)->get();
        }

        $leave_types = ['Annual', 'Sick', 'Hospitalisation', 'Maternity', 'Paternity', 'LOP'];
        $half_day = ['Full Day', 'Half Day'];
        $status = ['New', 'Approved', 'Declined'];
        return view('admin.leave.create-employee', compact("branches", "approvers", "leave_types", "half_day"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Request\LeaveStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LeaveEmployeeStoreRequest $request)
    {
        try {
            
                $leave_date = explode(' - ', $request->leave_date);

                $leave = new Leave();
                $leave->employee_id = auth()->user()->id;
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
                    'receiver_name' => $receiver->name,
                    'text' => 'added new leave'
                ];

                Notification::send($receiver, new leavesNotification($leaveData));
                Mail::to($receiver->email)->send(new LeavesNotificationMail($leaveData));
                /*NOTIFICATION CREATE [END]*/

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
    public function edit(Leave $leave_employee)
    {
        if(!auth()->user()->hasRole('superadmin')){
            $branch_id = auth()->user()->getBranchIdsAttribute();
            $branches = Branch::whereIn('id',$branch_id)->get();
            $approvers = User::whereHas("roles", function($q){ $q->where("name", "superadmin")->orWhere("name", "developer"); })->orWhere("id", auth()->user()->parent_id)->get();
        }else{
            $branches = Branch::all();
            $approvers = User::whereHas("roles", function($q){ $q->where("name", "superadmin")->orWhere("name", "developer"); })->orWhere("id", auth()->user()->parent_id)->get();
        }

        $leave_types = ['Annual', 'Sick', 'Hospitalisation', 'Maternity', 'Paternity', 'LOP'];
        $half_day = ['Full Day', 'Half Day'];
        $status = ['New', 'Approved', 'Declined'];
        return view('admin.leave.edit-employee', compact("leave_employee", "branches", "approvers", "leave_types", "half_day", "status"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\LeaveUpdateRequest  $request
     * @param  \App\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function update(LeaveEmployeeUpdateRequest $request, Leave $leave_employee)
    {
        try {

            if (empty($leave_employee)) {
                //Session::flash('failed', 'branch Update Denied');
                //return redirect()->back();
                return response()->json([
                    'error' => 'leave update denied.' // for status 200
                ]);   
            }

            $leave_date = explode(' - ', $request->leave_date);
            $leave_employee->employee_id = auth()->user()->id;
            $leave_employee->approved_by = $request->approved_by;
            $leave_employee->branch_id = $request->branch_id;
            $leave_employee->start_at = Carbon::parse($leave_date[0]);
            $leave_employee->end_at = Carbon::parse($leave_date[1]);
            $leave_employee->leave_days = $request->days;
            $leave_employee->leave_type = $request->leave_type;
            $leave_employee->reason = $request->reason;
            $leave_employee->description = $request->description;
            $leave_employee->half_day = $request->half_day;
            $leave_employee->status = 'New';
            $leave_employee->save();

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
     * @param  \App\Leave  $leave_employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Leave $leave_employee)
    {
        // delete branch
        $leave_employee->delete();

        //return redirect('admin/branch')->with('delete', 'branch deleted successfully.');
        return response()->json([
            'delete' => 'leave deleted successfully.' // for status 200
        ]);
    }
}
