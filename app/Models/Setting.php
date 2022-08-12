<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_preference',
        'text_message_preference',
    ];

    protected $hidden = [
        'id',
        'changeable_id',
        'changeable_type',
        'created_at',
        'updated_at',
    ];

    public function changeable(){
        return $this->morphTo();
    }
}
