<?php

declare(strict_types=1);

namespace App\Application\DTOs;

final class JobDTO
{
    /** @var string */
    public $title;
    /** @var string */
    public $company;
    /** @var string|null */
    public $location;
    /** @var int|null */
    public $salaryMin;
    /** @var int|null */
    public $salaryMax;
    /** @var string|null */
    public $currency;
    /** @var string */
    public $description;
    /** @var array */
    public $skills;

    public function __construct(
        string $title,
        string $company,
        ?string $location,
        ?int $salaryMin,
        ?int $salaryMax,
        ?string $currency,
        string $description,
        array $skills
    ) {
        $this->title = $title;
        $this->company = $company;
        $this->location = $location;
        $this->salaryMin = $salaryMin;
        $this->salaryMax = $salaryMax;
        $this->currency = $currency;
        $this->description = $description;
        $this->skills = $skills;
    }
}
