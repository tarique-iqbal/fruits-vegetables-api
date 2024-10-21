<?php

declare(strict_types=1);

namespace App\Helper;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaginationHelper extends Paginator
{
    private const PAGE_SIZE = 4;

    public function __construct(Query $query)
    {
        parent::__construct($query);
    }

    public function paginate(int $page): \stdClass
    {
        $pager = new \stdClass();
        $pager->totalItems = $this->count();
        $pager->totalPages = ceil($pager->totalItems / self::PAGE_SIZE);
        $pager->currentPage = $page;
        $pager->previousPage = $page > 1 ? $page - 1 : null;
        $pager->nextPage = $page < $pager->totalPages ? $page + 1 : null;
        $pager->limit = self::PAGE_SIZE;
        $pager->offset = $pager->limit * ($page-1);

        if ($pager->totalPages < $pager->currentPage) {
            throw new NotFoundHttpException(
                sprintf('Invalid page number %d requested.', $page)
            );
        }

        return $pager;
    }
}
