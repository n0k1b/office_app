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

Route::get('test','UserController@test');

Route::post('edit','UserController@edit');
Route::post('delete','UserController@delete');
Route::post('send_token','UserController@send_token');

Route::post('login','UserController@login');
Route::post('get_debit_list','UserController@get_debit_list');
Route::post('get_credit_list','UserController@get_credit_list');
Route::post('get_remaining_fund','UserController@get_remaining_fund');
Route::post('send_debit_data','UserController@send_debit_data');

Route::post('send_credit_data','UserController@send_credit_data');

Route::post('get_history','UserController@get_history');
Route::post('get_history_yearly','UserController@get_history_yearly');

Route::post('get_profile','UserController@get_profile');