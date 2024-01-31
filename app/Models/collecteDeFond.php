<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class collecteDeFond extends Model
{
    use HasFactory;

    public function fondation(){
        return $this->belongsTo(User::class);
    }

    public function dons(){
        return $this->hasMany(Payment::class);
    }
}
