<?php

namespace App\Http\Controllers;

use DB;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\SolarCalculation;

class SolarCalculationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reports = SolarCalculation::with([
            'items.appliance'
        ])
        ->latest()
        ->paginate(20);

        return Inertia::render('report/index', [
            'reports' => $reports
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('home');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'nullable|email',
            'whatsapp' => 'required',
            'total_load' => 'required|integer',
            'essential_load' => 'required|integer',
            'generator_load' => 'required|integer',
            'backup_hours' => 'required|numeric|min:0|max:16',
            'rows' => 'required|array|min:1',
        ]);

        DB::transaction(function () use ($request) {

            $calculation = SolarCalculation::create([
                'name' => $request->name,
                'email' => $request->email,
                'whatsapp' => $request->whatsapp,

                'total_load' => $request->total_load,
                'essential_load' => $request->essential_load,
                'generator_load' => $request->generator_load,

                'backup_hours' => $request->backup_hours,
            ]);

            foreach ($request->rows as $row) {

                $calculation->items()->create([
                    'appliance_id' => $row['appliance_id'],
                    'watts' => $row['watts'],
                    'quantity' => $row['quantity'],
                    'type' => $row['type'],
                ]);
            }
        });

        return redirect()->route('home');
    }

    /**
     * Display the specified resource.
     */
    public function show(SolarCalculation $solarCalculation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SolarCalculation $solarCalculation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SolarCalculation $solarCalculation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SolarCalculation $solarCalculation)
    {
        //
    }
}
