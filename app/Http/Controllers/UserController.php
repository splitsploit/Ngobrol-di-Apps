<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

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

        $data['password'] = Hash::make($data['password']);

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

    public function profile(User $user) {

        return view('profile-posts', 
        [
            'username' => $user->username,
            'avatar' => $user->avatar, 
            'posts' => $user->posts()->latest()->get(), 
            'postCount' => $user->posts()->count() ]);
    
    }

    public function showAvatarForm() {
        return view('avatar-form');
    }

    public function storeAvatar(Request $request) {
        $request->validate([
            'avatar' => 'required|image|max:6000',
        ]);

        // store avatar original size
        // $request->file('avatar')->store('public/avatar');

        // format saved avatar
        $user = auth()->user();

        $fileName = $user->id . '-' . uniqid() . '.jpg';

        // store avatar ( resize )
        $imgData = Image::make($request->file('avatar'))->fit(120)->encode('jpg');
        Storage::put('public/avatars/' . $fileName, $imgData);

        $oldAvatar = $user->avatar;

        $user->avatar = $fileName;
        $user->save();

        if($oldAvatar != 'default-avatar.jpg') {
            Storage::delete(str_replace('/storage/', 'public/', $oldAvatar));
        };

        return back()->with('success', 'Berhasil Upload Avatar');
    }
}
