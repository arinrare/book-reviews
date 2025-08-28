<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;


class Navbar extends Component
{
    /*public string $landing = 'https://michaelbaggott.site';
    public string $home = 'https://michaelbaggott.site/bookreviews';
    public string $reviews = 'https://michaelbaggott.site/bookreviews/reviews';*/
    public string $landing = 'https://michaelbaggott.site';
    public string $home = 'http://book-reviews';
    public string $reviews = 'http://book-reviews/reviews';

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        // No need to set anything here if you use property defaults
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.navbar');
    }
}
