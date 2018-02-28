<?php



Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');

Route::get('/', 'ThreadsController@index')->name('home');
Route::get('threads', 'ThreadsController@index')->name('threads');
Route::get('threads/create', 'ThreadsController@create')->name('createThread');
Route::get('threads/{channelId}/{thread}', 'ThreadsController@show')->name('threadDetail');
Route::patch('threads/{channelId}/{thread}', 'ThreadsController@update');
Route::delete('threads/{channelId}/{thread}', 'ThreadsController@destroy');
Route::post('threads', 'ThreadsController@store')->name('storeThread')->middleware('must-be-confirmed');
Route::get('threads/{channel}', 'ThreadsController@index')->name('threads4Channel');
Route::get('threads/{channelId}/{thread}/replies', 'RepliesController@index');
Route::post('threads/{channelId}/{thread}/replies', 'RepliesController@store')->name('saveReply');
Route::patch('replies/{reply}', 'RepliesController@update');
Route::delete('replies/{reply}', 'RepliesController@destroy')->name('replies.destroy');
Route::post('replies/{reply}/favorites', 'FavoritesController@store')->name('favorite');
Route::delete('replies/{reply}/favorites', 'FavoritesController@destroy');

Route::post('/replies/{reply}/best', 'BestRepliesController@store')->name('best-reply.store');

Route::post('/lock-threads/{thread}', 'LockThreadsController@store')->name('lock-threads.store')->middleware('admin');
Route::delete('/lock-threads/{thread}', 'LockThreadsController@destroy')->name('lock-threads.destroy')->middleware('admin');

Route::post('threads/{channelId}/{thread}/subscriptions', 'ThreadSubscriptionsController@store');
Route::delete('threads/{channelId}/{thread}/subscriptions', 'ThreadSubscriptionsController@destroy');

Route::get('profiles/{user}', 'ProfilesController@show')->name('profileUser');
Route::get('profiles/{user}/notifications', 'UserNotificationsController@index');
Route::delete('profiles/{user}/notification/{notification}', 'UserNotificationsController@destroy');

Route::get('/register/confirm', 'Auth\RegisterConfirmationController@index')->name('register.confirm');

Route::get('/api/users', 'Api\UsersController@index');
Route::post('/api/users/{user}/avatar', 'Api\UserAvatarsController@store')->middleware('auth')->name('avatar');

