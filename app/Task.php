<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Task extends Model
{
    protected $table = 'tasks';

    public function user()
  {
    return $this->belongsTo('App\User', 'id', 'task_user_id');
  }
}
