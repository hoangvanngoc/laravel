<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function show($id)
    {
        return User::findOrFail($id);
    }

    public function index()
    {
        $users = User::join('departments', 'users.department_id', '=', 'departments.id')
            ->join('user_status', 'users.status_id', '=', 'user_status.id')
            ->select(
                'users.*',
                'departments.name as department',
                'user_status.name as status'
            )
            ->get();

        return response()->json($users);
    }

    public function create() {
        $user_status = \DB::table("user_status")->select(
            "id as value",
            "name as label"
        )->get();
        $departments = \DB::table("departments")->select(
            "id as value",
            "name as label"
        )->get();


        return response()->json([
            "user_status" => $user_status,
            "departments" =>  $departments
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'status_id' => 'required',
            'username' => 'required|unique:users,username',
            'name' => 'required|max:255',
            'email' => 'required|email',
            'departments' => 'required',
            'password' => 'required|confirmed'
        ],
        [
          'status_id.require' => 'This field is required',
          'username.required' => 'This field is required',
          'username.unique' => "This username alrealy exist!",

          'name.required' => 'This field is required',
          'name.max' => 'Name is maximum 255 characters',

          'email.required' => 'This field is required',
          'email.email' => 'Invalid email',

          'departments.required' => 'This field is required',

          'password.required' => 'This field is required',
          'password.confirmed' => 'Password is not match'
        ]);

        return $validated;
    }
}
