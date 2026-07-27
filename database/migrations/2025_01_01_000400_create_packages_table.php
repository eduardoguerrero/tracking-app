<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number', 50)->unique();
            $table->string('description', 255);
            $table->decimal('weight', 8, 2)->nullable();
            $table->string('status', 50)->default('Registered');
            $table->string('delivery_address', 255)->nullable();
            $table->string('recipient_name', 150)->nullable();
            $table->string('recipient_phone', 20)->nullable();
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('courier_id')->nullable()->constrained('couriers')->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index('tracking_number');
            $table->index('status');
            $table->index('branch_id');
            $table->index('courier_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
