<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// LOGIN
Route::get('/login', fn() => view('login'))->name('web.login');

// REGISTER
Route::get('/register', fn() => view('register'))->name('web.register');

// DASHBOARD
Route::get('/dashboard', fn() => view('dashboard'))->name('web.dashboard');

// ADD TASK
Route::get('/add-task', fn() => view('add'))->name('web.add');
Route::post('/add-task', function (Request $request) {
    return 'Add Task diproses';
})->name('web.add.post');

// ROOT → LOGIN
Route::redirect('/', '/login');
