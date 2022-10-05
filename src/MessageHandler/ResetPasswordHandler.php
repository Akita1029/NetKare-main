<?php

namespace App\MessageHandler;

use App\Message\EmailNotification;
use App\Message\ResetPassword;
use App\Repository\SchoolRepository;
use App\Service\PasswordGenerator;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler]
class ResetPasswordHandler
{
    private PasswordGenerator $passwordGenerator;
    private UserPasswordHasherInterface $userPasswordHasher;
    private EntityManagerInterface $entityManager;
    private MessageBusInterface $messageBus;
    private ParameterBagInterface $parameterBag;
    private SchoolRepository $schoolRepository;

    public function __construct(PasswordGenerator $passwordGenerator, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, MessageBusInterface $messageBus, ParameterBagInterface $parameterBag, SchoolRepository $schoolRepository)
    {
        $this->passwordGenerator = $passwordGenerator;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
        $this->messageBus = $messageBus;
        $this->parameterBag = $parameterBag;
        $this->schoolRepository = $schoolRepository;
    }

    public function __invoke(ResetPassword $resetPassword)
    {
        $school = $this->schoolRepository->find($resetPassword->getSchoolId());

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $source = [
            [
                'Kullanıcı Adı',
                'Şifre'
            ]
        ];

        $password = $this->passwordGenerator->generatePassword();

        $school->setPassword($this->userPasswordHasher->hashPassword($school, $password));

        $source[] = [
            $school->getEmail(),
            $password
        ];

        foreach ($school->getClassrooms() as $classroom) {
            foreach ($classroom->getStudents() as $student) {
                $password = $this->passwordGenerator->generatePassword();

                $student->setPassword($this->userPasswordHasher->hashPassword($student, $password));

                $source[] = [
                    $student->getSchoolNumber(),
                    $password
                ];
            }
        }

        $this->entityManager->flush();

        $sheet->fromArray($source);

        $name = $school->getId() . '.xlsx';
        $path = $this->parameterBag->get('passwords_directory') . '/' . $name;

        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        $body = file_get_contents($path);

        // unlink($path);

        $email = (new Email())
            ->to($school->getOwner()->getEmail())
            ->subject('Şifreler')
            ->text('Okulun şifreleri ektedir.')
            ->attach($body, $name, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $this->messageBus->dispatch(new EmailNotification($email));
    }
}
