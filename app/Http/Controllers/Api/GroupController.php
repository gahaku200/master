<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Group;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    protected $redirectTo = '/';

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
        ]);
    }

    protected function create(array $data)
    {
        return Group::create([
            'name' => $data['name'],
        ]);
    }

    public function index()
    {
        $group = Group::select('id', 'name')->get();

        return $group;
    }
}
