<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        "title",
    ];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
