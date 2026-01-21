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
         Schema::create('guardians', function (Blueprint $table) {
            $table->id('grd_id');
            $table->string('grd_first_name', 150);
            $table->string('grd_middle_name', 150)->nullable();
            $table->string('grd_last_name', 150);
            $table->string('grd_contact_number', 20);

            $table->foreignId('grd_current_addr_id')
            ->constrained('addresses', 'addr_id')
            ->restrictOnDelete();
            
            $table->string('grd_relationship', 50)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};
