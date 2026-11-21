<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Saisine;
use App\Models\User;
use App\Models\WorkflowStage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ContributorControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /** @test */
    public function authorized_user_can_add_contributor()
    {
        // Create a user with RJG role (validator)
        $validator = User::factory()->create();
        $rjgRole = Role::where('name', Role::RJG)->first();
        $validator->roles()->attach($rjgRole);

        // Create a saisine at RJG Qualification stage
        $rjgStage = WorkflowStage::where('name', WorkflowStage::RJG_QUALIFICATION)->first();
        $saisine = Saisine::factory()->create([
            'current_stage_id' => $rjgStage->id,
        ]);

        // Create a user to be added as contributor
        $contributor = User::factory()->create();

        // Add contributor
        $response = $this->actingAs($validator)->post(route('saisines.contributors.store', $saisine), [
            'user_id' => $contributor->id,
            'contributor_type' => 'contributor',
        ]);

        $response->assertRedirect(route('saisines.show', $saisine));
        $response->assertSessionHas('success');

        // Check that contributor was added
        $this->assertDatabaseHas('saisine_contributors', [
            'saisine_id' => $saisine->id,
            'user_id' => $contributor->id,
            'contributor_type' => 'contributor',
            'added_by' => $validator->id,
        ]);
    }

    /** @test */
    public function authorized_user_can_add_observer()
    {
        // Create a user with RJG role (validator)
        $validator = User::factory()->create();
        $rjgRole = Role::where('name', Role::RJG)->first();
        $validator->roles()->attach($rjgRole);

        // Create a saisine at RJG Qualification stage
        $rjgStage = WorkflowStage::where('name', WorkflowStage::RJG_QUALIFICATION)->first();
        $saisine = Saisine::factory()->create([
            'current_stage_id' => $rjgStage->id,
        ]);

        // Create a user to be added as observer
        $observer = User::factory()->create();

        // Add observer
        $response = $this->actingAs($validator)->post(route('saisines.contributors.store', $saisine), [
            'user_id' => $observer->id,
            'contributor_type' => 'observer',
        ]);

        $response->assertRedirect(route('saisines.show', $saisine));
        $response->assertSessionHas('success');

        // Check that observer was added
        $this->assertDatabaseHas('saisine_contributors', [
            'saisine_id' => $saisine->id,
            'user_id' => $observer->id,
            'contributor_type' => 'observer',
            'added_by' => $validator->id,
        ]);
    }

    /** @test */
    public function creator_can_add_contributors()
    {
        // Create a saisine with a creator
        $creator = User::factory()->create();
        $businessStage = WorkflowStage::where('name', WorkflowStage::BUSINESS_SUBMISSION)->first();
        $saisine = Saisine::factory()->create([
            'creator_id' => $creator->id,
            'current_stage_id' => $businessStage->id,
        ]);

        // Create a user to be added as contributor
        $contributor = User::factory()->create();

        // Creator adds contributor
        $response = $this->actingAs($creator)->post(route('saisines.contributors.store', $saisine), [
            'user_id' => $contributor->id,
            'contributor_type' => 'contributor',
        ]);

        $response->assertRedirect(route('saisines.show', $saisine));
        $response->assertSessionHas('success');

        // Check that contributor was added
        $this->assertDatabaseHas('saisine_contributors', [
            'saisine_id' => $saisine->id,
            'user_id' => $contributor->id,
            'contributor_type' => 'contributor',
        ]);
    }

    /** @test */
    public function contributors_can_be_added_at_any_workflow_stage()
    {
        // Test at different stages
        $stages = [
            WorkflowStage::BUSINESS_SUBMISSION,
            WorkflowStage::RJG_QUALIFICATION,
            WorkflowStage::COMPLIANCE_VALIDATION,
            WorkflowStage::DRJ_STUDY,
        ];

        foreach ($stages as $stageName) {
            $stage = WorkflowStage::where('name', $stageName)->first();
            $requiredRoles = $stage->getRequiredRoles();

            // Create a user with appropriate role
            $validator = User::factory()->create();
            $role = Role::whereIn('name', $requiredRoles)->first();
            $validator->roles()->attach($role);

            // Create a saisine at this stage
            $saisine = Saisine::factory()->create([
                'current_stage_id' => $stage->id,
            ]);

            // Create a user to be added as contributor
            $contributor = User::factory()->create();

            // Add contributor
            $response = $this->actingAs($validator)->post(route('saisines.contributors.store', $saisine), [
                'user_id' => $contributor->id,
                'contributor_type' => 'contributor',
            ]);

            $response->assertRedirect(route('saisines.show', $saisine));
            $response->assertSessionHas('success');

            // Check that contributor was added
            $this->assertDatabaseHas('saisine_contributors', [
                'saisine_id' => $saisine->id,
                'user_id' => $contributor->id,
                'contributor_type' => 'contributor',
            ]);
        }
    }

    /** @test */
    public function unauthorized_user_cannot_add_contributors()
    {
        // Create a user without appropriate role
        $user = User::factory()->create();

        // Create a saisine
        $rjgStage = WorkflowStage::where('name', WorkflowStage::RJG_QUALIFICATION)->first();
        $saisine = Saisine::factory()->create([
            'current_stage_id' => $rjgStage->id,
        ]);

        // Create a user to be added as contributor
        $contributor = User::factory()->create();

        // Attempt to add contributor
        $response = $this->actingAs($user)->post(route('saisines.contributors.store', $saisine), [
            'user_id' => $contributor->id,
            'contributor_type' => 'contributor',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Check that contributor was not added
        $this->assertDatabaseMissing('saisine_contributors', [
            'saisine_id' => $saisine->id,
            'user_id' => $contributor->id,
        ]);
    }

    /** @test */
    public function cannot_add_same_user_twice()
    {
        // Create a user with RJG role (validator)
        $validator = User::factory()->create();
        $rjgRole = Role::where('name', Role::RJG)->first();
        $validator->roles()->attach($rjgRole);

        // Create a saisine at RJG Qualification stage
        $rjgStage = WorkflowStage::where('name', WorkflowStage::RJG_QUALIFICATION)->first();
        $saisine = Saisine::factory()->create([
            'current_stage_id' => $rjgStage->id,
        ]);

        // Create a user to be added as contributor
        $contributor = User::factory()->create();

        // Add contributor first time
        $this->actingAs($validator)->post(route('saisines.contributors.store', $saisine), [
            'user_id' => $contributor->id,
            'contributor_type' => 'contributor',
        ]);

        // Attempt to add same user again
        $response = $this->actingAs($validator)->post(route('saisines.contributors.store', $saisine), [
            'user_id' => $contributor->id,
            'contributor_type' => 'contributor',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Check that only one record exists
        $count = DB::table('saisine_contributors')
            ->where('saisine_id', $saisine->id)
            ->where('user_id', $contributor->id)
            ->count();
        $this->assertEquals(1, $count);
    }

    /** @test */
    public function authorized_user_can_remove_contributor()
    {
        // Create a user with RJG role (validator)
        $validator = User::factory()->create();
        $rjgRole = Role::where('name', Role::RJG)->first();
        $validator->roles()->attach($rjgRole);

        // Create a saisine at RJG Qualification stage
        $rjgStage = WorkflowStage::where('name', WorkflowStage::RJG_QUALIFICATION)->first();
        $saisine = Saisine::factory()->create([
            'current_stage_id' => $rjgStage->id,
        ]);

        // Create and add a contributor
        $contributor = User::factory()->create();
        DB::table('saisine_contributors')->insert([
            'saisine_id' => $saisine->id,
            'user_id' => $contributor->id,
            'contributor_type' => 'contributor',
            'added_by' => $validator->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Remove contributor
        $response = $this->actingAs($validator)->delete(route('saisines.contributors.destroy', [$saisine, $contributor]));

        $response->assertRedirect(route('saisines.show', $saisine));
        $response->assertSessionHas('success');

        // Check that contributor was removed
        $this->assertDatabaseMissing('saisine_contributors', [
            'saisine_id' => $saisine->id,
            'user_id' => $contributor->id,
        ]);
    }

    /** @test */
    public function unauthorized_user_cannot_remove_contributors()
    {
        // Create a user without appropriate role
        $user = User::factory()->create();

        // Create a saisine
        $rjgStage = WorkflowStage::where('name', WorkflowStage::RJG_QUALIFICATION)->first();
        $saisine = Saisine::factory()->create([
            'current_stage_id' => $rjgStage->id,
        ]);

        // Create and add a contributor
        $contributor = User::factory()->create();
        DB::table('saisine_contributors')->insert([
            'saisine_id' => $saisine->id,
            'user_id' => $contributor->id,
            'contributor_type' => 'contributor',
            'added_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Attempt to remove contributor
        $response = $this->actingAs($user)->delete(route('saisines.contributors.destroy', [$saisine, $contributor]));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Check that contributor still exists
        $this->assertDatabaseHas('saisine_contributors', [
            'saisine_id' => $saisine->id,
            'user_id' => $contributor->id,
        ]);
    }
}
