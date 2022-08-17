<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = [];

    public function schedulable_owner(){
        return $this->morphTo();
    }

    public function schedulable(){
        return $this->morphTo();
    }
}
