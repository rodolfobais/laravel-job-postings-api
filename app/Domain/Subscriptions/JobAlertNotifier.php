<?php

declare(strict_types=1);

namespace App\Domain\Subscriptions;

use App\Domain\Jobs\Job;

interface JobAlertNotifier
{
    public function notify(Subscription $subscription, Job $job): void;
}
