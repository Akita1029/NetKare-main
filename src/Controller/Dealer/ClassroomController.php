<?php

namespace App\Controller\Dealer;

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
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/dealer/classroom')]
class ClassroomController extends AbstractController
{
    private function checkClassroomOwner(Classroom $classroom): void
    {
        if ($classroom->getSchool()->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
    }

    #[Route('/', name: 'dealer_classroom_index', methods: ['GET'])]
    public function index(ClassroomRepository $classroomRepository, Pagination $pagination, Search $search): Response
    {
        $school = $search->getSchool();

        if (!$school) {
            return $this->render('dealer/classroom/index.html.twig');
        }

        $paginatorPerPage = ClassroomRepository::PAGINATOR_PER_PAGE;

        $page = $pagination->getActivePage();
        $offset = ($page - 1) * $paginatorPerPage;

        $classrooms = $classroomRepository->getClassroomPaginator($offset, $this->getUser(), $school);

        return $this->render('dealer/classroom/index.html.twig', [
            'classrooms' => $classrooms
        ]);
    }

    #[Route('/new', name: 'dealer_classroom_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Search $search, SluggerInterface $slugger): Response
    {
        $school = $search->getSchool();

        if (!$school) {
            return $this->render('dealer/classroom/index.html.twig');
        }

        $classroom = new Classroom();
        $classroom->setSchool($search->getSchool());

        $form = $this->createForm(ClassroomType::class, $classroom, ['has_school' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('classrooms_directory'),
                    $newFilename
                );

                $classroom->setImageFilename($newFilename);
            }

            $entityManager->persist($classroom);
            $entityManager->flush();

            return $this->redirectToRoute('dealer_classroom_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dealer/classroom/new.html.twig', [
            'classroom' => $classroom,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'dealer_classroom_show', methods: ['GET'])]
    public function show(Classroom $classroom): Response
    {
        $this->checkClassroomOwner($classroom);

        return $this->render('dealer/classroom/show.html.twig', [
            'classroom' => $classroom,
        ]);
    }

    #[Route('/{id}/edit', name: 'dealer_classroom_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Classroom $classroom, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $this->checkClassroomOwner($classroom);

        $form = $this->createForm(ClassroomType::class, $classroom, ['has_school' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('classrooms_directory'),
                    $newFilename
                );

                $classroom->setImageFilename($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('dealer_classroom_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dealer/classroom/edit.html.twig', [
            'classroom' => $classroom,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'dealer_classroom_delete', methods: ['POST'])]
    public function delete(Request $request, Classroom $classroom, EntityManagerInterface $entityManager): Response
    {
        $this->checkClassroomOwner($classroom);

        if ($this->isCsrfTokenValid('delete' . $classroom->getId(), $request->request->get('_token'))) {
            $entityManager->remove($classroom);
            $entityManager->flush();
        }

        return $this->redirectToRoute('dealer_classroom_index', [], Response::HTTP_SEE_OTHER);
    }
}
