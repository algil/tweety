<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

auth()->loginUsingId(1);

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/tweets', 'TweetController@index')->name('home');
    Route::post('/tweets', 'TweetController@store')->name('tweets.store');

    Route::post('/tweets/{tweet}/like', 'TweetLikeController@store');
    Route::delete('/tweets/{tweet}/like', 'TweetLikeController@destroy');

    Route::post('/profiles/{user:username}/follow', 'FollowController@store')->name('follow');
    Route::get('/profiles/{user:username}/edit', 'ProfileController@edit')->middleware('can:edit,user');
    Route::patch('/profiles/{user:username}', 'ProfileController@update')->middleware('can:edit,user');

    Route::get('/explore', 'ExploreController')->name('explore');
});

Route::get('/profiles/{user:username}', 'ProfileController@show')->name('profile');
