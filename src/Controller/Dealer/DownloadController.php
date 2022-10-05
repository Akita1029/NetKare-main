<?php

namespace App\Controller\Dealer;

use App\Entity\Download;
use App\Entity\Permission;
use App\Form\Dealer\DownloadType;
use App\Message\DownloadPreparation;
use App\Repository\DealerPermissionRepository;
use App\Repository\DownloadRepository;
use App\Repository\PermissionRepository;
use App\Service\ObjectStorage;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/dealer/download')]
class DownloadController extends AbstractController
{
    #[Route('/', name: 'dealer_download_index', methods: ['GET', 'POST'])]
    public function index(Request $request, PermissionRepository $permissionRepository, DealerPermissionRepository $dealerPermissionRepository, DownloadRepository $downloadRepository, MessageBusInterface $bus, TranslatorInterface $translator): Response
    {
        $download = new Download;

        $permissions = $permissionRepository->findBy(['allowed' => true]);

        $types = array_map(function (Permission $permission) {
            return $permission->getId();
        }, $permissions);

        $dealerPermissions = $dealerPermissionRepository->findBy(['dealer' => $this->getUser()]);

        foreach ($dealerPermissions as $dealerPermission) {
            if (in_array($dealerPermission->getPermission()->getId(), $types)) {
                continue;
            }

            $types[] = $dealerPermission->getPermission()->getId();
        }

        $optionTypes = [];

        foreach ($types as $type) {
            $optionTypes[$translator->trans('download.types.' . $type)] = $type;
        }

        $form = $this->createForm(DownloadType::class, $download, [
            'types' => $optionTypes,
            'schools' => $this->getUser()->getSchools()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $download->setRequestDate(new DateTimeImmutable());

            $downloadRepository->add($download);

            $bus->dispatch(new DownloadPreparation($download->getId()));

            $this->addFlash('success', 'Dosyalarınız oluşturuluyor. Oluşturulma işlemi tamamlandığında eposta ile bildirim alacaksınız.');

            return $this->redirectToRoute('dealer_download_index');
        }

        $downloads = $downloadRepository->findByDealer($this->getUser());

        return $this->renderForm('dealer/download/index.html.twig', [
            'form' => $form,
            'downloads' => $downloads
        ]);
    }

    #[Route('/{id}', name: 'dealer_download_download', methods: ['GET'])]
    public function download(Download $download, ObjectStorage $objectStorage): Response
    {
        if ($download->getStatus() !== Download::STATUS_DONE) {
            throw $this->createNotFoundException();
        }

        $object = $objectStorage->getObject($download->getAsset()->getName());

        $extension = '';

        switch ($object['ContentType']) {
            case 'application/zip':
                $extension = '.zip';
                break;
        }

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $download->getId() . $extension
        );

        $response = new Response($object['Body']->getContents());

        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
