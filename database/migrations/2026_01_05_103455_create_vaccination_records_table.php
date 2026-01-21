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
         Schema::create('vaccination_records', function (Blueprint $table) {
            $table->id('rec_id');

            $table->foreignId('rec_child_id')
            ->constrained('children', 'chd_id')
            ->restrictOnDelete();

            $table->foreignId('rec_vaccine_id')
            ->constrained('vaccines', 'vacc_id')
            ->restrictOnDelete();
            
            $table->integer('rec_dose_number');
            $table->date('rec_date_administered')->nullable();
            
            $table->foreignId('rec_administered_by')
            ->nullable()
            ->constrained('health_workers', 'wrk_id')
            ->nullOnDelete();
            
            $table->text('rec_remarks')->nullable();

            $table->string('rec_status');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaccination_records');
    }
};
