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
       Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id('log_id');

            $table->foreignId('log_user_id')
                ->constrained('users', 'id')
                ->restrictOnDelete();

            $table->foreignId('log_inventory_id')
                ->constrained('vaccine_inventory', 'inv_id')
                ->restrictOnDelete();

            $table->string('log_change_type');
            $table->integer('log_quantity_changed'); 

            $table->nullableMorphs('logable'); 

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_log');
    }
};
