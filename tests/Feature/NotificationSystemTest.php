<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Saisine;
use App\Models\User;
use App\Models\WorkflowStage;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /** @test */
    public function notification_bell_displays_unread_count()
    {
        $user = User::factory()->create();
        $role = Role::where('name', Role::BUSINESS_TEAM)->first();
        $user->roles()->attach($role);

        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Notification Bell');
    }

    /** @test */
    public function user_can_view_notifications_page()
    {
        $user = User::factory()->create();
        $role = Role::where('name', Role::BUSINESS_TEAM)->first();
        $user->roles()->attach($role);

        $this->actingAs($user);

        $response = $this->get(route('notifications.index'));

        $response->assertStatus(200);
        $response->assertSee('Notifications');
    }

    /** @test */
    public function user_can_mark_all_notifications_as_read()
    {
        $user = User::factory()->create();
        $role = Role::where('name', Role::BUSINESS_TEAM)->first();
        $user->roles()->attach($role);

        // Create a saisine to generate notifications
        $saisine = Saisine::factory()->create([
            'creator_id' => $user->id,
        ]);

        // Create some notifications manually
        $user->notify(new \App\Notifications\ContributorAddedNotification($saisine));

        $this->actingAs($user);

        $this->assertEquals(1, $user->unreadNotifications->count());

        $response = $this->post(route('notifications.mark-all-read'));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals(0, $user->fresh()->unreadNotifications->count());
    }

    /** @test */
    public function delay_check_command_runs_successfully()
    {
        $this->artisan('saisine:check-delays')
            ->expectsOutput('Checking for delayed requests...')
            ->assertExitCode(0);
    }

    /** @test */
    public function notification_service_sends_stage_validation_notifications()
    {
        Notification::fake();

        $notificationService = app(NotificationService::class);

        // Create RJG users
        $rjgRole = Role::where('name', Role::RJG)->first();
        $rjgUser1 = User::factory()->create();
        $rjgUser1->roles()->attach($rjgRole);
        $rjgUser2 = User::factory()->create();
        $rjgUser2->roles()->attach($rjgRole);

        // Create a saisine at RJG stage
        $rjgStage = WorkflowStage::where('name', WorkflowStage::RJG_QUALIFICATION)->first();
        $saisine = Saisine::factory()->create([
            'current_stage_id' => $rjgStage->id,
        ]);

        // Send notifications
        $notificationService->notifyStageValidators($saisine, $rjgStage);

        // Assert notifications were sent to both RJG users
        Notification::assertSentTo(
            [$rjgUser1, $rjgUser2],
            \App\Notifications\StageValidationNotification::class
        );
    }

    /** @test */
    public function notification_service_sends_completion_request_notifications()
    {
        Notification::fake();

        $notificationService = app(NotificationService::class);

        // Create creator
        $creator = User::factory()->create();
        $businessRole = Role::where('name', Role::BUSINESS_TEAM)->first();
        $creator->roles()->attach($businessRole);

        // Create requestor
        $requestor = User::factory()->create();
        $rjgRole = Role::where('name', Role::RJG)->first();
        $requestor->roles()->attach($rjgRole);

        // Create a saisine
        $saisine = Saisine::factory()->create([
            'creator_id' => $creator->id,
        ]);

        // Send completion request notification
        $notificationService->notifyCompletionRequest($saisine, $requestor, 'Need more information');

        // Assert notification was sent to creator
        Notification::assertSentTo(
            $creator,
            \App\Notifications\CompletionRequestNotification::class
        );
    }

    /** @test */
    public function notification_service_sends_contributor_added_notifications()
    {
        Notification::fake();

        $notificationService = app(NotificationService::class);

        // Create contributor
        $contributor = User::factory()->create();

        // Create a saisine
        $saisine = Saisine::factory()->create();

        // Send contributor added notification
        $notificationService->notifyContributorAdded($saisine, $contributor);

        // Assert notification was sent to contributor
        Notification::assertSentTo(
            $contributor,
            \App\Notifications\ContributorAddedNotification::class
        );
    }

    /** @test */
    public function notification_service_sends_critical_urgency_notifications()
    {
        Notification::fake();

        $notificationService = app(NotificationService::class);

        // Create RJG users
        $rjgRole = Role::where('name', Role::RJG)->first();
        $rjgUser = User::factory()->create();
        $rjgUser->roles()->attach($rjgRole);

        // Create a critical saisine at RJG stage
        $rjgStage = WorkflowStage::where('name', WorkflowStage::RJG_QUALIFICATION)->first();
        $saisine = Saisine::factory()->create([
            'current_stage_id' => $rjgStage->id,
            'urgency_level' => 'critical',
        ]);

        // Send critical urgency notification
        $notificationService->notifyCriticalUrgency($saisine);

        // Assert notification was sent to RJG user
        Notification::assertSentTo(
            $rjgUser,
            \App\Notifications\CriticalUrgencyNotification::class
        );
    }

    /** @test */
    public function notification_service_sends_delay_alert_notifications()
    {
        Notification::fake();

        $notificationService = app(NotificationService::class);

        // Create RJG users
        $rjgRole = Role::where('name', Role::RJG)->first();
        $rjgUser = User::factory()->create();
        $rjgUser->roles()->attach($rjgRole);

        // Create a delayed saisine at RJG stage
        $rjgStage = WorkflowStage::where('name', WorkflowStage::RJG_QUALIFICATION)->first();
        $saisine = Saisine::factory()->create([
            'current_stage_id' => $rjgStage->id,
            'stage_entered_at' => now()->subDays(30), // Delayed
        ]);

        // Send delay alert notification
        $notificationService->notifyDelayAlert($saisine);

        // Assert notification was sent to RJG user
        Notification::assertSentTo(
            $rjgUser,
            \App\Notifications\DelayAlertNotification::class
        );
    }
}
