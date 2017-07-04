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
    //return view('search');
});
Route::get('/', 'SearchController@search')->name('search');
Route::get('/match', 'SearchController@match');
Route::get('/spouser', 'SearchController@makeSpouse');
Route::get('/child', 'SearchController@makeChild');
Route::post('/search', 'SearchController@searchByAadhar');

