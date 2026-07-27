<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number', 20)->unique();
            $table->string('type', 50);
            $table->string('brand', 50)->nullable();
            $table->string('model', 50)->nullable();
            $table->foreignId('courier_id')->nullable()->constrained('couriers')->nullOnDelete();
            $table->foreignId('branch_id')->constrained('branches');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('courier_id');
            $table->index('branch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
