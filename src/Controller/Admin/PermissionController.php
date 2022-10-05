<?php

namespace App\Controller\Admin;

use App\Entity\Dealer;
use App\Entity\DealerPermission;
use App\Entity\Permission;
use App\Form\PermissionType;
use App\Repository\DealerPermissionRepository;
use App\Repository\PermissionRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/permission')]
class PermissionController extends AbstractController
{
    #[Route('/', name: 'admin_permission_index', methods: ['GET', 'POST'])]
    public function index(Request $request, ManagerRegistry $doctrine, PermissionRepository $permissionRepository,): Response
    {
        $entityManager = $doctrine->getManager();

        $permissions = [];

        foreach (PermissionRepository::PERMISSIN_IDS as $permissionId) {
            $permission = $permissionRepository->find($permissionId);

            if (!$permission) {
                $permission = new Permission($permissionId);

                $entityManager->persist($permission);
            }

            $permissions[] = $permission;
        }

        $formBuilder = $this->createFormBuilder(['permissions' => $permissions])
            ->add('permissions', CollectionType::class, [
                'label' => false,
                'entry_type' => PermissionType::class,
                'entry_options' => [
                    'label' => false
                ]
            ]);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Yetkiler başarıyla güncellendi.');

            return $this->redirectToRoute('admin_permission_index');
        }

        return $this->renderForm('admin/permission/index.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: 'admin_permission_dealer', methods: ['GET', 'POST'])]
    public function dealer(Request $request, ManagerRegistry $doctrine, PermissionRepository $permissionRepository, DealerPermissionRepository $dealerPermissionRepository,  Dealer $dealer): Response
    {
        $entityManager = $doctrine->getManager();

        $permissions = [];

        foreach (PermissionRepository::PERMISSIN_IDS as $permissionId) {
            $permission = $permissionRepository->find($permissionId);

            if (!$permission) {
                $permission = new Permission($permissionId);

                $entityManager->persist($permission);
            }

            $permissions[] = $permission;
        }

        $formBuilder = $this->createFormBuilder();

        foreach ($permissions as $permission) {
            $data = !!$dealerPermissionRepository->findOneBy(['dealer' => $dealer, 'permission' => $permission]);

            $formBuilder->add($permission->getId(), CheckboxType::class, [
                'required' => false,
                'data' => $data
            ]);
        }

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($dealer->getPermissions() as $permission) {
                $dealer->removePermission($permission);
            }

            foreach ($permissions as $permission) {
                if ($form->get($permission->getId())->getData()) {
                    $dealerPermission = new DealerPermission;
                    $dealerPermission->setDealer($dealer);
                    $dealerPermission->setPermission($permission);

                    $entityManager->persist($dealerPermission);
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Yetkiler başarıyla güncellendi.');

            return $this->redirectToRoute('admin_permission_dealer', [
                'id' => $dealer->getId()
            ]);
        }

        return $this->renderForm('admin/permission/dealer.html.twig', [
            'form' => $form,
            'dealer' => $dealer
        ]);
    }
}
