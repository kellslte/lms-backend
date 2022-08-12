<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function submittable(){
        return $this->morphTo();
    }

    public function taskable(){
        return $this->morphTo();
    }
}
