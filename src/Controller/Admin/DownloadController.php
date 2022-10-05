<?php

namespace App\Controller\Admin;

use App\Entity\Download;
use App\Form\Admin\DownloadType;
use App\Message\DownloadPreparation;
use App\Repository\DownloadRepository;
use App\Service\ObjectStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/download')]
class DownloadController extends AbstractController
{
    #[Route('/', name: 'admin_download_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DownloadRepository $downloadRepository, MessageBusInterface $bus): Response
    {
        $download = new Download;

        $form = $this->createForm(DownloadType::class, $download);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $download->setRequestDate(new \DateTimeImmutable());

            $downloadRepository->add($download);

            $bus->dispatch(new DownloadPreparation($download->getId()));

            $this->addFlash('success', 'Dosyalarınız oluşturuluyor. Oluşturulma işlemi tamamlandığında eposta ile bildirim alacaksınız.');

            return $this->redirectToRoute('admin_download_index');
        }

        $downloads = $downloadRepository->findBy([], ['requestDate' => 'DESC']);

        return $this->renderForm('admin/download/index.html.twig', [
            'form' => $form,
            'downloads' => $downloads
        ]);
    }

    #[Route('/{id}', name: 'admin_download_download', methods: ['GET'])]
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
