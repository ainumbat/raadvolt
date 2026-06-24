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
            'wire_length' => 'required|numeric|min:0|max:300',
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
    public function show($report_id)
    {
        $report = SolarCalculation::with(['items.appliance'])->findOrFail($report_id);

        if (empty($report->report_data)) {
            $this->generateDetailedReport($report);

            // Reload updated value from database
            $report->refresh();
        }

        return Inertia::render('report/premium', [
            'report' => $report
        ]);
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

    public function updateJson(Request $request, SolarCalculation $report)
    {
        $data = json_decode($request->report_data, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->withErrors([
                'report_data' => 'Invalid JSON'
            ]);
        }
        
        $report->update(['report_data' => $data]);
        
        return back()->with('success', 'Report updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SolarCalculation $solarCalculation)
    {
        //
    }

    private function generateDetailedReport($report)
    {
        $essentialLoad = $report->essential_load;
        $generatorLoad = $report->generator_load;
        $totalLoad = $report->total_load;

        $essentialAppliances = [];
        $generatorAppliances = [];

        $nonInverterFan = false;
        $hasAc = false;
        $hasMotor = false;
        $hasIron = false;
        $hasFridge = false;

        foreach ($report->items as $item) {

            $load = $item->watts * $item->quantity;

            // $totalLoad += $load;
            if ($item->type == 'essential') {
                // $essentialLoad += $load;
                $essentialAppliances[] = [
                    'name' => $item->appliance->name,
                    'watts' => $item->watts,
                    'quantity' => $item->quantity,
                    'daily_runtime' => $item->daily_runtime." hours"
                ];
            } else {
                $generatorAppliances[] = [
                    'name' => $item->appliance->name,
                    'watts' => $item->watts,
                    'quantity' => $item->quantity,
                    'daily_runtime' => $item->daily_runtime." hours"
                ];
            }

            $name = strtolower($item->appliance->name);

            if (str_contains($name, 'fan') && $item->watts >= 90) {
                $nonInverterFan = true;
            }

            if (str_contains($name, 'ac')) {
                $hasAc = true;
            }

            if (
                str_contains($name, 'motor') ||
                str_contains($name, 'pump')
            ) {
                $hasMotor = true;
            }

            if (str_contains($name, 'iron')) {
                $hasIron = true;
            }

            if (
                str_contains($name, 'fridge') ||
                str_contains($name, 'refrigerator')
            ) {
                $hasFridge = true;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | PLAN 1
        |--------------------------------------------------------------------------
        */

        $plan1Inverter = ceil(($essentialLoad * 1.25) / 1000);
        $plan1Solar = ceil(($essentialLoad * 1.5) / 1000);

        /*
        |--------------------------------------------------------------------------
        | PLAN 2
        |--------------------------------------------------------------------------
        */

        $avgLoadKw = round($totalLoad / 1000, 2);

        $plan2Solar = max(
            ceil($avgLoadKw + 3),
            ceil($avgLoadKw * 1.4)
        );

        $surplusKw = max(
            0,
            $plan2Solar - $avgLoadKw
        );

        $monthlyExportUnits =
            round($surplusKw * 5 * 30);

        $buybackRate = config(
            'solar.buyback_rate',
            10
        );

        $monthlyExportIncome =
            round(
                $monthlyExportUnits *
                $buybackRate
            );

        /*
        |--------------------------------------------------------------------------
        | PLAN 3
        |--------------------------------------------------------------------------
        */

        $backupHours = 4;

        $requiredBackupWh =
            $essentialLoad *
            $backupHours;

        $batteryWh =
            $requiredBackupWh /
            (0.8 * 0.9);

        $batteryKwh =
            round($batteryWh / 1000, 1);

        $batteryVoltage =
            $essentialLoad <= 3000
                ? 24
                : 48;

        $batteryAh =
            round(
                $batteryWh /
                $batteryVoltage
            );

        $plan3Solar =
            ceil(($essentialLoad * 1.8) / 1000);

        /*
        |--------------------------------------------------------------------------
        | PLAN 4
        |--------------------------------------------------------------------------
        */

        $nightHours = 14;

        $offgridWh =
            $totalLoad *
            $nightHours;

        $offgridBatteryWh =
            $offgridWh /
            (0.8 * 0.9);

        $offgridBatteryKwh =
            round(
                $offgridBatteryWh / 1000,
                1
            );

        $offgridSolar =
            ceil(
                ($totalLoad * 2.5) / 1000
            );

        $offgridInverter =
            ceil(
                ($totalLoad * 1.25) / 1000
            );

        /*
        |--------------------------------------------------------------------------
        | Personalized Tips
        |--------------------------------------------------------------------------
        */

        $tips = [];
        $warnings = [];

        $warnings[] = 'Do not clean your solar panels during sunshine you could get high voltage shock.';

        if ($nonInverterFan) {
            $tips[] =
                'Use inverter Fans to reduce solar and grid requirements by up to 70%.';
        }

        if ($hasAc) {
            $tips[] =
                'Use inverter ACs to reduce solar and battery requirements by up to 60%.';
        }

        if ($hasFridge) {
            $tips[] =
                'Keep refrigerator on solar-backed circuit for maximum savings.';
        }

        if ($hasMotor) {
            $warnings[] =
                'Water pumps require high startup current. Select inverter with sufficient surge capacity.';
        }

        if ($hasIron) {
            $warnings[] =
                'Operate iron during peak solar production hours (10AM-3PM).';
        }

        if ($totalLoad > 8000) {
            $warnings[] =
                'Your load is relatively high. Consider dividing loads into essential and non-essential circuits.';
        }

        $report_data = [
            'report_id' => 'RV-0900' . $report->id,

            'summary' => [
                'essential_load' => $essentialLoad." watts",
                'essential_load_kw' => round($essentialLoad / 1000, 2)." KW",
                'generator_load' => $generatorLoad." watts",
                'generator_load_kw' => round($generatorLoad / 1000, 2)." KW",
                'total_load' => $totalLoad." watts",
                'total_load_kw' => round($totalLoad / 1000, 2)." KW",
                'essential_appliances' => $essentialAppliances,
                'generator_appliances' => $generatorAppliances,
            ],

            'plan_1' => [
                'title' => 'Budget Solar Setup',
                'inverter_kw' => $plan1Inverter,
                'solar_kw' => $plan1Solar,
                'battery' => false,
                'roi_time' => '27 months',
            ],

            'plan_2' => [
                'title' => 'Ongrid Net Metering',
                'inverter_kw' => $plan1Inverter + 2,
                'solar_kw' => $plan2Solar,
                'surplus_kw' => $surplusKw,
                'monthly_export_units' => $monthlyExportUnits,
                'estimated_income' => $monthlyExportIncome,
                'roi_time' => '32 months',
            ],

            'plan_3' => [
                'title' => 'Hybrid Backup Solution',
                'inverter_kw' => $plan1Inverter + 1,
                'solar_kw' => $plan3Solar,
                'battery_voltage' => $batteryVoltage,
                'battery_ah' => $batteryAh,
                'battery_kwh' => $batteryKwh,
                'backup_hours' => 4,
                'roi_time' => '46 months',
            ],

            'plan_4' => [
                'title' => 'Complete Offgrid Solution',
                'inverter_kw' => $offgridInverter + 1,
                'solar_kw' => $offgridSolar,
                'battery_kwh' => $offgridBatteryKwh,
                'night_hours' => 14,
                'roi_time' => '48 months',
            ],

            'tips' => array_merge(
                $tips,
                [
                    'Clean solar panels every 2-4 weeks.',
                    'Avoid panel shading at all costs.',
                    'Install SPD and proper earthing.',
                    'Use correctly sized DC and AC cables.',
                    'Schedule generator/heavy loads during daytime.',
                ]
            ),

            'warnings' => $warnings,
        ];

        $report->update([
            'report_data' => $report_data
        ]);
    }
}
