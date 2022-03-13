<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChFavorite extends Model
{
    //
    protected $fillable = [
        'id',
        'user_id',
        'favorite_id'
    ];
}
