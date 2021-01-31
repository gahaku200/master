<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\KindOftask;
use Auth;

class KindOfTaskController extends Controller
{
    public function createTask(Request $request)
    {
      $kindOftask = new KindOftask();

      if (empty(KindOftask::where('groupId', Auth::user()->group_id)->get())) {
        $kindOftask->orderNum = 1;
      } else {
        $kindOftask->orderNum = KindOftask::where('groupId', Auth::user()->group_id)
        ->get()->count() + 1;
      }
      
      $kindOftask->taskName = request('task');
      $kindOftask->groupId = Auth::user()->group_id;
      $kindOftask->save();

      return redirect('/kindOfTasks');
    }
}
