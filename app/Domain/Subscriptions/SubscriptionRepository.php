<?php

declare(strict_types=1);

namespace App\Domain\Subscriptions;

interface SubscriptionRepository
{
    public function save(Subscription $subscription): void;

    /**
     * @return Subscription[]
     */
    public function findAll(): array;
}
