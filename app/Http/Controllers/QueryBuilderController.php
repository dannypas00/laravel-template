<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Interfaces\QueryBuilderControllerInterface;
use App\Repositories\AllRepository;
use App\Repositories\IndexRepository;
use App\Repositories\ShowRepository;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\PaginatedDataCollection;

abstract class QueryBuilderController extends Controller implements QueryBuilderControllerInterface
{
    public function show(int $id, ShowRepository $showRepository): Data
    {
        return $this->getDataObject()::from($showRepository->show($this->getSettings(), $id));
    }

    public function index(IndexRepository $indexRepository): PaginatedDataCollection
    {
        return $this->getDataObject()::collect(
            $indexRepository->getPaginator($this->getSettings()),
            PaginatedDataCollection::class
        );
    }

    public function all(AllRepository $allRepository): DataCollection
    {
        return $this->getDataObject()::collect($allRepository->get($this->getSettings()));
    }
}
