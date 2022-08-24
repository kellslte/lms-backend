<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = [];

    protected $hidden = [
        "id",
        "schedulable_id",
        "schedulable_type",
        "created_at",
        "updated_at",
    ];

    public function schedulable(){
        return $this->morphTo();
    }
}
