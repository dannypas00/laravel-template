<?php

declare(strict_types=1);

namespace App\Interfaces;

interface QueryBuilderControllerInterface
{
    public function getDataObject(): string;

    public function getSettings(): SettingsInterface;
}
