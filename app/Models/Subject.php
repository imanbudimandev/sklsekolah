<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model
{
    protected $fillable = ['code', 'name', 'category', 'order_number', 'jurusan', 'tampil_skl', 'tampil_transkip'];

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'grades')
                    ->withPivot('score')
                    ->withTimestamps();
    }
}
