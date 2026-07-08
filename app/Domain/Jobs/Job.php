<?php

declare(strict_types=1);

namespace App\Domain\Jobs;

use DateTimeImmutable;

final class Job
{
    /** @var string */
    public $id;
    /** @var string */
    public $title;
    /** @var DateTimeImmutable */
    public $postedAt;
    /** @var string|null */
    public $company;
    /** @var string|null */
    public $location;
    /** @var int|null */
    public $salaryMin;
    /** @var int|null */
    public $salaryMax;
    /** @var string|null */
    public $currency;
    /** @var string|null */
    public $description;
    /** @var array */
    public $skills;
    /** @var string */
    public $source;
    /** @var string|null */
    public $externalId;

    public function __construct(
        string $id,
        string $title,
        DateTimeImmutable $postedAt,
        ?string $company = null,
        ?string $location = null,
        ?int $salaryMin = null,
        ?int $salaryMax = null,
        ?string $currency = null,
        ?string $description = null,
        array $skills = [],
        string $source = 'internal',
        ?string $externalId = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->postedAt = $postedAt;
        $this->company = $company;
        $this->location = $location;
        $this->salaryMin = $salaryMin;
        $this->salaryMax = $salaryMax;
        $this->currency = $currency;
        $this->description = $description;
        $this->skills = $skills;
        $this->source = $source;
        $this->externalId = $externalId;
    }

    public function matchesSearch(string $pattern): bool
    {
        $pattern = strtolower($pattern);

        return str_contains(strtolower($this->title), $pattern)
            || str_contains(strtolower($this->description ?? ''), $pattern)
            || !empty(array_filter($this->skills, fn(string $skill): bool => str_contains(strtolower($skill), $pattern)));
    }
}
