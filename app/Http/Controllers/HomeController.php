<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Appliance;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return Inertia::render('Welcome', [
            'appliances' => Appliance::latest()->get()
        ]);
    }
}
