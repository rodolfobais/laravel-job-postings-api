<?php

declare(strict_types=1);

namespace App\Infrastructure\Mail;

use App\Domain\Jobs\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobAlertMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /** @var Job */
    public $job;

    public function __construct(Job $job)
    {
        $this->job = $job;
    }

    public function build()
    {
        return $this
            ->subject('New job posted: ' . $this->job->title)
            ->view('emails.job-alert');
    }
}
