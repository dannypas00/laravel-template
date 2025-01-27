<?php

declare(strict_types=1);
use App\Enums\JobStatusEnum;
use App\Events\JobUpdatedEvent;
use App\Jobs\BaseProgressJob;

beforeEach(function (): void {
    $testClass = new class extends BaseProgressJob
    {
        protected int $ratePerMinute = 3;

        public function exposeStartEvent(): void
        {
            $this->startEvent();
        }

        public function exposeSucceedEvent(): void
        {
            $this->succeedEvent();
        }

        public function exposeProgress(int $amount): void
        {
            $this->progress($amount);
        }
    };

    Event::fake();

    $this->job = new $testClass;
});

it('gets identifier', function (): void {
    expect($this->job->getIdentifier())
        ->toBeUuid();
});

it('dispatches start message', function (): void {
    $this->job->exposeStartEvent();

    Event::assertDispatched(JobUpdatedEvent::class,
        function (JobUpdatedEvent $event): bool {
            expect($event->broadcastWith())
                ->identifier->toBeUuid()
                ->progress->toBe(0)
                ->maxProgress->toBe(100)
                ->status->toBe(JobStatusEnum::RUNNING->value);

            return true;
        });
});

it('dispatches succeed message', function (): void {
    $this->job->exposeSucceedEvent();

    Event::assertDispatched(JobUpdatedEvent::class,
        function (JobUpdatedEvent $event): bool {
            expect($event->broadcastWith())
                ->identifier->toBeUuid()
                ->progress->toBe(0)
                ->maxProgress->toBe(100)
                ->status->toBe(JobStatusEnum::SUCCEEDED->value);

            return true;
        });
});

it('dispatches fail message', function (): void {
    $this->job->fail();

    Event::assertDispatched(JobUpdatedEvent::class,
        function (JobUpdatedEvent $event): bool {
            expect($event->broadcastWith())
                ->identifier->toBeUuid()
                ->progress->toBe(0)
                ->maxProgress->toBe(100)
                ->status->toBe(JobStatusEnum::FAILED->value);

            return true;
        });
});

it('dispatches progress events', function (): void {
    $this->job->exposeProgress(1);
    $this->job->exposeProgress(1);
    $this->job->exposeProgress(1);

    Event::assertDispatchedTimes(JobUpdatedEvent::class, 3);

    Event::assertDispatched(JobUpdatedEvent::class,
        function (JobUpdatedEvent $event): bool {
            expect($event->broadcastWith())
                ->identifier->toBeUuid()
                ->progress->toBeGreaterThan(0)
                ->maxProgress->toBe(100)
                ->status->toBe(JobStatusEnum::RUNNING->value);

            return true;
        });
});

it('increases max progress', function (): void {
    $this->job->exposeProgress(1000);

    Event::assertDispatched(JobUpdatedEvent::class,
        function (JobUpdatedEvent $event): bool {
            expect($event->broadcastWith())
                ->identifier->toBeUuid()
                ->progress->toBe(1000)
                ->maxProgress->toBe(1000)
                ->status->toBe(JobStatusEnum::RUNNING->value);

            return true;
        });
});

it('rate limits', function (): void {
    $this->job->exposeProgress(1);
    $this->job->exposeProgress(1);
    $this->job->exposeProgress(1);
    $this->job->exposeProgress(1);
    $this->job->exposeProgress(1);
    $this->job->exposeProgress(1);

    Event::assertDispatchedTimes(JobUpdatedEvent::class, 3);
});
