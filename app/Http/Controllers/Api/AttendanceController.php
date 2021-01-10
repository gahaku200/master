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
    public function from_day($id, $time)
    {
      $attendance = User::find($id)->attendance()
        ->where('time' , '<', $time)
        ->latest('time')->get();

      if ($attendance->count() == 0) {
        return 'noData';
      } else {
        $attendance = User::find($id)->attendance()
          ->where('time' , '<', $time)
          ->latest('time')->first()->time;
        return $attendance;
      }
    }
    public function to_day($id, $time)
    {
      $attendance = User::find($id)->attendance()
        ->where('time' , '>', $time)
        ->orderBy('time', 'asc')->get();

      if ($attendance->count() == 0) {
        return 'noData';
      } else {
        $attendance = User::find($id)->attendance()
          ->where('time' , '>', $time)
          ->orderBy('time', 'asc')->first()->time;
        return $attendance;
      }
    }
    public function getDayAttendance($id, Request $request)
    {
      $query = User::find($id)->attendance()
        ->where('time', '>=', $request->get('to_day'))
        ->get();

      $attendance = User::find($id)->attendance()
        ->orderBy('time', 'asc')
        ->where('time', '>=', $request->get('day'))
        ->where('time', '<', $request->get('to_day'))->get();
      
      if ($query->count() == 0) {
        $empty = User::find($id)->attendance()
          ->orderBy('time', 'asc')
          ->where('time', '>=', $request->get('day'))
          ->get();
        if ($empty->count() == 0) {
          return 'noData';
        }

        $que = User::find($id)->attendance()
          ->orderBy('time', 'asc')
          ->where('time', '>=', $request->get('day'))
          ->first()->on_duty;
          if ($que == '出勤') {
            return $attendance;
          } else {
            $q = User::find($id)->attendance()
              ->orderBy('time', 'asc')
              ->where('time', '>=', $request->get('day'))
              ->where('on_duty', '出勤')
              ->first()->time;

            $attendance2 = User::find($id)->attendance()
              ->orderBy('time', 'asc')
              ->where('time', '>=', $q)
              ->where('time', '<', $request->get('to_day'))->get();

            return $attendance2;
          }
      } else {
        $que = User::find($id)->attendance()
          ->orderBy('time', 'asc')
          ->where('time', '>=', $request->get('to_day'))
          ->first()->on_duty;
        if ($que == '出勤') {
          return $attendance;
        } else {
          $q = User::find($id)->attendance()
            ->where('time', '>=', $request->get('to_day'))
            ->where('on_duty', '退勤')->get();
          if ($q->count() == 0) {
            $ques = User::find($id)->attendance()
              ->latest('time')
              ->first()->time;

            $attendance3 = User::find($id)->attendance()
              ->orderBy('time', 'asc')
              ->where('time', '>=', $request->get('day'))
              ->where('time', '<=', $ques)->get();
            
            return $attendance3;
          } else {
            $ques = User::find($id)->attendance()
              ->orderBy('time', 'asc')
              ->where('time', '>=', $request->get('to_day'))
              ->where('on_duty', '退勤')
              ->first()->time;
            
            $attendance4 = User::find($id)->attendance()
              ->orderBy('time', 'asc')
              ->where('time', '>=', $request->get('day'))
              ->where('time', '<=', $ques)->get();
            
            return $attendance4;
          }
        }
      }
    }
}