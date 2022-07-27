<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;


    protected $guarded = [];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function task()
    {
        return $this->hasOne(Task::class);
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }
}
