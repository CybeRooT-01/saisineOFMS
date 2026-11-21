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
        // Update workflow stage display names to French
        DB::table('workflow_stages')->where('name', 'business_submission')->update([
            'display_name' => 'Soumission Business'
        ]);

        DB::table('workflow_stages')->where('name', 'rjg_qualification')->update([
            'display_name' => 'Qualification RJG'
        ]);

        DB::table('workflow_stages')->where('name', 'compliance_validation')->update([
            'display_name' => 'Validation Conformité'
        ]);

        DB::table('workflow_stages')->where('name', 'drj_study')->update([
            'display_name' => 'Étude DRJ'
        ]);

        DB::table('workflow_stages')->where('name', 'business_partner_validation')->update([
            'display_name' => 'Validation Business/Partenaire'
        ]);

        DB::table('workflow_stages')->where('name', 'final_go_codification')->update([
            'display_name' => 'GO Final & Codification'
        ]);

        DB::table('workflow_stages')->where('name', 'printing_recovery')->update([
            'display_name' => 'Impression & Récupération'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to English names
        DB::table('workflow_stages')->where('name', 'business_submission')->update([
            'display_name' => 'Business Submission'
        ]);

        DB::table('workflow_stages')->where('name', 'rjg_qualification')->update([
            'display_name' => 'RJG Qualification'
        ]);

        DB::table('workflow_stages')->where('name', 'compliance_validation')->update([
            'display_name' => 'Compliance Validation'
        ]);

        DB::table('workflow_stages')->where('name', 'drj_study')->update([
            'display_name' => 'DRJ Study'
        ]);

        DB::table('workflow_stages')->where('name', 'business_partner_validation')->update([
            'display_name' => 'Business Partner Validation'
        ]);

        DB::table('workflow_stages')->where('name', 'final_go_codification')->update([
            'display_name' => 'Final GO Codification'
        ]);

        DB::table('workflow_stages')->where('name', 'printing_recovery')->update([
            'display_name' => 'Printing Recovery'
        ]);
    }
};
