<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; color: #222;">
    <h2>New job opportunity: {{ $job->title }}</h2>

    @if ($job->company)
        <p><strong>Company:</strong> {{ $job->company }}</p>
    @endif

    @if ($job->location)
        <p><strong>Location:</strong> {{ $job->location }}</p>
    @endif

    @if ($job->salaryMin || $job->salaryMax)
        <p>
            <strong>Salary:</strong>
            @if ($job->salaryMin && $job->salaryMax && $job->salaryMin !== $job->salaryMax)
                {{ $job->salaryMin }} - {{ $job->salaryMax }}
            @else
                {{ $job->salaryMin ?? $job->salaryMax }}
            @endif
            {{ $job->currency }}
        </p>
    @endif

    @if (!empty($job->skills))
        <p><strong>Skills:</strong> {{ implode(', ', $job->skills) }}</p>
    @endif

    @if ($job->description)
        <p>{{ $job->description }}</p>
    @endif

    <p style="color: #888; font-size: 12px;">
        You are receiving this because you subscribed to Jobberwocky job alerts.
    </p>
</body>
</html>
