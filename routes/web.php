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
    //return view('welcome');
    //abort(404);
    return redirect('/admin');
});

Route::get('/test/print/session', 'TestController@printSession');
Route::get('/test/model/orm', 'TestController@modelORM');
Route::get('/test/model/lang', 'TestController@mylang');
Route::get('/test/model/test_table', 'TestController@test_table');