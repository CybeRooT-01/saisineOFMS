<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Saisine;
use App\Models\User;
use App\Models\Validation;
use App\Models\WorkflowStage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /** @test */
    public function authorized_user_can_validate_saisine()
    {
        // Create a user with RJG role
        $user = User::factory()->create();
        $rjgRole = Role::where('name', Role::RJG)->first();
        $user->roles()->attach($rjgRole);

        // Create a saisine at RJG Qualification stage
        $rjgStage = WorkflowStage::where('name', WorkflowStage::RJG_QUALIFICATION)->first();
        $saisine = Saisine::factory()->create([
            'current_stage_id' => $rjgStage->id,
        ]);

        // Validate the saisine
        $response = $this->actingAs($user)->post(route('saisines.validate', $saisine), [
            'action' => 'validate',
            'comment' => 'This request looks good.',
        ]);

        $response->assertRedirect(route('saisines.show', $saisine));
        $response->assertSessionHas('success');

        // Check that validation was created
        $this->assertDatabaseHas('validations', [
            'saisine_id' => $saisine->id,
            'validator_id' => $user->id,
            'action' => 'validate',
            'comment' => 'This request looks good.',
        ]);

        // Check that saisine advanced to next stage
        $saisine->refresh();
        $complianceStage = WorkflowStage::where('name', WorkflowStage::COMPLIANCE_VALIDATION)->first();
        $this->assertEquals($complianceStage->id, $saisine->current_stage_id);
    }

    /** @test */
    public function unauthorized_user_cannot_validate_saisine()
    {
        // Create a user without appropriate role
        $user = User::factory()->create();

        // Create a saisine at RJG Qualification stage
        $rjgStage = WorkflowStage::where('name', WorkflowStage::RJG_QUALIFICATION)->first();
        $saisine = Saisine::factory()->create([
            'current_stage_id' => $rjgStage->id,
        ]);

        // Attempt to validate
        $response = $this->actingAs($user)->post(route('saisines.validate', $saisine), [
            'action' => 'validate',
            'comment' => 'This request looks good.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Check that validation was not created
        $this->assertDatabaseMissing('validations', [
            'saisine_id' => $saisine->id,
            'validator_id' => $user->id,
        ]);
    }

    /** @test */
    public function validation_requires_comment()
    {
        // Create a user with RJG role
        $user = User::factory()->create();
        $rjgRole = Role::where('name', Role::RJG)->first();
        $user->roles()->attach($rjgRole);

        // Create a saisine at RJG Qualification stage
        $rjgStage = WorkflowStage::where('name', WorkflowStage::RJG_QUALIFICATION)->first();
        $saisine = Saisine::factory()->create([
            'current_stage_id' => $rjgStage->id,
        ]);

        // Attempt to validate without comment
        $response = $this->actingAs($user)->post(route('saisines.validate', $saisine), [
            'action' => 'validate',
            'comment' => '',
        ]);

        $response->assertSessionHasErrors('comment');
    }

    /** @test */
    public function drj_user_can_use_partial_validation_at_drj_study_stage()
    {
        // Create a user with DRJ role
        $user = User::factory()->create();
        $drjRole = Role::where('name', Role::DRJ)->first();
        $user->roles()->attach($drjRole);

        // Create a saisine at DRJ Study stage
        $drjStage = WorkflowStage::where('name', WorkflowStage::DRJ_STUDY)->first();
        $saisine = Saisine::factory()->create([
            'current_stage_id' => $drjStage->id,
        ]);

        // Use partial validation
        $response = $this->actingAs($user)->post(route('saisines.validate', $saisine), [
            'action' => 'partial_validation',
            'comment' => 'Partial validation completed.',
        ]);

        $response->assertRedirect(route('saisines.show', $saisine));
        $response->assertSessionHas('success');

        // Check that validation was created with correct action
        $this->assertDatabaseHas('validations', [
            'saisine_id' => $saisine->id,
            'validator_id' => $user->id,
            'action' => 'partial_validation',
        ]);
    }

    /** @test */
    public function drj_specific_actions_only_available_at_drj_study_stage()
    {
        // Create a user with RJG role
        $user = User::factory()->create();
        $rjgRole = Role::where('name', Role::RJG)->first();
        $user->roles()->attach($rjgRole);

        // Create a saisine at RJG Qualification stage (not DRJ Study)
        $rjgStage = WorkflowStage::where('name', WorkflowStage::RJG_QUALIFICATION)->first();
        $saisine = Saisine::factory()->create([
            'current_stage_id' => $rjgStage->id,
        ]);

        // Attempt to use partial validation at non-DRJ stage
        $response = $this->actingAs($user)->post(route('saisines.validate', $saisine), [
            'action' => 'partial_validation',
            'comment' => 'Attempting partial validation.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function authorized_user_can_request_completion()
    {
        // Create a user with RJG role
        $user = User::factory()->create();
        $rjgRole = Role::where('name', Role::RJG)->first();
        $user->roles()->attach($rjgRole);

        // Create a saisine at RJG Qualification stage
        $rjgStage = WorkflowStage::where('name', WorkflowStage::RJG_QUALIFICATION)->first();
        $saisine = Saisine::factory()->create([
            'current_stage_id' => $rjgStage->id,
        ]);

        // Request completion
        $response = $this->actingAs($user)->post(route('saisines.request-completion', $saisine), [
            'reason' => 'Additional information needed.',
        ]);

        $response->assertRedirect(route('saisines.show', $saisine));
        $response->assertSessionHas('success');

        // Check that validation was created
        $this->assertDatabaseHas('validations', [
            'saisine_id' => $saisine->id,
            'validator_id' => $user->id,
            'action' => Validation::ACTION_REQUEST_COMPLETION,
            'comment' => 'Additional information needed.',
        ]);

        // Check that saisine returned to previous stage
        $saisine->refresh();
        $businessStage = WorkflowStage::where('name', WorkflowStage::BUSINESS_SUBMISSION)->first();
        $this->assertEquals($businessStage->id, $saisine->current_stage_id);
    }

    /** @test */
    public function request_completion_requires_reason()
    {
        // Create a user with RJG role
        $user = User::factory()->create();
        $rjgRole = Role::where('name', Role::RJG)->first();
        $user->roles()->attach($rjgRole);

        // Create a saisine at RJG Qualification stage
        $rjgStage = WorkflowStage::where('name', WorkflowStage::RJG_QUALIFICATION)->first();
        $saisine = Saisine::factory()->create([
            'current_stage_id' => $rjgStage->id,
        ]);

        // Attempt to request completion without reason
        $response = $this->actingAs($user)->post(route('saisines.request-completion', $saisine), [
            'reason' => '',
        ]);

        $response->assertSessionHasErrors('reason');
    }

    /** @test */
    public function unauthorized_user_cannot_request_completion()
    {
        // Create a user without appropriate role
        $user = User::factory()->create();

        // Create a saisine at RJG Qualification stage
        $rjgStage = WorkflowStage::where('name', WorkflowStage::RJG_QUALIFICATION)->first();
        $saisine = Saisine::factory()->create([
            'current_stage_id' => $rjgStage->id,
        ]);

        // Attempt to request completion
        $response = $this->actingAs($user)->post(route('saisines.request-completion', $saisine), [
            'reason' => 'Additional information needed.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Check that validation was not created
        $this->assertDatabaseMissing('validations', [
            'saisine_id' => $saisine->id,
            'validator_id' => $user->id,
        ]);
    }
}
