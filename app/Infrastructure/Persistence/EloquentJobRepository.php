<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Jobs\Job;
use App\Domain\Jobs\JobRepository;
use App\Domain\Jobs\JobSearchCriteria;
use App\Infrastructure\Persistence\Models\JobModel;
use DateTimeImmutable;

class EloquentJobRepository implements JobRepository
{
    public function save(Job $job): void
    {
        JobModel::create([
            'id' => $job->id,
            'title' => $job->title,
            'company' => $job->company,
            'location' => $job->location,
            'salary_min' => $job->salaryMin,
            'salary_max' => $job->salaryMax,
            'currency' => $job->currency,
            'description' => $job->description,
            'skills' => $job->skills,
            'posted_at' => $job->postedAt,
            'source' => $job->source,
            'external_id' => $job->externalId,
        ]);
    }

    public function findById(string $id): ?Job
    {
        $model = JobModel::find($id);

        return $model ? $this->toEntity($model) : null;
    }

    public function search(JobSearchCriteria $criteria): array
    {
        $query = JobModel::query();

        if ($criteria->title) {
            $query->where('title', 'like', "%{$criteria->title}%");
        }

        if ($criteria->location) {
            $query->where('location', 'like', "%{$criteria->location}%");
        }

        if ($criteria->minSalary) {
            $query->where('salary_max', '>=', $criteria->minSalary);
        }

        if ($criteria->maxSalary) {
            $query->where('salary_min', '<=', $criteria->maxSalary);
        }

        if ($criteria->source !== 'all') {
            $query->where('source', $criteria->source);
        }

        $models = $query->orderBy('posted_at', 'desc')->get();

        $jobs = $models->map(fn($model) => $this->toEntity($model))->all();

        if ($criteria->skills) {
            $jobs = array_filter($jobs, fn(Job $job) => $criteria->matches($job));
        }

        $offset = ($criteria->page - 1) * $criteria->perPage;

        return array_slice($jobs, $offset, $criteria->perPage);
    }

    public function count(JobSearchCriteria $criteria): int
    {
        $all = $this->search(new JobSearchCriteria(
            $criteria->title,
            $criteria->location,
            $criteria->minSalary,
            $criteria->maxSalary,
            $criteria->skills,
            $criteria->source,
            1,
            PHP_INT_MAX
        ));

        return count($all);
    }

    private function toEntity(JobModel $model): Job
    {
        return new Job(
            $model->id,
            $model->title,
            new DateTimeImmutable($model->posted_at->toDateTimeString()),
            $model->company,
            $model->location,
            $model->salary_min,
            $model->salary_max,
            $model->currency,
            $model->description,
            $model->skills ?? [],
            $model->source,
            $model->external_id
        );
    }
}
