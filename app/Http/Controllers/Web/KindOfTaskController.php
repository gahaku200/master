<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\KindOfTask;
use Auth;

class KindOfTaskController extends Controller
{
    public function createTask(Request $request)
    {
      $kindOfTask = new KindOfTask();
      $kindOfTask->orderNum = KindOfTask::where('groupId', Auth::user()->group_id)
      ->get()->count() + 1;
      $kindOfTask->taskName = request('task');
      $kindOfTask->groupId = Auth::user()->group_id;
      $kindOfTask->save();

      return redirect('/kindOfTasks');
    }
}
