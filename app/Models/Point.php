<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    use HasFactory;

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
