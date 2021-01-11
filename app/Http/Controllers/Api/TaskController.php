<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Task;

class TaskController extends Controller
{
    public function confirm($id)
    {
      $query = User::find($id)->attendance()
          ->latest('time')
          ->where(function($que){
            $que->where('on_duty', '出勤');
          })->first()->time;

      $task = User::find($id)->task()
      ->where('created_at', '>=', $query)->get();

      return $task;
    }
    public function confirmDetail($id, Request $request)
    {
      $task = Task::where('task_user_id', $id)
          ->where('created_at', '>=', $request->get('fromDay'))
          ->where('created_at', '<', $request->get('toDay'))
          ->get();
      return $task;
    }
}
