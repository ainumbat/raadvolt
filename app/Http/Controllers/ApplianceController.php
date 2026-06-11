<?php

namespace App\Http\Controllers;

use App\Models\Appliance;
use Inertia\Inertia;
use App\Http\Requests\StoreApplianceRequest;
use App\Http\Requests\UpdateApplianceRequest;

class ApplianceController extends Controller
{
    public function index()
    {
        return Inertia::render('appliance/index', [
            'appliances' => Appliance::orderBy('id')->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('appliance/form');
    }

    public function store(StoreApplianceRequest $request)
    {
        Appliance::create($request->validated());

        return redirect()->route('appliances.index');
    }

    public function edit(Appliance $appliance)
    {
        return Inertia::render('appliance/form', [
            'appliance' => $appliance
        ]);
    }

    public function update(UpdateApplianceRequest $request, Appliance $appliance)
    {
        $appliance->update($request->validated());

        return redirect()->route('appliances.index');
    }

    public function destroy(Appliance $appliance)
    {
        $appliance->delete();

        return redirect()->route('appliances.index');
    }
}