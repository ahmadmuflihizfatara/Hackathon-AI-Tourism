<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Landing page — hero with chatbot widget.
     */
    public function landing(): View
    {
        return view('pages.landing');
    }

    /**
     * Dashboard — two-column AI chat + itinerary result.
     */
    public function dashboard(): View
    {
        return view('pages.dashboard');
    }
}
