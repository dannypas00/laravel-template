<?php

declare(strict_types=1);

namespace App\Filters;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FilterDateRange implements Filter
{
    // TODO: This currently does weird stuff with dates in mysql
    public function __invoke(Builder $query, $value, string $property): void
    {
        if (is_array($value)) {
            $this->filterRange($query, $value, $property);
        }
        if (is_string($value)) {
            $this->filterExact($query, $value, $property);
        }
    }

    private function filterRange(Builder $query, array $value, string $property): void
    {
        if ($value['end'] ?? false) {
            $query->whereDate($property, '<=', Carbon::parse($value['end'])->endOfDay());
        }

        if ($value['start'] ?? false) {
            $query->whereDate($property, '>=', Carbon::parse($value['start'])->startOfDay());
        }
    }

    private function filterExact(Builder $query, string $value, string $property): void
    {
        $start = Carbon::parse($value);
        $end = Carbon::parse($value)->addDay();

        $query->whereDate($property, '>=', $start)
            ->whereDate($property, '<', $end);
    }
}
