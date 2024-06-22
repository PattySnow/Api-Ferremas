<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles
        Role::create(['name' => 'admin', 'guard_name' => 'api']);
        Role::create(['name' => 'client', 'guard_name' => 'api']);
        Role::create(['name' => 'employed', 'guard_name' => 'api']);
    }

    public function test_index_as_admin()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/users');

        $response->assertStatus(200);
    }

    public function test_index_as_non_admin()
    {
        $user = User::factory()->create();
        $user->assignRole('client');

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/users');

        $response->assertStatus(403);
    }

    public function test_show_own_profile()
    {
        $user = User::factory()->create();
        $user->assignRole('client');

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/users/' . $user->id);

        $response->assertStatus(200);
    }

    public function test_show_other_profile_as_admin()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();
        $user->assignRole('client');

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/users/' . $user->id);

        $response->assertStatus(200);
    }

    public function test_show_other_profile_as_non_admin()
    {
        $user1 = User::factory()->create();
        $user1->assignRole('client');

        $user2 = User::factory()->create();
        $user2->assignRole('client');

        $response = $this->actingAs($user1, 'sanctum')->getJson('/api/users/' . $user2->id);

        $response->assertStatus(403);
    }

    public function test_update_own_profile()
    {
        $user = User::factory()->create();
        $user->assignRole('client');

        $response = $this->actingAs($user, 'sanctum')->putJson('/api/users/' . $user->id, [
            'name' => 'Updated Name'
        ]);

        $response->assertStatus(200);
        $this->assertEquals('Updated Name', $user->fresh()->name);
    }

    public function test_update_other_profile_as_admin()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();
        $user->assignRole('client');

        $response = $this->actingAs($admin, 'sanctum')->putJson('/api/users/' . $user->id, [
            'name' => 'Updated Name by Admin'
        ]);

        $response->assertStatus(200);
        $this->assertEquals('Updated Name by Admin', $user->fresh()->name);
    }

    public function test_update_other_profile_as_non_admin()
    {
        $user1 = User::factory()->create();
        $user1->assignRole('client');

        $user2 = User::factory()->create();
        $user2->assignRole('client');

        $response = $this->actingAs($user1, 'sanctum')->putJson('/api/users/' . $user2->id, [
            'name' => 'Updated Name'
        ]);

        $response->assertStatus(403);
    }

    public function test_delete_own_profile()
    {
        $user = User::factory()->create();
        $user->assignRole('client');

        $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/users/' . $user->id);

        $response->assertStatus(200);
        $this->assertNull(User::find($user->id));
    }

    public function test_delete_other_profile_as_admin()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();
        $user->assignRole('client');

        $response = $this->actingAs($admin, 'sanctum')->deleteJson('/api/users/' . $user->id);

        $response->assertStatus(200);
        $this->assertNull(User::find($user->id));
    }

    public function test_delete_other_profile_as_non_admin()
    {
        $user1 = User::factory()->create();
        $user1->assignRole('client');

        $user2 = User::factory()->create();
        $user2->assignRole('client');

        $response = $this->actingAs($user1, 'sanctum')->deleteJson('/api/users/' . $user2->id);

        $response->assertStatus(403);
    }
}
