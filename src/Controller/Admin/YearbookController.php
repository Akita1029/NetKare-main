<?php

namespace App\Controller\Admin;

use App\Entity\Yearbook;
use App\Form\YearbookType;
use App\Repository\YearbookRepository;
use App\Service\Pagination;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/yearbook')]
class YearbookController extends AbstractController
{
    #[Route('/', name: 'app_admin_yearbook_index', methods: ['GET'])]
    public function index(YearbookRepository $yearbookRepository, Pagination $pagination): Response
    {
        $paginatorPerPage = YearbookRepository::PAGINATOR_PER_PAGE;

        $page = $pagination->getActivePage();
        $offset = ($page - 1) * $paginatorPerPage;

        $yearbooks = $yearbookRepository->getYearbookPaginator($offset);

        return $this->render('admin/yearbook/index.html.twig', [
            'yearbooks' => $yearbooks,
            'paginatorPerPage' => $paginatorPerPage
        ]);
    }

    #[Route('/new', name: 'app_admin_yearbook_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $yearbook = new Yearbook();
        $form = $this->createForm(YearbookType::class, $yearbook);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $yearbook->setEndsAt($form->get('endsAt')->getData());
            $yearbook->setMemoir($form->get('memoir')->getData());

            $entityManager->persist($yearbook);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_yearbook_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/yearbook/new.html.twig', [
            'yearbook' => $yearbook,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_yearbook_show', methods: ['GET'])]
    public function show(Yearbook $yearbook): Response
    {
        return $this->render('admin/yearbook/show.html.twig', [
            'yearbook' => $yearbook,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_yearbook_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Yearbook $yearbook, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(YearbookType::class, $yearbook, [
            'dealer' => $yearbook->getSchool()->getOwner()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $yearbook->setEndsAt($form->get('endsAt')->getData());
            $yearbook->setMemoir($form->get('memoir')->getData());

            $entityManager->flush();

            return $this->redirectToRoute('app_admin_yearbook_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/yearbook/edit.html.twig', [
            'yearbook' => $yearbook,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_yearbook_delete', methods: ['POST'])]
    public function delete(Request $request, Yearbook $yearbook, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $yearbook->getId(), $request->request->get('_token'))) {
            $entityManager->remove($yearbook);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_yearbook_index', [], Response::HTTP_SEE_OTHER);
    }
}
