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
         Schema::create('notifications', function (Blueprint $table) {
            $table->id('notif_id');
            
            $table->foreignId('notif_child_id')
            ->nullable()
            ->constrained('children', 'chd_id')
            ->nullOnDelete();

            $table->foreignId('notif_inventory_id')
            ->nullable()
            ->constrained('vaccine_inventory', 'inv_id')
            ->nullOnDelete();
            
            $table->string('notif_notification_type');
            $table->text('notif_message');
            $table->boolean('notif_is_read')->default(false);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
