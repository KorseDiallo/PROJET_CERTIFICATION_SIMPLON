<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class abonnement extends Model
{
    use HasFactory;

    protected $fillable = [
        'donateur_id',
        'fondation_id',
        'suivre',
    ];



    public function donateur(){

        return $this->belongsTo(User::class);
    }

    public function fondation(){
        return $this->belongsTo(User::class);
    }
}
