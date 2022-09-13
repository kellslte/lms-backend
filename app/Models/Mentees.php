<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mentees extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function mentorable(){
        return $this->morphTo();
    }
}
