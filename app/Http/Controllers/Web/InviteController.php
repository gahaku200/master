<?php

namespace App\Http\Controllers\Web;

use App\User;
use Auth;
use App\Invite;
use App\Mail\InviteCreated;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InviteController extends Controller
{
  protected $invite;
  
  public function process(Request $request)
  {
    do {
        $token = str_random();
    }
    while (Invite::where('token', $token)->first());

    $invite = Invite::create([
        'email' => $request->get('memberEmail'),
        'token' => $token,
        'inviting_user' => Auth::user()->name
    ]);

    Mail::to($request->get('memberEmail'))->send(new InviteCreated($invite));

    return redirect('/successSendEmail');
  }

  public function accept($token)
  {
    if (!$invite = Invite::where('token', $token)->first()) {
        return redirect('/notInviteData');
    }

    $invitedUser = \App\User::where('email', $invite->email)->first();
    if($invitedUser->group_id != null) {
      $invite->delete();
      return redirect('/alreadyJoinGroup');
    }
    $invitingUser = \App\User::where('name', $invite->inviting_user)->first();
    $invitedUser->group_id = $invitingUser->group_id;
    $invitedUser->save();

    $invite->delete();

    return redirect('/successInvite');
  }
}
