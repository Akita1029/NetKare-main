<?php

namespace App\Controller\Student;

use App\Entity\YearbookStudentImage;
use App\Entity\YearbookStudentVideo;
use App\Repository\YearbookStudentImageRepository;
use App\Repository\YearbookStudentVideoRepository;
use App\Service\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Regex;

#[Route('/student/gallery')]
class GalleryController extends YearbookController
{
    #[Route('/', name: 'student_gallery_index', methods: ['GET', 'POST'])]
    public function index(Request $request, ImageUploader $imageUploader, EntityManagerInterface $entityManager, YearbookStudentImageRepository $yearbookStudentImageRepository, YearbookStudentVideoRepository $yearbookStudentVideoRepository): Response
    {
        $student = $this->getUser();

        $yearbook = $this->getYearbook();
        $yearbookStudentImages = $yearbookStudentImageRepository->findBy(['student' => $student]);

        $form = null;

        if ($yearbook->getImageUploadLimit() > count($yearbookStudentImages)) {
            $formBuilder = $this->createFormBuilder()
                ->add('image', FileType::class, ['constraints' => [new Image()]]);

            $form = $formBuilder->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $image = $form->get('image')->getData();

                $image = $imageUploader->uploadImage($image, true);

                $yearbookStudentImage = new YearbookStudentImage;
                $yearbookStudentImage->setYearbook($this->getYearbook());
                $yearbookStudentImage->setStudent($student);
                $yearbookStudentImage->setImage($image);

                $entityManager->persist($yearbookStudentImage);

                $entityManager->flush();

                $this->addFlash('success', 'Fotoğrafınız başarıyla eklenmiştir.');

                return $this->redirectToRoute('student_gallery_index');
            }
        }

        $formBuilder = $this->createFormBuilder()
            ->add('youtubeUrl', UrlType::class, [
                'constraints' => [
                    new Regex('/https:\/\/(www\.)?youtube\.com\/watch\?.*v=([a-zA-Z0-9]+).*/')
                ]
            ]);

        $form2 = $formBuilder->getForm();

        $form2->handleRequest($request);

        if ($form2->isSubmitted() && $form2->isValid()) {
            $youtubeUrl = $form2->get('youtubeUrl')->getData();

            parse_str(parse_url($youtubeUrl, PHP_URL_QUERY), $query);

            $yearbookStudentVideo = new YearbookStudentVideo;
            $yearbookStudentVideo->setYearbook($this->getYearbook());
            $yearbookStudentVideo->setStudent($student);
            $yearbookStudentVideo->setYoutubeVideoId($query['v']);

            $entityManager->persist($yearbookStudentVideo);

            $entityManager->flush();

            $this->addFlash('success', 'Videonuz başarıyla eklenmiştir.');

            return $this->redirectToRoute('student_gallery_index');
        }

        $yearbookStudentVideos = $yearbookStudentVideoRepository->findBy(['student' => $student]);

        return $this->render('student/gallery/index.html.twig', [
            'form' => $form ? $form->createView() : null,
            'form2' => $form2->createView(),
            'images' => $yearbookStudentImages,
            'videos' => $yearbookStudentVideos
        ]);
    }

    #[Route('/image/{id}', name: 'student_gallery_image_delete', methods: ['POST'])]
    public function delete(Request $request, YearbookStudentImage $yearbookStudentImage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $yearbookStudentImage->getId(), $request->request->get('_token'))) {
            $entityManager->remove($yearbookStudentImage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('student_gallery_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/video/{id}', name: 'student_gallery_video_delete', methods: ['POST'])]
    public function deleteVideo(Request $request, YearbookStudentVideo $yearbookStudentVideo, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $yearbookStudentVideo->getId(), $request->request->get('_token'))) {
            $entityManager->remove($yearbookStudentVideo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('student_gallery_index', [], Response::HTTP_SEE_OTHER);
    }
}
