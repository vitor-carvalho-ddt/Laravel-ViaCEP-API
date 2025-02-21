<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(){
        $users = User::paginate(15);

        return view(view: "admin.users.index", data: [
            "users" => $users
        ]);
    } 
}