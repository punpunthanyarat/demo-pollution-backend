<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::post('/cyclone','CycloneController@post_cyclone_result');
Route::get('/cyclone_result/{user_id}','CycloneController@get_cyclone_result');
Route::get('/cyclone_type','CycloneController@get_cyclone_type');
Route::get('/cyclone_comparation/{user_id}','CycloneController@get_comparation');
Route::post('/cyclone_type_post','CycloneController@post_result');
