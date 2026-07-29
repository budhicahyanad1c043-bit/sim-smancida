<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function admin()
    {
        return view('dashboard.admin');
    }

    public function guru()
    {
        return view('dashboard.guru');
    }

    public function walikelas()
    {
        return view('dashboard.walikelas');
    }

    public function gurubk()
    {
        return view('dashboard.gurubk');
    }

    public function kepsek()
    {
        return view('dashboard.kepsek');
    }
}