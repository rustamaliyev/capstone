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



Route::group(['middleware' => ['web','auth']], function () {


Route::get('/', function () {
    return view('home');
});

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/allrecords', 'ImportController@getAllRecords')->name('all');
Route::get('edit/{id}', 'UserController@edit');
Route::post('edit/{id}','UserController@update');    
//import csv    
Route::post('import', 'ImportController@importCSV');    
    
});


Auth::routes();

