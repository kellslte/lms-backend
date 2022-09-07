<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = [];

    protected $hidden = [
        "id",
        "updated_at",
        "created_at",
    ];

    public function student(){
        return $this->belongsTo(User::class);
    }
}
