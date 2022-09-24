<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AllowedDevices extends Model
{
    use HasFactory, HasUuid;

    protected $table = "user_allowed_login_devices";

    protected $fillable = [
        'device_specification',
    ];

    public function loggable(){
        return $this->morphTo();
    }
}
