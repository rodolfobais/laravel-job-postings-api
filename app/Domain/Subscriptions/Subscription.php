<?php

declare(strict_types=1);

namespace App\Domain\Subscriptions;

use App\Domain\Jobs\Job;

final class Subscription
{
    /** @var string */
    public $id;
    /** @var string */
    public $email;
    /** @var string|null */
    public $searchPattern;

    public function __construct(string $id, string $email, ?string $searchPattern = null)
    {
        $this->id = $id;
        $this->email = $email;
        $this->searchPattern = $searchPattern;
    }

    public function matches(Job $job): bool
    {
        if ($this->searchPattern === null) {
            return true;
        }

        return $job->matchesSearch($this->searchPattern);
    }
}
