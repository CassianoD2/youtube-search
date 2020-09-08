<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class YoutubeController extends Controller
{
    public function index(Request $request)
    {
        echo $request->get('text');
    }
}
