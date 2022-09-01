<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Social extends Model
{
    use HasFactory, HasUuid;

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
        'sociable_id',
        'sociable_type'
    ];

    protected $guarded = [];

    public function sociable(){
        return $this->morphTo();
    }
}
