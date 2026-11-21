<?php

namespace Tests\Feature\Services;

use App\Models\Role;
use App\Models\Saisine;
use App\Models\User;
use App\Models\Validation;
use App\Models\WorkflowStage;
use App\Services\NotificationService;
use App\Services\VersioningService;
use App\Services\WorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class WorkflowServiceIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected WorkflowService $workflowService;

    protected function setUp(): void
    {
        parent::setUp();

        // Use real services for integration testing
        $this->workflowService = app(WorkflowService::class);
    }

    public function test_validate_and_advance_workflow_integration(): void
    {
        Notification::fake();

        // Create workflow stages in sequence
        $stage1 = WorkflowStage::create([
            'name' => WorkflowStage::BUSINESS_SUBMISSION,
            'display_name' => 'Business Submission',
            'sequence_order' => 1,
            'expected_duration_days' => 5,
            'required_roles' => [Role::BUSINESS_TEAM],
        ]);

        $stage2 = WorkflowStage::create([
            'name' => WorkflowStage::RJG_QUALIFICATION,
            'display_name' => 'RJG Qualification',
            'sequence_order' => 2,
            'expected_duration_days' => 3,
            'required_roles' => [Role::RJG],
        ]);

        // Create roles
        $businessRole = Role::create([
            'name' => Role::BUSINESS_TEAM,
            'display_name' => 'Business Team',
        ]);

        $rjgRole = Role::create([
            'name' => Role::RJG,
            'display_name' => 'RJG',
        ]);

        // Create users
        $businessUser = User::factory()->create();
        $businessUser->roles()->attach($businessRole);

        $rjgUser = User::factory()->create();
        $rjgUser->roles()->attach($rjgRole);

        // Create saisine at stage 1
        $saisine = Saisine::factory()->create([
            'current_stage_id' => $stage1->id,
            'creator_id' => $businessUser->id,
            'stage_entered_at' => now(),
        ]);

        // Business user validates and advances to RJG stage
        $this->workflowService->validateAndAdvance(
            $saisine,
            $businessUser,
            Validation::ACTION_VALIDATE,
            'Initial submission complete'
        );

        // Refresh saisine
        $saisine->refresh();

        // Assert saisine advanced to stage 2
        $this->assertEquals($stage2->id, $saisine->current_stage_id);

        // Assert validation record was created
        $this->assertDatabaseHas('validations', [
            'saisine_id' => $saisine->id,
            'validator_id' => $businessUser->id,
            'stage_id' => $stage1->id,
            'action' => Validation::ACTION_VALIDATE,
        ]);

        // Assert version was created
        $this->assertDatabaseHas('versions', [
            'saisine_id' => $saisine->id,
            'author_id' => $businessUser->id,
        ]);
    }

    public function test_request_completion_workflow_integration(): void
    {
        Notification::fake();

        // Create workflow stages in sequence
        $stage1 = WorkflowStage::create([
            'name' => WorkflowStage::BUSINESS_SUBMISSION,
            'display_name' => 'Business Submission',
            'sequence_order' => 1,
            'expected_duration_days' => 5,
            'required_roles' => [Role::BUSINESS_TEAM],
        ]);

        $stage2 = WorkflowStage::create([
            'name' => WorkflowStage::RJG_QUALIFICATION,
            'display_name' => 'RJG Qualification',
            'sequence_order' => 2,
            'expected_duration_days' => 3,
            'required_roles' => [Role::RJG],
        ]);

        // Create roles
        $rjgRole = Role::create([
            'name' => Role::RJG,
            'display_name' => 'RJG',
        ]);

        // Create users
        $creator = User::factory()->create();
        $rjgUser = User::factory()->create();
        $rjgUser->roles()->attach($rjgRole);

        // Create saisine at stage 2
        $saisine = Saisine::factory()->create([
            'current_stage_id' => $stage2->id,
            'creator_id' => $creator->id,
            'stage_entered_at' => now(),
        ]);

        // RJG user requests completion
        $this->workflowService->requestCompletion(
            $saisine,
            $rjgUser,
            'Need more information about the contract terms'
        );

        // Refresh saisine
        $saisine->refresh();

        // Assert saisine returned to stage 1
        $this->assertEquals($stage1->id, $saisine->current_stage_id);

        // Assert validation record was created
        $this->assertDatabaseHas('validations', [
            'saisine_id' => $saisine->id,
            'validator_id' => $rjgUser->id,
            'stage_id' => $stage2->id,
            'action' => Validation::ACTION_REQUEST_COMPLETION,
            'comment' => 'Need more information about the contract terms',
        ]);
    }
}
