<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        return resource_path('views');
    }

    public function show($slug)
    {
        return $slug;
    }
}
