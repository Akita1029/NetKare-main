<?php

namespace App\Controller\Operator;

use App\Entity\Memoir;
use App\Entity\Student;
use App\Repository\MemoirRepository;
use App\Repository\StudentRepository;
use App\Service\Pagination;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/operator/memoir')]
class MemoirController extends AbstractController
{
    #[Route('/', name: 'operator_memoir_index', methods: ['GET'])]
    public function index(Request $request, StudentRepository $studentRepository, MemoirRepository $memoirRepository, Pagination $pagination): Response
    {
        $students = $studentRepository->findBy($this->getUser()->getYearbook());

        $choice_label = function (Student $student) {
            return $student->getSchoolNumber() .  ' ' . $student->getName() . ' ' . $student->getSurname();
        };

        $formBuilder = $this->createFormBuilder()
            ->add('sender', EntityType::class, [
                'required' => false,
                'class' => Student::class,
                'choices' => $students,
                'choice_label' => $choice_label,
                'label' => 'memoir.labels.sender'
            ])
            ->add('receiver', EntityType::class, [
                'required' => false,
                'class' => Student::class,
                'choices' => $students,
                'choice_label' => $choice_label,
                'label' => 'memoir.labels.receiver'
            ]);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        $offset = $pagination->getOffset(MemoirRepository::PAGINATOR_PER_PAGE);

        if ($form->isSubmitted() && $form->isValid()) {
            $memoirs = $memoirRepository->getMemoirPaginator($offset, $form->get('sender')->getData(), $form->get('receiver')->getData());
        } else {
            $memoirs = $memoirRepository->getMemoirPaginator($offset);
        }

        return $this->renderForm('operator/memoir/index.html.twig', [
            'form' => $form,
            'memoirs' => $memoirs
        ]);
    }

    #[Route('/{id}', name: 'operator_memoir_delete', methods: ['POST'])]
    public function delete(Request $request, Memoir $memoir, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $memoir->getId(), $request->request->get('_token'))) {
            $entityManager->remove($memoir);
            $entityManager->flush();

            $this->addFlash('success', 'Anı yazısı başarıyla silindi.');
        }

        return $this->redirectToRoute('operator_memoir_index', [], Response::HTTP_SEE_OTHER);
    }
}
