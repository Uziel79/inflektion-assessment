<?php

namespace Tests\Feature;

use App\Models\SuccessfulEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuccessfulEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_successful_emails()
    {
        $user = User::factory()->create();
        SuccessfulEmail::factory()->count(5)->create();

        $response = $this->actingAs($user)->getJson('/api/successful-emails');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function test_can_create_successful_email()
    {
        $user = User::factory()->create();
        $emailData = SuccessfulEmail::factory()->make()->toArray();

        $response = $this->actingAs($user)->postJson('/api/successful-emails', $emailData);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'data']);
    }

    // Add more tests for update, delete, and other scenarios
    public function test_can_update_successful_email()
    {
        $user = User::factory()->create();
        $successfulEmail = SuccessfulEmail::factory()->create();
        $updatedData = ['subject' => 'Updated Subject'];

        $response = $this->actingAs($user)->putJson("/api/successful-emails/{$successfulEmail->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'data'])
            ->assertJson([
                'data' => [
                    'subject' => 'Updated Subject',
                ],
            ]);

        $this->assertDatabaseHas('successful_emails', $updatedData);
    }

    public function test_can_delete_successful_email()
    {
        $user = User::factory()->create();
        $successfulEmail = SuccessfulEmail::factory()->create();

        $response = $this->actingAs($user)->deleteJson("/api/successful-emails/{$successfulEmail->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['message']);

        $this->assertDatabaseMissing('successful_emails', ['id' => $successfulEmail->id]);
    }

    public function test_can_get_single_successful_email()
    {
        $user = User::factory()->create();
        $successfulEmail = SuccessfulEmail::factory()->create();

        $response = $this->actingAs($user)->getJson("/api/successful-emails/{$successfulEmail->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJson([
                'data' => [
                    'id' => $successfulEmail->id,
                    'subject' => $successfulEmail->subject,
                ],
            ]);
    }

    public function test_pagination_works_for_successful_emails()
    {
        $user = User::factory()->create();
        SuccessfulEmail::factory()->count(20)->create();

        $response = $this->actingAs($user)->getJson('/api/successful-emails?page=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta',
            ])
            ->assertJsonCount(config('successful_email.per_page', 15), 'data');
    }

    public function test_unauthorized_user_cannot_access_successful_emails()
    {
        $response = $this->getJson('/api/successful-emails');

        $response->assertStatus(401);
    }
}
