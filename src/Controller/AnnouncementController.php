<?php

namespace App\Controller;

use Exception;
use App\Entity\Keyword;
use App\Entity\Announcement;
use App\Form\AnnouncementType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AnnouncementRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted("ROLE_RECRUITER")]
#[Route('/announcement')]
class AnnouncementController extends AbstractController
{
    #[Route('/', name: 'app_announcement_index', methods: ['GET'])]
    public function index(AnnouncementRepository $announcementRepository): Response
    {
        return $this->render('announcement/index.html.twig', [
            'announcements' => $announcementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_announcement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $announcement = new Announcement();
        $form = $this->createForm(AnnouncementType::class, $announcement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $announcement->setRecruiter($user);
            foreach ($form->getData()->getKeywords() as $keyword) {
                $announcement->addKeyword($keyword);
                $keyword->addAnnouncement($announcement);
            }
            $announcement->setStatus('active');
            /* $announcement->addC($user->getCompany()); */
            $entityManager->persist($announcement);
            $entityManager->flush();
            return $this->redirectToRoute('app_announcement_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('announcement/new.html.twig', [
            'announcement' => $announcement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_announcement_show', methods: ['GET'])]
    public function show(Announcement $announcement): Response
    {
        return $this->render('announcement/show.html.twig', [
            'announcement' => $announcement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_announcement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Announcement $announcement, EntityManagerInterface $entityManager): Response
    {
        $originalKeywords = new ArrayCollection();

        foreach ($announcement->getKeywords() as $keyword) {
            $originalKeywords->add($keyword);
        }

        $form = $this->createForm(AnnouncementType::class, $announcement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($originalKeywords as $originalKeyword) {
                if (!$announcement->getKeywords()->contains($originalKeyword)) {
                    $originalKeyword->removeAnnouncement($announcement);
                    $entityManager->persist($originalKeyword);
                }
            }

            foreach ($form->getData()->getKeywords() as $keyword) {
                $announcement->addKeyword($keyword);
                $keyword->addAnnouncement($announcement);
            }

            $entityManager->persist($announcement);
            $entityManager->flush();

            return $this->redirectToRoute('app_announcement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('announcement/edit.html.twig', [
            'announcement' => $announcement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_announcement_delete', methods: ['POST'])]
    public function delete(Request $request, Announcement $announcement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $announcement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($announcement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_announcement_index', [], Response::HTTP_SEE_OTHER);
    }
}
