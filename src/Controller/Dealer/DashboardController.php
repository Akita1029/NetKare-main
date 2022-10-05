<?php

namespace App\Controller\Dealer;

use App\Repository\AnnouncementRepository;
use App\Repository\ProductRepository;
use App\Repository\SchoolRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dealer', name: 'dealer_dashboard')]
    public function index(AnnouncementRepository $announcementRepository, ProductRepository $productRepository, SchoolRepository $schoolRepository): Response
    {
        $announcements = $announcementRepository->findAll();

        $products = $productRepository->findAll();

        $schools = $schoolRepository->findBy([
            'owner' => $this->getUser()
        ]);

        return $this->render('dealer/dashboard/index.html.twig', [
            'announcements' => $announcements,
            'products' => $products,
            'schools' => $schools
        ]);
    }
}
