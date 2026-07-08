<?php

namespace Tests\Unit\Domain\Jobs;

use App\Domain\Jobs\Job;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class JobTest extends TestCase
{
    public function test_matches_search_by_title(): void
    {
        $job = $this->makeJob(['title' => 'Senior Laravel Developer']);

        $this->assertTrue($job->matchesSearch('laravel'));
        $this->assertTrue($job->matchesSearch('LARAVEL'));
        $this->assertFalse($job->matchesSearch('python'));
    }

    public function test_matches_search_by_description(): void
    {
        $job = $this->makeJob(['description' => 'We need someone who knows Kubernetes']);

        $this->assertTrue($job->matchesSearch('kubernetes'));
    }

    public function test_matches_search_by_skills(): void
    {
        $job = $this->makeJob(['skills' => ['PHP', 'MySQL']]);

        $this->assertTrue($job->matchesSearch('mysql'));
        $this->assertFalse($job->matchesSearch('mongodb'));
    }

    public function test_matches_search_handles_null_description(): void
    {
        $job = $this->makeJob(['description' => null, 'title' => 'DevOps Engineer']);

        $this->assertTrue($job->matchesSearch('devops'));
        $this->assertFalse($job->matchesSearch('unrelated'));
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
            'description' => 'Some description',
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
