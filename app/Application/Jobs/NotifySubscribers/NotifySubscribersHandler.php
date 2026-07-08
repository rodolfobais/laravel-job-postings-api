<?php

declare(strict_types=1);

namespace App\Application\Jobs\NotifySubscribers;

use App\Domain\Jobs\Job;
use App\Domain\Subscriptions\JobAlertNotifier;
use App\Domain\Subscriptions\SubscriptionRepository;
use Illuminate\Support\Facades\Log;

final class NotifySubscribersHandler
{
    /** @var SubscriptionRepository */
    private $subscriptionRepository;
    /** @var JobAlertNotifier */
    private $notifier;

    public function __construct(SubscriptionRepository $subscriptionRepository, JobAlertNotifier $notifier)
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->notifier = $notifier;
    }

    public function handle(Job $job): void
    {
        $subscribers = $this->subscriptionRepository->findAll();

        foreach ($subscribers as $subscriber) {
            if (!$subscriber->matches($job)) {
                continue;
            }

            try {
                $this->notifier->notify($subscriber, $job);
            } catch (\Throwable $e) {
                Log::warning('Failed to notify subscriber about new job', [
                    'subscription_id' => $subscriber->id,
                    'job_id' => $job->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
