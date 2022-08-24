<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory, HasUuid;

    protected $hidden = [
        "created_at",
        "updated_at",
        "id",
        "mediaable_type",
        "mediaable_id",
    ];

    public function mediaable(){
        return $this->morphTo();
    }
}
