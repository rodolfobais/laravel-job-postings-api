<?php

namespace Tests\Feature;

use App\Infrastructure\Mail\JobAlertMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class JobPostingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake(['*/jobs*' => Http::response([], 200)]);
    }

    public function test_it_creates_a_job(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/jobs', [
            'title' => 'Backend Developer',
            'company' => 'Avature',
            'location' => 'Remote',
            'salary_min' => 50000,
            'salary_max' => 70000,
            'currency' => 'USD',
            'description' => 'Great role',
            'skills' => ['PHP', 'Laravel'],
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.title', 'Backend Developer');
        $this->assertDatabaseHas('jobs', ['title' => 'Backend Developer', 'company' => 'Avature']);
    }

    public function test_it_rejects_a_job_missing_required_fields(): void
    {
        $response = $this->postJson('/api/jobs', ['company' => 'Avature']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'description']);
    }

    public function test_it_notifies_matching_subscribers_when_a_job_is_created(): void
    {
        Mail::fake();

        $this->postJson('/api/subscriptions', [
            'email' => 'match@example.com',
            'search_pattern' => 'laravel',
        ])->assertCreated();

        $this->postJson('/api/jobs', [
            'title' => 'Senior Laravel Developer',
            'company' => 'Avature',
            'description' => 'Laravel role',
        ])->assertCreated();

        Mail::assertSent(JobAlertMail::class, function (JobAlertMail $mail) {
            return $mail->hasTo('match@example.com') && $mail->job->title === 'Senior Laravel Developer';
        });
    }

    public function test_it_does_not_notify_non_matching_subscribers(): void
    {
        Mail::fake();

        $this->postJson('/api/subscriptions', [
            'email' => 'nomatch@example.com',
            'search_pattern' => 'python',
        ])->assertCreated();

        $this->postJson('/api/jobs', [
            'title' => 'Senior Laravel Developer',
            'company' => 'Avature',
            'description' => 'Laravel role',
        ])->assertCreated();

        Mail::assertNotSent(JobAlertMail::class);
    }
}
