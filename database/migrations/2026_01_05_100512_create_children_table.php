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
        Schema::create('children', function (Blueprint $table) {
            $table->id('chd_id');
            $table->string('chd_first_name', 150);
            $table->string('chd_middle_name', 150)->nullable();
            $table->string('chd_last_name', 150);
            $table->date('chd_date_of_birth');
            $table->string('chd_sex', 10);
	        $table->string('chd_residency_status', 50);
     
            $table->foreignId('chd_current_addr_id')
            ->constrained('addresses', 'addr_id')
            ->restrictOnDelete();

            $table->foreignId('chd_guardian_id')
            ->nullable()
            ->constrained('guardians', 'grd_id')
            ->nullOnDelete();

            $table->string('chd_status')->default('active');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('childrens');
    }
};
