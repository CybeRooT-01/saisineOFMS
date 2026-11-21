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
        Schema::create('mandatory_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_type_id')->constrained()->onDelete('cascade');
            $table->string('field_name', 100);
            $table->string('field_type', 50);
            $table->string('field_label', 255);
            $table->text('validation_rules')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mandatory_fields');
    }
};
