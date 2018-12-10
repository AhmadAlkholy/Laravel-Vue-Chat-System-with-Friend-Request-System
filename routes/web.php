<?php

Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');

// chat routes
Route::get('/messages', 'ChatController@chat');
Route::get('/chat/{chat_id}', 'ChatController@chat');
Route::post('/chat/{chat_id}', 'ChatController@getMsg');
Route::get('/chat/user/{user_id}', 'ChatController@chatUser');
Route::post('/message/sendMsg', 'MessageController@new');
Route::get('/message/sendMsg', 'MessageController@new');
Route::post('/unsen_messages', 'MessageController@unseen');
Route::post('/msgSeen/{message_id}','MessageController@markSeen');

// user routs
Route::get('/profile/{id}', 'UserController@index');
Route::get('/edit_profile', 'UserController@change');
Route::post('/profile/save_changes', 'UserController@saveChanges');

// friends routes
Route::get('/friends', 'FriendController@index');
Route::get('/find_friends', 'FriendController@find');
Route::post('/add_friend', 'FriendController@friend_request');
Route::post('/remove_friend', 'FriendController@remove');
Route::get('/friend_requests', 'FriendController@requests_list');
Route::post('/except_friend', 'FriendController@except');

Auth::routes();

View::composer(['*'], function($view){
	if (!empty(Auth::user())) {
		$user = Auth::user();
		$view->with('unseenFriendRequests', 
			App\FriendRequest::where('receiver', $user->id)
				->where('seen', false)->get()
		);
	}
});