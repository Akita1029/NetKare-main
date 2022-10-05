<?php

namespace App\Controller\School;

use App\Entity\Classroom;
use App\Entity\Order;
use App\Entity\OrderLineStudent;
use App\Repository\OrderLineRepository;
use App\Repository\OrderLineStudentRepository;
use App\Repository\OrderRepository;
use App\Repository\StudentRepository;
use App\Service\Pagination;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/school/order')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'school_order_index', methods: ['GET'])]
    public function index(OrderRepository $orderRepository, Pagination $pagination): Response
    {
        $paginatorPerPage = OrderRepository::PAGINATOR_PER_PAGE;

        $offset = $pagination->getOffset($paginatorPerPage);

        $orders = $orderRepository->getOrderPaginator($offset, null, $this->getUser());

        return $this->render('school/order/index.html.twig', [
            'orders' => $orders,
            'paginatorPerPage' => $paginatorPerPage
        ]);
    }

    private function getFilter(): FormInterface
    {
        $formBuilder = $this->createFormBuilder()
            ->setMethod('GET')
            ->add('classroom', EntityType::class, [
                'class' => Classroom::class,
                'choices' => $this->getUser()->getClassrooms(),
                'required' => false,
                'placeholder' => 'Sınıf'
            ]);

        return $formBuilder->getForm();
    }

    #[Route('/{id}/included', name: 'school_order_line_student_included', methods: ['GET', 'POST'])]
    public function orderLineStudentInclude(Request $request, Order $order, Pagination $pagination, StudentRepository $studentRepository, OrderLineRepository $orderLineRepository, OrderLineStudentRepository $orderLineStudentRepository, EntityManagerInterface $entityManager): Response
    {
        if ($order->getStatus() !== Order::STATUS_PENDING) {
            throw $this->createAccessDeniedException();
        }

        $paginatorPerPage = StudentRepository::PAGINATOR_PER_PAGE;

        $offset = $pagination->getOffset($paginatorPerPage);

        $filter = $this->getFilter();

        $filter->handleRequest($request);

        if ($filter->isSubmitted() && $filter->isValid()) {
            $classroom = $filter->get('classroom')->getData();

            $students = $studentRepository->getStudentPaginator($offset, null, null, $classroom, $order);
        } else {
            $students = $studentRepository->getStudentPaginator($offset, null, null, null, $order);
        }

        $formBuilder = $this->createFormBuilder();

        $orderLines = $order->getOrderLines();

        foreach ($students as $student) {
            $f1 = $formBuilder->create($student->getId(), FormType::class);

            foreach ($orderLines as $orderLine) {
                $orderLineStudent = $orderLineStudentRepository->findOneBy([
                    'student' => $student,
                    'orderLine' => $orderLine
                ]);

                $included = false;

                if ($orderLineStudent) {
                    $included = $orderLineStudent->getIncluded();
                }

                $f2 = $formBuilder->create($orderLine->getId(), FormType::class)
                    ->add('included', CheckboxType::class, [
                        'label' => false,
                        'required' => false,
                        'data' => $included
                    ]);

                $f1->add($f2);
            }

            $formBuilder->add($f1);
        }

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->all() as $c1) {
                $studentId = $c1->getName();

                foreach ($c1->all() as $c2) {

                    $orderLineId = $c2->getName();

                    $orderLineStudent = $orderLineStudentRepository->findOneBy([
                        'orderLine' => $orderLineId,
                        'student' => $studentId,
                    ]);

                    if (!$orderLineStudent) {
                        $student = $studentRepository->find($studentId);
                        $orderLine = $orderLineRepository->find($orderLineId);

                        if (!$student || !$orderLine) {
                            continue;
                        }

                        $orderLineStudent = new OrderLineStudent;
                        $orderLineStudent->setOrderLine($orderLine);
                        $orderLineStudent->setStudent($student);

                        $entityManager->persist($orderLineStudent);
                    }

                    $orderLineStudent->setIncluded($c2->get('included')->getData());
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Ürün talepleri başarıyla güncellendi.');

            $this->redirectToRoute('school_order_line_student_included', ['id' => $order->getId()]);
        }

        return $this->renderForm('school/order/included.html.twig', [
            'form' => $form,
            'students' => $students,
            'paginatorPerPage' => $paginatorPerPage,
            'orderLines' => $orderLines,
            'filter' => $filter
        ]);
    }

    #[Route('/{id}/missed', name: 'school_order_line_student_missed', methods: ['GET', 'POST'])]
    public function orderLineStudentMissed(Request $request, Order $order, Pagination $pagination, StudentRepository $studentRepository, OrderLineRepository $orderLineRepository, OrderLineStudentRepository $orderLineStudentRepository, EntityManagerInterface $entityManager): Response
    {
        if ($order->getStatus() !== Order::STATUS_SHIPPED) {
            throw $this->createAccessDeniedException();
        }

        $paginatorPerPage = StudentRepository::PAGINATOR_PER_PAGE;

        $offset = $pagination->getOffset($paginatorPerPage);

        $filter = $this->getFilter();

        $filter->handleRequest($request);

        if ($filter->isSubmitted() && $filter->isValid()) {
            $classroom = $filter->get('classroom')->getData();

            $students = $studentRepository->getStudentPaginator($offset, null, null, $classroom, $order, true);
        } else {
            $students = $studentRepository->getStudentPaginator($offset, null, null, null, $order, true);
        }

        $formBuilder = $this->createFormBuilder();

        $orderLines = $order->getOrderLines();

        foreach ($students as $student) {
            $f1 = $formBuilder->create($student->getId(), FormType::class);

            foreach ($orderLines as $orderLine) {
                $orderLineStudent = $orderLineStudentRepository->findOneBy([
                    'student' => $student,
                    'orderLine' => $orderLine
                ]);

                $missed = false;

                if ($orderLineStudent) {
                    $missed = $orderLineStudent->getMissed();
                }

                $f2 = $formBuilder->create($orderLine->getId(), FormType::class)
                    ->add('missed', CheckboxType::class, [
                        'label' => false,
                        'required' => false,
                        'data' => $missed
                    ]);

                $f1->add($f2);
            }

            $formBuilder->add($f1);
        }

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->all() as $c1) {
                $studentId = $c1->getName();

                foreach ($c1->all() as $c2) {

                    $orderLineId = $c2->getName();

                    $orderLineStudent = $orderLineStudentRepository->findOneBy([
                        'orderLine' => $orderLineId,
                        'student' => $studentId,
                    ]);

                    if (!$orderLineStudent) {
                        $student = $studentRepository->find($studentId);
                        $orderLine = $orderLineRepository->find($orderLineId);

                        if (!$student || !$orderLine) {
                            continue;
                        }

                        $orderLineStudent = new OrderLineStudent;
                        $orderLineStudent->setOrderLine($orderLine);
                        $orderLineStudent->setStudent($student);

                        $entityManager->persist($orderLineStudent);
                    }

                    $orderLineStudent->setMissed($c2->get('missed')->getData());
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Ürün talepleri başarıyla güncellendi.');

            $this->redirectToRoute('school_order_line_student_missed', ['id' => $order->getId()]);
        }

        return $this->renderForm('school/order/missed.html.twig', [
            'form' => $form,
            'students' => $students,
            'paginatorPerPage' => $paginatorPerPage,
            'orderLines' => $orderLines,
            'filter' => $filter
        ]);
    }
}
