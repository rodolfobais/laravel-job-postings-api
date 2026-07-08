<?php

namespace Tests\Unit\Application\Jobs\CreateJob;

use App\Application\DTOs\JobDTO;
use App\Application\Jobs\CreateJob\CreateJobCommand;
use App\Application\Jobs\CreateJob\CreateJobHandler;
use App\Domain\Jobs\Job;
use App\Domain\Jobs\JobRepository;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class CreateJobHandlerTest extends MockeryTestCase
{
    public function test_saves_and_returns_a_job_built_from_the_command(): void
    {
        $dto = new JobDTO('Backend Developer', 'Avature', 'Remote', 50000, 70000, 'USD', 'Great role', ['PHP']);
        $command = new CreateJobCommand($dto);

        $repository = Mockery::mock(JobRepository::class);
        $repository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function (Job $job) {
                return $job->title === 'Backend Developer'
                    && $job->company === 'Avature'
                    && $job->source === 'internal'
                    && $job->skills === ['PHP'];
            }));

        $handler = new CreateJobHandler($repository);
        $job = $handler->handle($command);

        $this->assertSame('Backend Developer', $job->title);
        $this->assertNotEmpty($job->id);
    }
}
