<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;

class JobModel extends Model
{
    protected $table = 'jobs';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;

    protected $casts = [
        'skills' => 'array',
        'posted_at' => 'datetime',
        'salary_min' => 'integer',
        'salary_max' => 'integer',
    ];

    protected $fillable = [
        'id', 'title', 'company', 'location', 'salary_min', 'salary_max',
        'currency', 'description', 'skills', 'posted_at', 'source', 'external_id',
    ];
}
