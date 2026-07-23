<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class JobSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_combines_internal_and_external_results(): void
    {
        Http::fake([
            '*/jobs*' => Http::response([
                'USA' => [
                    ['Cloud Engineer', 65000, '<skills><skill>AWS</skill></skills>'],
                ],
            ], 200),
        ]);

        $this->postJson('/api/jobs', [
            'title' => 'Backend Developer',
            'company' => 'Acme Corp',
            'description' => 'Internal role',
        ])->assertCreated();

        $response = $this->getJson('/api/jobs');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $sources = collect($response->json('data'))->pluck('source')->all();
        $this->assertEqualsCanonicalizing(['internal', 'external'], $sources);
    }

    public function test_it_filters_by_source(): void
    {
        Http::fake([
            '*/jobs*' => Http::response([
                'USA' => [
                    ['Cloud Engineer', 65000, '<skills><skill>AWS</skill></skills>'],
                ],
            ], 200),
        ]);

        $this->postJson('/api/jobs', [
            'title' => 'Backend Developer',
            'company' => 'Acme Corp',
            'description' => 'Internal role',
        ])->assertCreated();

        $response = $this->getJson('/api/jobs?source=internal');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $this->assertSame('internal', $response->json('data.0.source'));
    }

    public function test_it_filters_by_title(): void
    {
        Http::fake(['*/jobs*' => Http::response([], 200)]);

        $this->postJson('/api/jobs', [
            'title' => 'Backend Developer',
            'company' => 'Acme Corp',
            'description' => 'desc',
        ])->assertCreated();
        $this->postJson('/api/jobs', [
            'title' => 'Frontend Developer',
            'company' => 'Acme Corp',
            'description' => 'desc',
        ])->assertCreated();

        $response = $this->getJson('/api/jobs?title=Backend&source=internal');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $this->assertSame('Backend Developer', $response->json('data.0.title'));
    }

    public function test_it_returns_paginated_meta(): void
    {
        Http::fake(['*/jobs*' => Http::response([], 200)]);

        for ($i = 1; $i <= 3; $i++) {
            $this->postJson('/api/jobs', [
                'title' => "Developer {$i}",
                'company' => 'Acme Corp',
                'description' => 'desc',
            ])->assertCreated();
        }

        $response = $this->getJson('/api/jobs?source=internal&per_page=2&page=1');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $response->assertJsonPath('meta.total', 3);
        $response->assertJsonPath('meta.total_pages', 2);
    }
}
