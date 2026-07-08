<?php

namespace Tests\Unit\Domain\Subscriptions;

use App\Domain\Jobs\Job;
use App\Domain\Subscriptions\Subscription;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class SubscriptionTest extends TestCase
{
    public function test_matches_any_job_when_no_search_pattern_is_set(): void
    {
        $subscription = new Subscription('sub-1', 'user@example.com');
        $job = $this->makeJob(['title' => 'Anything']);

        $this->assertTrue($subscription->matches($job));
    }

    public function test_matches_job_when_search_pattern_matches(): void
    {
        $subscription = new Subscription('sub-1', 'user@example.com', 'laravel');
        $job = $this->makeJob(['title' => 'Senior Laravel Developer']);

        $this->assertTrue($subscription->matches($job));
    }

    public function test_does_not_match_job_when_search_pattern_does_not_match(): void
    {
        $subscription = new Subscription('sub-1', 'user@example.com', 'laravel');
        $job = $this->makeJob(['title' => 'Python Developer', 'skills' => ['Django']]);

        $this->assertFalse($subscription->matches($job));
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function makeJob(array $overrides = []): Job
    {
        $defaults = [
            'id' => 'job-1',
            'title' => 'Backend Developer',
            'postedAt' => new DateTimeImmutable(),
            'company' => 'Avature',
            'location' => 'Remote',
            'salaryMin' => null,
            'salaryMax' => null,
            'currency' => null,
            'description' => null,
            'skills' => [],
            'source' => 'internal',
            'externalId' => null,
        ];

        $data = array_merge($defaults, $overrides);

        return new Job(
            $data['id'],
            $data['title'],
            $data['postedAt'],
            $data['company'],
            $data['location'],
            $data['salaryMin'],
            $data['salaryMax'],
            $data['currency'],
            $data['description'],
            $data['skills'],
            $data['source'],
            $data['externalId']
        );
    }
}
