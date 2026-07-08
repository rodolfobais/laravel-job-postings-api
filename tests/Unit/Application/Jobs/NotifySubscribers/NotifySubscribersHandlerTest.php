<?php

namespace Tests\Unit\Application\Jobs\NotifySubscribers;

use App\Application\Jobs\NotifySubscribers\NotifySubscribersHandler;
use App\Domain\Jobs\Job;
use App\Domain\Subscriptions\JobAlertNotifier;
use App\Domain\Subscriptions\Subscription;
use App\Domain\Subscriptions\SubscriptionRepository;
use DateTimeImmutable;
use Mockery;
use Tests\TestCase;

class NotifySubscribersHandlerTest extends TestCase
{
    public function test_notifies_only_matching_subscribers(): void
    {
        $job = new Job('job-1', 'Senior Laravel Developer', new DateTimeImmutable());
        $matching = new Subscription('sub-1', 'match@example.com', 'laravel');
        $nonMatching = new Subscription('sub-2', 'nomatch@example.com', 'python');

        $subscriptionRepository = Mockery::mock(SubscriptionRepository::class);
        $subscriptionRepository->shouldReceive('findAll')->once()->andReturn([$matching, $nonMatching]);

        $notifier = Mockery::mock(JobAlertNotifier::class);
        $notifier->shouldReceive('notify')->once()->with($matching, $job);
        $notifier->shouldNotReceive('notify')->with($nonMatching, Mockery::any());

        (new NotifySubscribersHandler($subscriptionRepository, $notifier))->handle($job);
    }

    public function test_a_failing_notification_does_not_stop_the_others(): void
    {
        $job = new Job('job-1', 'Backend Developer', new DateTimeImmutable());
        $first = new Subscription('sub-1', 'first@example.com');
        $second = new Subscription('sub-2', 'second@example.com');

        $subscriptionRepository = Mockery::mock(SubscriptionRepository::class);
        $subscriptionRepository->shouldReceive('findAll')->once()->andReturn([$first, $second]);

        $notifier = Mockery::mock(JobAlertNotifier::class);
        $notifier->shouldReceive('notify')->once()->with($first, $job)->andThrow(new \RuntimeException('SMTP down'));
        $notifier->shouldReceive('notify')->once()->with($second, $job);

        (new NotifySubscribersHandler($subscriptionRepository, $notifier))->handle($job);

        $this->assertTrue(true);
    }
}
