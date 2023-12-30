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
    #[Route('/saved-jobs', name: 'user_saved_jobs',methods: ['GET'])]
    public function savedJobs(): Response
    {
        $user = $this->getUser();

        $savedJobs = $user->getSavedJobs();

        return $this->render('user/saved_jobs.html.twig', [
            'savedJobs' => $savedJobs,
        ]);
    }
}
