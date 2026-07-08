<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Subscriptions\Subscription;
use App\Domain\Subscriptions\SubscriptionRepository;
use App\Infrastructure\Persistence\Models\SubscriptionModel;

class EloquentSubscriptionRepository implements SubscriptionRepository
{
    public function save(Subscription $subscription): void
    {
        SubscriptionModel::create([
            'id' => $subscription->id,
            'email' => $subscription->email,
            'search_pattern' => $subscription->searchPattern,
        ]);
    }

    public function findAll(): array
    {
        return SubscriptionModel::all()
            ->map(fn($model) => new Subscription(
                (string) $model->id,
                $model->email,
                $model->search_pattern
            ))
            ->all();
    }
}
