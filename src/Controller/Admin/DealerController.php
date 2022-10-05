<?php

namespace App\Controller\Admin;

use App\Entity\Dealer;
use App\Form\DealerType;
use App\Repository\DealerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/dealer')]
class DealerController extends AbstractController
{
    #[Route('/', name: 'admin_dealer_index', methods: ['GET'])]
    public function index(DealerRepository $dealerRepository): Response
    {
        return $this->render('admin/dealer/index.html.twig', [
            'dealers' => $dealerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_dealer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $dealer = new Dealer();

        $form = $this->createForm(DealerType::class, $dealer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dealer->setPassword(
                $userPasswordHasher->hashPassword(
                    $dealer,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($dealer);
            $entityManager->flush();

            return $this->redirectToRoute('admin_dealer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/dealer/new.html.twig', [
            'dealer' => $dealer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_dealer_show', methods: ['GET'])]
    public function show(Dealer $dealer): Response
    {
        return $this->render('admin/dealer/show.html.twig', [
            'dealer' => $dealer,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_dealer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Dealer $dealer, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $form = $this->createForm(DealerType::class, $dealer, ['password_required' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($plainPassword = $form->get('plainPassword')->getData()) {
                $dealer->setPassword(
                    $userPasswordHasher->hashPassword(
                        $dealer,
                        $plainPassword
                    )
                );
            }

            $entityManager->flush();

            return $this->redirectToRoute('admin_dealer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/dealer/edit.html.twig', [
            'dealer' => $dealer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_dealer_delete', methods: ['POST'])]
    public function delete(Request $request, Dealer $dealer, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $dealer->getId(), $request->request->get('_token'))) {
            $entityManager->remove($dealer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_dealer_index', [], Response::HTTP_SEE_OTHER);
    }

    public function authorization() {}
}
