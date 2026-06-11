<?php

namespace Database\Seeders;

use App\Models\Appliance;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ApplianceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Ceiling Fan', 'watts' => 40, 'type' => 'essential', 'icon' => 'Fan'],
            ['name' => 'LED Light', 'watts' => 12, 'type' => 'essential', 'icon' => 'Lightbulb'],
            ['name' => 'Bracket Fan', 'watts' => 60, 'type' => 'essential', 'icon' => 'Wind'],
            ['name' => 'Stand Fan', 'watts' => 80, 'type' => 'generator', 'icon' => 'Wind'],
            ['name' => 'Air Cooler', 'watts' => 200, 'type' => 'generator', 'icon' => 'Wind'],
            ['name' => 'Fridge', 'watts' => 200, 'type' => 'essential', 'icon' => 'Refrigerator'],
            ['name' => 'Deep Freezer', 'watts' => 300, 'type' => 'generator', 'icon' => 'Refrigerator'],
            ['name' => 'Iron', 'watts' => 1000, 'type' => 'generator', 'icon' => 'Flame'],
            ['name' => 'Motor', 'watts' => 800, 'type' => 'generator', 'icon' => 'Cpu'],
            ['name' => 'Washing Machine', 'watts' => 500, 'type' => 'generator', 'icon' => 'WashingMachine'],
            ['name' => 'Microwave', 'watts' => 1200, 'type' => 'generator', 'icon' => 'Flame'],
            ['name' => 'Air Fryer', 'watts' => 1800, 'type' => 'generator', 'icon' => 'Flame'],
            ['name' => 'Desktop Computer', 'watts' => 120, 'type' => 'generator', 'icon' => 'Monitor'],
            ['name' => 'Laptop', 'watts' => 65, 'type' => 'generator', 'icon' => 'Laptop'],
            ['name' => 'Security Camera', 'watts' => 25, 'type' => 'essential', 'icon' => 'Camera'],
            ['name' => 'WiFi Router', 'watts' => 10, 'type' => 'essential', 'icon' => 'Router'],
            ['name' => 'Air Conditioner', 'watts' => 2000, 'type' => 'generator', 'icon' => 'AirVent'],
            ['name' => 'Electric Geyser', 'watts' => 2500, 'type' => 'generator', 'icon' => 'Heater'],
            ['name' => 'UPS', 'watts' => 800, 'type' => 'generator', 'icon' => 'SmartphoneCharging'],
            ['name' => 'Electric Stove', 'watts' => 1800, 'type' => 'generator', 'icon' => 'Flame'],
            ['name' => 'Tube Light', 'watts' => 35, 'type' => 'essential', 'icon' => 'Lightbulb'],
            ['name' => 'Custom Appliance', 'watts' => 500, 'type' => 'generator', 'icon' => 'Plus'],
        ];

        foreach ($data as $item) {
            Appliance::create($item);
        }
    }
}
