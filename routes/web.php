<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return 'Ini adalah halaman login sementara'; // Ganti dengan view atau controller yang sebenarnya
})->name('login'); // **Penting: Memberi nama 'login'**

Route::get('/', function () {
    return view('welcome');
});