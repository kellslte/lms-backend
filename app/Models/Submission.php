<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = [
        'submittable_id',
        'submittable_type',
        'taskable_id',
        'taskable_type',
        'updated_at',
        'id'
    ];

    public function submittable(){
        return $this->morphTo();
    }

    public function taskable(){
        return $this->morphTo();
    }
}
