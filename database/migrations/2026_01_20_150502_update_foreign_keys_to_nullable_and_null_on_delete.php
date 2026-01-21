<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update HEALTH_WORKERS table
        Schema::table('health_workers', function (Blueprint $table) {
            // Drop old constraint (You must match the exact foreign key name)
            $table->dropForeign(['wrk_addr_id']);
            
            // Re-add as Nullable with NullOnDelete
            $table->unsignedBigInteger('wrk_addr_id')->nullable()->change();
            $table->foreign('wrk_addr_id')
                  ->references('addr_id')->on('addresses')
                  ->onDelete('set null');
        });

        // 2. Update GUARDIANS table
        Schema::table('guardians', function (Blueprint $table) {
            $table->dropForeign(['grd_current_addr_id']);
            
            $table->unsignedBigInteger('grd_current_addr_id')->nullable()->change();
            $table->foreign('grd_current_addr_id')
                  ->references('addr_id')->on('addresses')
                  ->onDelete('set null');
        });

        // 3. Update CHILDREN table
        Schema::table('children', function (Blueprint $table) {
            $table->dropForeign(['chd_current_addr_id']);
            
            $table->unsignedBigInteger('chd_current_addr_id')->nullable()->change();
            $table->foreign('chd_current_addr_id')
                  ->references('addr_id')->on('addresses')
                  ->onDelete('set null');
        });

        // 4. Update INVENTORY_LOGS (Set to Cascade)
        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->dropForeign(['log_inventory_id']);
            
            $table->foreign('log_inventory_id')
                  ->references('inv_id')->on('vaccine_inventory')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Reverse changes back to Restrict if needed
        Schema::table('health_workers', function (Blueprint $table) {
            $table->dropForeign(['wrk_addr_id']);
            $table->unsignedBigInteger('wrk_addr_id')->nullable(false)->change();
            $table->foreign('wrk_addr_id')->references('addr_id')->on('addresses')->onDelete('restrict');
        });
        
        Schema::table('guardians', function (Blueprint $table) {
            $table->dropForeign(['grd_current_addr_id']);
            $table->unsignedBigInteger('grd_current_addr_id')->nullable(false)->change();
            $table->foreign('grd_current_addr_id')->references('addr_id')->on('addresses')->onDelete('restrict');
        });

        Schema::table('children', function (Blueprint $table) {
            $table->dropForeign(['chd_current_addr_id']);
            $table->unsignedBigInteger('chd_current_addr_id')->nullable(false)->change();
            $table->foreign('chd_current_addr_id')->references('addr_id')->on('addresses')->onDelete('restrict');
        });
        
        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->dropForeign(['log_inventory_id']);
            $table->foreign('log_inventory_id')->references('inv_id')->on('vaccine_inventory')->onDelete('restrict');
        });
    }
};