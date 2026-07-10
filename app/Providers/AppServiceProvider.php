<?php

namespace App\Providers;

use App\Domain\Jobs\JobRepository;
use App\Domain\Subscriptions\JobAlertNotifier;
use App\Domain\Subscriptions\SubscriptionRepository;
use App\Infrastructure\External\CompositeJobRepository;
use App\Infrastructure\External\ExtraSourceAdapter;
use App\Infrastructure\External\ExtraSourceClient;
use App\Infrastructure\External\ExtraSourceMapper;
use App\Infrastructure\Notifications\MailJobAlertNotifier;
use App\Infrastructure\Persistence\EloquentJobRepository;
use App\Infrastructure\Persistence\EloquentSubscriptionRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SubscriptionRepository::class, EloquentSubscriptionRepository::class);
        $this->app->singleton(JobAlertNotifier::class, MailJobAlertNotifier::class);

        $this->app->singleton(EloquentJobRepository::class, function () {
            return new EloquentJobRepository();
        });

        $this->app->singleton(ExtraSourceClient::class, function () {
            return new ExtraSourceClient(
                config('services.extra_source.url', 'http://localhost:8080')
            );
        });

        $this->app->singleton(ExtraSourceAdapter::class, function ($app) {
            return new ExtraSourceAdapter(
                $app->make(ExtraSourceClient::class),
                new ExtraSourceMapper()
            );
        });

        $this->app->singleton(JobRepository::class, function ($app) {
            return new CompositeJobRepository(
                $app->make(EloquentJobRepository::class),
                [
                    $app->make(ExtraSourceAdapter::class),
                    // To add another external source: bind its own Client +
                    // Mapper + Adapter (implementing ExternalJobSource) above,
                    // then just append it to this array — nothing else in this
                    // method, or anywhere outside App\Infrastructure\External,
                    // needs to change.
                ]
            );
        });
    }

    public function boot(): void
    {
        //
    }
}
