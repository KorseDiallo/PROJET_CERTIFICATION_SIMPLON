<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\SwaggerExclude
 */

class Payment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'token',
        'amount',
        'user_id',
        'collecte_de_fond_id',
    ];

    protected $table = 'payments';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function collecteDeFond(){
        return $this->belongsTo(collecteDeFond::class);
    }


}