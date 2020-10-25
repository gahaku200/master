<?php
Route::get('/', function () {
    return view('index');
});
Route::get('/schedule', function () {
    return view('schedule');
});