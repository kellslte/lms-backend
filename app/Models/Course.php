<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        "title",
        "playlistId"
    ];

    protected $hidden = [
        'updated_at',
        'created_at',
        'track_id',
    ];

    public function facilitator(){
        return $this->hasMany(Facilitator::class);
    }

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function students(){
        return $this->hasMany(User::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function curriculum()
    {
        return $this->hasOne(LessonPlan::class);
    }
}
