<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;


class Navbar extends Component
{
    public $landing;
    public $home;
    public $reviews;

    /**
     * Create a new component instance.
     */ 
    public function __construct()
    {
        $host_url = config('bookreviews.host_url');
        $landing_url=config('bookreviews.landing_url');
        $this->landing = $landing_url;
        $this->home = $host_url;
        $this->reviews = $host_url . '/reviews';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.navbar');
    }
}
