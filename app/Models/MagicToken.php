<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MagicToken extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = [
        'expires_at', 'consumed_at',
    ];

    public function tokenable()
    {
        return $this->morphTo();
    }

    public function isValid()
    {
        return !$this->isExpired() && !$this->isConsumed();
    }

    public function isExpired()
    {
        return $this->expires_at->isBefore(now());
    }

    public function isConsumed()
    {
        return $this->consumed_at !== null;
    }

    public function consume()
    {
        return $this->update([
            "consumed_at" => now()
        ]);
    }
}
