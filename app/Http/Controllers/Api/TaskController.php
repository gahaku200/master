<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;

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
}
