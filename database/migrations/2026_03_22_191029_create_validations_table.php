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
        Schema::create('validations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saisine_id')->constrained()->onDelete('cascade');
            $table->foreignId('validator_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('stage_id')->constrained('workflow_stages')->onDelete('restrict');
            $table->enum('action', ['validate', 'request_completion', 'partial_validation', 'total_validation']);
            $table->text('comment');
            $table->timestamp('created_at');
            
            $table->index('saisine_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validations');
    }
};
