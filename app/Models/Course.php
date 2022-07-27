<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        "title"
    ];

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }
}
