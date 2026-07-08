<?php

declare(strict_types=1);

namespace App\Infrastructure\External;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExtraSourceClient
{
    /** @var string */
    private $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @return array<string, array<int, array{0: string, 1: int, 2: string}>> jobs grouped by country
     */
    public function getJobs(): array
    {
        try {
            $response = Http::timeout(5)->retry(3, 100)->get("{$this->baseUrl}/jobs");

            if ($response->successful()) {
                return $response->json() ?? [];
            }

            Log::warning('Extra source returned non-success status', [
                'status' => $response->status(),
            ]);

            return [];
        } catch (\Throwable $e) {
            Log::warning('Extra source request failed', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }
}
