<?php

namespace Tests\Unit\Domain\Jobs;

use App\Domain\Jobs\Job;
use App\Domain\Jobs\JobSearchCriteria;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class JobSearchCriteriaTest extends TestCase
{
    public function test_matches_by_title(): void
    {
        $criteria = new JobSearchCriteria('engineer');
        $job = $this->makeJob(['title' => 'Cloud Engineer']);

        $this->assertTrue($criteria->matches($job));
        $this->assertFalse((new JobSearchCriteria('designer'))->matches($job));
    }

    public function test_matches_by_salary_range(): void
    {
        $job = $this->makeJob(['salaryMin' => 40000, 'salaryMax' => 60000]);

        $this->assertTrue((new JobSearchCriteria(null, null, 50000))->matches($job));
        $this->assertFalse((new JobSearchCriteria(null, null, 70000))->matches($job));
        $this->assertTrue((new JobSearchCriteria(null, null, null, 45000))->matches($job));
        $this->assertFalse((new JobSearchCriteria(null, null, null, 30000))->matches($job));
    }

    public function test_matches_by_skills_requires_at_least_one_overlap(): void
    {
        $job = $this->makeJob(['skills' => ['PHP', 'Laravel']]);

        $this->assertTrue((new JobSearchCriteria(null, null, null, null, 'laravel,react'))->matches($job));
        $this->assertFalse((new JobSearchCriteria(null, null, null, null, 'python,go'))->matches($job));
    }

    public function test_matches_by_source(): void
    {
        $job = $this->makeJob(['source' => 'external']);

        $this->assertTrue((new JobSearchCriteria(null, null, null, null, null, 'external'))->matches($job));
        $this->assertFalse((new JobSearchCriteria(null, null, null, null, null, 'internal'))->matches($job));
        $this->assertTrue((new JobSearchCriteria())->matches($job));
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
