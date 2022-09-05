<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KnowledgebaseResource extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'tags',
        'title',
        'moderator',
        'resource_link',
        'thumbnail'
    ];

    protected $hidden = [
        'id',
        'updated_at'
    ];
}
