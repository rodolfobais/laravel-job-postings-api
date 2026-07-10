<?php

declare(strict_types=1);

namespace App\Infrastructure\External;

use App\Domain\Jobs\Job;

/**
 * To plug in a new external source:
 *   1. Implement this interface (see ExtraSourceAdapter for the reference
 *      shape: a Client for raw I/O + a Mapper for its specific format).
 *   2. Bind the new Client/Mapper/Adapter in AppServiceProvider and append
 *      the Adapter to the array passed into CompositeJobRepository.
 * Nothing in the Application or Domain layer needs to change.
 */
interface ExternalJobSource
{
    /**
     * @return Job[]
     */
    public function fetchJobs(): array;
}
