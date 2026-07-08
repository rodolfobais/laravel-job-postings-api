<?php

declare(strict_types=1);

namespace App\Application\Jobs\SearchJobs;

use App\Domain\Jobs\JobSearchCriteria;

final class SearchJobsQuery
{
    /** @var JobSearchCriteria */
    public $criteria;

    public function __construct(JobSearchCriteria $criteria)
    {
        $this->criteria = $criteria;
    }
}
