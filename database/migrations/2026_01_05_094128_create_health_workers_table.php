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
         Schema::create('health_workers', function (Blueprint $table) {
            $table->id('wrk_id');
            $table->string('wrk_first_name', 150);
            $table->string('wrk_middle_name', 150)->nullable();
            $table->string('wrk_last_name', 150);
            $table->string('wrk_contact_number', 20);
            
            $table->foreignId('wrk_addr_id')
            ->constrained('addresses', 'addr_id')
            ->restrictOnDelete();

            $table->string('wrk_role', 50);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_workers');
    }
};
