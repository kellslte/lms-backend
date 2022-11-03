<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonPlan extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = [];

    public function course(){
        return $this->belongsTo(Course::class, "course_id");
    }
}

// array that will hold courses will have to be an ordered one. order will be autogenerated so the array will look like this
/*
lessonplan = [ "128":{"id" : ""} ];
*/
