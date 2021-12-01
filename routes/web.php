<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');

//Clear Cache facade value:
Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    return '<h1>Cache facade value cleared</h1>';
});

//Reoptimized class loader:
Route::get('/optimize', function() {
    $exitCode = Artisan::call('optimize');
    return '<h1>Reoptimized class loader</h1>';
});

//Route cache:
Route::get('/route-cache', function() {
    $exitCode = Artisan::call('route:cache');
    return '<h1>Routes cached</h1>';
});

//Clear Route cache:
Route::get('/route-clear', function() {
    $exitCode = Artisan::call('route:clear');
    return '<h1>Route cache cleared</h1>';
});

//Clear View cache:
Route::get('/view-clear', function() {
    $exitCode = Artisan::call('view:clear');
    return '<h1>View cache cleared</h1>';
});

//Clear Config cache:
Route::get('/config-cache', function() {
    $exitCode = Artisan::call('config:cache');
    return '<h1>Clear Config cleared</h1>';
});

Route::group([
    'prefix' => 'admin',
    'as' => 'admin.',
    'namespace' => 'Admin',
    'middleware' => ['auth']
], function () {
    Route::get('/', 'DashboardController@index');
    //Notifications
    Route::view('notifications-dropdown-menu', 'admin.layouts.notifications')->name('notifications-dropdown-menu');
    Route::get('/notificationMarkAsRead/{id}', 'DashboardController@notificationMarkAsRead');
    Route::get('/notificationMarkAllAsRead/{id}', 'DashboardController@notificationMarkAllAsRead');

    // Profile Routes
    Route::view('profile', 'admin.profile.index')->name('profile.index');
    Route::view('profile/edit', 'admin.profile.edit')->name('profile.edit');
    Route::put('profile/edit', 'ProfileController@update')->name('profile.update');
    Route::put('profile/updateProfileImage', 'ProfileController@updateProfileImage')->name('profile.updateProfileImage');
    Route::view('profile/password', 'admin.profile.edit_password')->name('profile.edit.password');
    Route::post('profile/password', 'ProfileController@updatePassword')->name('profile.update.password');

    // User Routes
    Route::get( 'user-treeview', 'UserController@tree' )->name('user-treeview'); // Get user option by 
    Route::resource('/user', 'UserController');
    Route::post( 'user/ajax/users', 'UserController@get_users_by_branch' )->name('user.ajax.users'); // Get user option by branch in ajax


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
    Route::get('attendance/employee', 'AttendanceController@index_employee')->name('attendance.employee');
    Route::get('attendance/ajax/datatables_employee', 'AttendanceController@datatables_employee')->name('attendance.ajax.datatables_employee');
    Route::resource('attendance', 'AttendanceController');
    Route::post('attendance/store_admin', 'AttendanceController@store_admin')->name('attendance.store_admin'); // For Admin 
    Route::get('attendance/ajax/data', 'AttendanceController@datatables'); // For Datatables
    Route::post( 'attendance/ajax/status', 'AttendanceController@status' )->name('attendance.ajax.status'); // Get status option by branch in ajax

    //Report Routes
    Route::get('report/employee_daily_summary', 'ReportController@employee_daily_summary')->name('report.employee_daily_summary');
    Route::get('report/ajax/data', 'ReportController@datatables'); // For Datatables

    

    // Department Routes
    Route::resource('department', 'DepartmentController');
    Route::get('department/ajax/data', 'DepartmentController@datatables'); // For Datatables

    // Department Routes
    Route::resource('holiday', 'HolidayController');

    // Leave - Admin Routes
    Route::resource('leave', 'LeaveAdminController');
    Route::get('leave/ajax/data', 'LeaveAdminController@datatables'); // For Datatables
    Route::get('leave/ajax/change_status', 'LeaveAdminController@change_status')->name('leave.ajax.change_status'); // For change status

    // Leave - Employee Routes
    Route::resource('leave-employee', 'LeaveEmployeeController');
    Route::get('leave-employee/ajax/data', 'LeaveEmployeeController@datatables'); // For Datatables

    // Rota Template Routes
    Route::get('rota_template/ajax/data', 'RotaTemplateController@datatables'); // For Datatables
    Route::get('rota_template/ajax/get_rota_template', 'RotaTemplateController@get_rota_template')->name('rota_template.ajax.get_rota_template');
    Route::resource('rota_template', 'RotaTemplateController');
    
    Route::get('rota_template/replicate/{rota_template}', 'RotaTemplateController@replicate')->name('rota_template.replicate');

    Route::get('rota/create_bulk', 'RotaController@create_bulk')->name('rota.create_bulk');
    Route::put('rota/store_bulk', 'RotaController@store_bulk')->name('rota.store_bulk');
    Route::get('rota/ajax/table', 'RotaController@table')->name('rota.ajax.table');
    Route::get('rota/create_single_rota/{user_id}/{date}', 'RotaController@create_single_rota')->name('rota.create_single_rota');
    Route::put('rota/store_single_rota', 'RotaController@store_single_rota')->name('rota.store_single_rota');
    Route::get('rota/employee', 'RotaController@index_employee')->name('rota.employee');
    Route::get('rota/ajax/table_employee', 'RotaController@table_employee')->name('rota.ajax.table_employee');
    Route::get('rota/edit_employee', 'RotaController@edit_employee')->name('rota.edit_employee');
    Route::put('rota/update_employee', 'RotaController@update_employee')->name('rota.update_employee');
    
    Route::get('rota/ajax/calendarRota', 'RotaController@calendarRota')->name('rota.ajax.calendarRota');
    Route::resource('rota', 'RotaController')->parameters(['rota' => 'rota']);

    // Wiki Category Routes
    Route::resource('wikiCategory', 'WikiCategoriesController')->parameters(['wikiCategory' => 'wikiCategory']);
    Route::get('wikiCategory/ajax/change_status', 'WikiCategoriesController@change_status')->name('wikiCategory.ajax.change_status'); // For change status

    // Wiki Blog Routes
    Route::resource('wikiBlog', 'WikiBlogsController')->parameters(['wikiBlog' => 'wikiBlog']);
    Route::get('wikiBlog/ajax/data', 'WikiBlogsController@datatables'); // For Datatables
    Route::post( 'wikiBlog/ajax/get_blog_by_category', 'WikiBlogsController@get_blog_by_category' )->name('wikiBlog.ajax.get_blog_by_category'); // Get user option by branch in ajax
    Route::get('wikiBlog/ajax/change_status', 'WikiBlogsController@change_status')->name('wikiBlog.ajax.change_status'); // For change status
    
    Route::get('wikiBlogView', 'WikiBlogsViewController@index')->name('wikiBlogView'); // For View
    Route::post( 'wikiBlogView/ajax/get_blog_details', 'WikiBlogsViewController@get_blog_details' )->name('wikiBlogView.ajax.get_blog_details'); 

    Route::post( 'wikiBlogView/ajax/search', 'WikiBlogsViewController@search' )->name('wikiBlogView.ajax.search'); 


});

