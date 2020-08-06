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

Route::post('/auth/login', 'UserController@login');

Route::middleware('auth.get')->group(function () {
    Route::get('/auth/logout', 'UserController@logout');

    Route::get('place', 'PlaceController@index');
    Route::get('place/{place}', 'PlaceController@show');
    Route::post('place', 'PlaceController@store')
        ->middleware('admin');
    Route::delete('place/{place}', 'PlaceController@destroy')
        ->middleware('admin')
        ->name('place-delete');
    Route::post('place/{place}', 'PlaceController@update')
        ->middleware('admin')
        ->name('place-update');

    Route::post('schedule', 'ScheduleController@store')
        ->middleware('admin');
    Route::delete('schedule/{schedule}', 'ScheduleController@destroy')
        ->middleware('admin')
        ->name('schedule-delete');

    Route::get('/route/search/{from_place}/{to_place}/{departure_time?}', 'RouteController@search');
    Route::post('/route/selection', 'RouteController@store');
});

