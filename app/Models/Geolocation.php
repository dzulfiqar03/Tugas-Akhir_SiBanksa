<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Geolocation extends Model
{

        /** @use HasFactory<\Database\Factories\GeolocationFactory> */
    use HasFactory;
    protected $table = 'user_geolocations';

    protected $fillable = [
        'id_userdetail',
        'amenity',
        'house_number',
        'city',
        'county',
        'state',
        'country',
        'postal_code',
    ];


    public function user_detail()
    {
        return $this->belongsTo(UserDetail::class, 'id_userdetail', 'id');
    }

    public function open_street()
    {
        return $this->hasOne(OpenStreetView::class, 'id_geoLoc', 'id');
    }
}
