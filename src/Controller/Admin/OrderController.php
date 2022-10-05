<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Message\EmailNotification;
use App\Repository\OrderRepository;
use App\Service\Pagination;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/order')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'admin_order_index', methods: ['GET'])]
    public function index(OrderRepository $orderRepository, Pagination $pagination): Response
    {
        $paginatorPerPage = OrderRepository::PAGINATOR_PER_PAGE;

        $offset = $pagination->getOffset($paginatorPerPage);

        $orders = $orderRepository->getOrderPaginator($offset);

        return $this->render('admin/order/index.html.twig', [
            'paginatorPerPage' => $paginatorPerPage,
            'orders' => $orders
        ]);
    }

    #[Route('/{id}', name: 'admin_order_show', methods: ['GET', 'POST'])]
    public function show(Order $order, Request $request, TranslatorInterface $translator, EntityManagerInterface $entityManager, MessageBusInterface $messageBus): Response
    {
        $formBuilder = $this->createFormBuilder()
            ->add('status', ChoiceType::class, [
                'choices' => [
                    $translator->trans('order.statuses.' . Order::STATUS_PENDING) => Order::STATUS_PENDING,
                    $translator->trans('order.statuses.' . Order::STATUS_WAITING_FOR_APPROVAL) => Order::STATUS_WAITING_FOR_APPROVAL,
                    $translator->trans('order.statuses.' . Order::STATUS_WAITING_FOR_PAYMENT) => Order::STATUS_WAITING_FOR_PAYMENT,
                    $translator->trans('order.statuses.' . Order::STATUS_PROCESSED) => Order::STATUS_PROCESSED,
                    $translator->trans('order.statuses.' . Order::STATUS_COMPLETED) => Order::STATUS_COMPLETED,
                    $translator->trans('order.statuses.' . Order::STATUS_CANCELED) => Order::STATUS_CANCELED
                ],
                'choice_attr' => [
                    $translator->trans('order.statuses.' . Order::STATUS_PENDING) => [
                        'disabled' => 'disabled'
                    ]
                ],
            ])
            ->setData([
                'status' => $order->getStatus()
            ]);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $status = $form->get('status')->getData();

            $order->setStatus($status);

            $entityManager->flush();

            $email = (new Email)
                ->from('emircanok@gmail.com')
                ->to($order->getSchool()->getOwner()->getEmail())
                ->subject('Siparişiniz Güncellendi')
                ->text("\"{$order->getSchool()->getName()}\" adlı okul için vermiş olduğunuz siparişin durumu \"{$translator->trans('order.statuses.' .$status)}\" olarak güncellenmiştir.");

            $messageBus->dispatch(new EmailNotification($email));

            $this->addFlash('success', 'Siparişin durumu başarıyla güncellendi. Bayimize güncelleme bilgisi iletilmiştir.');

            return $this->redirectToRoute('admin_order_show', [
                'id' => $order->getId()
            ]);
        }

        return $this->render('admin/order/show.html.twig', [
            'order' => $order,
            'form' => $form->createView()
        ]);
    }
}
