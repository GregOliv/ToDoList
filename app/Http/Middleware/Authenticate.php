<?php

// app/Http/Middleware/Authenticate.php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request; // Pastikan ini di-use

class Authenticate extends Middleware
{
    /**
     * Dapatkan path yang harus dialihkan pengguna jika mereka tidak terautentikasi.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Jika permintaan TIDAK mengharapkan JSON (misalnya, permintaan browser biasa),
        // maka kembalikan nama rute login. Ini yang memicu RouteNotFoundException
        // jika rute 'login' tidak ada.
        if (! $request->expectsJson()) {
            return route('login'); // Baris ini yang memicu error sebelumnya
        }

        // Jika permintaan MENGHARAPKAN JSON (misalnya dari API/AJAX),
        // maka kembalikan null. Laravel akan secara otomatis memicu
        // Http response 401 Unauthenticated.
        return null; 
    }
}
