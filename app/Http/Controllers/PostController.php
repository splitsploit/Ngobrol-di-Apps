<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Str;
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
        // check author or not ( at post )
        // if($post->user_id === auth()->user()->id) {
        //     return "You are author!";
        // } return "You are not author!"; 

        $post['body'] = Str::markdown($post->body);

        // return view('single-post', ['post' => $post]);
        return view('single-post', compact('post'));
    }

    public function delete(Post $post) {
        
        // delete post using PostPolicy ( cannot method )
        // if(auth()->user()->cannot('delete', $post)) {
        //     return "Hanya Pembuat Post Yang Boleh Menghapus!";
        // }

        $post->delete();

        return redirect('/profile/' . auth()->user()->username)->with('success', 'Berhasil Hapus Post!');
    }

    public function edit(Post $post) {
        return view('edit-post', compact('post'));
    }

    public function update(Post $post, Request $request) {
        $data = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);

        $data['title'] = strip_tags($data['title']);
        $data['body'] = strip_tags($data['body']);

        $post->update($data);

        return back()->with('success', 'Post Berhasil di Update!');
    }
}
