<?php

namespace App\Controller\Anonymous;

use Exception;
use App\Entity\Announcement;
use App\Repository\AnnouncementRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AnonymousController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('anonymous/home/index.html.twig');
    }
    #[Route('/announcements/search', name: 'search_announcements', methods: ['POST'])]
    public function searchAnnouncements(
        AnnouncementRepository $announcementRepository,
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        $jobTitle = $request->request->get('title');
        $place = $request->request->get('place');

        $query = $announcementRepository->createQueryBuilder('a')
            ->leftJoin('a.company', 'c')
            ->andWhere('a.title LIKE :jobTitle')
            ->andWhere('c.address LIKE :place')
            ->setParameter('jobTitle', '%' . $jobTitle . '%')
            ->setParameter('place', '%' . $place . '%')
            ->getQuery();

        $announcements = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            6 // items per page
        );

        return $this->render('anonymous/announcement/index.html.twig', [
            'announcements' => $announcements,
        ]);
    }

    #[Route('/announcements', name: 'app_announcements')]
    public function announcements(AnnouncementRepository $announcementRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $query = $announcementRepository->createQueryBuilder('a')
            ->where('a.status = :status')
            ->setParameter('status', 'active')
            ->getQuery();

        $announcements = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            6 
        );

        return $this->render('anonymous/announcement/index.html.twig', [
            'announcements' => $announcements,
        ]);
    }

    #[Route('/announcements/{slug}', name: 'app_announcement_show', methods: ['GET'])]
    public function show(Announcement $announcement): Response
    {
        try {
            return $this->render('anonymous/announcement/show.html.twig', [
                'announcement' => $announcement,
            ]);
        } catch (Exception $e) {
            throw $this->createNotFoundException('Announcement not found', $e);
        }
    }
}
