<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the landing (entry) page for PatientCare.
     */
    public function index()
    {
        return view('entry');
    }
}
