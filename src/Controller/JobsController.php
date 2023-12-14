<?php

namespace App\Controller;

use App\Repository\AnnouncementRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class JobsController extends AbstractController
{
    #[Route('/jobs', name: 'app_jobs')]
    public function index(AnnouncementRepository $announcementRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $query = $announcementRepository->createQueryBuilder('a')
            ->getQuery();

        $announcements = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5 // items per page
        );

        return $this->render('jobs/index.html.twig', [
            'announcements' => $announcements,
        ]);
    }
}
