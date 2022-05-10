<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    use HasFactory;

    protected $fillable = ['code_value', 'max_rides', 'status', 'radius', 'expiry_date', 'event_id'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
