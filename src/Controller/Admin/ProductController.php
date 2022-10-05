<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'admin_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('admin/product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

            $imageFile->move(
                $this->getParameter('products_directory'),
                $newFilename
            );

            $product->setImageFilename($newFilename);

            $knifeTemplateFile = $form->get('knifeTemplate')->getData();

            if ($knifeTemplateFile) {
                $originalFilename = pathinfo($knifeTemplateFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $knifeTemplateFile->guessExtension();

                $knifeTemplateFile->move(
                    $this->getParameter('products_directory'),
                    $newFilename
                );

                $product->setKnifeTemplateFilename($newFilename);
            }

            $entityManager->persist($product);

            foreach ($form->get('options') as $option) {
                $optionData = $option->getData();

                $entityManager->persist($optionData);
            }

            foreach ($form->get('templates') as $template) {
                $templateData = $template->getData();

                $preview1ImageFile = $template->get('preview1Image')->getData();

                if ($preview1ImageFile) {
                    $originalFilename = pathinfo($preview1ImageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $preview1ImageFile->guessExtension();

                    $preview1ImageFile->move(
                        $this->getParameter('products_directory'),
                        $newFilename
                    );

                    $templateData->setPreview1ImageFilename($newFilename);
                }

                $preview2ImageFile = $template->get('preview2Image')->getData();

                if ($preview2ImageFile) {
                    $originalFilename = pathinfo($preview2ImageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $preview2ImageFile->guessExtension();

                    $preview2ImageFile->move(
                        $this->getParameter('products_directory'),
                        $newFilename
                    );

                    $templateData->setPreview2ImageFilename($newFilename);
                }

                $entityManager->persist($templateData);
            }

            foreach ($form->get('fields') as $field) {
                $fieldData = $field->getData();

                $entityManager->persist($fieldData);
            }

            $entityManager->flush();

            return $this->redirectToRoute('admin_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('admin/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProductType::class, $product, ['image_required' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('products_directory'),
                    $newFilename
                );

                $product->setImageFilename($newFilename);
            }

            $entityManager->persist($product);

            foreach ($form->get('options') as $option) {
                $optionData = $option->getData();

                $entityManager->persist($optionData);
            }

            foreach ($form->get('templates') as $template) {
                $templateData = $template->getData();

                $preview1ImageFile = $template->get('preview1Image')->getData();

                if ($preview1ImageFile) {
                    $originalFilename = pathinfo($preview1ImageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $preview1ImageFile->guessExtension();

                    $preview1ImageFile->move(
                        $this->getParameter('products_directory'),
                        $newFilename
                    );

                    $templateData->setPreview1ImageFilename($newFilename);
                }

                $preview2ImageFile = $template->get('preview2Image')->getData();

                if ($preview2ImageFile) {
                    $originalFilename = pathinfo($preview2ImageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $preview2ImageFile->guessExtension();

                    $preview2ImageFile->move(
                        $this->getParameter('products_directory'),
                        $newFilename
                    );

                    $templateData->setPreview2ImageFilename($newFilename);
                }

                $entityManager->persist($templateData);
            }

            foreach ($form->get('fields') as $field) {
                $fieldData = $field->getData();

                $entityManager->persist($fieldData);
            }

            $entityManager->flush();

            return $this->redirectToRoute('admin_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
