<?php

namespace Tests\Feature;

use App\Models\RequestType;
use App\Models\Role;
use App\Models\Saisine;
use App\Models\User;
use App\Models\WorkflowStage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaisineControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create necessary data for tests
        $this->createTestData();
    }
    
    protected function createTestData(): void
    {
        // Create roles
        Role::create(['name' => 'business_team', 'display_name' => 'Business Team']);
        Role::create(['name' => 'rjg', 'display_name' => 'RJG']);
        
        // Create workflow stages
        WorkflowStage::create([
            'name' => WorkflowStage::BUSINESS_SUBMISSION,
            'display_name' => 'Business Submission',
            'sequence_order' => 1,
            'expected_duration_days' => 2,
            'required_roles' => ['business_team'],
        ]);
        
        WorkflowStage::create([
            'name' => WorkflowStage::RJG_QUALIFICATION,
            'display_name' => 'RJG Qualification',
            'sequence_order' => 2,
            'expected_duration_days' => 3,
            'required_roles' => ['rjg'],
        ]);
        
        // Create request type
        RequestType::create([
            'name' => 'contract',
            'display_name' => 'Contract',
            'description' => 'New contract creation',
        ]);
    }

    public function test_authenticated_user_can_view_create_form(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get(route('saisines.create'));
        
        $response->assertStatus(200);
        $response->assertViewIs('saisines.create');
        $response->assertViewHas('requestTypes');
    }

    public function test_authenticated_user_can_create_saisine(): void
    {
        $user = User::factory()->create();
        $requestType = RequestType::first();
        
        $data = [
            'request_type_id' => $requestType->id,
            'title' => 'Test Saisine',
            'description' => 'This is a test saisine description',
            'urgency_level' => 'standard',
        ];
        
        $response = $this->actingAs($user)->post(route('saisines.store'), $data);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('saisines', [
            'title' => 'Test Saisine',
            'creator_id' => $user->id,
        ]);
        
        // Verify initial stage is Business Submission
        $saisine = Saisine::where('title', 'Test Saisine')->first();
        $this->assertEquals(WorkflowStage::BUSINESS_SUBMISSION, $saisine->currentStage->name);
        
        // Verify initial version was created
        $this->assertCount(1, $saisine->versions);
    }

    public function test_authenticated_user_can_view_saisine_details(): void
    {
        $user = User::factory()->create();
        $requestType = RequestType::first();
        $stage = WorkflowStage::first();
        
        $saisine = Saisine::factory()->create([
            'creator_id' => $user->id,
            'request_type_id' => $requestType->id,
            'current_stage_id' => $stage->id,
        ]);
        
        $response = $this->actingAs($user)->get(route('saisines.show', $saisine));
        
        $response->assertStatus(200);
        $response->assertViewIs('saisines.show');
        $response->assertViewHas('saisine');
        $response->assertSee($saisine->title);
    }

    public function test_authenticated_user_can_edit_their_saisine(): void
    {
        $user = User::factory()->create();
        $requestType = RequestType::first();
        $stage = WorkflowStage::first();
        
        $saisine = Saisine::factory()->create([
            'creator_id' => $user->id,
            'request_type_id' => $requestType->id,
            'current_stage_id' => $stage->id,
        ]);
        
        $response = $this->actingAs($user)->get(route('saisines.edit', $saisine));
        
        $response->assertStatus(200);
        $response->assertViewIs('saisines.edit');
        $response->assertViewHas('saisine');
    }

    public function test_authenticated_user_can_update_their_saisine(): void
    {
        $user = User::factory()->create();
        $requestType = RequestType::first();
        $stage = WorkflowStage::first();
        
        $saisine = Saisine::factory()->create([
            'creator_id' => $user->id,
            'request_type_id' => $requestType->id,
            'current_stage_id' => $stage->id,
        ]);
        
        $data = [
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'urgency_level' => 'priority',
        ];
        
        $response = $this->actingAs($user)->put(route('saisines.update', $saisine), $data);
        
        $response->assertRedirect(route('saisines.show', $saisine));
        $this->assertDatabaseHas('saisines', [
            'id' => $saisine->id,
            'title' => 'Updated Title',
            'urgency_level' => 'priority',
        ]);
        
        // Verify new version was created
        $saisine->refresh();
        $this->assertGreaterThan(0, $saisine->versions()->count());
    }

    public function test_user_cannot_view_saisine_without_permission(): void
    {
        $creator = User::factory()->create();
        $otherUser = User::factory()->create();
        $requestType = RequestType::first();
        $stage = WorkflowStage::first();
        
        $saisine = Saisine::factory()->create([
            'creator_id' => $creator->id,
            'request_type_id' => $requestType->id,
            'current_stage_id' => $stage->id,
        ]);
        
        $response = $this->actingAs($otherUser)->get(route('saisines.show', $saisine));
        
        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_saisine_routes(): void
    {
        $response = $this->get(route('saisines.create'));
        $response->assertRedirect(route('login'));
        
        $response = $this->get(route('saisines.index'));
        $response->assertRedirect(route('login'));
    }
}
