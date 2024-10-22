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

    public function paginate(int $page): Pager
    {
        $pager = new Pager();
        $pager->setTotalItems($this->count());
        $pager->setTotalPages(
            (int) ceil($pager->getTotalItems() / self::PAGE_SIZE)
        );
        $pager->setCurrentPage($page);
        $pager->setPreviousPage(
            $page > 1 ? $page - 1 : null
        );
        $pager->setNextPage(
            $page < $pager->getTotalPages() ? $page + 1 : null
        );
        $pager->setLimit(self::PAGE_SIZE);
        $pager->setOffset(
            $pager->getLimit() * ($page-1)
        );

        if ($pager->getTotalPages() < $pager->getCurrentPage()) {
            throw new NotFoundHttpException(
                sprintf('Invalid page number %d requested.', $page)
            );
        }

        return $pager;
    }
}
