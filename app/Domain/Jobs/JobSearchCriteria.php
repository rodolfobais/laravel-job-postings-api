<?php

declare(strict_types=1);

namespace App\Domain\Jobs;

final class JobSearchCriteria
{
    /** @var string|null */
    public $title;
    /** @var string|null */
    public $location;
    /** @var int|null */
    public $minSalary;
    /** @var int|null */
    public $maxSalary;
    /** @var string|null */
    public $skills;
    /** @var string */
    public $source;
    /** @var int */
    public $page;
    /** @var int */
    public $perPage;

    public function __construct(
        ?string $title = null,
        ?string $location = null,
        ?int $minSalary = null,
        ?int $maxSalary = null,
        ?string $skills = null,
        string $source = 'all',
        int $page = 1,
        int $perPage = 20
    ) {
        $this->title = $title;
        $this->location = $location;
        $this->minSalary = $minSalary;
        $this->maxSalary = $maxSalary;
        $this->skills = $skills;
        $this->source = $source;
        $this->page = $page;
        $this->perPage = $perPage;
    }

    public function matches(Job $job): bool
    {
        if ($this->title && !str_contains(strtolower($job->title), strtolower($this->title))) {
            return false;
        }

        if ($this->location && $job->location && !str_contains(strtolower($job->location), strtolower($this->location))) {
            return false;
        }

        if ($this->minSalary && $job->salaryMax && $job->salaryMax < $this->minSalary) {
            return false;
        }

        if ($this->maxSalary && $job->salaryMin && $job->salaryMin > $this->maxSalary) {
            return false;
        }

        if ($this->skills && $job->skills) {
            $requiredSkills = array_map('trim', explode(',', strtolower($this->skills)));
            $jobSkills = array_map('strtolower', $job->skills);
            $matches = array_intersect($requiredSkills, $jobSkills);
            if (empty($matches)) {
                return false;
            }
        }

        if ($this->source !== 'all' && $job->source !== $this->source) {
            return false;
        }

        return true;
    }
}
