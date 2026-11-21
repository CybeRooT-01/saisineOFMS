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
        Schema::create('saisines', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number', 50)->unique();
            $table->string('title', 255);
            $table->text('description');
            $table->foreignId('request_type_id')->constrained()->onDelete('restrict');
            $table->foreignId('creator_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('current_stage_id')->constrained('workflow_stages')->onDelete('restrict');
            $table->enum('urgency_level', ['critical', 'priority', 'standard']);
            $table->json('custom_fields')->nullable();
            $table->timestamp('stage_entered_at');
            $table->timestamps();
            
            $table->index('reference_number');
            $table->index('current_stage_id');
            $table->index('urgency_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saisines');
    }
};
