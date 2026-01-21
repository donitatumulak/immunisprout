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
         Schema::create('nip_schedule', function (Blueprint $table) {
            $table->id('nip_schedule_id');

            $table->foreignId('nip_vaccine_id')
            ->constrained('vaccines', 'vacc_id')
            ->restrictOnDelete();
            
            $table->integer('nip_dose_number');
            $table->integer('nip_recommended_age_days');
            $table->integer('nip_minimum_age_days');
            $table->integer('nip_maximum_age_days');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nip_schedule');
    }
};
