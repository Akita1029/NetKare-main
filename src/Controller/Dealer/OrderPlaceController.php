<?php

namespace App\Controller\Dealer;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\Product;
use App\Form\OrderLineType;
use App\Repository\CategoryRepository;
use App\Repository\OrderLineRepository;
use App\Repository\OrderRepository;
use App\Service\Search;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dealer/order-place')]
class OrderPlaceController extends AbstractController
{
    #[Route('/', name: 'dealer_order_place_index', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoryRepository $categoryRepository, Search $search, OrderRepository $orderRepository, EntityManagerInterface $entityManager): Response
    {
        $categories = $categoryRepository->findAll();

        $school = $search->getSchool();

        $order = $orderRepository->findOneBy([
            'school' => $school,
            'status' => Order::STATUS_PENDING
        ]);

        $formBuilder = $this->createFormBuilder();

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$order || empty($order->getOrderLines())) {
                $this->addFlash('danger', 'Sepetinizde ürün olmadığı için siparişinizi tamamlayamıyoruz. "Ürün Ekle" butonunu kullanarak siparişinize ürün ekleyebilirsiniz.');

                return $this->redirectToRoute('dealer_order_place_index');
            }

            $order->setStatus(Order::STATUS_WAITING_FOR_APPROVAL);

            $entityManager->flush();

            $this->addFlash('success', 'Siparişiniz yönetici onayına sunulmuştur. Teşekkür ederiz.');

            return $this->redirectToRoute('dealer_order_place_index');
        }

        return $this->render('dealer/order_place/new.html.twig', [
            'categories' => $categories,
            'order' => $order,
            'form' => $form->createView()
        ]);
    }

    #[Route('/add-order-line/{id}', name: 'dealer_order_place_add_order_line', methods: ['GET', 'POST'])]
    public function addOrderLine(Request $request, Product $product, Search $search, OrderRepository $orderRepository, EntityManagerInterface $entityManager): Response
    {
        $school = $search->getSchool();

        $orderLine = new OrderLine;
        $orderLine->setProduct($product);
        $orderLine->setPrice(0);

        $form = $this->createForm(OrderLineType::class, $orderLine, [
            'school' => $school,
            'product' => $product
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order = $orderRepository->findOneBy(['school' => $school, 'status' => Order::STATUS_PENDING]);

            if (!$order) {
                $order = new Order;
                $order->setSchool($school);

                $entityManager->persist($order);
            }

            $orderLine->setParentOrder($order);
            $entityManager->persist($orderLine);

            $entityManager->flush();

            $this->addFlash('success', 'Ürün sepetinize eklendi.');

            return $this->redirectToRoute('dealer_order_place_index');
        }

        return $this->render('dealer/order_place/add_order_line.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    #[Route('/remove-order-line/{id}', name: 'dealer_order_place_remove_order_line', methods: ['POST'])]
    public function removeOrderLine(Request $request, OrderLine $orderLine, OrderLineRepository $orderLineRepository)
    {
        if ($this->isCsrfTokenValid('delete' . $orderLine->getId(), $request->request->get('_token'))) {
            $orderLineRepository->remove($orderLine);
        }

        return $this->redirectToRoute('dealer_order_place_index', [], Response::HTTP_SEE_OTHER);
    }
}
