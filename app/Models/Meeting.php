<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = [];

    public function attendees(){
        return $this->morphMany(Attendance::class, 'attendable');
    }
}
