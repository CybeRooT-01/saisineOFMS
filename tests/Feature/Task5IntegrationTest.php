<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Saisine;
use App\Models\User;
use App\Models\WorkflowStage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Integration test for Task 5: Validation workflow and contributor management
 * 
 * This test verifies that all components work together correctly:
 * - ValidationController
 * - ContributorController
 * - Validation views
 * - Contributor management views
 */
class Task5IntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /** @test */
    public function complete_validation_workflow_works()
    {
        // Create a user with RJG role
        $rjgUser = User::factory()->create();
        $rjgRole = Role::where('name', Role::RJG)->first();
        $rjgUser->roles()->attach($rjgRole);

        // Create a saisine at RJG Qualification stage
        $rjgStage = WorkflowStage::where('name', WorkflowStage::RJG_QUALIFICATION)->first();
        $saisine = Saisine::factory()->create([
            'current_stage_id' => $rjgStage->id,
        ]);

        // 1. View the saisine (should show validation actions)
        $response = $this->actingAs($rjgUser)->get(route('saisines.show', $saisine));
        $response->assertOk();
        $response->assertSee('Validation Actions');

        // 2. Validate the saisine
        $response = $this->actingAs($rjgUser)->post(route('saisines.validate', $saisine), [
            'action' => 'validate',
            'comment' => 'Request is properly qualified.',
        ]);
        $response->assertRedirect(route('saisines.show', $saisine));

        // 3. Verify saisine advanced to next stage
        $saisine->refresh();
        $complianceStage = WorkflowStage::where('name', WorkflowStage::COMPLIANCE_VALIDATION)->first();
        $this->assertEquals($complianceStage->id, $saisine->current_stage_id);

        // 4. Verify validation record was created
        $this->assertDatabaseHas('validations', [
            'saisine_id' => $saisine->id,
            'validator_id' => $rjgUser->id,
            'action' => 'validate',
        ]);
    }

    /** @test */
    public function complete_contributor_management_workflow_works()
    {
        // Create a user with RJG role
        $rjgUser = User::factory()->create();
        $rjgRole = Role::where('name', Role::RJG)->first();
        $rjgUser->roles()->attach($rjgRole);

        // Create a saisine at RJG Qualification stage
        $rjgStage = WorkflowStage::where('name', WorkflowStage::RJG_QUALIFICATION)->first();
        $saisine = Saisine::factory()->create([
            'current_stage_id' => $rjgStage->id,
        ]);

        // Create users to add as contributors
        $contributor = User::factory()->create(['name' => 'John Contributor']);
        $observer = User::factory()->create(['name' => 'Jane Observer']);

        // 1. View the saisine (should show contributor management)
        $response = $this->actingAs($rjgUser)->get(route('saisines.show', $saisine));
        $response->assertOk();
        $response->assertSee('Contributors & Observers');

        // 2. Add a contributor
        $response = $this->actingAs($rjgUser)->post(route('saisines.contributors.store', $saisine), [
            'user_id' => $contributor->id,
            'contributor_type' => 'contributor',
        ]);
        $response->assertRedirect(route('saisines.show', $saisine));

        // 3. Verify contributor was added
        $this->assertDatabaseHas('saisine_contributors', [
            'saisine_id' => $saisine->id,
            'user_id' => $contributor->id,
            'contributor_type' => 'contributor',
        ]);

        // 4. Add an observer
        $response = $this->actingAs($rjgUser)->post(route('saisines.contributors.store', $saisine), [
            'user_id' => $observer->id,
            'contributor_type' => 'observer',
        ]);
        $response->assertRedirect(route('saisines.show', $saisine));

        // 5. Verify observer was added
        $this->assertDatabaseHas('saisine_contributors', [
            'saisine_id' => $saisine->id,
            'user_id' => $observer->id,
            'contributor_type' => 'observer',
        ]);

        // 6. View the saisine again (should show contributors and observers)
        $response = $this->actingAs($rjgUser)->get(route('saisines.show', $saisine));
        $response->assertOk();
        $response->assertSee('John Contributor');
        $response->assertSee('Jane Observer');

        // 7. Remove the contributor
        $response = $this->actingAs($rjgUser)->delete(route('saisines.contributors.destroy', [$saisine, $contributor]));
        $response->assertRedirect(route('saisines.show', $saisine));

        // 8. Verify contributor was removed
        $this->assertDatabaseMissing('saisine_contributors', [
            'saisine_id' => $saisine->id,
            'user_id' => $contributor->id,
        ]);
    }

    /** @test */
    public function drj_validation_options_work_correctly()
    {
        // Create a user with DRJ role
        $drjUser = User::factory()->create();
        $drjRole = Role::where('name', Role::DRJ)->first();
        $drjUser->roles()->attach($drjRole);

        // Create a saisine at DRJ Study stage
        $drjStage = WorkflowStage::where('name', WorkflowStage::DRJ_STUDY)->first();
        $saisine = Saisine::factory()->create([
            'current_stage_id' => $drjStage->id,
        ]);

        // 1. View the saisine (should show DRJ-specific validation options)
        $response = $this->actingAs($drjUser)->get(route('saisines.show', $saisine));
        $response->assertOk();
        $response->assertSee('Partial Validation');
        $response->assertSee('Total Validation');

        // 2. Use partial validation
        $response = $this->actingAs($drjUser)->post(route('saisines.validate', $saisine), [
            'action' => 'partial_validation',
            'comment' => 'Partial legal review completed.',
        ]);
        $response->assertRedirect(route('saisines.show', $saisine));

        // 3. Verify validation record was created with correct action
        $this->assertDatabaseHas('validations', [
            'saisine_id' => $saisine->id,
            'validator_id' => $drjUser->id,
            'action' => 'partial_validation',
        ]);

        // 4. Verify saisine advanced to next stage
        $saisine->refresh();
        $businessPartnerStage = WorkflowStage::where('name', WorkflowStage::BUSINESS_PARTNER_VALIDATION)->first();
        $this->assertEquals($businessPartnerStage->id, $saisine->current_stage_id);
    }

    /** @test */
    public function request_completion_workflow_works()
    {
        // Create a user with Compliance Director role
        $complianceUser = User::factory()->create();
        $complianceRole = Role::where('name', Role::COMPLIANCE_DIRECTOR)->first();
        $complianceUser->roles()->attach($complianceRole);

        // Create a saisine at Compliance Validation stage
        $complianceStage = WorkflowStage::where('name', WorkflowStage::COMPLIANCE_VALIDATION)->first();
        $saisine = Saisine::factory()->create([
            'current_stage_id' => $complianceStage->id,
        ]);

        // 1. Request completion
        $response = $this->actingAs($complianceUser)->post(route('saisines.request-completion', $saisine), [
            'reason' => 'Additional compliance documentation needed.',
        ]);
        $response->assertRedirect(route('saisines.show', $saisine));

        // 2. Verify validation record was created
        $this->assertDatabaseHas('validations', [
            'saisine_id' => $saisine->id,
            'validator_id' => $complianceUser->id,
            'action' => 'request_completion',
            'comment' => 'Additional compliance documentation needed.',
        ]);

        // 3. Verify saisine returned to previous stage
        $saisine->refresh();
        $rjgStage = WorkflowStage::where('name', WorkflowStage::RJG_QUALIFICATION)->first();
        $this->assertEquals($rjgStage->id, $saisine->current_stage_id);
    }
}
