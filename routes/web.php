<?php

Route::get('/', 'HomeController@index');

/*
 *  Memberships
 */
Route::get('/plans', 'PlanController@index')->name('plans.index');

/*
 *Customers
 */

//Memberships
Route::get('/customer/memberships', 'CustomerMembershipController@index')->name('customer.memberships.index');

//Payments
Route::get('/customer/payments', 'CustomerPaymentController@index')->name('customer.payments.index');

//Attendances
Route::get('/customer/attendances', 'CustomerAttendanceController@index')->name('customer.attendances.index');

//Admin Profile
Route::get('profile', 'Admin\UserProfileController@edit')->name('admin.profile.edit');
Route::patch('profile', 'Admin\UserProfileController@update')->name('admin.profile.update');


/*
 * Removed routes belonging to admin lte package.
 */
Route::get('/home', function () {
    abort(404);
});
Route::get('register', function () {
    abort(404);
});
Route::post('register', function () {
    abort(404);
});

/*
 * Activate account
 */
Route::get('/activation/account/{token}', 'Auth\ActivationController@activate')->name('auth.activate.account');
Route::get('/activate/resend/{email}', 'Auth\ActivationController@resend')->name('auth.activate.resend');
