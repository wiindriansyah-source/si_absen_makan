<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'institution',
    ];

    /**
     * Relasi ke tabel MealAttendance.
     */
    public function mealAttendances(): HasMany
    {
        return $this->hasMany(MealAttendance::class);
    }
}
