<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;

class ChatController extends Controller
{
    public function index()
    {
        return resource_path('views');
    }

    public function show($slug)
    {
        $team = Team::where('slug', $slug)->first();
        if($team)
            return view('chat.show', ['team' => $team]);
        abort(404);
    }
}
