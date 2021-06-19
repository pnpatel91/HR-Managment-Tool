<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Permission;

class RoleController extends Controller
{
    function __construct()
    {
        $this->middleware('can:create role', ['only' => ['create', 'store']]);
        $this->middleware('can:edit role', ['only' => ['edit', 'update']]);
        $this->middleware('can:delete role', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();
        return view('admin.role.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::pluck('name', 'id');
        return view('admin.role.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        $role = Role::firstOrCreate($request->only('name'));
        $role->syncPermissions($request->input('permission'));
        return response()->json([
                'success' => 'Role created successfully.' // for status 200
            ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all();
        return view('admin.role.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, Role $role)
    {
        $role->update($request->only('name'));
        $role->syncPermissions($request->input('permission'));

        return response()->json([
                    'success' => 'Role updated successfully.' // for status 200
                ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        if($role->name === 'admin') {
            
            return response()->json([
                                        'errors' => 'You cannot delete admin role.' // for status 200
                                    ]);
        }
        if($role->delete()) {
            
            return response()->json([
                                        'success' => 'A role has been deleted successfully.' // for status 200
                                    ]);
        }
        
        return response()->json([
                                    'errors' => 'A role cannot delete.' // for status 200
                                ]);
    }
}