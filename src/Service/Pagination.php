<?php

namespace App\Service;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\RequestStack;

class Pagination
{
    public const PAGINATOR_PER_PAGE = 20;

    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getActivePage(): int
    {
        return $this->requestStack->getMainRequest()->query->getInt('page', 1);
    }

    public function getMaxPage(Paginator $paginator, $paginatorPerPage = self::PAGINATOR_PER_PAGE): int
    {
        return ceil(count($paginator) / $paginatorPerPage);
    }

    public function getOffset(int $paginatorPerPage): int
    {
        $page = $this->getActivePage();

        return ($page - 1) * $paginatorPerPage;
    }
}
