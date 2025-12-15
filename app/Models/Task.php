<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

// app/Models/Task.php
class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'completed',
        'user_id',
        'priority',
        'deadline'
    ];
    // Mutator untuk menangani field 'completed' dari frontend
    protected function completed(): Attribute
    {
        return Attribute::make(
            // Mengubah nilai completed (true/false) dari DB status (enum)
            get: fn ($value, $attributes) => $attributes['status'] === 'completed',
            
            // Mengubah nilai completed (true/false) dari request menjadi DB status
            set: function ($value) {
                $newStatus = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'completed' : 'pending';
                
                return [
                    'status' => $newStatus,
                    'finished_at' => ($newStatus === 'completed') ? now() : null,
                ];
            },
        );
    }
}
