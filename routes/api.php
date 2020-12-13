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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'api'],function(){
    Route::get('/getGroup/{group_id}',function($group_id){
        $user = App\User::select('id', 'name', 'now_task', 'now_task_start')
        ->where('group_id',$group_id)->get();
        return $user;
    });
    Route::post('/taskStart/{id}',function($id){
        $user = App\User::where('id',$id)->first();
        $user->now_task = request('task');
        $user->now_task_start = now();
        $user->save();
    });
    Route::post('/banishMember/{id}',function($id){
        $user = App\User::where('id',$id)->first();
        $user->group_id = null;
        $user->save();
    });
});