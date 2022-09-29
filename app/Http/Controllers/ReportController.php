<?php

namespace App\Http\Controllers;

class ReportController extends Controller
{

    public function index()
    {
        return view('report.billing');
    }

    public function show($key)
    {
        if($key=='request'){
            return view('report.request');
        }elseif($key=='sms'){
            return view('report.sms_blast');
        }elseif($key=='billing'){
            return view('report.billing');
        }else{
            return view('report.log');
        }
    }
}
