<?php

namespace App\Controller\User;

use App\Entity\Announcement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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
    #[Route('/apply/{id}', name: 'apply_to_job')]
    public function applyToJob(Announcement $job, Request $request,EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $job->addAppliedUser($user);
        $entityManager->flush();
        return $this->redirectToRoute('app_announcements', ['id' => $job->getId()]);

    }
}
