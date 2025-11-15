<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResources;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $menu = (new DataResources(null))->toArray(request());

        return view('pages/dashboard', [
            'sidebardata' => $menu,
        ]);
    }
}
