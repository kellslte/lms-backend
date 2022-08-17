<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function submissions(){
        return $this->morphMany(Submission::class, 'submittable');
    }

    public function running(){
        return ($this->status === 'running') ? true : false;
    }

    public function expired
}
