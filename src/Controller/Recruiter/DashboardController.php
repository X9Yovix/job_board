<?php

namespace App\Controller\Recruiter;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/recruiter', name: 'app_recruiter_dashboard')]
    public function index(): Response
    {
        //return $this->render('recruiter/dashboard/updateProfil.html.twig.html.twig');
        return $this->redirectToRoute('app_company_index');
    }
}
