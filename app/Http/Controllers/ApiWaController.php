<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiWaController extends Controller
{
    public function show($messege)
    {
        return response()->json([
            'from'  => '081999222185',
            'state' => $messege
        ]);
    }
}
