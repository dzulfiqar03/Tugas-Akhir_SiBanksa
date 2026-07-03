<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpenStreetView extends Model
{

        protected $table = 'open_street_views';

    protected $fillable = [
        'id_geoLoc',
        'display_name',
        'latitude',
        'longitude',
        'type',
    ];


    public function location()
    {
        return $this->belongsTo(Geolocation::class, 'id_geoLoc', 'id');
    }
}
