<?php

namespace App\Http\Controllers\Admin;

use App\Rota;
use App\Rota_template;
use App\User;
use App\Branch;
use App\Holiday;
use App\Leave;

use App\Http\Controllers\Controller;
use App\Http\Requests\RotaStoreByRotaTemplateRequest;
use App\Traits\UploadTrait;
use App\Notifications\rotasNotification;
use App\Mail\RotasNotificationMail;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use JeroenDesloovere\Distance\Distance;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Notification;
use Mail;

class RotaController extends Controller
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

        return view('admin.rota.index', compact("users","branches"));

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function table(Request $request)
    {
        $startDate = $request->startDate ? $request->startDate :Carbon\Carbon::today();
        $endDate = $request->endDate ? $request->endDate :Carbon\Carbon::today()->addDay(7);

        if(!empty($request->employee)){
            $users = User::whereIn('id',$request->employee)->get();
        }else{
            if(!auth()->user()->hasRole('superadmin')){
                $branch_id = auth()->user()->getBranchIdsAttribute();
                $users = User::whereHas('branches', function($q) use ($branch_id) { 
                                        $q->whereIn('branch_id', $branch_id); })
                                ->get();
            }else{
                $users = User::all();
            } 
        }
        
        return view('admin.rota.table', compact("users","startDate","endDate"));

    }

    /**
     * Show the form for creating a new resource.[When Click '+'(create new) on rota.index then this funaction call]
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $rota_templates = Rota_template::all();
        $types = ['Day', 'Week', 'Month'];
        $day_list = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];
        $over_time = ["Yes","No"];
        $remotely_work = ["Yes","No"];

        if(!auth()->user()->hasRole('superadmin')){
            $branch_id = auth()->user()->getBranchIdsAttribute();
            $branches = Branch::whereIn('id',$branch_id)->get();
        }else{
            $branches = Branch::all();
        }

        return view('admin.rota.create', compact("rota_templates", "types", "day_list", "over_time", "remotely_work", "branches"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create_single_rota($user_id,$date)
    {
        $over_time = ["Yes","No"];
        $remotely_work = ["Yes","No"];
        $user = User::findOrFail($user_id);
        $branches = Branch::with('company')->whereHas('users', function($q) use ($user_id) { $q->where('user_id', $user_id); })->get();

        return view('admin.rota.create_single_rota', compact("user", "date", "over_time", "remotely_work", "branches"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         try {

            $employee_id = $request->employee_id;
            $branch_id = $request->branch_id;
            $start_date = $request->start_date;
            //$holiday = Holiday::where('holiday_date',$start_date)->whereHas('branches', function($q) use ($branch_id) { $q->where('branch_id','=',$branch_id); })->get();

            $leave = Leave::where('start_at', '>=', $start_date)->where('end_at', '<=', $start_date)->where('branch_id','=',$branch_id)->where('employee_id','=',$employee_id)->where('status','=','Approved')->get();

            // Check this date & user rota already created or not
            $checkRotaExist = Rota::where('user_id',$employee_id)->where('start_date',$start_date)->get();
            
            if(/*$holiday->count()==0 &&*/ $leave->count()==0 && $checkRotaExist->count()==0){
                $start_time = $request->start_at;
                $end_time = $request->end_at;
                if($start_time>$end_time){
                   $end_date = Carbon::parse($start_date)->addDays(1); 
                }else{
                   $end_date = $start_date;  
                }


                $rota = new Rota();
                $rota->start_date = $start_date;
                $rota->start_time = $start_time;
                $rota->end_date = $end_date;
                $rota->end_time = $end_time;
                $rota->max_start_time = $request->max_start_at;
                $rota->break_time = $request->break_time;
                $rota->break_start_time = $request->break_start_at;
                $rota->over_time = $request->over_time;
                $rota->remotely_work = $request->remotely_work;
                $rota->user_id = $employee_id;
                $rota->branch_id = $branch_id;
                $rota->rota_template_id = $request->rota_template;
                $rota->notes = $request->notes;
                $rota->created_by = auth()->user()->id;
                $rota->updated_by = auth()->user()->id;
                $rota->save();  

                /*NOTIFICATION CREATE [START]*/
                $sender = User::find(auth()->user()->id);
                $receiver = User::find($employee_id);
                $rotaData = [
                    'name' => 'rota' ,
                    'subject' => 'Rota Notification' ,
                    'body' => 'You received a rota.',
                    'thanks' => 'Thank you',
                    'rotaUrl' => url('admin/rota/employee'),
                    'id' => $rota->id,
                    'employee_id' => $sender->id,
                    'employee_name' => $sender->name,
                    'receiver_name' => $receiver->name,
                    'text' => 'You received a rota.',
                ];

                //Notification::send($receiver, new rotasNotification($rotaData));
                //Mail::to($receiver->email)->send(new RotasNotificationMail($rotaData));
                /*NOTIFICATION CREATE [END]*/

            }

            //Session::flash('success', 'A rota_template updated successfully.');
            //return redirect('admin/rota_template');

            return response()->json([
                'success' => 'rota create successfully.' // for status 200
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
     * @param  \App\Rota  $rota
     * @return \Illuminate\Http\Response
     */
    public function show(Rota $rota)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Rota  $rota
     * @return \Illuminate\Http\Response
     */
    public function edit(Rota $rota)
    {
        $over_time = ["Yes","No"];
        $remotely_work = ["Yes","No"];
        $user_id = $rota->user_id;
        $user = User::findOrFail($user_id);
        $branches = Branch::with('company')->whereHas('users', function($q) use ($user_id) { $q->where('user_id', $user_id); })->get();

        return view('admin.rota.edit', compact("rota", "user", "over_time", "remotely_work", "branches"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Rota  $rota
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Rota $rota)
    {
        try {

            $employee_id = $request->employee_id;
            $branch_id = $request->branch_id;
            $start_date = $request->start_date;
            $start_time = $request->start_at;
            $end_time = $request->end_at;
            if($start_time>$end_time){
               $end_date = Carbon::parse($start_date)->addDays(1); 
            }else{
               $end_date = $start_date;  
            }

            $rota->start_date = $start_date;
            $rota->start_time = $start_time;
            $rota->end_date = $end_date;
            $rota->end_time = $end_time;
            $rota->max_start_time = $request->max_start_at;
            $rota->break_time = $request->break_time;
            $rota->break_start_time = $request->break_start_at;
            $rota->over_time = $request->over_time;
            $rota->remotely_work = $request->remotely_work;
            $rota->user_id = $employee_id;
            $rota->branch_id = $branch_id;
            $rota->rota_template_id = $request->rota_template;
            $rota->notes = $request->notes;
            $rota->updated_by = auth()->user()->id;
            $rota->save(); 

            /*NOTIFICATION CREATE [START]*/
            $sender = User::find(auth()->user()->id);
            $receiver = User::find($employee_id);
            $rotaData = [
                'name' => 'rota' ,
                'subject' => 'Rota Notification' ,
                'body' => 'Your rota updated.'. $this->mailBody($employee_id),
                'thanks' => 'Thank you',
                'rotaUrl' => url('admin/rota/employee'),
                'id' => $rota->id,
                'employee_id' => $sender->id,
                'employee_name' => $sender->name,
                'receiver_name' => $receiver->name,
                'text' => 'Your rota updated.',
            ];

            //Notification::send($receiver, new rotasNotification($rotaData));
            //Mail::to($receiver->email)->send(new RotasNotificationMail($rotaData));
            /*NOTIFICATION CREATE [END]*/ 

            //Session::flash('success', 'A rota updated successfully.');
            //return redirect('admin/rota');

            return response()->json([
                'success' => 'rota update successfully.' // for status 200
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
     * @param  \App\Rota  $rota
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rota $rota)
    {
        // delete rota
        $rota->delete();

        //return redirect('admin/rota')->with('delete', 'rota deleted successfully.');
        return response()->json([
            'delete' => 'rota deleted successfully.' // for status 200
        ]);
    }

    /**
     * Show the form for create_rota the specified resource.
     *
     * @param  \App\Rota_template  $rota_template
     * @return \Illuminate\Http\Response
     */
    public function create_bulk(Request $request)
    {
        $rota_template = Rota_template::find($request->rota_template);
        $types = ['Day', 'Week', 'Month'];
        $day_list = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];
        $over_time = ["Yes","No"];
        $remotely_work = ["Yes","No"];

        if(!auth()->user()->hasRole('superadmin')){
            $branch_id = auth()->user()->getBranchIdsAttribute();
            $branches = Branch::whereIn('id',$branch_id)->get();
        }else{
            $branches = Branch::all();
        }

        return view('admin.rota.create_bulk', compact("rota_template", "types", "day_list", "over_time", "remotely_work", "branches"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Rota_template  $rota_template
     * @return \Illuminate\Http\Response
     */
    public function store_bulk(RotaStoreByRotaTemplateRequest $request)
    {
        try {

            //loop by employee
            foreach ($request->employee_id as $employee_id) {

                //loop by two date
                for($d = Carbon::parse($request->start_date); $d->lte(Carbon::parse($request->end_date)); $d->addDay()) {

                    $branch_id = $request->branch_id;
                    $holiday = Holiday::where('holiday_date',$d)->whereHas('branches', function($q) use ($branch_id) { $q->where('branch_id','=',$branch_id); })->get();

                    $leave = Leave::where('start_at', '>=', $d)->where('end_at', '<=', $d)->where('branch_id','=',$branch_id)->where('employee_id','=',$employee_id)->where('status','=','Approved')->get();

                    // Check this date & user rota already created or not
                    $checkRotaExist = Rota::where('user_id',$employee_id)->where('start_date',$d)->get();
                    
                    if(in_array($d->format('l'), $request->day_list) && $holiday->count()==0 && $leave->count()==0 && $checkRotaExist->count()==0){
                        $start_date = $d;
                        $start_time = $request->start_at;
                        $end_time = $request->end_at;
                        if($start_time>$end_time){
                           $end_date = Carbon::parse($start_date)->addDays(1); 
                        }else{
                           $end_date = $start_date;  
                        }


                        $rota = new Rota();
                        $rota->start_date = $start_date;
                        $rota->start_time = $start_time;
                        $rota->end_date = $end_date;
                        $rota->end_time = $end_time;
                        $rota->max_start_time = $request->max_start_at;
                        $rota->break_time = $request->break_time;
                        $rota->break_start_time = $request->break_start_at;
                        $rota->over_time = $request->over_time;
                        $rota->remotely_work = $request->remotely_work;
                        $rota->user_id = $employee_id;
                        $rota->branch_id = $branch_id;
                        $rota->rota_template_id = $request->rota_template;
                        $rota->notes = $request->notes;
                        $rota->created_by = auth()->user()->id;
                        $rota->updated_by = auth()->user()->id;
                        $rota->save(); 
                    }
                } 

                /*NOTIFICATION CREATE [START]*/
                $sender = User::find(auth()->user()->id);
                $receiver = User::find($employee_id);
                $rotaData = [
                    'name' => 'rota' ,
                    'subject' => 'Rota Notification' ,
                    'body' => 'You received a rota.'. $this->mailBody($employee_id),
                    'thanks' => 'Thank you',
                    'rotaUrl' => url('admin/rota/employee'),
                    'id' => 1, //Not reqiered 
                    'employee_id' => $sender->id,
                    'employee_name' => $sender->name,
                    'receiver_name' => $receiver->name,
                    'text' => 'added new rota'
                ];

                //Notification::send($receiver, new rotasNotification($rotaData));
                //Mail::to($receiver->email)->send(new RotasNotificationMail($rotaData));
                /*NOTIFICATION CREATE [END]*/ 

            }

            //Session::flash('success', 'A rota_template updated successfully.');
            //return redirect('admin/rota_template');

            return response()->json([
                'success' => 'rota create successfully.' // for status 200
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

    /*Employee Rota [Start]*/

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_employee()
    {
        return view('admin.rota.index_employee');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function table_employee(Request $request)
    {
        $startDate = $request->startDate ? $request->startDate :Carbon::today();
        $endDate = $request->endDate ? $request->endDate :Carbon::today()->addDay(7);
        $user = User::find(auth()->user()->id);
        
        return view('admin.rota.table_employee', compact("user","startDate","endDate"));

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit_employee(Request $request)
    {
        $over_time = ["Yes","No"];
        $remotely_work = ["Yes","No"];
        $user_id = auth()->user()->id;
        $user = User::findOrFail($user_id);
        $branches = Branch::with('company')->whereHas('users', function($q) use ($user_id) { $q->where('user_id', $user_id); })->get();
        $rota = Rota::find($request->rota);
        return view('admin.rota.edit_employee', compact("rota", "user", "over_time", "remotely_work", "branches"));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Rota  $rota
     * @return \Illuminate\Http\Response
     */
    public function update_employee(Request $request)
    {
        try {

            $rota = Rota::find($request->rota);
            $rota->employee_notes = $request->employee_notes;
            $rota->updated_by = auth()->user()->id;
            $rota->save(); 

            /*NOTIFICATION CREATE [START]*/
            $sender = User::find(auth()->user()->id);
            $receiver = User::find($rota->created_by);
            $rotaData = [
                'name' => 'rota' ,
                'subject' => 'Rota Notification' ,
                'body' => 'The rotas notes have been changed from '.$sender->name.', please check the date of '.$rota->start_date.' rota employee notes',
                'thanks' => 'Thank you',
                'rotaUrl' => url('admin/rota'),
                'id' => $rota->id,
                'employee_id' => $sender->id,
                'employee_name' => $sender->name,
                'receiver_name' => $receiver->name,
                'text' => 'The rotas notes have been changed from '.$sender->name.', please check the date of '.$rota->start_date.' rota employee notes',
            ];

            //Notification::send($receiver, new rotasNotification($rotaData));
            //Mail::to($receiver->email)->send(new RotasNotificationMail($rotaData));
            /*NOTIFICATION CREATE [END]*/ 

            //Session::flash('success', 'A rota updated successfully.');
            //return redirect('admin/rota');

            return response()->json([
                'success' => 'rota update successfully.' // for status 200
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

    function mailBody($user_id){
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDay(7);
        $user = User::find($user_id);
        return view('admin.rota.mail_body', compact("user","startDate","endDate"));

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function calendarRota(Request $request)
    {
        if($request->ajax()) {  
            if(isset($request->user_id) && $request->user_id!=null){
                $user_id = $request->user_id;
            }else{
                $user_id = auth()->user()->id;
            }
            
            $data = Rota::with('branch')->whereDate('start_date', '>=', $request->start)
                ->whereDate('end_date',   '<=', $request->end)->where('user_id',$user_id)
                ->get(['id', 'start_date AS start', 'end_date AS end', 'end_date', 'start_time', 'end_time', 'break_time', 'break_start_time', 'branch_id', 'remotely_work']);

            return response()->json($data);
        }

    }
    
}
