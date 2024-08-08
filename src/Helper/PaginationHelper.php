<?php

declare(strict_types=1);

namespace App\Helper;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;

class PaginationHelper implements PaginationHelperInterface
{
    private const PAGE_SIZE = 4;

    public function paginate(Query $query, int $page): \stdClass
    {
        $paginator = new Paginator($query);

        $pager = new \stdClass();
        $pager->totalItems = $paginator->count();
        $pager->totalPages = ceil($pager->totalItems / self::PAGE_SIZE);
        $pager->currentPage = $page;
        $pager->previousPage = $page > 1 ? $page - 1 : null;
        $pager->nextPage = $page < $pager->totalPages ? $page + 1 : null;
        $pager->limit = self::PAGE_SIZE;
        $pager->offset = $pager->limit * ($page-1);

        return $pager;
    }
}
