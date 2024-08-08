<?php

namespace App\Helper;

use Doctrine\ORM\Query;

interface PaginationHelperInterface
{
    public function paginate(Query $query, int $page): \stdClass;
}
