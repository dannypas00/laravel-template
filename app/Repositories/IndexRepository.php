<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\SettingsInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

class IndexRepository
{
    /**
     * @codeCoverageIgnore It's just the vendor query builder
     */
    public function getPaginator(SettingsInterface $settings): LengthAwarePaginator
    {
        return QueryBuilder::for($settings->getModelName())
            ->allowedFields($settings->getAllowedFields())
            ->allowedIncludes($settings->getAllowedIncludes())
            ->allowedFilters($settings->getAllowedFilters())
            ->allowedSorts($settings->getAllowedSorts())
            ->paginate(request('per_page'))
            ->appends(request()?->query());
    }
}
