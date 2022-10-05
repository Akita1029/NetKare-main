<?php

namespace App\Controller\Operator;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/operator')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'operator_dashboard')]
    public function index(): Response
    {
        return $this->render('operator/dashboard/index.html.twig');
    }
}
