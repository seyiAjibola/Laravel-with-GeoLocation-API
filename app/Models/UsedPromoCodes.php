<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsedPromoCodes extends Model
{
    use HasFactory;

    protected $fillable = [
        'pick_up', 
        'pick_up_lat', 
        'pick_up_long', 
        'destination', 
        'destination_lat', 
        'destination_long',
        'promo_code_id'
    ];
}
