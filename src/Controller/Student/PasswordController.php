<?php

namespace App\Controller\Student;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/student/password')]
class PasswordController extends AbstractController
{
    #[Route('/', name: 'student_password_index', methods: ['GET', 'POST'])]
    public function index(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $formBuilder = $this->createFormBuilder()
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => [
                    'label' => 'password.labels.password'
                ],
                'second_options' => [
                    'label' => 'password.labels.password_again'
                ],
            ]);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $password = $form->get('password')->getData();

            $user->setPassword($userPasswordHasher->hashPassword($user, $password));

            $entityManager->flush();

            $this->addFlash('success', 'Şifreniz başarıyla güncellendi.');

            return $this->redirectToRoute('student_password_index');
        }

        return $this->renderForm('student/password/index.html.twig', [
            'form' => $form
        ]);
    }
}
