<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controllers;

use App\Application\DTOs\JobDTO;
use App\Application\Jobs\CreateJob\CreateJobCommand;
use App\Application\Jobs\CreateJob\CreateJobHandler;
use App\Application\Jobs\NotifySubscribers\NotifySubscribersHandler;
use App\Application\Jobs\SearchJobs\SearchJobsHandler;
use App\Application\Jobs\SearchJobs\SearchJobsQuery;
use App\Domain\Jobs\JobSearchCriteria;
use App\Domain\Subscriptions\Subscription;
use App\Domain\Subscriptions\SubscriptionRepository;
use App\Http\Controllers\Controller;
use App\Infrastructure\Http\Requests\SearchJobRequest;
use App\Infrastructure\Http\Requests\StoreJobRequest;
use App\Infrastructure\Http\Requests\StoreSubscriptionRequest;
use App\Infrastructure\Http\Resources\JobResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;

class JobController extends Controller
{
    /** @var CreateJobHandler */
    private $createHandler;
    /** @var SearchJobsHandler */
    private $searchHandler;
    /** @var NotifySubscribersHandler */
    private $notifyHandler;
    /** @var SubscriptionRepository */
    private $subscriptionRepository;

    public function __construct(
        CreateJobHandler $createHandler,
        SearchJobsHandler $searchHandler,
        NotifySubscribersHandler $notifyHandler,
        SubscriptionRepository $subscriptionRepository
    ) {
        $this->createHandler = $createHandler;
        $this->searchHandler = $searchHandler;
        $this->notifyHandler = $notifyHandler;
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function index(SearchJobRequest $request): JsonResponse
    {
        $criteria = new JobSearchCriteria(
            $request->input('title'),
            $request->input('location'),
            $request->filled('min_salary') ? (int) $request->input('min_salary') : null,
            $request->filled('max_salary') ? (int) $request->input('max_salary') : null,
            $request->input('skills'),
            $request->input('source', 'all'),
            (int) $request->input('page', 1),
            (int) $request->input('per_page', 20)
        );

        $result = $this->searchHandler->handle(new SearchJobsQuery($criteria));

        return response()->json([
            'data' => JobResource::collection($result['data']),
            'meta' => $result['meta'],
        ]);
    }

    public function store(StoreJobRequest $request): JsonResponse
    {
        $dto = new JobDTO(
            $request->input('title'),
            $request->input('company'),
            $request->input('location'),
            $request->input('salary_min'),
            $request->input('salary_max'),
            $request->input('currency'),
            $request->input('description'),
            $request->input('skills', [])
        );

        $job = $this->createHandler->handle(new CreateJobCommand($dto));

        try {
            $this->notifyHandler->handle($job);
        } catch (\Throwable $e) {
            report($e);
        }

        return (new JobResource($job))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function subscribe(StoreSubscriptionRequest $request): JsonResponse
    {
        $subscription = new Subscription(
            Uuid::uuid4()->toString(),
            $request->input('email'),
            $request->input('search_pattern')
        );

        $this->subscriptionRepository->save($subscription);

        return response()->json([
            'message' => 'Subscription created successfully',
            'data' => [
                'id' => $subscription->id,
                'email' => $subscription->email,
                'searchPattern' => $subscription->searchPattern,
            ],
        ], Response::HTTP_CREATED);
    }
}
