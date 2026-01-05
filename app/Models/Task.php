<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'completed',
        'user_id',
        'priority',
        'deadline',
        'category_id'
    ];

    /**
     * Relationship to Category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relationship to User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mutator for 'completed' field.
     */
    protected function completed(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => isset($attributes['status']) && $attributes['status'] === 'completed',
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
