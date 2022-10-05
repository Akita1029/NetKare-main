<?php

namespace App\Controller\Dealer;

use App\Entity\Album;
use App\Entity\Classroom;
use App\Entity\School;
use App\Entity\Student;
use App\Form\CustomFieldType;
use App\Form\ImportStudentImageClassroomMatchType;
use App\Form\SchoolType;
use App\Message\ImportArchive;
use App\Message\ResetPassword;
use App\Repository\ClassroomRepository;
use App\Repository\SchoolRepository;
use App\Repository\StudentRepository;
use App\Service\Pagination;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as Reader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\File;
use ZipArchive;

#[Route('/dealer/school')]
class SchoolController extends AbstractController
{
    const IMPORT_SESSION_NAME = 'school-import-file-name';
    const IMPORT_SESSION_NAME_IMAGE = 'school-import-file-name-image';

    private function checkSchoolOwner(School $school): void
    {
        if ($school->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
    }

    #[Route('/', name: 'dealer_school_index', methods: ['GET'])]
    public function index(SchoolRepository $schoolRepository, Pagination $pagination): Response
    {
        $dealer = $this->getUser();

        $paginatorPerPage = SchoolRepository::PAGINATOR_PER_PAGE;

        $page = $pagination->getActivePage();
        $offset = ($page - 1) * $paginatorPerPage;

        $schools = $schoolRepository->getSchoolPaginator($offset, $dealer);

        return $this->render('dealer/school/index.html.twig', [
            'schools' => $schools,
            'paginatorPerPage' => $paginatorPerPage
        ]);
    }

    #[Route('/new', name: 'dealer_school_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, SluggerInterface $slugger): Response
    {
        $school = new School();
        $school->setPassword($userPasswordHasher->hashPassword($school, ''));

        $school->setOwner($this->getUser());

        $form = $this->createForm(SchoolType::class, $school);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $logoFile = $form->get('logo')->getData();

            if ($logoFile) {
                $originalFilename = pathinfo($logoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $logoFile->guessExtension();

                $logoFile->move(
                    $this->getParameter('schools_directory'),
                    $newFilename
                );

                $school->setLogoFilename($newFilename);
            }

            $entityManager->persist($school);

            $album = new Album;
            $album->setSchool($school);
            $album->setMain(true);
            $album->setName('e-Okul');

            $entityManager->persist($album);

            $entityManager->flush();

            return $this->redirectToRoute('dealer_school_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dealer/school/new.html.twig', [
            'school' => $school,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'dealer_school_show', methods: ['GET'])]
    public function show(School $school): Response
    {
        $this->checkSchoolOwner($school);

        return $this->render('dealer/school/show.html.twig', [
            'school' => $school,
        ]);
    }

    #[Route('/{id}/edit', name: 'dealer_school_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, School $school, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $this->checkSchoolOwner($school);

        $form = $this->createForm(SchoolType::class, $school);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $logoFile = $form->get('logo')->getData();

            if ($logoFile) {
                $originalFilename = pathinfo($logoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $logoFile->guessExtension();

                $logoFile->move(
                    $this->getParameter('schools_directory'),
                    $newFilename
                );

                $school->setLogoFilename($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('dealer_school_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dealer/school/edit.html.twig', [
            'school' => $school,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'dealer_school_delete', methods: ['POST'])]
    public function delete(Request $request, School $school, EntityManagerInterface $entityManager): Response
    {
        $this->checkSchoolOwner($school);

        if ($this->isCsrfTokenValid('delete' . $school->getId(), $request->request->get('_token'))) {
            $entityManager->remove($school);
            $entityManager->flush();
        }

        return $this->redirectToRoute('dealer_school_index', [], Response::HTTP_SEE_OTHER);
    }

    private function deleteImportExcelFile(SessionInterface $session): void
    {
        $excelFileName = $session->get(self::IMPORT_SESSION_NAME);

        if ($excelFileName) {
            $filesystem = new Filesystem;
            $filesystem->remove($excelFileName);

            $session->remove(self::IMPORT_SESSION_NAME);
        }
    }

    #[Route('/{id}/import', name: 'dealer_school_import', methods: ['GET', 'POST'])]
    public function import(Request $request, School $school, SessionInterface $session, FormFactoryInterface $formFactory)
    {
        $this->checkSchoolOwner($school);

        $this->deleteImportExcelFile($session);

        $form1 = $formFactory->createNamedBuilder('form1')
            ->add('excel', FileType::class, [
                'label' => 'school_import.labels.excel',
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        ]
                    ])
                ]
            ])
            ->getForm();

        $form1->handleRequest($request);

        if ($form1->isSubmitted() && $form1->isValid()) {
            $excelFile = $form1->get('excel')->getData();

            $directory = $this->getParameter('excels_directory');
            $newFilename = uniqid() . '.' . $excelFile->guessExtension();

            $excelFile->move(
                $directory,
                $newFilename
            );

            $session->set(self::IMPORT_SESSION_NAME, $directory . '/' . $newFilename);

            return $this->redirectToRoute('dealer_school_import_match', [
                'id' => $school->getId()
            ]);
        }

        $form2 = $formFactory->createNamedBuilder('form2')
            ->add('archive', FileType::class, [
                'label' => 'school_import.labels.archive',
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'application/zip'
                        ]
                    ])
                ]
            ])
            ->getForm();

        $form2->handleRequest($request);

        if ($form2->isSubmitted() && $form2->isValid()) {
            $archiveFile = $form2->get('archive')->getData();

            $directory = $this->getParameter('archives_directory');
            $newFilename = uniqid() . '.' . $archiveFile->guessExtension();

            $archiveFile->move(
                $directory,
                $newFilename
            );

            $session->set(self::IMPORT_SESSION_NAME_IMAGE, $directory . '/' . $newFilename);

            return $this->redirectToRoute('dealer_school_import_match_image', [
                'id' => $school->getId()
            ]);
        }

        return $this->render('dealer/school/import.html.twig', [
            'form1' => $form1->createView(),
            'form2' => $form2->createView()
        ]);
    }

    #[Route('/{id}/import/match', name: 'dealer_school_import_match', methods: ['GET', 'POST'])]
    public function importMatch(Request $request, School $school, SessionInterface $session, EntityManagerInterface $entityManager, ClassroomRepository $classroomRepository, StudentRepository $studentRepository)
    {
        $this->checkSchoolOwner($school);

        $excelFileName = $session->get(self::IMPORT_SESSION_NAME);

        if (!$excelFileName) {
            return $this->redirectToRoute('dealer_school_import', [
                'id' => $school->getId()
            ]);
        }

        $reader = new Reader();
        $reader->setReadDataOnly(true);

        $spreadsheet = $reader->load($excelFileName);

        $activeSheet = $spreadsheet->getActiveSheet();

        $highestDataColumn = $activeSheet->getHighestDataColumn();

        $columns = $activeSheet->rangeToArray('A1:' . $highestDataColumn . '1');
        $columns = $columns[0];

        $choices = array_flip($columns);

        $form = $this->createFormBuilder()
            ->add('classroom', ChoiceType::class, [
                'label' => 'student.labels.classroom',
                'choices' => $choices
            ])
            ->add('schoolNumber', ChoiceType::class, [
                'label' => 'student.labels.schoolNumber',
                'choices' => $choices
            ])
            ->add('name', ChoiceType::class, [
                'label' => 'student.labels.name',
                'choices' => $choices
            ])
            ->add('surname', ChoiceType::class, [
                'label' => 'student.labels.surname',
                'choices' => $choices
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'student.labels.gender',
                'choices' => $choices,
                'required' => false
            ])
            ->add('customFields', CollectionType::class, [
                'label' => 'student.labels.customFields',
                'entry_type' => CustomFieldType::class,
                'entry_options' => [
                    'label' => false,
                    'choices' => $choices
                ],
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Kaydet',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $matches = $form->getData();

            $classrooms = [];

            $rows = $activeSheet->rangeToArray('A2:' . $highestDataColumn . $activeSheet->getHighestDataRow());

            foreach ($rows as $row) {
                if (!$row[$matches['classroom']] || !$row[$matches['schoolNumber']] || !$row[$matches['name']] || !$row[$matches['surname']]) {
                    continue;
                }

                $classroomValue = trim($row[$matches['classroom']]);

                if (isset($classrooms[$classroomValue])) {
                    $classroom = $classrooms[$classroomValue];
                } else {
                    $classroom = $classroomRepository->findOneBy([
                        'school' => $school,
                        'name' => $classroomValue
                    ]);

                    if (!$classroom) {
                        $classroom = new Classroom;
                        $classroom->setSchool($school);
                        $classroom->setName($classroomValue);

                        $entityManager->persist($classroom);
                    }

                    $classrooms[$classroomValue] = $classroom;
                }

                $student = null;

                $schoolNumber = trim($row[$matches['schoolNumber']]);

                if (!$student) {
                    $student = $studentRepository->findOneBy([
                        'schoolNumber' => $schoolNumber
                    ]);
                }

                $name = trim($row[$matches['name']]);
                $surname = trim($row[$matches['surname']]);

                if (!$student) {
                    $student = $studentRepository->findOneBy([
                        'name' => $name,
                        'surname' => $surname
                    ]);
                }

                if (!$student) {
                    $student = new Student;

                    $entityManager->persist($student);
                }

                $student->setClassroom($classroom);
                $student->setSchoolNumber($schoolNumber);
                $student->setName($name);
                $student->setSurname($surname);

                if ($matches['gender'] && $row[$matches['gender']]) {
                    $student->setGender($row[$matches['gender']]);
                }
            }

            $entityManager->flush();

            $this->addFlash('success', $school->getName() . ' okuluna bağlı ' . count($rows) . ' adet kayıt güncellendi.');

            $this->deleteImportExcelFile($session);

            return $this->redirectToRoute('dealer_school_import', [
                'id' => $school->getId()
            ]);
        }

        return $this->renderForm('dealer/school/import_match.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}/import/match/image', name: 'dealer_school_import_match_image', methods: ['GET', 'POST'])]
    public function importMatchImage(Request $request, School $school, SessionInterface $session, ClassroomRepository $classroomRepository, MessageBusInterface $messageBus)
    {
        $this->checkSchoolOwner($school);

        $archiveFileName = $session->get(self::IMPORT_SESSION_NAME_IMAGE);

        if (!$archiveFileName) {
            return $this->redirectToRoute('dealer_school_import', [
                'id' => $school->getId()
            ]);
        }

        $paths = explode('/', $archiveFileName);
        $folderName = explode('.', $paths[count($paths) - 1])[0];

        $archivePathName = $this->getParameter('archives_directory') . '/' . $folderName;

        $zip = new ZipArchive;

        if ($zip->open($archiveFileName) === true) {
            $zip->extractTo($archivePathName);
            $zip->close();
        }

        unset($zip);

        $finder = new Finder;

        $finder->in($archivePathName)->directories()->depth(0)->filter(function (\SplFileInfo $file) {
            if ($file->getFilename() === '__MACOSX') {
                return false;
            }
        })->sortByName();

        $folders = [];

        foreach ($finder as $file) {
            $filename = $file->getFilename();
            $folders[$filename] = $filename;
        }

        unset($finder);

        $classrooms = $classroomRepository->findBy(['school' => $school]);

        $data = [
            'albumName' => 'E-Okul',
            'matches' => []
        ];

        foreach ($classrooms as $classroom) {
            $data['matches'][] = [
                'classroom' => $classroom,
                'folder' => null
            ];
        }

        $formBuilder = $this->createFormBuilder($data);

        $formBuilder
            ->add('albumName', TextType::class)
            ->add('matches', CollectionType::class, [
                'label' => false,
                'entry_type' => ImportStudentImageClassroomMatchType::class,
                'entry_options' => [
                    'folders' => $folders,
                    'label' => false
                ]
            ]);

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $messageBus->dispatch(new ImportArchive($this->getUser()->getId(), $school->getId(), $archivePathName, $formData));

            unlink($archiveFileName);

            $this->addFlash('success_for_form2', 'İçe aktarma işlemi tamamlandığında eposta ile bildirim alacaksınız.');

            return $this->redirectToRoute('dealer_school_import', ['id' => $school->getId()]);
        }

        return $this->renderForm('dealer/school/import_match_image.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}/password', name: 'dealer_school_password', methods: 'POST')]
    public function makePassword(School $school, Request $request, MessageBusInterface $messageBus)
    {
        $this->checkSchoolOwner($school);

        if ($this->isCsrfTokenValid('password' . $school->getId(), $request->request->get('_token'))) {
            $messageBus->dispatch(new ResetPassword($school->getId()));

            $this->addFlash('success', 'Şifreler hazır olduğunda eposta adresinize iletilecektir.');

            return $this->redirectToRoute('dealer_school_index');
        }

        return $this->redirectToRoute('dealer_school_index', [], Response::HTTP_SEE_OTHER);
    }
}
