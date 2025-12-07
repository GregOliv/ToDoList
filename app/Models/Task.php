<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


// app/Models/Task.php
class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'completed', 'user_id'];

    // Tambahkan relasi ke User (opsional, tapi disarankan)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
