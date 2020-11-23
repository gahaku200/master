<?php
Route::get('/', function () {
  return view('index');
});
Route::get('/login', function () {
    return view('auth.login.index');
  });
Route::get('/register', function () {
  return view('auth.register.index');
});
Route::get('/schedule', function () {
  return view('index');
});
Route::get('/group', function () {
  return view('index');
});
Route::get('/groups',function(){
	return App\Group::all();
});
Route::get('/inviteMember', function () {
    return view('index');
  });
Route::get('/users',function(){
    return App\User::all();
});
Route::get('/successSendEmail', function () {
    return view('index');
    });
Route::get('/alreadyJoinGroup', function () {
    return view('index');
  });
Route::get('/notInviteData', function () {
return view('index');
});
Route::get('/successInvite', function () {
return view('index');
});

Route::post('/register', 'Auth\RegisterController@register');
Route::post('/login', 'Auth\LoginController@login');
Route::post('/logout', 'Auth\LoginController@logout');
Route::post('/group', 'Web\GroupController@createGroup');
Route::post('invite', 'Web\InviteController@process')->name('process');

Route::get('accept/{token}', 'Web\InviteController@accept')->name('accept');