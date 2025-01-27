<?php

namespace App\Http\Controllers\Users;

use App\DataObjects\UserData;
use App\Http\Controllers\QueryBuilderController;
use App\Interfaces\SettingsInterface;
use App\Settings\UserSettings;

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
