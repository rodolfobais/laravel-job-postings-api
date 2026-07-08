<?php

declare(strict_types=1);

namespace App\Infrastructure\External;

use App\Domain\Jobs\Job;
use DateTimeImmutable;
use Illuminate\Support\Facades\Log;

/**
 * Formato real confirmado corriendo jobberwocky-extra-source-v2 (GET /jobs):
 *   { "<country>": [ ["<name>", <salary:int>, "<skills XML>"], ... ], ... }
 * No provee company, description, id ni fecha de publicación.
 */
class ExtraSourceMapper
{
    /**
     * @param array<string, array<int, array{0: string, 1: int, 2: string}>> $raw
     * @return Job[]
     */
    public function mapAll(array $raw): array
    {
        $jobs = [];

        foreach ($raw as $country => $countryJobs) {
            if (!is_array($countryJobs)) {
                continue;
            }

            foreach ($countryJobs as $entry) {
                $job = $this->mapEntry((string) $country, $entry);
                if ($job !== null) {
                    $jobs[] = $job;
                }
            }
        }

        return $jobs;
    }

    /**
     * @param mixed $entry
     */
    private function mapEntry(string $country, $entry): ?Job
    {
        try {
            if (!is_array($entry) || count($entry) < 3) {
                Log::warning('Extra source job entry has unexpected shape', ['country' => $country, 'entry' => $entry]);
                return null;
            }

            [$name, $salary, $skillsXml] = $entry;

            if (!is_string($name) || trim($name) === '') {
                Log::warning('Extra source job entry missing name', ['country' => $country, 'entry' => $entry]);
                return null;
            }

            $salary = is_numeric($salary) ? (int) $salary : null;
            $skills = $this->parseSkillsXml((string) $skillsXml);
            $externalId = md5($country . '|' . $name . '|' . $salary);

            return new Job(
                $externalId,
                trim($name),
                new DateTimeImmutable(),
                null,
                $country,
                $salary,
                $salary,
                null,
                null,
                $skills,
                'external',
                $externalId
            );
        } catch (\Throwable $e) {
            Log::warning('Failed to map extra source job', [
                'country' => $country,
                'entry' => $entry,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * @return string[]
     */
    private function parseSkillsXml(string $skillsXml): array
    {
        if (trim($skillsXml) === '') {
            return [];
        }

        $previous = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($skillsXml);
        libxml_use_internal_errors($previous);

        if ($xml === false) {
            Log::warning('Failed to parse extra source skills XML', ['skills' => $skillsXml]);
            return [];
        }

        $skills = [];
        foreach ($xml->skill as $skill) {
            $value = trim((string) $skill);
            if ($value !== '') {
                $skills[] = $value;
            }
        }

        return $skills;
    }
}
