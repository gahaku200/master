<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;

class AttendanceController extends Controller
{
    public function confirm($id)
    {
      $query = User::find($id)->attendance()
          ->latest('time')
          ->where(function($que){
            $que->where('on_duty', '出勤');
          })->first()->time;

      $attendance = User::find($id)->attendance()
      ->where('time', '>=', $query)->get();

      return $attendance;
    }
}