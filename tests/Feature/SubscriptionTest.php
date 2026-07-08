<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_subscription(): void
    {
        $response = $this->postJson('/api/subscriptions', [
            'email' => 'user@example.com',
            'search_pattern' => 'laravel',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.email', 'user@example.com');
        $this->assertDatabaseHas('subscriptions', [
            'email' => 'user@example.com',
            'search_pattern' => 'laravel',
        ]);
    }

    public function test_it_creates_a_subscription_without_a_search_pattern(): void
    {
        $response = $this->postJson('/api/subscriptions', ['email' => 'user@example.com']);

        $response->assertCreated();
        $this->assertDatabaseHas('subscriptions', ['email' => 'user@example.com', 'search_pattern' => null]);
    }

    public function test_it_rejects_an_invalid_email(): void
    {
        $response = $this->postJson('/api/subscriptions', ['email' => 'not-an-email']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }
}
