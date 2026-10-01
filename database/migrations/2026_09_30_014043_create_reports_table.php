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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_number')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('area_id')->constrained()->restrictOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('location_detail');
            $table->text('description');
            $table->enum('urgency', ['low', 'medium', 'high', 'emergency']);
            $table->enum('status', [
                'reported', 'verified', 'in_progress',
                'waiting_material', 'completed', 'rejected',
            ])->default('reported');
            $table->string('photo')->nullable();
            $table->foreignId('assigned_to')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->timestamp('reported_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
