<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function createFollow(User $user) {
        // cannot follow itself
        if($user->id == auth()->user()->id) {
            return back()->with('error', 'Tidak Bisa Follow Akun Sendiri!');
        }

        // cannot follow user already followed
        $existCheck = Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]])->count();

        if($existCheck) {
            return back()->with('error', 'Anda Sudah Follow Akun Ini!');
        }

        $newFollow = new Follow;
        $newFollow->user_id = auth()->user()->id;
        $newFollow->followeduser = $user->id;
        $newFollow->save();

        return back()->with('success', 'Berhasil di Follow!');
    }

    public function removeFollow() {
        
    }
}
