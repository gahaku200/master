<?php

use Illuminate\Http\Request;
use App\Attendance;
use App\Task;
use App\KindOfTask;
use App\User;
use Illuminate\Support\Facades\Hash;

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
        $user = App\User::where('group_id',$group_id)
        ->select('id', 'name', 'now_task', 'now_task_start')->get();
        return $user;
    });
    Route::get('/attendance/{id}', 'Api\AttendanceController@confirm');
    Route::get('/task/{id}', 'Api\TaskController@confirm');
    Route::get('/getKindOfTasks/{group_id}',function($group_id){
        $KindOfTask = App\KindOfTask::where('groupId',$group_id)
        ->orderBy('orderNum', 'asc')->get();
        return $KindOfTask;
    });
    Route::get('/getUserInfo/{id}',function($id){
        $user = App\User::find($id);
        return $user;
    });
    Route::post('/attendanceTheDay/{id}',function($id){
        
        return request('day');
    });
    Route::get('/from_day/{id}/{time}', 'Api\AttendanceController@from_day');
    Route::get('/to_day/{id}/{time}', 'Api\AttendanceController@to_day');

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
    Route::post('/attendance/{id}',function($id){
        $attendance = new Attendance();
        $attendance->on_duty = request('on_duty');
        $attendance->time = now();
        $attendance->user_id = $id;
        $attendance->save();

        $query = App\User::find($id)->attendance()
            ->latest('time')
            ->where(function($que){
                $que->where('on_duty', '出勤');
            })->first()->time;

        $getAttendance = App\User::find($id)->attendance()
        ->where('time', '>=', $query)->get();

        return $getAttendance;
    });
    Route::post('/taskEnd/{id}',function($id){
        $user = App\User::where('id', $id)->first();
        $user->now_task = null;
        $user->now_task_start = null;
        $user->save();
    });
    Route::post('/task/{id}',function($id){
        $task = new Task();
        $task->task_name = request('task_name');
        $task->task_time = request('task_time');
        $task->task_user_id = $id;
        $task->save();

        $query = App\User::find($id)->attendance()
            ->latest('time')
            ->where(function($que){
                $que->where('on_duty', '出勤');
            })->first()->time;

        $getTask = User::find($id)->task()
        ->where('created_at', '>=', $query)->get();

        return $getTask;
    });
    Route::post('/kindOfTask/update/{id}',function($id){
        $kindOfTask = App\KindOfTask::where('id', $id)->first();
        $kindOfTask->taskName = request('name');
        $kindOfTask->save();
    });
    Route::post('/kindOfTask/delete/{id}',function($id){
        $kindOfTaskStart = App\KindOfTask::where('id', $id)->first();
        $KindOfTaskEnd = App\KindOfTask::where('groupId',$kindOfTaskStart->groupId)
        ->get()->count();
        $i = $kindOfTaskStart->orderNum + 1;
        while($i <= $KindOfTaskEnd) {
          $kindOfTaskDown = App\KindOfTask::where('orderNum', $i)->first();
          $kindOfTaskDown->orderNum--;
          $kindOfTaskDown->save();
          $i++;
        }
        $kindOfTask = App\KindOfTask::destroy($id);

        $KindOfTask = App\KindOfTask::where('groupId',$kindOfTaskStart->groupId)
        ->orderBy('orderNum', 'asc')->get();
        return $KindOfTask;
    });
    Route::post('/kindOfTask/up/{id}',function($id){
        $kindOfTaskUp = App\KindOfTask::where('id', $id)->first();
        $kindOfTaskDown = App\KindOfTask::where('groupId', $kindOfTaskUp->groupId)->where('orderNum', $kindOfTaskUp->orderNum + 1)->first();
        $kindOfTaskUp->orderNum++;
        $kindOfTaskDown->orderNum--;
        $kindOfTaskUp->save();
        $kindOfTaskDown->save();

        $KindOfTask = App\KindOfTask::where('groupId',$kindOfTaskUp->groupId)
        ->orderBy('orderNum', 'asc')->get();
        return $KindOfTask;
    });
    Route::post('/kindOfTask/down/{id}',function($id){
        $kindOfTaskDown = App\KindOfTask::where('id', $id)->first();
        $kindOfTaskUp = App\KindOfTask::where('groupId', $kindOfTaskDown->groupId)->where('orderNum', $kindOfTaskDown->orderNum - 1)->first();
        $kindOfTaskDown->orderNum--;
        $kindOfTaskUp->orderNum++;
        $kindOfTaskDown->save();
        $kindOfTaskUp->save();

        $KindOfTask = App\KindOfTask::where('groupId',$kindOfTaskDown->groupId)
        ->orderBy('orderNum', 'asc')->get();
        return $KindOfTask;
    });
    Route::post('/editUserName/{id}',function($id){
        $user = App\User::find($id);
        $user->name = request('name');
        $user->save();

        return $user;
    });
    Route::post('/editUserEmail/{id}',function($id){
        $user = App\User::find($id);
        $user->email = request('email');
        $user->save();

        return $user;
    });
    Route::post('/updatePassword/{id}',function($id){
        $user = App\User::find($id);
        $new_password = request('newpassword');
        $old_password = request('oldpassword');

        if(!(Hash::check($old_password, $user->password))) {
            return '現在のパスワードが間違っています。';
        } else {
            if(Hash::check($new_password, $user->password)) {
                return '新しいパスワードが、現在のパスワードと同じです。違うパスワードを設定してください。';
            } else {
                $user->password = Hash::make($new_password);
                $user->save();
                return 'パスワードが正しく変更されました';
            }
        }
    });
    Route::post('/userAttendance/{id}', 'Api\AttendanceController@getDayAttendance');
    Route::post('/attendanceDetail/update/{id}',function($id){
        $attendance = App\Attendance::find($id);
        $attendance->time = request('time');
        $attendance->note = request('note');
        $attendance->save();
    });
    Route::post('/taskDetail/{id}', 'Api\TaskController@confirmDetail');
});