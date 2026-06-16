<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('solar_calculation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solar_calculation_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('appliance_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->integer('watts');
            $table->integer('quantity');
            $table->float('daily_runtime')->default(1.5);
            $table->string('type')->default('essential');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solar_calculation_items');
    }
};
