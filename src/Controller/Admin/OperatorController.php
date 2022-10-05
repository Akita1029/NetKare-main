<?php

namespace App\Controller\Admin;

use App\Entity\Operator;
use App\Entity\Yearbook;
use App\Form\OperatorType;
use App\Repository\OperatorRepository;
use App\Service\PasswordGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/yearbook/{yearbookId}/operator')]
#[Entity('yearbook', expr: 'repository.find(yearbookId)')]
class OperatorController extends AbstractController
{
    #[Route('/', name: 'admin_operator_index', methods: ['GET'])]
    public function index(OperatorRepository $operatorRepository, Yearbook $yearbook): Response
    {
        $operators = $operatorRepository->findBy(['yearbook' => $yearbook]);

        return $this->render('admin/operator/index.html.twig', [
            'yearbook' => $yearbook,
            'operators' => $operators
        ]);
    }

    #[Route('/new', name: 'admin_operator_new', methods: ['GET', 'POST'])]
    public function new(Request $request, OperatorRepository $operatorRepository, Yearbook $yearbook, PasswordGenerator $passwordGenerator, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $operator = new Operator;
        $operator->setYearbook($yearbook);

        $plainPassword = $passwordGenerator->generatePassword();

        $operator->setPassword($userPasswordHasher->hashPassword($operator, $plainPassword));

        $form = $this->createForm(OperatorType::class, $operator);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $operatorRepository->add($operator);

            $this->addFlash('success', "Operator başarıyla oluşturuldu. Kimlik: {$operator->getId()} Şifre: {$plainPassword}");

            return $this->redirectToRoute('admin_operator_index', [
                'yearbookId' => $yearbook->getId()
            ]);
        }

        return $this->renderForm('admin/operator/new.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_operator_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Yearbook $yearbook, Operator $operator, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OperatorType::class, $operator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_operator_index', [
                'yearbookId' => $yearbook->getId()
            ]);
        }

        return $this->renderForm('admin/operator/edit.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: 'admin_operator_delete', methods: ['POST'])]
    public function delete(Request $request, Operator $operator, OperatorRepository $operatorRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $operator->getId(), $request->request->get('_token'))) {
            $operatorRepository->remove($operator);
        }

        return $this->redirectToRoute('admin_operator_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/password', name: 'admin_operator_password', methods: ['POST'])]
    public function resetPassword(Request $request, Operator $operator, PasswordGenerator $passwordGenerator, OperatorRepository $operatorRepository, UserPasswordHasherInterface $userPasswordHasher)
    {
        if ($this->isCsrfTokenValid('reset_password' . $operator->getId(), $request->request->get('_token'))) {
            $plainPassword = $passwordGenerator->generatePassword();
            $operator->setPassword($userPasswordHasher->hashPassword($operator, $plainPassword));
            $this->addFlash('success', "Operator şifresi yenilendi. Kimlik: {$operator->getId()} Şifre: {$plainPassword}");
        }

        return $this->redirectToRoute('admin_operator_index', [], Response::HTTP_SEE_OTHER);
    }
}
