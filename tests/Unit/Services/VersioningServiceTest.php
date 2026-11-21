<?php

namespace Tests\Unit\Services;

use App\Models\Saisine;
use App\Models\User;
use App\Models\Version;
use App\Services\VersioningService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VersioningServiceTest extends TestCase
{
    use RefreshDatabase;

    protected VersioningService $versioningService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->versioningService = new VersioningService();
    }

    public function test_create_version_creates_version_with_correct_data(): void
    {
        $user = User::factory()->create();
        $saisine = Saisine::factory()->create([
            'title' => 'Test Saisine',
            'description' => 'Test Description',
        ]);

        $changes = [
            'action' => 'created',
            'comment' => 'Initial creation',
        ];

        $version = $this->versioningService->createVersion($saisine, $user, $changes);

        $this->assertInstanceOf(Version::class, $version);
        $this->assertEquals($saisine->id, $version->saisine_id);
        $this->assertEquals($user->id, $version->author_id);
        $this->assertEquals(1, $version->version_number);
        $this->assertNotNull($version->content_snapshot);
        $this->assertIsArray($version->content_snapshot);
    }

    public function test_create_version_increments_version_number(): void
    {
        $user = User::factory()->create();
        $saisine = Saisine::factory()->create();

        // Create first version
        $version1 = $this->versioningService->createVersion($saisine, $user, ['action' => 'created']);
        $this->assertEquals(1, $version1->version_number);

        // Create second version
        $version2 = $this->versioningService->createVersion($saisine, $user, ['action' => 'updated']);
        $this->assertEquals(2, $version2->version_number);

        // Create third version
        $version3 = $this->versioningService->createVersion($saisine, $user, ['action' => 'updated']);
        $this->assertEquals(3, $version3->version_number);
    }

    public function test_get_version_history_returns_versions_in_reverse_chronological_order(): void
    {
        $user = User::factory()->create();
        $saisine = Saisine::factory()->create();

        // Create multiple versions
        $version1 = $this->versioningService->createVersion($saisine, $user, ['action' => 'created']);
        $version2 = $this->versioningService->createVersion($saisine, $user, ['action' => 'updated']);
        $version3 = $this->versioningService->createVersion($saisine, $user, ['action' => 'updated']);

        $history = $this->versioningService->getVersionHistory($saisine);

        $this->assertCount(3, $history);
        $this->assertEquals($version3->id, $history[0]->id);
        $this->assertEquals($version2->id, $history[1]->id);
        $this->assertEquals($version1->id, $history[2]->id);
    }

    public function test_compare_versions_returns_differences(): void
    {
        $user = User::factory()->create();
        $saisine = Saisine::factory()->create([
            'title' => 'Original Title',
        ]);

        // Create first version
        $version1 = $this->versioningService->createVersion($saisine, $user, ['action' => 'created']);

        // Update saisine
        $saisine->update(['title' => 'Updated Title']);

        // Create second version
        $version2 = $this->versioningService->createVersion($saisine, $user, ['action' => 'updated']);

        $diff = $this->versioningService->compareVersions($version2, $version1);

        $this->assertIsArray($diff);
        $this->assertArrayHasKey('title', $diff);
        $this->assertEquals('Original Title', $diff['title']['old']);
        $this->assertEquals('Updated Title', $diff['title']['new']);
    }

    public function test_compare_versions_throws_exception_for_different_saisines(): void
    {
        $user = User::factory()->create();
        $saisine1 = Saisine::factory()->create();
        $saisine2 = Saisine::factory()->create();

        $version1 = $this->versioningService->createVersion($saisine1, $user, ['action' => 'created']);
        $version2 = $this->versioningService->createVersion($saisine2, $user, ['action' => 'created']);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot compare versions from different saisines');

        $this->versioningService->compareVersions($version1, $version2);
    }
}
