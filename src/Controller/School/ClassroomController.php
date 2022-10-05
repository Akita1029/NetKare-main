<?php

namespace App\Controller\School;

use App\Entity\Classroom;
use App\Form\ClassroomType;
use App\Repository\ClassroomRepository;
use App\Service\Pagination;
use App\Service\Search;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/school/classroom')]
class ClassroomController extends AbstractController
{
    private function checkClassroomOwner(Classroom $classroom): void
    {
        if ($classroom->getSchool() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
    }

    #[Route('/', name: 'school_classroom_index', methods: ['GET'])]
    public function index(ClassroomRepository $classroomRepository, Pagination $pagination): Response
    {
        $paginatorPerPage = ClassroomRepository::PAGINATOR_PER_PAGE;

        $offset = $pagination->getOffset($paginatorPerPage);

        $classrooms = $classroomRepository->getClassroomPaginator($offset, null, $this->getUser());

        return $this->render('school/classroom/index.html.twig', [
            'classrooms' => $classrooms,
            'paginatorPerPage' => $paginatorPerPage
        ]);
    }

    #[Route('/new', name: 'school_classroom_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Search $search): Response
    {
        $classroom = new Classroom();
        $classroom->setSchool($this->getUser());

        $form = $this->createForm(ClassroomType::class, $classroom, ['has_school' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($classroom);
            $entityManager->flush();

            return $this->redirectToRoute('school_classroom_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('school/classroom/new.html.twig', [
            'classroom' => $classroom,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'school_classroom_show', methods: ['GET'])]
    public function show(Classroom $classroom): Response
    {
        $this->checkClassroomOwner($classroom);

        return $this->render('school/classroom/show.html.twig', [
            'classroom' => $classroom,
        ]);
    }

    #[Route('/{id}/edit', name: 'school_classroom_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Classroom $classroom, EntityManagerInterface $entityManager): Response
    {
        $this->checkClassroomOwner($classroom);

        $form = $this->createForm(ClassroomType::class, $classroom, ['has_school' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('school_classroom_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('school/classroom/edit.html.twig', [
            'classroom' => $classroom,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'school_classroom_delete', methods: ['POST'])]
    public function delete(Request $request, Classroom $classroom, EntityManagerInterface $entityManager): Response
    {
        $this->checkClassroomOwner($classroom);

        if ($this->isCsrfTokenValid('delete' . $classroom->getId(), $request->request->get('_token'))) {
            $entityManager->remove($classroom);
            $entityManager->flush();
        }

        return $this->redirectToRoute('school_classroom_index', [], Response::HTTP_SEE_OTHER);
    }
}
