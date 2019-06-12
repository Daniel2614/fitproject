<?php

// Users
Route::get('/users', 'UserController@index')->name('admin.users.index');
Route::get('/users/create', 'UserController@create')->name('admin.users.create');
Route::get('/users/{userId}/edit', 'UserController@edit')->name('admin.users.edit');
Route::post('/users', 'UserController@store')->name('admin.users.store');
Route::patch('/users/{userId}', 'UserController@update')->name('admin.users.update');
Route::delete('/users/{userId}', 'UserController@destroy')->name('admin.users.destroy');

//Levels
Route::resource('levels', 'LevelController')->names([
    'create'   => 'admin.levels.create',
    'show'   => 'admin.levels.show',
    'edit'   => 'admin.levels.edit',
    'update'   => 'admin.levels.update',
    'store'   => 'admin.levels.store',
    'index'   => 'admin.levels.index',
    'destroy' => 'admin.levels.destroy',
]);

//Routines
Route::get('/routines', 'RoutineController@index')->name('admin.routines.index');
Route::get('/routines/create', 'RoutineController@create')->name('admin.routines.create');
Route::get('/routines/{routineId}/details', 'RoutineController@show')->name('admin.routines.show');
Route::get('/routines/{routineId}/edit', 'RoutineController@edit')->name('admin.routines.edit');
Route::post('/routines', 'RoutineController@store')->name('admin.routines.store');
Route::patch('/routines/{routineId}', 'RoutineController@update')->name('admin.routines.update');
Route::delete('/routines/{routineId}', 'RoutineController@destroy')->name('admin.routines.destroy');
Route::get('/download/{routineId}', 'RoutineController@downloadFile')->name('admin.routines.download');

//Plans
Route::get('/plans', 'PlanController@index')->name('admin.plans.index');
Route::get('/plans/create', 'PlanController@create')->name('admin.plans.create');
Route::get('/plans/{planId}/edit', 'PlanController@edit')->name('admin.plans.edit');
Route::post('/plans', 'PlanController@store')->name('admin.plans.store');
Route::patch('/plans/{planId}', 'PlanController@update')->name('admin.plans.update');
Route::delete('/plans/{planId}', 'PlanController@destroy')->name('admin.plans.destroy');

//Payments
Route::get('/payments', 'PaymentController@index')->name('admin.payments.index');

//Memberships
Route::get('/memberships', 'MembershipController@index')->name('admin.memberships.index');
Route::get('/memberships/{planId}/create', 'MembershipController@create')->name('admin.memberships.create');
Route::get('/memberships/{id}/details', 'MembershipController@show')->name('admin.memberships.show');
Route::get('/memberships/{id}/edit', 'MembershipController@edit')->name('admin.memberships.edit');
Route::post('/memberships/{planId}/', 'MembershipController@store')->name('admin.memberships.store');
Route::patch('/memberships/{membershipId}/', 'MembershipController@update')->name('admin.memberships.update');
Route::delete('/memberships/{membershipId}/', 'MembershipController@destroy')->name('admin.memberships.destroy');

//Membership Exports
Route::get('/memberships/exports/excel', 'MembershipExportController@excel')->name('admin.memberships-export.excel');

//Customers
Route::get('/customers', 'CustomerController@index')->name('admin.customers.index');
Route::get('/customers/create', 'CustomerController@create')->name('admin.customers.create');
Route::get('/customers/{id}/edit', 'CustomerController@edit')->name('admin.customers.edit');
Route::get('/customers/{customerId}/details', 'CustomerController@show')->name('admin.customers.show');
Route::post('/customers', 'CustomerController@store')->name('admin.customers.store');
Route::patch('/customers/{customerId}', 'CustomerController@update')->name('admin.customers.update');
Route::delete('/customers/{customerId}', 'CustomerController@destroy')->name('admin.customers.destroy');

//Attendances
Route::get('/attendances/', 'AttendanceController@index')->name('admin.attendances.index');
Route::get('/attendances/create', 'AttendanceController@create')->name('admin.attendances.create');
Route::post('/attendances', 'AttendanceController@store')->name('admin.attendances.store');
Route::delete('/attendances/{attendanceId}', 'AttendanceController@destroy')->name('admin.attendances.destroy');

//Membership Exports
Route::get('/customers/exports/excel', 'CustomerExportController@excel')->name('admin.customers-export.excel');

//Reports
Route::get('/reports', 'ReportController@index')->name('admin.reports.index');
