<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class SessionExpiredController extends Controller
{
    public function index()
    {
        return Inertia::render('Errors/SessionExpired', [
            'message' => 'Sesi Anda telah berakhir. Silakan login kembali.',
        ]);
    }
}
