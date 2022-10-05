<?php

namespace App\Controller\Student;

use App\Entity\Classroom;
use App\Entity\Memoir;
use App\Entity\Yearbook;
use App\Repository\MemoirRepository;
use App\Repository\StudentRepository;
use App\Repository\YearbookRepository;
use App\Service\Pagination;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/student/memoir')]
class MemoirController extends YearbookController
{
    #[Route('/self', name: 'student_memoir_self', methods: ['GET', 'POST'])]
    public function selfMemoir(Request $request, MemoirRepository $memoirRepository, EntityManagerInterface $entityManager): Response
    {
        $this->checkUserYearbook();

        $user = $this->getUser();

        $memoir = $memoirRepository->findOneBy([
            'sender' => $user,
            'receiver' => $user
        ]);

        if (!$memoir) {
            $memoir = new Memoir;

            $memoir->setSender($user);
            $memoir->setReceiver($user);

            $entityManager->persist($memoir);
        }

        $data = [
            'text' => $memoir->getText()
        ];

        $formBuilder = $this->createFormBuilder($data)
            ->add('text', TextareaType::class);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $memoir->setText($form->get('text')->getData());

            $entityManager->flush();

            $this->addFlash('success', 'Başarıyla güncellendi.');

            return $this->redirectToRoute('student_memoir_self');
        }

        return $this->renderForm('student/memoir/self.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/self/read', name: 'student_memoir_self_read', methods: 'GET')]
    public function showForMe(MemoirRepository $memoirRepository): Response
    {
        $this->checkUserYearbook();

        $memoirs = $memoirRepository->getWrittenForMe($this->getUser());

        return $this->render('student/memoir/self_read.html.twig', [
            'memoirs' => $memoirs
        ]);
    }

    #[Route('/friend', name: 'student_memoir', methods: 'GET')]
    public function index(Request $request, StudentRepository $studentRepository, Pagination $pagination, YearbookRepository $yearbookRepository): Response
    {
        $this->checkUserYearbook();

        $yearbook = $this->getYearbook();

        $paginatorPerPage = StudentRepository::PAGINATOR_PER_PAGE;

        $offset = $pagination->getOffset($paginatorPerPage);

        $formBuilder = $this->createFormBuilder()
            ->setMethod('get');

        if ($yearbook->getMemoir() === Yearbook::MEMOIR_EVERYBODY) {
            $formBuilder->add('classroom', EntityType::class, [
                'class' => Classroom::class,
                'choices' => $yearbook->getClassrooms(),
                'required' => false,
                'placeholder' => 'Sınıf'
            ]);
        }

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $students = $studentRepository->getStudentPaginatorWithMemoir($this->getUser(), $yearbook, $offset, $form->get('classroom')->getData());
        } else {
            $students = $studentRepository->getStudentPaginatorWithMemoir($this->getUser(), $yearbook, $offset);
        }

        return $this->renderForm('student/memoir/friend.html.twig', [
            'form' => $form,
            'students' => $students,
            'paginatorPerPage' => $paginatorPerPage
        ]);
    }

    #[Route('/friend/{receiverId}', name: 'student_memoir_write', methods: ['GET', 'POST'])]
    public function write(Request $request, int $receiverId, StudentRepository $studentRepository, MemoirRepository $memoirRepository, EntityManagerInterface $entityManager, YearbookRepository $yearbookRepository): Response
    {
        $this->checkUserYearbook();

        $yearbook = $this->getYearbook();

        $sender = $this->getUser();
        $receiver = $studentRepository->find($receiverId);

        if (!in_array($receiver->getClassroom()->getId(), $yearbook->getClassrooms()->map(function (Classroom $classrooom) {
            return $classrooom->getId();
        })->toArray())) {
            $this->addFlash('danger', 'Kayıtlı olduğunuz albümde mesaj yazmak istediğiniz öğrenci bulunamadı.');

            return $this->redirectToRoute('student_dashboard');
        }

        $memoir = $memoirRepository->findOneBy([
            'sender' => $sender,
            'receiver' => $receiver
        ]);

        $data = [
            'text' => null
        ];

        if ($memoir) {
            $data['text'] = $memoir->getText();
        }

        $formBuilder = $this->createFormBuilder($data)
            ->add('text', TextareaType::class);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$memoir) {
                $memoir = (new Memoir)
                    ->setSender($sender)
                    ->setReceiver($receiver);

                $entityManager->persist($memoir);
            }

            $memoir->setText($form->get('text')->getData());

            $entityManager->flush();

            $this->addFlash('success', "\"{$receiver->getName()} {$receiver->getSurname()}\" için yazdığınız anı yazısı kaydedilmiştir. İsterseniz, tekrar düzenleyebilirsiniz.");

            return $this->redirectToRoute('student_memoir');
        }

        return $this->renderForm('student/memoir/friend_detail.html.twig', [
            'form' => $form,
            'sender' => $sender,
            'receiver' => $receiver
        ]);
    }
}
