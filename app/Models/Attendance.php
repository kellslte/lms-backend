<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    public function attender(){
        return $this->morphTo();
    }

    public function attendable(){
        return $this->morphTo();
    }
}
