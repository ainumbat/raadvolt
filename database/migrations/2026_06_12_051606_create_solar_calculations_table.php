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
        Schema::create('solar_calculations', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->nullable();
            $table->string('whatsapp')->nullable();

            $table->integer('total_load')->default(0);
            $table->integer('essential_load')->default(0);
            $table->integer('generator_load')->default(0);
            $table->integer('wire_length')->default(15);
            $table->integer('backup_hours')->default(0);
            
            $table->jsonb('report_data')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solar_calculations');
    }
};
