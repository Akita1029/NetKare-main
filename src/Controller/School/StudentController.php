<?php

namespace App\Controller\School;

use App\Entity\Classroom;
use App\Entity\Student;
use App\Form\StudentAlbumPhotoType;
use App\Form\StudentType;
use App\Repository\AlbumPhotoRepository;
use App\Repository\StudentRepository;
use App\Service\ImageUploader;
use App\Service\Pagination;
use App\Service\Search;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/school/student')]
class StudentController extends AbstractController
{
    private function checkStudentOwner(Student $student): void
    {
        if ($student->getClassroom()->getSchool()->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
    }

    #[Route('/', name: 'school_student_index', methods: ['GET'])]
    public function index(Request $request, StudentRepository $studentRepository, Search $search, Pagination $pagination): Response
    {
        $school = $this->getUser();

        $formBuilder = $this->createFormBuilder()
            ->setMethod('get')
            ->add('classroom', EntityType::class, [
                'required' => false,
                'class' => Classroom::class,
                'choices' => $school ? $school->getClassrooms() : [],
                'disabled' => !$school || $school->getClassrooms() === []
            ]);

        $filter = $formBuilder->getForm();

        $filter->handleRequest($request);

        $paginatorPerPage = StudentRepository::PAGINATOR_PER_PAGE;

        $offset = $pagination->getOffset($paginatorPerPage);

        if ($filter->isSubmitted() && $filter->isValid()) {
            $students = $studentRepository->getStudentPaginator($offset, null, $school, $filter->get('classroom')->getData());
        } else {
            $students = $studentRepository->getStudentPaginator($offset, null, $school);
        }

        return $this->render('school/student/index.html.twig', [
            'filter' => $filter->createView(),
            'students' => $students,
            'paginatorPerPage' => $paginatorPerPage
        ]);
    }

    #[Route('/new', name: 'school_student_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Search $search): Response
    {
        $student = new Student();

        $form = $this->createForm(StudentType::class, $student, [
            'school' => $this->getUser(),
            'has_school' => false
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($student);
            $entityManager->flush();

            return $this->redirectToRoute('school_student_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('school/student/new.html.twig', [
            'student' => $student,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'school_student_show', methods: ['GET'])]
    public function show(Student $student): Response
    {
        $this->checkStudentOwner($student);

        return $this->render('school/student/show.html.twig', [
            'student' => $student,
        ]);
    }

    #[Route('/{id}/edit', name: 'school_student_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Student $student, AlbumPhotoRepository $albumPhotoRepository, ImageUploader $imageUploader, EntityManagerInterface $entityManager): Response
    {
        $this->checkStudentOwner($student);

        $formBuilder = $this->createFormBuilder([
            'student' => $student,
            'photos' => $albumPhotoRepository->getStudentPhotos($student)
        ])
            ->add('student', StudentType::class, [
                'school' => $this->getUser(),
                'has_school' => false
            ])
            ->add('photos', CollectionType::class, [
                'entry_type' => StudentAlbumPhotoType::class
            ]);

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->get('photos') as $photo) {
                $imageFile = $photo->get('imageFile')->getData();

                if (!$imageFile) {
                    continue;
                }

                $image = $imageUploader->uploadImage($imageFile, true);

                $albumPhoto = $photo->getData();
                $albumPhoto->setImage($image);
            }

            $entityManager->flush();

            return $this->redirectToRoute('school_student_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('school/student/edit.html.twig', [
            'student' => $student,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'school_student_delete', methods: ['POST'])]
    public function delete(Request $request, Student $student, EntityManagerInterface $entityManager): Response
    {
        $this->checkStudentOwner($student);

        if ($this->isCsrfTokenValid('delete' . $student->getId(), $request->request->get('_token'))) {
            $entityManager->remove($student);
            $entityManager->flush();
        }

        return $this->redirectToRoute('school_student_index', [], Response::HTTP_SEE_OTHER);
    }
}
