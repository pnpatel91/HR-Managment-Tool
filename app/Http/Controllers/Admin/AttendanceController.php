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

class AttendanceController extends Controller
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
    public function index()
    {

        if(!auth()->user()->hasRole('superadmin')){
            $branch_id = auth()->user()->getBranchIdsAttribute();
            $branches = Branch::whereIn('id',$branch_id)->get();
            $users = User::whereHas('branches', function($q) use ($attendance) { $q->where('branch_id', $attendance->branch_id); })->get();
        }else{
            $branches = Branch::all();
            $users = User::all();
        }
        return view('admin.attendance.index', compact("users","branches"));
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
                $model = Attendance::with('branch','creator','editor')
                                    ->whereHas('branch', function($q) use ($branch_id) { 
                                            $q->where('branch_id', $branch_id); });
            }else{
               $model = Attendance::with('branch','creator','editor');
            }
            
            return Datatables::eloquent($model)
                    ->addColumn('action', function (Attendance $data) {
                        $html='';
                        if (auth()->user()->can('edit attendance')){
                            $html.= '<a href="'.  route('admin.attendance.edit', ['attendance' => $data->id]) .'" class="btn btn-success btn-sm float-left mr-3"  id="popup-modal-button"><span tooltip="Edit" flow="left"><i class="fas fa-edit"></i></span></a>';
                        }

                        if (auth()->user()->can('delete attendance')){
                            $html.= '<form method="post" class="float-left delete-form" action="'.  route('admin.attendance.destroy', ['attendance' => $data->id ]) .'"><input type="hidden" name="_token" value="'. Session::token() .'"><input type="hidden" name="_method" value="delete"><button type="submit" class="btn btn-danger btn-sm"><span tooltip="Delete" flow="up"><i class="fas fa-trash"></i></span></button></form>';
                        }

                        return $html; 
                    })

                    ->addColumn('activity', function (Attendance $data) {
                        if($data->status=='punch_in'){ $status='<span class="text-success"><i class="fas fa-sign-in-alt"></i></span> In at'; }else{ $status='<span class="text-danger"><i class="fas fa-sign-out-alt"></i></span> Out at'; }
                        return $status .' '. date_format (date_create($data->attendance_at), "g:ia").' On '.date_format (date_create($data->attendance_at), "l jS F Y");
                    })

                    ->addColumn('branch', function (Attendance $data) {
                        return $data->branch->company->name.' - '.$data->branch->name;
                    })

                    ->addColumn('username', function (Attendance $data) {
                        return '<img src="'.$data->creator->getImageUrlAttribute($data->creator->id).'" alt="user_id_'.$data->creator->id.'" class="profile-user-img-small img-circle"> '. $data->creator->name;
                    })
                    
                    ->addColumn('search_username', function (Attendance $data) {
                        return 'user_id_'.$data->creator->id;
                    })
                    ->addColumn('editor', function (Attendance $data) {
                        return '<img src="'.$data->editor->getImageUrlAttribute($data->editor->id).'" alt="Admin" class="profile-user-img-small img-circle"> '. $data->editor->name;
                    })
                    
                    ->rawColumns(['activity', 'username', 'editor', 'action'])

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
        // Where condition on Role and Branch, If role super admin then show all records, other than only user branch records show.
        if(!auth()->user()->hasRole('superadmin')){
            $branch_id = auth()->user()->getBranchIdsAttribute();
            $branches = Branch::whereIn('id',$branch_id)->get();
        }else{
            $branches = Branch::all();
        }

        return view('admin.attendance.create', compact("branches"));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    function status( Request $request )
    {
        $this->validate( $request, [ 'id' => 'required' ] );
        $attendance = Attendance::where('created_by',$request->id)->latest('attendance_at')->take(1)->get();
        $data = array();
        $data['status'] = 'success';
        
        if(isset($attendance[0]) && $attendance[0]->status=='punch_in'){
            $data['status'] = 'punch out';
        }else{
            $data['status'] = 'punch in';
        }   

        echo json_encode($data);    


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AttendanceStoreRequest $request)
    {
        try {

            if($request->latitude==''){
                $request_latitude = $this->get_location()->latitude;
            }else{
                $request_latitude = $request->latitude;
            }

            if($request->longitude==''){
                $request_longitude = $this->get_location()->longitude;
            }else{
                $request_longitude = $request->longitude;
            }

            $user = User::find(auth()->user()->id);
            $branches = $user->branches;
            foreach ($branches as $key => $branch) {
                $branch_latitude = $branch->latitude;
                $branch_longitude = $branch->longitude;

                $distance = Distance::between(
                    $branch_latitude,
                    $branch_longitude,
                    $request_latitude,
                    $request_longitude
                );

                $distance = $distance*1000; // distance convert into kilometers to meters

                if($branch->radius >= $distance){

                    $attendance = new Attendance();
                    $attendance->status = $request->status;
                    $attendance->distance = $distance;
                    $attendance->latitude = $request_latitude;
                    $attendance->longitude = $request_longitude;
                    $attendance->ip_address = $request->ip();
                    $attendance->branch_id = $branch->id;
                    $attendance->created_by = auth()->user()->id;
                    $attendance->updated_by = auth()->user()->id;
                    $attendance->save();

                    Session::flash('success', 'Your attendance has been confirmed successfully.');
                    return redirect()->back();
                    exit;
                }
            }

            Session::flash('failed', 'You are away from your branch, Distance between the two locations = ' . $distance . ' m');
            return redirect()->back()->withErrors('You are away from your branch, Distance between the two locations = ' . $distance . ' m');

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
     * Store a newly created resource in storage by Admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_admin(Request $request)
    {
        try {
            
                $branch = Branch::find($request->branch_id);

                $attendance = new Attendance();
                $attendance->status = str_replace(' ', '_', $request->status);
                $attendance->distance = 0;
                $attendance->latitude = $branch->latitude;
                $attendance->longitude = $branch->longitude;
                $attendance->ip_address = $request->ip();
                $attendance->branch_id = $request->branch_id;
                $attendance->created_by = $request->user_id;
                $attendance->updated_by = auth()->user()->id;
                $attendance->attendance_at = $request->attendance_at;
                $attendance->save();

                //Session::flash('success', 'Your attendance has been confirmed successfully.');
                //return redirect()->back();

                return response()->json([
                    'success' => 'attendance has been confirmed successfully.' // for status 200
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
     * @param  \App\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function edit(Attendance $attendance)
    {
        // Where condition on Role and Branch, If role super admin then show all records, other than only user branch records show.
        if(!auth()->user()->hasRole('superadmin')){
            $branch_id = auth()->user()->getBranchIdsAttribute();
            $branches = Branch::whereIn('id',$branch_id)->get();
            $users = User::whereHas('branches', function($q) use ($attendance) { $q->where('branch_id', $attendance->branch_id); })->get();
        }else{
            $branches = Branch::all();
            $users = User::all();
        }

        return view('admin.attendance.edit', compact('attendance', 'branches', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function update(AttendanceUpdateRequest $request, Attendance $attendance)
    {
        try {
                if (empty($attendance)) {
                    //Session::flash('failed', 'attendance Update Denied');
                    //return redirect()->back();
                    return response()->json([
                        'error' => 'attendance update denied.' // for status 200
                    ]);   
                }
            
                $branch = Branch::find($request->branch_id);

                $attendance->status = str_replace(' ', '_', $request->status);
                $attendance->distance = 0;
                //$attendance->latitude = $branch->latitude;
                //$attendance->longitude = $branch->longitude;
                $attendance->ip_address = $request->ip();
                $attendance->branch_id = $request->branch_id;
                $attendance->created_by = $request->user_id;
                $attendance->updated_by = auth()->user()->id;
                $attendance->attendance_at = $request->attendance_at;
                $attendance->save();

                //Session::flash('success', 'Your attendance has been confirmed successfully.');
                //return redirect()->back();

                return response()->json([
                    'success' => 'attendance has been confirmed successfully.' // for status 200
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
     * @param  \App\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attendance $attendance)
    {
        // delete attendance
        $attendance->delete();

        //return redirect('admin/attendance')->with('success', 'attendance deleted successfully.');
        return response()->json([
            'success' => 'Attendance deleted successfully.' // for status 200
        ]);
    }


    public function get_location()
    {
        $ip = \Request::ip();
        if($ip=='::1'){$ip='';}
        $data = \Location::get($ip);
        return $data;
    }
}
