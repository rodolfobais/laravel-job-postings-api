<?php

declare(strict_types=1);

namespace App\Infrastructure\External;

use App\Domain\Jobs\Job;

class ExtraSourceAdapter implements ExternalJobSource
{
    /** @var ExtraSourceClient */
    private $client;
    /** @var ExtraSourceMapper */
    private $mapper;

    public function __construct(ExtraSourceClient $client, ExtraSourceMapper $mapper)
    {
        $this->client = $client;
        $this->mapper = $mapper;
    }

    public function fetchJobs(): array
    {
        return $this->mapper->mapAll($this->client->getJobs());
    }
}
