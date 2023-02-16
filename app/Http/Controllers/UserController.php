<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'min:3', Rule::unique('users', 'username') ],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        User::create($data);

        return view('success');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'loginusername' => 'required',
            'loginpassword' => 'required',
        ]);

        // if (auth()->attempt(['username' => $data['loginusername'], 'password' => $data['loginpassword']]
        // {
        //     return "Login berhasil";
        // }else {
        //     return "Login gagal";
        // }

        if (auth()->attempt(['username' => $data['loginusername'], 'password' => $data['loginpassword']])){
            return "Login sukses";
        } else {
            return "login berhasil";
        }
    }
}
