<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionModel extends Model
{
    protected $table = 'subscriptions';
    public $timestamps = true;

    protected $fillable = ['email', 'search_pattern'];
}
