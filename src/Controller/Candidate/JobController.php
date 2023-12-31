<?php

namespace App\Controller\Candidate;

use App\Entity\Announcement;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/candidate')]
#[IsGranted("ROLE_CANDIDATE")]
class JobController extends AbstractController
{
    #[Route('/applied-jobs', name: 'app_jobs', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        /**
         * @var \App\Entity\User $user
         */
        $user = $this->getUser();

        $list = $user->getAppliedJobs();

        $appliedJobs = $paginator->paginate(
            $list,
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('candidate/applied_jobs.html.twig', [
            'appliedJobs' => $appliedJobs,
        ]);
    }

    #[Route('/saved-jobs', name: 'user_saved_jobs', methods: ['GET'])]
    public function savedJobs(Request $request, PaginatorInterface $paginator): Response
    {
        /**
         * @var \App\Entity\User $user
         */
        $user = $this->getUser();

        $list = $user->getSavedJobs();

        $savedJobs = $paginator->paginate(
            $list,
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('candidate/saved_jobs.html.twig', [
            'savedJobs' => $savedJobs,
        ]);
    }

    #[Route('/save-job/{id}/{page}', name: 'app_save_job', methods: ['GET'], defaults: ['page' => 1])]
    public function saveJob(int $id, EntityManagerInterface $em, int $page): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $jobToSave = $em->getRepository(Announcement::class)->find($id);

        if (!$jobToSave) {
            throw $this->createNotFoundException('Job not found');
        }

        if (!$user->getSavedJobs()->contains($jobToSave)) {
            $user->addSavedJob($jobToSave);
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Job saved successfully');
        } else {
            $this->addFlash('warning ', 'Job is already saved');
        }

        return $this->redirectToRoute('app_announcements', ['page' => $page]);
    }

    #[Route('/remove-saved-job/{id}', name: 'app_remove_saved_job', methods: ['POST'])]
    public function removeSavedJob(int $id, EntityManagerInterface $em): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $jobToRemove = $em->getRepository(Announcement::class)->find($id);

        if (!$jobToRemove) {
            throw $this->createNotFoundException('Job not found');
        }

        $user->removeSavedJob($jobToRemove);
        $em->persist($user);
        $em->flush();
        $this->addFlash('success', 'Job removed successfully');

        return $this->redirectToRoute('user_saved_jobs');
    }

    #[Route('/apply-job/{id}', name: 'app_apply_job', methods: ['GET'])]
    public function applyJob(int $id, EntityManagerInterface $em): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $jobToApply = $em->getRepository(Announcement::class)->find($id);

        if (!$jobToApply) {
            throw $this->createNotFoundException('Job not found');
        }

        if (!$user->getAppliedJobs()->contains($jobToApply)) {
            $user->addAppliedJob($jobToApply);
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Application submitted successfully');
        } else {
            $this->addFlash('warning ', 'You have already applied to this job');
        }

        return $this->redirectToRoute('app_jobs');
    }
}
