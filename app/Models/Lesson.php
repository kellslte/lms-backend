<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory, HasUuid;

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

    public function comments(){
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function media(){
        return $this->morphMany(Media::class, 'mediaable');
    }

    public function views(){
        return $this->morphOne(View::class, 'viewable');
    }
}
