<?php

namespace Tests\Unit\Services;

use App\Models\Role;
use App\Models\Saisine;
use App\Models\User;
use App\Models\Validation;
use App\Models\WorkflowStage;
use App\Services\NotificationService;
use App\Services\VersioningService;
use App\Services\WorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkflowServiceTest extends TestCase
{
    use RefreshDatabase;

    protected WorkflowService $workflowService;
    protected NotificationService $notificationService;
    protected VersioningService $versioningService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->notificationService = $this->createMock(NotificationService::class);
        $this->versioningService = $this->createMock(VersioningService::class);
        $this->workflowService = new WorkflowService(
            $this->notificationService,
            $this->versioningService
        );
    }

    public function test_can_user_validate_returns_true_when_user_has_required_role(): void
    {
        // Create workflow stages
        $stage = WorkflowStage::factory()->create([
            'name' => WorkflowStage::RJG_QUALIFICATION,
            'required_roles' => [Role::RJG],
        ]);

        // Create user with RJG role
        $role = Role::factory()->create(['name' => Role::RJG]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        // Create saisine at RJG stage
        $saisine = Saisine::factory()->create([
            'current_stage_id' => $stage->id,
        ]);

        $result = $this->workflowService->canUserValidate($saisine, $user);

        $this->assertTrue($result);
    }

    public function test_can_user_validate_returns_false_when_user_lacks_required_role(): void
    {
        // Create workflow stages
        $stage = WorkflowStage::factory()->create([
            'name' => WorkflowStage::RJG_QUALIFICATION,
            'required_roles' => [Role::RJG],
        ]);

        // Create user without RJG role
        $user = User::factory()->create();

        // Create saisine at RJG stage
        $saisine = Saisine::factory()->create([
            'current_stage_id' => $stage->id,
        ]);

        $result = $this->workflowService->canUserValidate($saisine, $user);

        $this->assertFalse($result);
    }

    public function test_validate_and_advance_throws_exception_when_user_not_authorized(): void
    {
        // Create workflow stages
        $stage = WorkflowStage::factory()->create([
            'name' => WorkflowStage::RJG_QUALIFICATION,
            'required_roles' => [Role::RJG],
        ]);

        // Create user without required role
        $user = User::factory()->create();

        // Create saisine
        $saisine = Saisine::factory()->create([
            'current_stage_id' => $stage->id,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User is not authorized to validate this saisine at the current stage');

        $this->workflowService->validateAndAdvance($saisine, $user, Validation::ACTION_VALIDATE, 'Test comment');
    }

    public function test_validate_and_advance_throws_exception_for_invalid_action(): void
    {
        // Create workflow stages
        $stage = WorkflowStage::factory()->create([
            'name' => WorkflowStage::RJG_QUALIFICATION,
            'required_roles' => [Role::RJG],
        ]);

        // Create user with required role
        $role = Role::factory()->create(['name' => Role::RJG]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        // Create saisine
        $saisine = Saisine::factory()->create([
            'current_stage_id' => $stage->id,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid validation action');

        $this->workflowService->validateAndAdvance($saisine, $user, 'invalid_action', 'Test comment');
    }

    public function test_request_completion_throws_exception_when_reason_is_empty(): void
    {
        // Create workflow stages
        $stage = WorkflowStage::factory()->create([
            'name' => WorkflowStage::RJG_QUALIFICATION,
            'required_roles' => [Role::RJG],
            'sequence_order' => 2,
        ]);

        // Create user with required role
        $role = Role::factory()->create(['name' => Role::RJG]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        // Create saisine
        $saisine = Saisine::factory()->create([
            'current_stage_id' => $stage->id,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('A comment is required when requesting completion');

        $this->workflowService->requestCompletion($saisine, $user, '');
    }

    public function test_get_next_stage_returns_correct_stage(): void
    {
        $currentStage = WorkflowStage::factory()->create(['sequence_order' => 1]);
        $nextStage = WorkflowStage::factory()->create(['sequence_order' => 2]);

        $result = $this->workflowService->getNextStage($currentStage);

        $this->assertNotNull($result);
        $this->assertEquals($nextStage->id, $result->id);
    }

    public function test_get_previous_stage_returns_correct_stage(): void
    {
        $previousStage = WorkflowStage::factory()->create(['sequence_order' => 1]);
        $currentStage = WorkflowStage::factory()->create(['sequence_order' => 2]);

        $result = $this->workflowService->getPreviousStage($currentStage);

        $this->assertNotNull($result);
        $this->assertEquals($previousStage->id, $result->id);
    }
}
