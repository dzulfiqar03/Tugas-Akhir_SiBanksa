<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\FormResources;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
          $form = (new FormResources(null))->toArray(request());
        return view('pages/auth/signin' , [
            'formdata' => $form,
        ]);
        
    }

    public function login(Request $request)
    {
        // Logic for handling login
        $form = (new FormResources(null))->toArray(request());

        return redirect()->route('data-sampah', [
            'formdata' => $form,
        ]);
    }

    public function showRegisterForm()
    {
        $form = (new FormResources(null))->toArray(request());

        return view('pages/auth/signup', [
            'formdata' => $form,
        ]);
    }


    public function logout(Request $request)
    {
        // Logic for handling logout
    }
}
