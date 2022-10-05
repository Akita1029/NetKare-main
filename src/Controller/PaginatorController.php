<?php

namespace App\Controller;

use App\Service\Pagination;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class PaginatorController extends AbstractController
{
    private $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getMainRequest();
    }

    public function index(Pagination $pagination, Paginator $paginator, $paginatorPerPage = null): Response
    {
        if (!$paginatorPerPage) {
            $paginatorPerPage  = Pagination::PAGINATOR_PER_PAGE;
        }

        $route = $this->request->attributes->get('_route');
        $parameters = array_merge($this->request->attributes->get('_route_params'), $this->request->query->all());

        $page = $pagination->getActivePage();

        $prev = null;

        if ($page > 1) {
            $parameters['page'] = $page - 1;
            $prev = $this->generateUrl($route, $parameters);
        }

        $next = null;

        if ($page < $pagination->getMaxPage($paginator, $paginatorPerPage)) {
            $parameters['page'] = $page + 1;
            $next = $this->generateUrl($route, $parameters);
        }

        return $this->render('paginator/index.html.twig', [
            'prev' => $prev,
            'next' => $next
        ]);
    }
}
