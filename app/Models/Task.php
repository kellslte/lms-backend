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
        return $this->hasMany(Submission::class);
    }

    public function running(){
        return ($this->status === 'running') ? true : false;
    }
}
