<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get(uri: '/users', action: [UserController::class, 'index'])->name(name: 'users.index');

Route::get(uri: '/', action: function (){
    return view('welcome');
});
