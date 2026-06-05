<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class InternetConnController extends Controller
{
    public function checkConnection()
    {

        return Inertia::render('404', [
            'message' => session('message')
        ]);
    }
}
