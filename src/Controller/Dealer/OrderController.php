<?php

namespace App\Controller\Dealer;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Service\Pagination;
use App\Service\Search;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dealer/order')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'dealer_order_index', methods: ['GET'])]
    public function index(OrderRepository $orderRepository, Pagination $pagination, Search $search): Response
    {
        $view = 'dealer/order/index.html.twig';

        $school = $search->getSchool();

        if (!$school) {
            return $this->render($view);
        }

        $paginatorPerPage = OrderRepository::PAGINATOR_PER_PAGE;

        $offset = $pagination->getOffset($paginatorPerPage);

        $orders = $orderRepository->getOrderPaginator($offset, $this->getUser(), $school);

        return $this->render($view, [
            'paginatorPerPage' => $paginatorPerPage,
            'orders' => $orders
        ]);
    }

    #[Route('/{id}', name: 'dealer_order_show', methods: ['GET', 'POST'])]
    public function show(Order $order): Response
    {
        return $this->render('dealer/order/show.html.twig', [
            'order' => $order
        ]);
    }
}
