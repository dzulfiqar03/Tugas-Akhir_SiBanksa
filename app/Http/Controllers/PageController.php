<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{
    public function pageNotFound()
    {
        return inertia('Errors/PageNotFound', ['title' => 'Page Not Found']);
    }
}
