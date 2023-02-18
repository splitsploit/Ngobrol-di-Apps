<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function checkHomePage()
    {
        if(auth()->check()) {
            return view('homepage-feed');
        } else {
            return view('homepage');
        }
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'min:3', Rule::unique('users', 'username') ],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = User::create($data);

        Auth::login($user);

        return redirect('/')->with('success', 'Daftar Akun Berhasil!. Selamat Berkomunikasi di Ngobrol di Apps!');
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

        if (auth()->attempt(['username' => $data['loginusername'], 'password' => $data['loginpassword']]))
        {
            $request->session()->regenerate();
            return redirect('/')->with('success', "Anda Berhasil Login!");
        } else {
            return redirect('/')->with('error', 'Login Gagal, Harap Periksa Lagi Username / Password!');
        }
    }

    public function logout()
    {
        Auth::logout();

        return redirect('/')->with('success', 'Anda Berhasil Logout!');
    }
}
