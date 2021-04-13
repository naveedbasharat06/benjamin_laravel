<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['prefix' => 'users'], function () {
    Route::post('/login', 'UsersController@login');
    Route::post('/register', 'UsersController@register');
    Route::get('/logout', 'UsersController@logout')->middleware('auth:api');

Route::post('send_invite', 'UsersController@sendInvitetoUser')->name('send_invite');

});	

 // Route::get('/inviteResponce', 'UsersController@apiResponce');

 Route::post('/inviteResponce', 'UsersController@inviteResponce');

 Route::post('/verifyPinCode', 'UsersController@verifyPinCode');

 Route::post('/profileComplete', 'UsersController@profileComplete');

   