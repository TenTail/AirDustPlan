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
// Route::get('history-compare', ['as' => 'history-compare.index', 'uses' => 'HistoryCompareController@index']);
// Route::post('history-compare', ['as' => 'history-compare.compare', 'uses' => 'HistoryCompareController@compare']);
Route::get('history-compare2', ['as' => 'history-compare2.index', 'uses' => 'HistoryCompareController@index2']);
Route::post('history-compare2', ['as' => 'history-compare2.compare', 'uses' => 'HistoryCompareController@compare2']);

/**
 * excel export
 */
Route::get('excel-export',  ['as' => 'excel-export.index',  'uses' => 'DataExportController@index']);
Route::post('excel-export', ['as' => 'excel-export.export', 'uses' => 'DataExportController@export']);
Route::post('excel-table', ['as' => 'excel-export.table', 'uses' => 'DataExportController@table']);

/**
 * upload files
 */
Route::get('file-upload', ['as' => 'file-upload.index', 'uses' => 'UploadFilesController@index']);
Route::get('file-single', ['as' => 'file-upload.single', 'uses' => 'UploadFilesController@single']);
Route::post('file-single', ['as' => 'file-single.upload', 'uses' => 'UploadFilesController@singleUpload']);
Route::get('file-batch', ['as' => 'file-upload.batch', 'uses' => 'UploadFilesController@batch']);
Route::post('file-batch', ['as' => 'file-batch.start', 'uses' => 'UploadFilesController@batchStart']);
Route::post('file-delete', ['as' => 'file-upload.delete', 'uses' => 'UploadFilesController@fileDelete']);

/**
 *  PM2.5 instant info
**/
Route::get('instant_info', ['as' => 'instant_info.index', 'uses' => 'InstantInfomationController@index']);
Route::post('instant_info', ['as' => 'instant_info.show', 'uses' => 'InstantInfomationController@show']);
Route::post('past_6_hours_data', ['as' => 'instant_info.past', 'uses' => 'InstantInfomationController@show_past_6_hours_data']);

/**
 * Research
 */
Route::get('research', ['as' => 'research.index', 'uses' => 'ResearchController@index']);
Route::get('average', ['as' => 'research.average', 'uses' => 'ResearchController@average']);
Route::get('excessive', ['as' => 'research.excessive', 'uses' => 'ResearchController@excessive']);
Route::post('excessive', ['as' => 'research.excessive-post', 'uses' => 'ResearchController@excessiveGetData']);
Route::get('check-data', ['as' => 'research.check', 'uses' => 'ResearchController@check']);

/**
 * Mail Testing
 */
Route::get('mail', function() {
	 $data = ['name' => 'Test'];
	 // dd(Config::get('mail'));
	 Mail::send('mail', $data, function($message) {
	  	
	  	$message->to('40243137@gm.nfu.edu.tw')->subject('This is test email');
	 
	 });
	 // return view('mail');
});

