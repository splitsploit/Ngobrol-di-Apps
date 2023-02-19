<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function showCreatePost()
    {
        return view('create-post');
    }

    public function storeNewPost(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'body' => 'required',
        ]);

        $data['title'] = strip_tags($data['title']);
        $data['body'] = strip_tags($data['body']);
        $data['user_id'] = Auth::id();

        $newPost = Post::create($data);
        
        return redirect("post/{$newPost->id}")->with('success', 'Postingan Berhasil Dipublish!');
    }

    public function viewSinglePost(Post $post)
    {
        // return view('single-post', ['post' => $post]);
        return view('single-post', compact('post'));
    }
}
