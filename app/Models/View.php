<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = [];

    public function viewable(){
        return $this->morphTo();
    }
}
