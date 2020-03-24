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

Route::group(['prefix' => 'accounts'], function (){

    Route::post('/',    ['as' => 'accounts.create', 'uses' => 'AccountController@store']);

    Route::post('/{id}/orders', ['as' => 'accounts.orders', 'uses' => 'AccountController@orders']);
});