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

Route::get('/', 'ImportController@getImport')->name('import')->middleware('auth');
Route::post('/import_parse', 'ImportController@parseImport')->name('import_parse')->middleware('auth');
Route::post('/import_process', 'ImportController@processImport')->name('import_process')->middleware('auth');
Route::get('/contacts', 'ContactController@index')->name('contacts')->middleware('auth');
Route::get('/files', 'CSVDataController@index')->name('csv_data')->middleware('auth');

Auth::routes();

