<?php

namespace App\Enums;

enum JobStatusEnum: string
{
    case QUEUED = 'queued';
    case RUNNING = 'running';
    case SUCCEEDED = 'succeeded';
    case FAILED = 'failed';
}
