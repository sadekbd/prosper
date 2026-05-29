<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Service;

class ServicesController extends Controller
{
    public function index()
    {
        $services = Service::active()->with('features')->get();
        return view('public.services', compact('services'));
    }

    public function show(string $slug)
    {
        $service  = Service::where('slug', $slug)->where('status', 'active')
                           ->with('features')->firstOrFail();
        $others   = Service::active()->where('id', '!=', $service->id)->take(2)->get();
        return view('public.services-single', compact('service', 'others'));
    }
}