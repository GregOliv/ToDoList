<?php

use Illuminate\Support\Facades\Route;

// LOGIN
Route::view('/login', 'login')->name('login');

// REGISTER
Route::view('/register', 'register');

// DASHBOARD
Route::view('/dashboard', 'dashboard');

// ADD TASK
Route::view('/add-task', 'add');

// ROOT → LOGIN
Route::redirect('/', '/login');
