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
       Schema::create('addresses', function (Blueprint $table) {
            $table->id('addr_id');
            $table->string('addr_line_1', 150);
            $table->string('addr_line_2', 150)->nullable();
            $table->string('addr_barangay', 150);
            $table->string('addr_city_municipality', 150);
            $table->string('addr_province', 150);
            $table->string('addr_zip_code', 10)->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
