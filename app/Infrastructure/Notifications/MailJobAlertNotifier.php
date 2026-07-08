<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifications;

use App\Domain\Jobs\Job;
use App\Domain\Subscriptions\JobAlertNotifier;
use App\Domain\Subscriptions\Subscription;
use App\Infrastructure\Mail\JobAlertMail;
use Illuminate\Support\Facades\Mail;

class MailJobAlertNotifier implements JobAlertNotifier
{
    public function notify(Subscription $subscription, Job $job): void
    {
        Mail::to($subscription->email)->send(new JobAlertMail($job));
    }
}
