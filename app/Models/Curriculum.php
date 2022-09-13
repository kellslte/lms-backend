<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = [];

    public function plannable(){
        return $this->morphTo();
    }
}
