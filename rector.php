<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\Set\ValueObject\LevelSetList;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/config',
        __DIR__ . '/database',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ])
    ->withSets([LevelSetList::UP_TO_PHP_84, LaravelSetList::LARAVEL_110])
    ->withSkip([
        AddOverrideAttributeToOverriddenMethodsRector::class
    ])
    ->withTypeCoverageLevel(0);
