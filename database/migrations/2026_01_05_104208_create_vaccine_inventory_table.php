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
         Schema::create('vaccine_inventory', function (Blueprint $table) {
            $table->id('inv_id');
            
            $table->foreignId('inv_vaccine_id')
                ->constrained('vaccines', 'vacc_id')
                ->restrictOnDelete();

            $table->string('inv_batch_number', 50);
            $table->integer('inv_quantity_available')->default(0);
            $table->date('inv_expiry_date')->nullable();
            $table->date('inv_received_date')->nullable();
            $table->string('inv_source')->nullable()->default('DOH Central'); 
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaccine_inventory');
    }
};
