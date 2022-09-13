<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = [];

    protected $hidden = [
        "attender_id",
        "attender_type",
        "id"
    ];

    public function attendable(){
        return $this->morphTo();
    }
}
