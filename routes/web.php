<?php

use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');

Route::group([
    'prefix' => 'admin',
    'as' => 'admin.',
    'namespace' => 'Admin',
    'middleware' => ['auth']
], function () {
    Route::get('/', 'DashboardController@index');
    
    // Profile Routes
    Route::view('profile', 'admin.profile.index')->name('profile.index');
    Route::view('profile/edit', 'admin.profile.edit')->name('profile.edit');
    Route::put('profile/edit', 'ProfileController@update')->name('profile.update');
    Route::put('profile/updateProfileImage', 'ProfileController@updateProfileImage')->name('profile.updateProfileImage');
    Route::view('profile/password', 'admin.profile.edit_password')->name('profile.edit.password');
    Route::post('profile/password', 'ProfileController@updatePassword')->name('profile.update.password');

    // User Routes
    Route::resource('/user', 'UserController');

    // Role Routes
    Route::put('role/{id}/update', 'RoleController@update');
    Route::resource('role', 'RoleController');

    // Company Routes
    Route::resource('company', 'CompanyController');
    Route::get('company/ajax/data', 'CompanyController@datatables'); // For Datatables

    // Branch Routes
    Route::resource('branch', 'BranchController');
    Route::get('branch/ajax/data', 'BranchController@datatables'); // For Datatables

    // Attendance Routes
    Route::resource('attendance', 'AttendanceController');
    Route::post('attendance/store_admin', 'AttendanceController@store_admin')->name('attendance.store_admin'); // For Admin 
    Route::get('attendance/ajax/data', 'AttendanceController@datatables'); // For Datatables
    Route::post( 'attendance/ajax/user', 'AttendanceController@user' )->name('attendance.ajax.user'); // Get user option by branch in ajax
    Route::post( 'attendance/ajax/status', 'AttendanceController@status' )->name('attendance.ajax.status'); // Get status option by branch in ajax

    // Department Routes
    Route::resource('department', 'DepartmentController');
    Route::get('department/ajax/data', 'DepartmentController@datatables'); // For Datatables

    // Department Routes
    Route::resource('holiday', 'HolidayController');

});

