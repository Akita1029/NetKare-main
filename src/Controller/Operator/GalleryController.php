<?php

namespace App\Controller\Operator;

use App\Entity\Student;
use App\Entity\YearbookStudentImage;
use App\Entity\YearbookStudentVideo;
use App\Repository\StudentRepository;
use App\Repository\YearbookStudentImageRepository;
use App\Repository\YearbookStudentVideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/operator/gallery')]
class GalleryController extends AbstractController
{
    #[Route('/', name: 'operator_gallery_index', methods: ['GET'])]
    public function index(StudentRepository $studentRepository): Response
    {
        $students = $studentRepository->findByYearbook($this->getUser()->getYearbook());

        return $this->render('operator/gallery/index.html.twig', [
            'students' => $students
        ]);
    }

    #[Route('/{id}', name: 'operator_gallery_show', methods: ['GET'])]
    public function show(Student $student, YearbookStudentImageRepository $yearbookStudentImageRepository, YearbookStudentVideoRepository $yearbookStudentVideoRepository): Response
    {
        $yearbookStudentImages = $yearbookStudentImageRepository->findBy(['student' => $student]);
        $yearbookStudentVideos = $yearbookStudentVideoRepository->findBy(['student' => $student]);

        return $this->render('operator/gallery/show.html.twig', [
            'images' => $yearbookStudentImages,
            'videos' => $yearbookStudentVideos
        ]);
    }

    #[Route('/image/{id}', name: 'operator_gallery_image_delete', methods: ['POST'])]
    public function delete(Request $request, YearbookStudentImage $yearbookStudentImage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $yearbookStudentImage->getId(), $request->request->get('_token'))) {
            $entityManager->remove($yearbookStudentImage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('operator_gallery_show', ['id' => $yearbookStudentImage->getStudent()->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/video/{id}', name: 'operator_gallery_video_delete', methods: ['POST'])]
    public function deleteVideo(Request $request, YearbookStudentVideo $yearbookStudentVideo, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $yearbookStudentVideo->getId(), $request->request->get('_token'))) {
            $entityManager->remove($yearbookStudentVideo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('operator_gallery_show', ['id' => $yearbookStudentVideo->getStudent()->getId()], Response::HTTP_SEE_OTHER);
    }
}
