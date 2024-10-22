<?php

declare(strict_types=1);

namespace App\Helper;

use Symfony\Component\Serializer\Annotation\Groups;

#[Groups(['list'])]
class Pager
{
    private int $currentPage;

    private ?int $previousPage;

    private ?int $nextPage;

    private int $totalPages;

    private int $totalItems;

    private int $offset;

    private int $limit;

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function setCurrentPage(int $currentPage): void
    {
        $this->currentPage = $currentPage;
    }

    public function getPreviousPage(): int
    {
        return $this->previousPage;
    }

    public function setPreviousPage(?int $previousPage): void
    {
        $this->previousPage = $previousPage;
    }

    public function getNextPage(): int
    {
        return $this->nextPage;
    }

    public function setNextPage(?int $nextPage): void
    {
        $this->nextPage = $nextPage;
    }

    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    public function setTotalPages(int $totalPages): void
    {
        $this->totalPages = $totalPages;
    }

    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    public function setTotalItems(int $totalItems): void
    {
        $this->totalItems = $totalItems;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }
}
