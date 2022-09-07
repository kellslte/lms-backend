<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'status',
        'start_date',
        'end_date',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "id"
    ];

    public function duration(){
        return formatToMonth($this->start_date)." - ".formatDate($this->end_date);
    }
}
