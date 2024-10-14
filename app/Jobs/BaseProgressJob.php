<?php

namespace App\Jobs;

use App\Enums\JobStatusEnum;
use App\Events\JobUpdatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Log;
use Str;

class BaseProgressJob implements ShouldQueue
{
    use Queueable;

    // Progress is always displayed a $progress / $maxProgress
    // Updating $maxProgress during runtime is supported
    private int $progress = 0;
    protected int $maxProgress = 100;
    protected string $identifier;

    public function __construct()
    {
        $this->identifier = Str::uuid();
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    protected function startEvent(): void
    {
        JobUpdatedEvent::dispatch($this->identifier, $this->progress, $this->maxProgress, JobStatusEnum::RUNNING);
    }

    protected function succeedEvent(): void
    {
        JobUpdatedEvent::dispatch($this->identifier, $this->progress, $this->maxProgress, JobStatusEnum::SUCCEEDED);
    }

    protected function progress(int $amount): void
    {
        $this->progress += $amount;

        // Max progress can never be less than progress, so make it at least match if we guess the max wrong
        if ($this->progress > $this->maxProgress) {
            $this->maxProgress = $this->progress;
        }

        Log::debug('Progressing');

        JobUpdatedEvent::dispatch($this->identifier, $this->progress, $this->maxProgress, JobStatusEnum::RUNNING);
    }

    public function fail($exception = null)
    {
        JobUpdatedEvent::dispatch($this->identifier, $this->progress, $this->maxProgress, JobStatusEnum::FAILED);
    }
}
