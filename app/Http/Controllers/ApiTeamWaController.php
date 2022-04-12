<?php

namespace App\Http\Controllers;

use App\Models\WaWeb;
use Illuminate\Http\Request;

class ApiTeamWaController extends Controller
{
    public function getAuth()
    {
        return response()->json([
            'team' => auth()->user()->currentTeam,
        ]);
    }

    public function getTeam($id)
    {
        return response()->json([
            'old' => $id
        ]);
    }

    public function postTeamAuth(Request $request)
    {
        return response()->json([
            'data' => $request
        ]);
    }

    public function put($id, Request $request)
    {
        return response()->json([
            'old' => $id,
            'new' => $request
        ]);
    }
}
