<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
Route::get('/', 'WelcomeController@loginForm')->name('welcomeLogin');
Route::post('/', 'WelcomeController@login');

Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false,
    'confirm' => false,
]);

Route::get('chat', 'HomeController@index')->name('chat')->middleware('auth');

Route::get('api/private/{user}', 'MessageController@show')->middleware('auth');
Route::post('api/private', 'MessageController@store')->middleware('auth');
Route::get('api/acceptedBy', 'AcceptedClientController@show')->middleware('auth');
Route::post('verify/{user}', 'VerifyClientController@store')->middleware('auth');
Route::post('api/file-upload', 'FileUploadController@store')->middleware('auth');

Route::middleware(['auth', 'check.role'])->group(function () {
    Route::get('admin', 'AdminController@index')->name('admin');
    Route::get('responses', 'GlobalResponseController@index')->name('responses.index');
    Route::get('responses/create', 'GlobalResponseController@create')->name('responses.create');
    Route::post('responses', 'GlobalResponseController@store')->name('responses.store');
    Route::get('responses/{response}/edit', 'GlobalResponseController@edit')->name('responses.edit');
    Route::put('responses/{response}', 'GlobalResponseController@update')->name('responses.update');
    Route::delete('responses/{response}', 'GlobalResponseController@destroy')->name('responses.destroy');

    Route::get('responses/personal', 'PersonalResponseController@index')->name('responses.personal.index');
    Route::get('responses/personal/create', 'PersonalResponseController@create')->name('responses.personal.create');
    Route::post('responses/personal', 'PersonalResponseController@store')->name('responses.personal.store');
    Route::get('responses/personal/{response}/edit', 'PersonalResponseController@edit')->name('responses.personal.edit');
    Route::put('responses/personal/{response}', 'PersonalResponseController@update')->name('responses.personal.update');
    Route::delete('responses/personal/{response}', 'PersonalResponseController@destroy')->name('responses.personal.destroy');

    Route::get('api/responses', 'ResponseController@index');
    Route::get('api/personal-responses', 'ResponsePersonalController@index');

    Route::get('search', 'PinSearchController@index');
    Route::get('api/pins', 'PinController@index');
    Route::post('api/pins', 'PinController@store');
    Route::delete('api/pins/{pin}', 'PinController@destroy');

    Route::get('api/users', 'UserController@index')->name('user');
    Route::get('api/users/{user}', 'UserController@show');

    Route::get('verify/{user}', 'VerifyClientController@index');

    Route::get('api/pools', 'PoolController@index');
    Route::post('api/pool/{id}', 'PoolController@destroy');
    Route::post('api/pool/{id}/add', 'PoolController@store');

    Route::get('api/clients', 'ClientController@index');
    Route::get('api/clients/accepted', 'AcceptedClientController@index');

    Route::get('profile', 'ProfileController@show')->name('profile');
    Route::get('profile/edit', 'ProfileController@edit')->name('profile.edit');
    Route::put('profile/{user}', 'ProfileController@update')->name('profile.update');
});

