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
        dd($request);
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
                    'icon' => $item->appliance->icon,
                    'watts' => $item->watts,
                    'quantity' => $item->quantity,
                    'daily_runtime' => $item->daily_runtime." hours"
                ];
            } else {
                $generatorAppliances[] = [
                    'name' => $item->appliance->name,
                    'icon' => $item->appliance->icon,
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
            'for_gemini' => 'Calculate and update each section according to ai_instruction given in each section and give response in same JSON format as given.',
            'report_id' => 'RV-0900' . $report->id,

            'summary' => [
                "ai_instruction" => "Do not change this section except the tilt angle according to location",
                'essential_load' => $essentialLoad." watts",
                'essential_load_kw' => round($essentialLoad / 1000, 2)." KW",
                'generator_load' => $generatorLoad." watts",
                'generator_load_kw' => round($generatorLoad / 1000, 2)." KW",
                'total_load' => $totalLoad." watts",
                'total_load_kw' => round($totalLoad / 1000, 2)." KW",
                'essential_appliances' => $essentialAppliances,
                'generator_appliances' => $generatorAppliances,
                'location' => 'Rawalpindi',
                'tilt_angle' => 17,
            ],

            'plan_1' => [
                "ai_instruction" => "Update plan_1 according to users essential + generator load, suggest suitable budget friendly inverter with minimum number of solar panels so that user can run his daytime load directly on solar and also give updated prices of items from given location/area.",
                "title" => "Budget Solar Setup",
                "battery" => false,
                "roi_time" => "24 months",
                "solar_kw" => 1,
                "description" => "Lowest cost solar setup designed to run all essential appliances and selected heavy appliances during daylight hours.",
                "inverter_kw" => 1,
                "limitations" => [
                  "No backup during load shedding",
                  "Heavy loads must be used one at a time"
                ],
                "estimated_cost" => 110000,
                "monthly_savings" => 6500,
                "supported_loads" => ["Fans", "Lights", "Fridge"],
                "backup_available" => false,
                "future_upgrade_path" => "Can be upgraded to Hybrid System",
                "recommended_equipment" => [
                  "panel_type" => "N-Type TOPCon",
                  "inverter_type" => "PV Inverter"
                ],
                "net_metering_supported" => false,
                "daytime_supported_loads" => ["Iron", "Motor", "Washing Machine"],
                "recommended_panel_count" => 2,
                "recommended_panel_wattage" => 645,
                "items_list" => [
                    ["name" => "PV Inverter", "quantity" => 1, "price" => 40000],
                    ["name" => "Solar Panels", "quantity" => 4, "price" => 104000],
                    ["name" => "DB Box", "quantity" => 1, "price" => 3000],
                    ["name" => "DC Breaker", "quantity" => 1, "price" => 2000],
                    ["name" => "AC Breaker", "quantity" => 1, "price" => 1200],
                    ["name" => "Voltage Protector", "quantity" => 1, "price" => 2500],
                    ["name" => "DC Wire (4mm) 45 feet double", "quantity" => 1, "price" => 12000],
                    ["name" => "AC Wire 7/0.36 6 feet double", "quantity" => 1, "price" => 600],
                    ["name" => "MC4 Connector", "quantity" => 2, "price" => 300],
                    ["name" => "Galvanized Nuts", "quantity" => 16, "price" => 400],
                    ["name" => "L2 Stand", "quantity" => 2, "price" => 5200],
                    ["name" => "Rawal Bolt", "quantity" => 16, "price" => 5200],
                    ["name" => "Zip tie", "quantity" => 6, "price" => 100],
                    ["name" => "Transportation", "quantity" => 0, "price" => 4000],
                    ["name" => "Installation", "quantity" => 0, "price" => 10000],
                ]
            ],

            'plan_2' => [
                "ai_instruction" => "Update plan_2 according to users load and maximum reduction in electricity bill, suggest suitable ongrid inverter with number of solar panels so that user can get maximum production + update prices of items from given location/area.",
                "title" => "Ongrid Net Metering",
                "roi_time" => "36 months",
                "solar_kw" => 6,
                "surplus_kw" => 3.1,
                "description" => "Best solution for maximum bill reduction and export of excess energy.",
                "inverter_kw" => 3,
                "limitations" => [
                  "No backup during power outage",
                  "Requires approved net metering"
                ],
                "estimated_cost" => 450000,
                "grid_dependency" => true,
                "monthly_savings" => 14000,
                "backup_available" => false,
                "estimated_income" => 4650,
                "future_upgrade_path" => "Can add Hybrid Inverter and Battery",
                "monthly_export_units" => 465,
                "net_metering_supported" => true,
                "recommended_panel_count" => 10,
                "recommended_panel_wattage" => 645,
                "items_list" => [
                    ["name" => "OnGrid Inverter", "quantity" => 1, "price" => 40000],
                    ["name" => "Solar Panels", "quantity" => 4, "price" => 104000],
                    ["name" => "Green Meter", "quantity" => 1, "price" => 120000],
                    ["name" => "DB Box", "quantity" => 1, "price" => 3000],
                    ["name" => "DC Breaker", "quantity" => 1, "price" => 2000],
                    ["name" => "AC Breaker", "quantity" => 1, "price" => 1200],
                    ["name" => "Voltage Protector", "quantity" => 1, "price" => 2500],
                    ["name" => "DC Wire (4mm) 45 feet double", "quantity" => 1, "price" => 12000],
                    ["name" => "AC Wire 7/0.36 6 feet double", "quantity" => 1, "price" => 600],
                    ["name" => "MC4 Connector", "quantity" => 2, "price" => 300],
                    ["name" => "Galvanized Nuts", "quantity" => 16, "price" => 400],
                    ["name" => "L2 Stand", "quantity" => 2, "price" => 5200],
                    ["name" => "Rawal Bolt", "quantity" => 16, "price" => 5200],
                    ["name" => "Zip tie", "quantity" => 6, "price" => 100],
                    ["name" => "Transportation", "quantity" => 0, "price" => 4000],
                    ["name" => "Installation", "quantity" => 0, "price" => 10000],
                ]
            ],

            'plan_3' => [
                "ai_instruction" => "Update plan_3 according to users essential load and backup_hours so that user can get backup on essential load, suggest suitable Hybrid inverter with number of solar panels and battery so that user can get maximum production and backup according to his/her needs.",
                "title" => "Hybrid Backup Solution",
                "roi_time" => "52 months",
                "solar_kw" => 2,
                "battery_ah" => 138,
                "battery_kwh" => 3.3,
                "description" => "Balanced solution with battery backup and reduced grid dependency.",
                "inverter_kw" => 2,
                "limitations" => [
                  "Heavy loads not recommended during backup"
                ],
                "backup_hours" => 4,
                "battery_type" => "Lithium Iron Phosphate",
                "battery_cycles" => 6000,
                "estimated_cost" => 390000,
                "battery_voltage" => 24,
                "monthly_savings" => 9000,
                "future_upgrade_path" => "Expandable to Complete Offgrid",
                "supported_backup_load" => "Essential Load Only",
                "recommended_panel_count" => 4,
                "recommended_panel_wattage" => 645,
                "items_list" => [
                    ["name" => "Hybrid Inverter", "quantity" => 1, "price" => 40000],
                    ["name" => "Solar Panels", "quantity" => 4, "price" => 104000],
                    ["name" => "Battery (2.5kwh)", "quantity" => 1, "price" => 104000],
                    ["name" => "DB Box", "quantity" => 1, "price" => 3000],
                    ["name" => "DC Breaker", "quantity" => 1, "price" => 2000],
                    ["name" => "AC Breaker", "quantity" => 1, "price" => 1200],
                    ["name" => "Voltage Protector", "quantity" => 1, "price" => 2500],
                    ["name" => "DC Wire (4mm) 45 feet double", "quantity" => 1, "price" => 12000],
                    ["name" => "AC Wire 7/0.36 6 feet double", "quantity" => 1, "price" => 600],
                    ["name" => "MC4 Connector", "quantity" => 2, "price" => 300],
                    ["name" => "Galvanized Nuts", "quantity" => 16, "price" => 400],
                    ["name" => "L2 Stand", "quantity" => 2, "price" => 5200],
                    ["name" => "Rawal Bolt", "quantity" => 16, "price" => 5200],
                    ["name" => "Zip tie", "quantity" => 6, "price" => 100],
                    ["name" => "Transportation", "quantity" => 0, "price" => 4000],
                    ["name" => "Installation", "quantity" => 0, "price" => 10000],
                ]
            ],

            'plan_4' => [
                "ai_instruction" => "Update plan_4 according to users essential load + limited generator load like ACs so that user can go fully offgrid, suggest suitable Hybrid/OffGrid inverter with number of solar panels and battery bank so that user can get maximum production and 14 hours backup for all the essential appliances and limited generator appliances.",
                "title" => "Complete Offgrid Solution",
                "roi_time" => "48 months",
                "solar_kw" => 8,
                "battery_kwh" => 56.3,
                "description" => "Maximum energy independence with full day and night operation.",
                "inverter_kw" => 5,
                "limitations" => [
                  "Highest initial investment"
                ],
                "night_hours" => 14,
                "battery_type" => "Lithium Iron Phosphate",
                "battery_cycles" => 6000,
                "estimated_cost" => 1800000,
                "grid_dependency" => false,
                "monthly_savings" => 25560,
                "generator_required" => false,
                "energy_independence" => "95-100%",
                "recommended_panel_count" => 14,
                "recommended_panel_wattage" => 645,
                "future_expansion_capacity_kw" => 2,
                "items_list" => [
                    ["name" => "Hybrid Inverter", "quantity" => 1, "price" => 40000],
                    ["name" => "Solar Panels", "quantity" => 4, "price" => 104000],
                    ["name" => "Battery (16kwh)", "quantity" => 1, "price" => 104000],
                    ["name" => "DB Box", "quantity" => 1, "price" => 3000],
                    ["name" => "DC Breaker", "quantity" => 1, "price" => 2000],
                    ["name" => "AC Breaker", "quantity" => 1, "price" => 1200],
                    ["name" => "Voltage Protector", "quantity" => 1, "price" => 2500],
                    ["name" => "DC Wire (4mm) 45 feet double", "quantity" => 1, "price" => 12000],
                    ["name" => "AC Wire 7/0.36 6 feet double", "quantity" => 1, "price" => 600],
                    ["name" => "MC4 Connector", "quantity" => 2, "price" => 300],
                    ["name" => "Galvanized Nuts", "quantity" => 16, "price" => 400],
                    ["name" => "L2 Stand", "quantity" => 2, "price" => 5200],
                    ["name" => "Rawal Bolt", "quantity" => 16, "price" => 5200],
                    ["name" => "Zip tie", "quantity" => 6, "price" => 100],
                    ["name" => "Transportation", "quantity" => 0, "price" => 4000],
                    ["name" => "Installation", "quantity" => 0, "price" => 10000],
                ]
            ],

            "ai_instruction" => "Personalize sections like site_checklist, solar_analysis, battery_analysis, financial_analysis, personalized_insights, tips and warnings according to users load and installation setup.",

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

            "warnings" => array_merge(
                $warnings,
                [
                    "Water pumps require high startup current. Verify inverter surge rating.",
                    "Iron, heaters and electric stoves should only be used during strong sunlight.",
                    "Battery backup duration decreases if additional appliances are added.",
                    "Do not wash solar panels during peak sunlight hours.",
                    "Improper earthing may damage inverter and panels.",
                    "Roof shading can significantly reduce solar production."
                ]
            ),
            
            "site_checklist" => [
                "Ensure shadow-free roof area.",
                "Install proper earthing.",
                "Install Surge Protection Device.",
                "Use certified solar installer."
            ],

            "solar_analysis" => [
                "average_solar_hours" => 5,
                "expected_panel_life" => "25 years",
                "recommended_panel_size" => "585W",
                "recommended_panel_type" => "N-Type TOPCon",
                "performance_degradation" => "0.4% annually"
            ],
            
            "battery_analysis" => [
                "replacement_cycle" => "10-15 years",
                "expected_life_years" => 12,
                "recommended_technology" => "Lithium Iron Phosphate",
                "daily_backup_energy_kwh" => 8.3
            ],
            
            "financial_analysis" => [
                "electricity_rate" => 60,
                "annual_bill_estimate" => 306720,
                "monthly_bill_estimate" => 25560,
                "monthly_units_consumed" => 426
            ],
            
            "personalized_insights" => [
                "Your refrigerator consumes energy continuously and should remain on the solar-backed circuit.",
                "Your motor requires high startup current and may need a higher surge inverter rating.",
                "Your iron should only be operated during peak solar production hours.",
                "Your load profile is ideal for a Hybrid Solar System."
            ]
        ];

        $report->update([
            'report_data' => $report_data
        ]);
    }
}
