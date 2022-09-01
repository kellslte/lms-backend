<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory, HasUuid;
    
    protected $guarded = [];

    protected $hidden = [
        "created_at",
        "updated_at"
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function submissions(){
        return $this->morphMany(Submission::class, 'submittable');
    }

    public function running(){
        return ($this->status === 'pending') ? true : false;
    }

    public function expired(){
        return ($this->status === 'expired') ? true : false;
    }
}
