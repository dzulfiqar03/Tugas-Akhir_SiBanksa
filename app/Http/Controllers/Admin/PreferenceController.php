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

        return Inertia::render('Preference', [
            'sidebardata' => $menu,
        ]);
    }
}
