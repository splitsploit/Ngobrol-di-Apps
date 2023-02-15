<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExampleController extends Controller
{
    public function homePage()
    {
        return view('homepage');
    }

    public function singlePost()
    {
        return view('single-post');
    }
}

