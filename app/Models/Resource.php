<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = [
        "id",
        "created_at",
        "updated_at",
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
