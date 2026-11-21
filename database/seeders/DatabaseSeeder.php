<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles
        $roles = [
            ['name' => 'business_team', 'display_name' => 'Business Team', 'description' => 'Creates requests and validates at Business/Partner stage'],
            ['name' => 'rjg', 'display_name' => 'RJG', 'description' => 'Regulation, Compliance, and Governance Manager who qualifies incoming requests'],
            ['name' => 'compliance_director', 'display_name' => 'Compliance Director', 'description' => 'Director who validates qualified requests before legal review'],
            ['name' => 'drj', 'display_name' => 'DRJ', 'description' => 'Legal Department that performs legal analysis'],
            ['name' => 'internal_contributor', 'display_name' => 'Internal Contributor', 'description' => 'Subject matter expert added to assist at any workflow stage'],
            ['name' => 'observer', 'display_name' => 'Observer', 'description' => 'User with read-only access to track request progress'],
        ];

        foreach ($roles as $role) {
            \DB::table('roles')->insert(array_merge($role, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Seed workflow stages
        $stages = [
            ['name' => 'business_submission', 'display_name' => 'Business Submission', 'sequence_order' => 1, 'expected_duration_days' => 2, 'required_roles' => json_encode(['business_team'])],
            ['name' => 'rjg_qualification', 'display_name' => 'RJG Qualification', 'sequence_order' => 2, 'expected_duration_days' => 3, 'required_roles' => json_encode(['rjg'])],
            ['name' => 'compliance_validation', 'display_name' => 'Compliance Validation', 'sequence_order' => 3, 'expected_duration_days' => 5, 'required_roles' => json_encode(['compliance_director'])],
            ['name' => 'drj_study', 'display_name' => 'DRJ Study', 'sequence_order' => 4, 'expected_duration_days' => 10, 'required_roles' => json_encode(['drj'])],
            ['name' => 'business_partner_validation', 'display_name' => 'Business/Partner Validation', 'sequence_order' => 5, 'expected_duration_days' => 5, 'required_roles' => json_encode(['business_team'])],
            ['name' => 'final_go_codification', 'display_name' => 'Final GO & Codification', 'sequence_order' => 6, 'expected_duration_days' => 3, 'required_roles' => json_encode(['rjg', 'compliance_director'])],
            ['name' => 'printing_recovery', 'display_name' => 'Printing & Recovery', 'sequence_order' => 7, 'expected_duration_days' => 2, 'required_roles' => json_encode(['business_team'])],
        ];

        foreach ($stages as $stage) {
            \DB::table('workflow_stages')->insert(array_merge($stage, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Seed request types
        $requestTypes = [
            ['name' => 'contract', 'display_name' => 'Contract', 'description' => 'New contract creation or review'],
            ['name' => 'amendment', 'display_name' => 'Amendment', 'description' => 'Modification to existing contract'],
            ['name' => 'legal_opinion', 'display_name' => 'Legal Opinion', 'description' => 'Request for legal advice or opinion'],
            ['name' => 'compliance_review', 'display_name' => 'Compliance Review', 'description' => 'Compliance assessment or review'],
            ['name' => 'regulatory_inquiry', 'display_name' => 'Regulatory Inquiry', 'description' => 'Question or inquiry about regulatory requirements'],
        ];

        foreach ($requestTypes as $type) {
            \DB::table('request_types')->insert(array_merge($type, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Create test user with business_team role
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Assign business_team role to test user
        $businessTeamRole = \DB::table('roles')->where('name', 'business_team')->first();
        \DB::table('role_user')->insert([
            'user_id' => $user->id,
            'role_id' => $businessTeamRole->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
