<?php

namespace App\Helpers;

use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Collection;

class PaginationHelper
{
    public static function paginate($query, $resourceClass, $defaultPerPage = 10)
    {
        $perPage = request()->get('per_page', $defaultPerPage);
        $currentPage = request()->get('page', 1);

        if ($query instanceof Collection) {
            $results = $query->forPage($currentPage, $perPage)->values();
            $paginator = new Paginator(
                $results,
                $query->count(),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } else {
            $paginator = $query->paginate($perPage)->appends(request()->query());
        }

        return $resourceClass::collection($paginator);
    }
}
