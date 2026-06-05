<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResources;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PreferenceController extends Controller
{
    public function index(){
                $menu = (new DataResources(null))->toArray(request());

                $nasabahs = [
    [
        'id' => 1,
        'nama' => 'Budi Santoso',
        'alamat' => 'Jl. Sudirman Jakarta',
        'lat' => -6.2088,
        'lng' => 106.8456,
    ],
    [
        'id' => 2,
        'nama' => 'Siti Aminah',
        'alamat' => 'Surabaya',
        'lat' => -7.2575,
        'lng' => 112.7521,
    ]
];


        return Inertia::render('Preference', [
            'sidebardata' => $menu,
            'nasabahs' =>$nasabahs
        ]);
    }
}
