<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Tests\TestCase;
use App\Notifications\NearbyPharmaciesNotification;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_user_can_fetch_notifications()
    {
        $this->user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'type' => 'App\Notifications\NearbyPharmaciesNotification',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $this->user->id,
            'data' => ['type' => 'nearby_pharmacies', 'message' => 'test'],
            'read_at' => null,
        ]);

        $response = $this->actingAs($this->user, 'api')->getJson('/api/v1/me/notifications');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data');
    }

    public function test_user_can_mark_notification_as_read()
    {
        $notification = $this->user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'type' => 'App\Notifications\NearbyPharmaciesNotification',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $this->user->id,
            'data' => ['type' => 'nearby_pharmacies', 'message' => 'test'],
            'read_at' => null,
        ]);

        $response = $this->actingAs($this->user, 'api')->postJson("/api/v1/me/notifications/{$notification->id}/read");

        $response->assertStatus(200);
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_can_mark_all_notifications_as_read()
    {
        $this->user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'type' => 'App\Notifications\NearbyPharmaciesNotification',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $this->user->id,
            'data' => ['type' => 'nearby_pharmacies', 'message' => 'test'],
            'read_at' => null,
        ]);

        $response = $this->actingAs($this->user, 'api')->postJson('/api/v1/me/notifications/read-all');

        $response->assertStatus(200);
        $this->assertEquals(0, $this->user->unreadNotifications()->count());
    }

    public function test_user_can_delete_notification()
    {
        $notification = $this->user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'type' => 'App\Notifications\NearbyPharmaciesNotification',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $this->user->id,
            'data' => ['type' => 'nearby_pharmacies', 'message' => 'test'],
            'read_at' => null,
        ]);

        $response = $this->actingAs($this->user, 'api')->deleteJson("/api/v1/me/notifications/{$notification->id}");

        $response->assertStatus(200);
        $this->assertEquals(0, $this->user->notifications()->count());
    }
}
