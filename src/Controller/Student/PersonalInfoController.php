<?php

namespace App\Controller\Student;

use App\Entity\StudentCustomField;
use App\Repository\StudentCustomFieldRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/student/personal-info')]
class PersonalInfoController extends AbstractController
{
    #[Route('/', name: 'student_personal_info_index', methods: ['GET', 'POST'])]
    public function index(Request $request, StudentCustomFieldRepository $studentCustomFieldRepository, EntityManagerInterface $entityManager): Response
    {
        $names = [
            StudentCustomField::NAME_EMAIL,
            StudentCustomField::NAME_PHONE,
            StudentCustomField::NAME_HOROSCOPE,
            StudentCustomField::NAME_BIRTHDATE,
            StudentCustomField::NAME_FACEBOOK,
            StudentCustomField::NAME_YOUTUBE,
            StudentCustomField::NAME_INSTAGRAM,
            StudentCustomField::NAME_WEB
        ];

        $studentCustomFields = [];

        foreach ($names as $name) {
            $studentCustomFields[$name] = $studentCustomFieldRepository->findOneBy(['name' => $name]);
        }

        $data = array_map(function (?StudentCustomField $studentCustomField) {
            if ($studentCustomField) {
                return $studentCustomField->getValue();
            }

            return null;
        }, $studentCustomFields);

        $formBuilder = $this->createFormBuilder($data)
            ->add(StudentCustomField::NAME_EMAIL, EmailType::class, [
                'required' => false,
                'label' => 'student_personal_info.labels.' . StudentCustomField::NAME_EMAIL
            ])
            ->add(StudentCustomField::NAME_PHONE, TelType::class, [
                'required' => false,
                'label' => 'student_personal_info.labels.' . StudentCustomField::NAME_PHONE
            ])
            ->add(StudentCustomField::NAME_HOROSCOPE, TextType::class, [
                'required' => false,
                'label' => 'student_personal_info.labels.' . StudentCustomField::NAME_HOROSCOPE
            ])
            ->add(StudentCustomField::NAME_BIRTHDATE, DateType::class, [
                'required' => false,
                'label' => 'student_personal_info.labels.' . StudentCustomField::NAME_BIRTHDATE,
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'input' => 'string'
            ])
            ->add(StudentCustomField::NAME_FACEBOOK, UrlType::class, [
                'required' => false,
                'label' => 'student_personal_info.labels.' . StudentCustomField::NAME_FACEBOOK
            ])
            ->add(StudentCustomField::NAME_YOUTUBE, UrlType::class, [
                'required' => false,
                'label' => 'student_personal_info.labels.' . StudentCustomField::NAME_YOUTUBE
            ])
            ->add(StudentCustomField::NAME_INSTAGRAM, UrlType::class, [
                'required' => false,
                'label' => 'student_personal_info.labels.' . StudentCustomField::NAME_INSTAGRAM
            ])
            ->add(StudentCustomField::NAME_WEB, UrlType::class, [
                'required' => false,
                'label' => 'student_personal_info.labels.' . StudentCustomField::NAME_WEB
            ]);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $student = $this->getUser();

            foreach ($names as $name) {
                if ($value = $form->get($name)->getData()) {
                    $studentCustomField = $studentCustomFields[$name];

                    if (!$studentCustomField) {
                        $studentCustomField = new StudentCustomField;
                        $studentCustomField->setStudent($student);
                        $studentCustomField->setName($name);

                        $entityManager->persist($studentCustomField);
                    }

                    $studentCustomField->setValue($value);
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Kişisel bilgileriniz başarıyla güncellendi.');

            return $this->redirectToRoute('student_personal_info_index');
        }

        return $this->renderForm('student/personal_info/index.html.twig', [
            'form' => $form
        ]);
    }
}
