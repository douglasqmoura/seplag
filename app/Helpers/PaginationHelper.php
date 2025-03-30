<?php

namespace App\Helpers;

class PaginationHelper
{
    public static function paginate($query, $resourceClass, $defaultPerPage = 10)
    {
        $perPage = request()->get('per_page', $defaultPerPage);
        $paginator = $query->paginate($perPage)->appends(request()->query());

        return $resourceClass::collection($paginator);
    }
}
