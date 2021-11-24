<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use App\Image;
use App\Branch;
use App\Department;
use App\Attendance;
use App\Leave;
use App\Rota;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Traits\UploadTrait;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use JeroenDesloovere\Distance\Distance;
use Carbon\Carbon;

class UserController extends Controller
{
    function __construct()
    {
        $this->middleware('can:create user', ['only' => ['create', 'store']]);
        $this->middleware('can:edit user', ['only' => ['edit', 'update']]);
        $this->middleware('can:delete user', ['only' => ['destroy']]);
    }
    
    public function index()
    {
        $search = request('search', null);
        /*$users = User::when($search, function($user) use($search) {
            return $user->where("name", 'like', '%' . $search . '%')
            ->orWhere('id', $search);
        })->orderBy('id', 'ASC')->get();
        $users->load('roles');*/

        if(!auth()->user()->hasRole('superadmin')){
            $branch_id = auth()->user()->getBranchIdsAttribute();
            $users = User::with(['branches' => function($query) use ($branch_id) {
                                    $query->whereIn('branch_id', $branch_id);
                                }])
                            ->whereHas('branches', function($q) use ($branch_id) { 
                                    $q->whereIn('branch_id', $branch_id); })
                            ->orderBy('id', 'ASC')
                            ->get();
        }else{
           $users = User::orderBy('id', 'ASC')->get(); 
        }

        
        return view('admin.user.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::pluck('name', 'id');
        
        // Where condition on Role and Branch, If role super admin then show all records, other than only user branch records show.
        if(!auth()->user()->hasRole('superadmin')){
            $branch_id = auth()->user()->getBranchIdsAttribute();
            $branches = Branch::whereIn('id',$branch_id)->get();
        }else{
            $branches = Branch::all();
        }

        $departments = Department::all();

        $parents = User::all();
        return view('admin.user.create', compact('roles', 'branches', 'departments', 'parents'));
    }

    public function store(UserStoreRequest $request)
    {
        $input = $request->only('name', 'email', 'password', 'parent_id', 'position', 'remote_employee');
        $input['password'] = bcrypt($request->password);
        $user = User::create($input);
        $user->assignRole($request->role);
        $user->branches()->attach($request->branch_id);
        $user->departments()->attach($request->department_id);
        //return redirect()->route('admin.user.index')->with('success', 'A user was created.');
        return response()->json([
            'success' => 'A team member was created successfully.' // for status 200
        ]);
    }

    public function show()
    {
        return redirect()->route('admin.user.index');
    }

    public function edit(User $user)
    {
        $roles = Role::pluck('name', 'id');
        $userRole = $user->getRoleNames()->first();
        // Where condition on Role and Branch, If role super admin then show all records, other than only user branch records show.
        if(!auth()->user()->hasRole('superadmin')){
            $branch_id = auth()->user()->getBranchIdsAttribute();
            $branches = Branch::whereIn('id',$branch_id)->get();
        }else{
            $branches = Branch::all();
        }
        $departments = Department::all();
        $userBranches = $user->branches->pluck('id')->toArray();
        $userDepartments = $user->departments->pluck('id')->toArray();

        $parents = User::where('id', '!=' , $user->id)->get();
        return view('admin.user.edit', compact('user', 'roles', 'userRole', 'branches', 'userBranches', 'departments', 'userDepartments', 'parents'));
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        $input = $request->only('name', 'email', 'parent_id', 'position', 'remote_employee');
        if($request->filled('password')) {
            $input['password'] = bcrypt($request->password);
        }
        $user->update($input);
        $user->syncRoles($request->role);
        $user->branches()->sync($request->branch_id);
        $user->departments()->sync($request->department_id);
        //return redirect()->route('admin.user.index')->with('success', 'A user was updated.');
        return response()->json([
            'success' => 'A team member was updated successfully.' // for status 200
        ]);
    }

    public function destroy(User $user)
    {
        if(auth()->id() === $user->id) {
            //return back()->withErrors('You cannot delete current logged in user.');
            return response()->json([
                'errors' => 'You cannot delete current logged in user.' // for status 200
            ]);
        }
        $user->branches()->detach();
        $user->departments()->detach();

        // delete related attendances 
        $attendances = Attendance::where('created_by',$user->id)->orWhere('updated_by',$user->id)->get();
        foreach ($attendances as $attendance) {
            if(isset($attendance->punch_in->id)){
               $attendance->find($attendance->punch_in->id)->delete(); 
            }
            if(isset($attendance->punch_out->id)){
               $attendance->find($attendance->punch_out->id)->delete(); 
            }
            $attendance->delete();
        }
        

        // delete related leaves
        $leave = Leave::where('employee_id',$user->id)->orWhere('approved_by',$user->id)->delete();

        $user->rota()->delete();
        $user->delete();
        //return redirect()->route('admin.user.index')->with('delete', 'A user was deleted.');
        return response()->json([
            'delete' => 'A team member was deleted successfully.' // for status 200
        ]);
    }

    public static function getImageUrlAttribute($id)
    {
        $image = Image::where('imageable_id', $id)->first();
        if(isset($image->filename)) {
            return asset('/storage/app/public/image/profile/'. $image->filename);
        }else{
            return asset('/public/image/default_profile.jpg');
        }
    }

    /**
     * Display the specified branch user DropDown (For Ajax DropDown).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    function get_users_by_branch( Request $request )
    {
          $this->validate( $request, [ 'id' => 'required' ] );
          $users = User::whereHas('branches', function($q) use ($request) { $q->where('branch_id', $request->id); })->get();
          
          //you can handle output in different ways, I just use a custom filled array. you may pluck data and directly output your data.
          $output = [];
          foreach( $users as $user )
          {
             $output[$user->id] = $user->name;
          }
          return $output;
    }

    /**
     * Display user tree.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function tree()
    {
          $users = User::with('allChildren')->where('parent_id',null)->get();
          return view('admin.user.tree', compact('users'));
    }

    
}