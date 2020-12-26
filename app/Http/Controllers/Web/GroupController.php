<?php

namespace App\Http\Controllers\Web;

use App\Group;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GroupController extends Controller
{
  protected $group;
  /**
   *
   *
   * @return void
   */
  public function __construct(Group $group)
  {
    $this->group = $group;
  }

  public function createGroup(Request $request)
  {
    if (Auth::check()) {
      $groupName = $request->input('name');
      $this->group->insertGroup((string) $groupName);
    }
    return redirect('/');
  }
}
