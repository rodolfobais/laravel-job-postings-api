<?php

namespace Tests\Unit\Infrastructure\External;

use App\Infrastructure\External\ExtraSourceMapper;
use Tests\TestCase;

class ExtraSourceMapperTest extends TestCase
{
    /**
     * Shape confirmed by running jobberwocky-extra-source-v2 locally:
     * { "<country>": [ ["<name>", <salary:int>, "<skills XML>"], ... ] }
     */
    public function test_maps_real_extra_source_response_shape(): void
    {
        $raw = [
            'USA' => [
                ['Cloud Engineer', 65000, '<skills><skill>AWS</skill><skill>Docker</skill></skills>'],
            ],
            'Spain' => [
                ['Machine Learning Engineer', 75000, '<skills><skill>Python</skill></skills>'],
            ],
        ];

        $jobs = (new ExtraSourceMapper())->mapAll($raw);

        $this->assertCount(2, $jobs);

        $cloudEngineer = $jobs[0];
        $this->assertSame('Cloud Engineer', $cloudEngineer->title);
        $this->assertSame('USA', $cloudEngineer->location);
        $this->assertSame(65000, $cloudEngineer->salaryMin);
        $this->assertSame(65000, $cloudEngineer->salaryMax);
        $this->assertSame(['AWS', 'Docker'], $cloudEngineer->skills);
        $this->assertSame('external', $cloudEngineer->source);
        $this->assertNull($cloudEngineer->company);
        $this->assertNull($cloudEngineer->description);
        $this->assertNotEmpty($cloudEngineer->externalId);
    }

    public function test_generates_stable_id_for_the_same_entry(): void
    {
        $raw = ['Argentina' => [['Backend Developer', 50000, '<skills></skills>']]];
        $mapper = new ExtraSourceMapper();

        $first = $mapper->mapAll($raw)[0];
        $second = $mapper->mapAll($raw)[0];

        $this->assertSame($first->externalId, $second->externalId);
    }

    public function test_skips_entry_with_missing_name(): void
    {
        $raw = ['USA' => [['', 50000, '<skills></skills>']]];

        $jobs = (new ExtraSourceMapper())->mapAll($raw);

        $this->assertCount(0, $jobs);
    }

    public function test_skips_malformed_entry_shape(): void
    {
        $raw = ['USA' => [['Only name and salary', 50000]]];

        $jobs = (new ExtraSourceMapper())->mapAll($raw);

        $this->assertCount(0, $jobs);
    }

    public function test_handles_entry_with_no_skills(): void
    {
        $raw = ['Spain' => [['CTO', 100000, '<skills></skills>']]];

        $jobs = (new ExtraSourceMapper())->mapAll($raw);

        $this->assertCount(1, $jobs);
        $this->assertSame([], $jobs[0]->skills);
    }

    public function test_handles_malformed_skills_xml_gracefully(): void
    {
        $raw = ['USA' => [['Broken Skills Job', 50000, '<not-valid-xml']]];

        $jobs = (new ExtraSourceMapper())->mapAll($raw);

        $this->assertCount(1, $jobs);
        $this->assertSame([], $jobs[0]->skills);
    }

    public function test_handles_non_numeric_salary(): void
    {
        $raw = ['USA' => [['Unpaid Internship', 'n/a', '<skills></skills>']]];

        $jobs = (new ExtraSourceMapper())->mapAll($raw);

        $this->assertCount(1, $jobs);
        $this->assertNull($jobs[0]->salaryMin);
        $this->assertNull($jobs[0]->salaryMax);
    }

    public function test_returns_empty_array_for_empty_response(): void
    {
        $this->assertSame([], (new ExtraSourceMapper())->mapAll([]));
    }
}
