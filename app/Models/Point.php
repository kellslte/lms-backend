<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    use HasFactory;

    protected $fillable = [
        "total",
        "attendance_points",
        "bonus_points",
        "task_points",
        "history",
        "user_id"
    ];

    public function getHistory(){
        return $this->history;
    }

    public function setHistory(Array $entry){
        return $this->update($entry);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
