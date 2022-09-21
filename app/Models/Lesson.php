<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = [];

    protected $hidden = [
        "created_at",
        "updated_at",
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    public function comments(){
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function media(){
        return $this->morphOne(Media::class, 'mediaable');
    }

    public function views(){
        return $this->morphOne(View::class, 'viewable');
    }
}
