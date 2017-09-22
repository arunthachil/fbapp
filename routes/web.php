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

Auth::routes();
Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/redirect', 'SocialAuthFacebookController@redirect');
Route::get('/callback', 'SocialAuthFacebookController@callback');
Route::get('/user', 'HomeController@get_user_table');
Route::get('/friend_requests', 'HomeController@friend_requests');
Route::post('/add_friend', 'HomeController@add_friend');
Route::get('/friend_action', ['uses' =>'HomeController@friend_request_action_mail']);
Route::post('/friend_action', ['uses' =>'HomeController@friend_request_action']);
Route::get('/requests_table', ['uses' =>'HomeController@get_friend_requests_table']);
Route::get('/profile/{id}', ['uses' =>'HomeController@profile']);
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
