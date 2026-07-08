<?php

namespace App\Infrastructure\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'company' => $this->company,
            'location' => $this->location,
            'salary' => $this->when(
                $this->salaryMin !== null || $this->salaryMax !== null,
                [
                    'min' => $this->salaryMin,
                    'max' => $this->salaryMax,
                    'currency' => $this->currency,
                ]
            ),
            'description' => $this->description,
            'skills' => $this->skills,
            'postedAt' => $this->postedAt->format('c'),
            'source' => $this->source,
            'externalId' => $this->externalId,
        ];
    }
}
