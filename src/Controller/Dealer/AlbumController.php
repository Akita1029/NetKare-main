<?php

namespace App\Controller\Dealer;

use App\Entity\Album;
use App\Entity\AlbumPhoto;
use App\Entity\Classroom;
use App\Entity\Student;
use App\Form\AlbumType;
use App\Repository\AlbumPhotoRepository;
use App\Repository\AlbumRepository;
use App\Repository\StudentRepository;
use App\Service\ImageUploader;
use App\Service\Search;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Image;

#[Route('/dealer/album')]
class AlbumController extends AbstractController
{
    #[Route('/', name: 'app_dealer_album_index', methods: ['GET'])]
    public function index(AlbumRepository $albumRepository, Search $search): Response
    {
        $school = $search->getSchool();

        if (!$school) {
            return $this->render('dealer/album/index.html.twig');
        }

        $albums = $albumRepository->findByDealer($this->getUser(), $school);

        return $this->render('dealer/album/index.html.twig', [
            'albums' => $albums
        ]);
    }

    #[Route('/new', name: 'app_dealer_album_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AlbumRepository $albumRepository, Search $search): Response
    {
        $school = $search->getSchool();

        if (!$school) {
            return $this->render('dealer/album/new.html.twig');
        }

        $album = new Album();
        $album->setSchool($school);

        $form = $this->createForm(AlbumType::class, $album, [
            'has_school' => false
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $albumRepository->add($album);
            return $this->redirectToRoute('app_dealer_album_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dealer/album/new.html.twig', [
            'album' => $album,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dealer_album_show', methods: ['GET'])]
    public function show(Album $album): Response
    {
        return $this->render('dealer/album/show.html.twig', [
            'album' => $album,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_dealer_album_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Album $album, AlbumRepository $albumRepository): Response
    {
        $form = $this->createForm(AlbumType::class, $album, [
            'has_school' => false
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $albumRepository->add($album);
            return $this->redirectToRoute('app_dealer_album_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dealer/album/edit.html.twig', [
            'album' => $album,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dealer_album_delete', methods: ['POST'])]
    public function delete(Request $request, Album $album, AlbumRepository $albumRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $album->getId(), $request->request->get('_token'))) {
            $albumRepository->remove($album);
        }

        return $this->redirectToRoute('app_dealer_album_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/photo', name: 'app_dealer_album_photo', methods: ['GET', 'POST'])]
    public function photo(Request $request, Album $album, ImageUploader $imageUploader, StudentRepository $studentRepository, AlbumPhotoRepository $albumPhotoRepository, EntityManagerInterface $entityManager, Search $search): Response
    {
        $view = 'dealer/album/photo.html.twig';

        $school = $search->getSchool();

        if (!$school) {
            return $this->render($view);
        }

        $formBuilder = $this->createFormBuilder(null, ['method' => 'GET', 'csrf_protection' => false]);
        $formBuilder
            ->add('classroom', EntityType::class, [
                'label' => 'Sınıf',
                'required' => false,
                'class' => Classroom::class,
                'choices' => $album->getSchool()->getClassrooms()
            ])
            ->add('schoolNumber', NumberType::class, [
                'label' => 'Öğrenci No',
                'required' => false
            ])
            ->add('name', TextType::class, [
                'label' => 'İsim',
                'required' => false
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Durum',
                'required' => false,
                'choices' => [
                    'Did not come' => false,
                    'Came' => true
                ]
            ])
            ->add('sort', SubmitType::class, [
                'label' => 'Sırala'
            ]);

        $filter = $formBuilder->getForm();
        $filter->handleRequest($request);

        if ($filter->isSubmitted() && $filter->isValid()) {
            $filterData = $filter->getData();

            $photos = $albumPhotoRepository->filter($album->getId(), $filterData, $filter->get('sort')->isClicked());
        } else {
            $photos = $albumPhotoRepository->filter($album->getId());
        }

        $students = $studentRepository->findByAlbum($album);

        $forms = [];

        foreach ($photos as $photo) {
            $forms[] = $this->getFormForPhotoMatch($album, $students, [
                'photo' => $photo->getId(),
                'student' => $photo->getStudent()
            ])->createView();
        }

        $formBuilder = $this->createFormBuilder();
        $formBuilder
            ->add('classroom', EntityType::class, [
                'label' => 'Sınıf',
                'class' => Classroom::class,
                'choices' => $school->getClassrooms(),
            ])
            ->add('images', FileType::class, [
                'label' => 'Resimler',
                'constraints' => [
                    new Count([
                        'min' => 1
                    ]),
                    new All([
                        new Image()
                    ])
                ],
                'multiple' => true
            ]);

        $form2 = $formBuilder->getForm();
        $form2->handleRequest($request);

        if ($form2->isSubmitted() && $form2->isValid()) {
            $formData = $form2->getData();

            $classroom = $formData['classroom'];
            $images = $formData['images'];

            foreach ($photos as $photo) {
                if ($photo->getStudent() && $photo->getStudent()->getClassroom() === $classroom) {
                    $entityManager->remove($photo);
                }
            }

            foreach ($images as $key => $image) {
                $image = $imageUploader->uploadImage($image, true);

                $student = $studentRepository->findNStudentByClassroom($classroom, $key);

                $albumPhoto = new AlbumPhoto;
                $albumPhoto->setAlbum($album);
                $albumPhoto->setImage($image);
                $albumPhoto->setStudent($student);

                $entityManager->persist($albumPhoto);
            }

            $entityManager->flush();

            $this->addFlash('success', 'All images uploaded');

            return $this->redirectToRoute('app_dealer_album_photo', [
                'id' => $album->getId(),
            ]);
        }

        return $this->render($view, [
            'album' => $album,
            'filter' => $filter->createView(),
            'forms' => $forms,
            'form2' => $form2->createView(),
            'photos' => $photos
        ]);
    }

    #[Route('/{id}/photo/change', name: 'app_dealer_album_photo_change', methods: ['POST'])]
    public function photoMatch(Request $request, Album $album, EntityManagerInterface $entityManager, AlbumPhotoRepository $albumPhotoRepository, StudentRepository $studentRepository)
    {
        $students = $studentRepository->findByAlbum($album);

        $form = $this->getFormForPhotoMatch($album, $students);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $student = $formData['student'];

            $albumPhoto = $albumPhotoRepository->find($formData['photo']);

            $oldStudent = $albumPhoto->getStudent();

            $albumPhoto->setStudent($student);

            $entityManager->persist($albumPhoto);

            $albumPhoto = $albumPhotoRepository->findOneBy([
                'album' => $album,
                'student' => $student
            ]);

            if ($albumPhoto) {
                $albumPhoto->setStudent($oldStudent);
                $entityManager->persist($albumPhoto);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Öğrenci fotoğrafları değiştirildi.');
        }

        return $this->redirectToRoute('app_dealer_album_photo', ['id' => $album->getId()]);
    }

    private function getFormForPhotoMatch(Album $album, $students, $data = null): Form
    {
        $formBuilder = $this->createFormBuilder($data, [
            'action' => $this->generateUrl('app_dealer_album_photo_change', [
                'id' => $album->getId()
            ])
        ]);

        $formBuilder
            ->add('photo', HiddenType::class, [
                'label' => 'Fotoğraf'
            ])
            ->add('student', EntityType::class, [
                'label' => 'Öğrenci',
                'class' => Student::class,
                'choices' => $students,
                'choice_label' => function (Student $student) {
                    return $student->getClassroom()->getName() .  ' | ' . $student->getSchoolNumber() . ' | ' . $student->getName() . ' ' . $student->getSurname();
                },
                'required' => false
            ]);

        return $formBuilder->getForm();
    }

    #[Route('/{id}/photo/came', name: 'app_dealer_album_photo_came', methods: ['POST'])]
    public function photoCameUpdate(Request $request, AlbumPhotoRepository $albumPhotoRepository, EntityManagerInterface $entityManager): Response
    {
        $photoId = $request->request->get('photoId');
        $came = $request->request->get('came');

        $albumPhoto = $albumPhotoRepository->find($photoId);
        $albumPhoto->setCame(boolval($came));

        $entityManager->persist($albumPhoto);
        $entityManager->flush();

        return new Response();
    }

    #[Route('/{id}/photo/came/update', name: 'app_dealer_album_photo_came_update', methods: ['GET', 'POST'])]
    public function updateDidNotCome(Request $request, Album $album, StudentRepository $studentRepository, AlbumPhotoRepository $albumPhotoRepository, EntityManagerInterface $entityManager, ImageUploader $imageUploader): Response
    {
        $students = $studentRepository->findByAlbum($album);

        $formBuilder = $this->createFormBuilder();
        $formBuilder
            ->add('students', CollectionType::class, [
                'label' => false,
                'entry_type' => EntityType::class,
                'entry_options' => [
                    'class' => Student::class,
                    'choices' => $students,
                    'choice_label' => function (Student $student) {
                        return $student->getClassroom()->getName() . ' | ' . $student->getSchoolNumber() . ' | ' . $student->getName() . ' ' . $student->getSurname();
                    }
                ],
                'constraints' => [
                    new Count([
                        'min' => 1
                    ])
                ],
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true
            ])
            ->add('photos', FileType::class, [
                'label' => 'Fotoğraflar',
                'multiple' => true,
                'constraints' => [
                    new Count([
                        'min' => 1
                    ]),
                    new All([
                        new Image()
                    ])
                ]
            ]);

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $students = $form->get('students')->getData();
            $photos = $form->get('photos')->getData();

            if (count($students) !== count($photos)) {
                $this->addFlash('danger', 'Öğrenci sayısı ile fotoğraf sayısı eşit olmalıdır.');

                return $this->redirectToRoute('app_dealer_album_photo_came_update', [
                    'id' => $album->getId()
                ]);
            }

            foreach ($students as $key => $student) {
                $photo = $photos[$key];

                $albumPhoto = $albumPhotoRepository->findOneBy([
                    'album' => $album,
                    'student' => $student
                ]);

                if (!$albumPhoto) {
                    continue;
                }

                $image = $imageUploader->uploadImage($photo, true);

                $albumPhoto->setImage($image);
                $albumPhoto->setCame(true);

                $entityManager->persist($albumPhoto);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Eksik fotoğraflar güncellendi.');

            return $this->redirectToRoute('app_dealer_album_photo', [
                'id' => $album->getId()
            ]);
        }

        return $this->renderForm('dealer/album/photo_came_update.html.twig', [
            'form' => $form
        ]);
    }
}
