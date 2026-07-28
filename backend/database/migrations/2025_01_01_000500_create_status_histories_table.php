<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->string('previous_status', 50)->nullable();
            $table->string('new_status', 50);
            $table->string('comment', 255)->nullable();
            $table->string('location', 150)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('package_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_histories');
    }
};
