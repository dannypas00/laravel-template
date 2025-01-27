<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\DataObjects\UserData;
use App\Http\Controllers\QueryBuilderController;
use App\Interfaces\SettingsInterface;
use App\Settings\UserSettings;

/**
 * @codeCoverageIgnore No reason to test query builder controllers
 */
class UserQueryBuilderController extends QueryBuilderController
{
    public function getDataObject(): string
    {
        return UserData::class;
    }

    public function getSettings(): SettingsInterface
    {
        return new UserSettings;
    }
}
