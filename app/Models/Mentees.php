<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mentees extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = [];

    public function mentor(){
        return $this->belongsTo(Mentor::class);
    }

    public function mentorable(){
        return $this->morphTo();
    }
}
