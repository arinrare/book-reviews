<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $title = "Michael's Book Reviews";
        $paragraph1 = "My name is Michael Baggott, and am an avid fan of Science Fiction and Fantasy novels. These are my favourite genres, though i will sometimes read other genres and the reviews will not necessarily be limited to these.";
        $paragraph2 = "This site contains spoiler free reviews, so there is no in depth discussion about plot here. One of my pet hates is reading reviews that spoil a story before you have a chance to read it and draw your own conclusion.";
        $paragraph3 = "Enjoy the reviews!";
        $copyright = "Copyright © 2025 Michael Baggott Web Development";
        return view('home_index', ['title' => $title, 'paragraph1' => $paragraph1, 'paragraph2' => $paragraph2, 'paragraph3' => $paragraph3, 'copyright' => $copyright]);
    }
}

