<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use App\Events\OurExampleEvent;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    public function checkHomePage()
    {
        if(auth()->check()) {
            return view('homepage-feed', ['posts' => Auth::user()->feedPosts()->latest()->paginate(5)]);
        } else {

            if (Cache::has('postCount')) {
                $postCount = Cache::get('postCount');
            } else {
                sleep(5);
                $postCount = Post::count();
                Cache::put('postCount', $postCount, 60);
            }

            return view('homepage', 
            [
                'postCount' => Post::count(),
                // 'userCount' => User::count(),
            ]);
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
            event(new OurExampleEvent(['username' => auth()->user()->username, 'action' => 'login']));
            return redirect('/')->with('success', "Anda Berhasil Login!");
        } else {
            return redirect('/')->with('error', 'Login Gagal, Harap Periksa Lagi Username / Password!');
        }
    }

    public function logout()
    {
        event(new OurExampleEvent(['username' => auth()->user()->username, 'action' => 'logout']));
        Auth::logout();

        return redirect('/')->with('success', 'Anda Berhasil Logout!');
    }

    private function getSharedData($user) {
        $currentlyFollowing = 0;

        if (auth()->check()) {
            $currentlyFollowing = Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]])->count();
        }

        View::share('sharedData', 
        [
            'currentlyFollowing' => $currentlyFollowing,
            'username' => $user->username,
            'avatar' => $user->avatar, 
            'postCount' => $user->posts()->count(),
            'followerCount' => $user->followers()->count(),
            'followingCount' => $user->followingTheseUsers()->count(),  
        ]);
    }

    public function profile(User $user) {

        $this->getSharedData($user);

        return view('profile-posts', 
        [
            'posts' => $user->posts()->latest()->get(), 
        ]);
    
    }

    public function profileRaw(User $user) {
        return response()->json(['theHTML' => view('profile-posts-only', ['posts' => $user->posts()->latest()->get()])->render(), 'docTitle' => $user->username . "'s Profile"]);
    }

    public function profileFollowers(User $user) {

        $this->getSharedData($user);

        // return $user->followers()->latest()->get();

        return view('profile-followers', 
        [
            'followers' => $user->followers()->latest()->get(), 
        ]);
    
    }

    public function profileFollowersRaw(User $user) {
        return response()->json(['theHTML' => view('profile-followers-only', ['followers' => $user->followers()->latest()->get()])->render(), 'docTitle' => $user->username . "'s Followers"]);
    }

    public function profileFollowings(User $user) {

        $this->getSharedData($user);

        return view('profile-followings', 
        [
            'followings' => $user->followingTheseUsers()->latest()->get(), 
        ]);
    
    }

    public function profileFollowingsRaw(User $user) {
        return response()->json(['theHTML' => view('profile-followings-only', ['followings' => $user->followingTheseUsers()->latest()->get()])->render(), 'docTitle' => 'Who ' . $user->username . " Follows"]);
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
