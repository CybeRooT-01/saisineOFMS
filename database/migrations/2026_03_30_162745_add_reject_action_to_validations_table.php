<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the enum to add 'reject' action
        DB::statement("ALTER TABLE validations MODIFY COLUMN action ENUM('validate', 'request_completion', 'partial_validation', 'total_validation', 'reject') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'reject' from the enum
        DB::statement("ALTER TABLE validations MODIFY COLUMN action ENUM('validate', 'request_completion', 'partial_validation', 'total_validation') NOT NULL");
    }
};
