<?php

namespace App\Controller\Student;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/student', name: 'student_dashboard')]
    public function index(): Response
    {
        return $this->render('student/dashboard/index.html.twig');
    }
}
