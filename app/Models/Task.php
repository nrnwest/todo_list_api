<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'priority',
        'title',
        'description',
        'created_at',
        'completed_at',
        'user_id',
        'parent_id',
    ];

    /**
     * Recursively loads all descendants (subtasks) through eager loading with `with`.
     *
     * @return HasMany
     */
    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id')->with('subtasks');
    }
}
