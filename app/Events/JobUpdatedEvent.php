<?php

declare(strict_types=1);

namespace App\Events;

use App\Enums\JobStatusEnum;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * @codeCoverageIgnore It's just data mapping
 */
class JobUpdatedEvent implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(private readonly string $identifier, private readonly int $progress, private readonly int $maxProgress, private readonly JobStatusEnum $status)
    {
    }

    public function broadcastWith(): array
    {
        return [
            'identifier'  => $this->identifier,
            'progress'    => $this->progress,
            'maxProgress' => $this->maxProgress,
            'status'      => $this->status->value,
        ];
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('jobs.' . $this->identifier),
        ];
    }
}
