<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InternetConnController extends Controller
{
    public function checkConnection()
    {
       return view('components.tailwind-admin.404')->with('message', session('message'));
    }
}
