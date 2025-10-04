<?php

declare(strict_types=1);

namespace App\Traits;

trait PadsArrayWithNull
{
    private function padArrayWithNull(array $keys, array $assocaitiveValues): array
    {
        return $assocaitiveValues + array_fill_keys($keys, null);
    }
}
