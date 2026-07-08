<?php

declare(strict_types=1);

namespace App\Infrastructure\External;

use App\Domain\Jobs\Job;

interface ExternalJobSource
{
    /**
     * @return Job[]
     */
    public function fetchJobs(): array;
}
