<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use App\Image;
use App\Branch;
use App\Department;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

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
                                    $q->where('branch_id', $branch_id); })
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
        return view('admin.user.create', compact('roles', 'branches', 'departments'));
    }

    public function store(Request $request)
    {
        $input = $request->only('name', 'email', 'password');
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
        return view('admin.user.edit', compact('user', 'roles', 'userRole', 'branches', 'userBranches', 'departments', 'userDepartments'));
    }

    public function update(Request $request, User $user)
    {
        $input = $request->only('name', 'email');
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
        $user->delete();
        //return redirect()->route('admin.user.index')->with('success', 'A user was deleted.');
        return response()->json([
            'success' => 'A team member was deleted successfully.' // for status 200
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
}