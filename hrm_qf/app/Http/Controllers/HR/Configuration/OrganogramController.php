<?php

namespace App\Http\Controllers\HR\Configuration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrganogramController extends Controller
{
    public function index()
    {
        return view('HR.Configuration.Organogram.chart');
    }
}
