<?php

namespace App\Controller\School;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/school', name: 'school_dashboard')]
    public function index(): Response
    {
        return $this->render('school/dashboard/index.html.twig');
    }
}
