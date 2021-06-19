<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use App\Image;
use App\Branch;
use App\Company;
use App\Department;
use App\Attendance;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class DashboardController extends Controller
{
    
    public function index()
    {

        if(!auth()->user()->hasRole('superadmin')){
            $branch_id = auth()->user()->getBranchIdsAttribute();
            $users_count = User::whereHas('branches', function($q) use ($branch_id) { $q->where('branch_id', $branch_id); })->count();
            $companies_count = Company::whereHas('branch', function($q) use ($branch_id) { $q->where('id', $branch_id); })->count();

            $branches_count = Branch::whereIn('id',$branch_id)->count();

            $today_attendances = Attendance::with('branch','creator','editor')
                                            ->whereHas('branch', function($q) use ($branch_id) { 
                                                $q->where('branch_id', $branch_id); 
                                            })->whereDate('attendance_at', Carbon::today())->get();

        }else{
           $users_count = User::all()->count(); 
           $companies_count = Company::all()->count();
           $branches_count = Branch::all()->count();
           $today_attendances = Attendance::with('branch','creator','editor')->whereDate('attendance_at', Carbon::today())->get();
        }


        $departments_count = Department::all()->count();

        return view('admin.layouts.dashboard', compact("users_count","companies_count","branches_count","departments_count","today_attendances"));
    }

}