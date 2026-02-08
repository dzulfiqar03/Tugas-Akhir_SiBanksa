<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Geolocation extends Model
{
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
