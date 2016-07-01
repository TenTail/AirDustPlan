<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', ['as' => 'index', function () {
    return view('welcome');
}]);

/**
 * PM2.5 introduction
 */
Route::get('introduction', ['as' => 'introduction', function () {
    return view('introduction');
}]);

/**
 * PM2.5 immediate
 */
Route::get('immediate', ['as' => 'immediate', function () {
    return view('immediate');
}]);

/**
 * PM2.5 history-compare
 */
Route::get('history-compare', ['as' => 'history-compare.index', function () {
    return view('history-compare');
}]);

/**
 * excel export
 */
Route::get('excel-export',  ['as' => 'excel-export.index',  'uses' => 'DataExportController@index']);
Route::post('excel-export', ['as' => 'excel-export.export', 'uses' => 'DataExportController@export']);
