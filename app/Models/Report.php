<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = [
        'id',
        'updated_at',
    ];

    public function reporter(){
        return $this->morphTo();
    }
}
