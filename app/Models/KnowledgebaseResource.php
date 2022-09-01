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
    ];

       /**
     * Interact with the knowledgebase created_at.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => formatDate($value),
            set: fn ($value) => formatDate($value),
        );
    }

    protected $hidden = [
        'id',
        'updated_at'
    ];
}
