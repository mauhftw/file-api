<?php

use Illuminate\Http\Request;

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

/*Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('jwt.auth');*/

Route::group(['prefix' => 'v1'], function () {

	
	Route::get('auth/me', 'Api\AuthController@getAuthenticatedUser');
	Route::post('auth', 'Api\AuthController@authenticate');

	Route::resource('files','FileController',['only' => ['index','store','destroy','show']]);
	Route::get('files/{file}', 'FileController@download')->name('files.download');

});





