<?php
Route::get('/', function () {
    return view('index');
});
Route::get('/schedule', function () {
    return view('schedule');
});
Route::get('/register', function () {
  return view('auth.register.index');
});