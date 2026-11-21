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
        Schema::create('saisine_contributors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saisine_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('contributor_type', ['contributor', 'observer']);
            $table->foreignId('added_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();
            
            $table->unique(['saisine_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saisine_contributors');
    }
};
