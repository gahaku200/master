<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Group extends Model
{
  protected $table = 'groups';
  protected $primaryKey = 'id';
  protected $groupId;

  public function insertGroup(string $groupName)
  {
    $group = new Group();
    $group->name = $groupName;
    $group->save();
    Auth::user()->group_id = $group->id;
    Auth::user()->is_admin = true;
    Auth::user()->save();
  }

  public function users()
  {
    return $this->hasMany('App\User');
  }
}
