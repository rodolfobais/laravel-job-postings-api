<?php

declare(strict_types=1);

namespace App\Application\Jobs\CreateJob;

use App\Application\DTOs\JobDTO;

final class CreateJobCommand
{
    /** @var JobDTO */
    public $jobData;

    public function __construct(JobDTO $jobData)
    {
        $this->jobData = $jobData;
    }
}
