<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user')]
#[IsGranted("ROLE_USER")]
class JobController extends AbstractController
{
    #[Route('/applied-jobs', name: 'app_jobs',methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();

        $appliedJobs = $user->getAppliedJobs();

        return $this->render('user/applied_jobs.html.twig', [
            'appliedJobs' => $appliedJobs,
        ]);
    }
}
