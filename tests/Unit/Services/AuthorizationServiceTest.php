<?php

namespace Tests\Unit\Services;

use App\Models\Role;
use App\Models\Saisine;
use App\Models\User;
use App\Models\WorkflowStage;
use App\Services\AuthorizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AuthorizationService $authorizationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authorizationService = new AuthorizationService();
    }

    public function test_can_view_saisine_returns_true_for_creator(): void
    {
        $user = User::factory()->create();
        $saisine = Saisine::factory()->create(['creator_id' => $user->id]);

        $result = $this->authorizationService->canViewSaisine($user, $saisine);

        $this->assertTrue($result);
    }

    public function test_can_view_saisine_returns_true_for_contributor(): void
    {
        $user = User::factory()->create();
        $creator = User::factory()->create();
        $saisine = Saisine::factory()->create(['creator_id' => $creator->id]);
        
        // Add user as contributor
        $saisine->contributors()->attach($user, ['contributor_type' => 'contributor', 'added_by' => $creator->id]);

        $result = $this->authorizationService->canViewSaisine($user, $saisine);

        $this->assertTrue($result);
    }

    public function test_can_view_saisine_returns_true_for_observer(): void
    {
        $user = User::factory()->create();
        $creator = User::factory()->create();
        $saisine = Saisine::factory()->create(['creator_id' => $creator->id]);
        
        // Add user as observer
        $saisine->observers()->attach($user, ['contributor_type' => 'observer', 'added_by' => $creator->id]);

        $result = $this->authorizationService->canViewSaisine($user, $saisine);

        $this->assertTrue($result);
    }

    public function test_can_view_saisine_returns_true_for_user_with_stage_role(): void
    {
        $stage = WorkflowStage::factory()->create([
            'required_roles' => [Role::RJG],
        ]);
        $role = Role::factory()->create(['name' => Role::RJG]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $saisine = Saisine::factory()->create(['current_stage_id' => $stage->id]);

        $result = $this->authorizationService->canViewSaisine($user, $saisine);

        $this->assertTrue($result);
    }

    public function test_can_view_saisine_returns_false_for_unauthorized_user(): void
    {
        $user = User::factory()->create();
        $creator = User::factory()->create();
        $saisine = Saisine::factory()->create(['creator_id' => $creator->id]);

        $result = $this->authorizationService->canViewSaisine($user, $saisine);

        $this->assertFalse($result);
    }

    public function test_can_edit_saisine_returns_true_for_creator(): void
    {
        $user = User::factory()->create();
        $saisine = Saisine::factory()->create(['creator_id' => $user->id]);

        $result = $this->authorizationService->canEditSaisine($user, $saisine);

        $this->assertTrue($result);
    }

    public function test_can_edit_saisine_returns_true_for_contributor(): void
    {
        $user = User::factory()->create();
        $creator = User::factory()->create();
        $saisine = Saisine::factory()->create(['creator_id' => $creator->id]);
        
        // Add user as contributor
        $saisine->contributors()->attach($user, ['contributor_type' => 'contributor', 'added_by' => $creator->id]);

        $result = $this->authorizationService->canEditSaisine($user, $saisine);

        $this->assertTrue($result);
    }

    public function test_can_edit_saisine_returns_false_for_observer(): void
    {
        $user = User::factory()->create();
        $creator = User::factory()->create();
        $saisine = Saisine::factory()->create(['creator_id' => $creator->id]);
        
        // Add user as observer
        $saisine->observers()->attach($user, ['contributor_type' => 'observer', 'added_by' => $creator->id]);

        $result = $this->authorizationService->canEditSaisine($user, $saisine);

        // Observers should not be able to edit
        $this->assertFalse($result);
    }

    public function test_can_validate_saisine_returns_true_for_user_with_required_role(): void
    {
        $stage = WorkflowStage::factory()->create([
            'required_roles' => [Role::RJG],
        ]);
        $role = Role::factory()->create(['name' => Role::RJG]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $saisine = Saisine::factory()->create(['current_stage_id' => $stage->id]);

        $result = $this->authorizationService->canValidateSaisine($user, $saisine);

        $this->assertTrue($result);
    }

    public function test_can_validate_saisine_returns_false_for_user_without_required_role(): void
    {
        $stage = WorkflowStage::factory()->create([
            'required_roles' => [Role::RJG],
        ]);
        $user = User::factory()->create();

        $saisine = Saisine::factory()->create(['current_stage_id' => $stage->id]);

        $result = $this->authorizationService->canValidateSaisine($user, $saisine);

        $this->assertFalse($result);
    }

    public function test_can_add_contributors_returns_true_for_creator(): void
    {
        $user = User::factory()->create();
        $saisine = Saisine::factory()->create(['creator_id' => $user->id]);

        $result = $this->authorizationService->canAddContributors($user, $saisine);

        $this->assertTrue($result);
    }

    public function test_can_add_contributors_returns_true_for_validator(): void
    {
        $stage = WorkflowStage::factory()->create([
            'required_roles' => [Role::RJG],
        ]);
        $role = Role::factory()->create(['name' => Role::RJG]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $saisine = Saisine::factory()->create(['current_stage_id' => $stage->id]);

        $result = $this->authorizationService->canAddContributors($user, $saisine);

        $this->assertTrue($result);
    }

    public function test_can_add_contributors_returns_false_for_unauthorized_user(): void
    {
        $user = User::factory()->create();
        $creator = User::factory()->create();
        $saisine = Saisine::factory()->create(['creator_id' => $creator->id]);

        $result = $this->authorizationService->canAddContributors($user, $saisine);

        $this->assertFalse($result);
    }
}
