<?php

namespace App\Http\Controllers\Web;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserTaskController extends Controller
{
  public function taskStart(Request $request)
  {
    Auth::user()->now_task = $request->get('task');
    Auth::user()->now_task_start = now();
    Auth::user()->save();

    return ['task' => request('task')];
  }
}
