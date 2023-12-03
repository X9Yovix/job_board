<?php

namespace App\Controller;

use App\Entity\Keyword;
use App\Entity\Announcement;
use App\Form\AnnouncementType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AnnouncementRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
        dd($form->getData());
        die;
        if ($form->isSubmitted() && $form->isValid()) {
            //dd($form->getData());
            
          /*   $submittedKeywords = $request->get('keywords');
            $keywordEntity = null;

            foreach ($submittedKeywords as $val) {
                if (is_numeric($val)) {
                    $existingKeyword = $entityManager->getRepository(Keyword::class)->findOneBy(['id' => $val]);
                    if ($existingKeyword) {
                        $keywordEntity = $existingKeyword;
                    }
                } else {
                    $keywordEntity = new Keyword();
                    $keywordEntity->setName(ucwords($val));
                    $entityManager->persist($keywordEntity);
                }
                //dd($keywordEntity);
                $announcement->addKeyword($keywordEntity);
            } */

            $user = $this->getUser();
            $announcement->setRecruiter($user);

            $entityManager->persist($announcement);
            $entityManager->flush();

            return $this->redirectToRoute('app_announcement_index', [], Response::HTTP_SEE_OTHER);
        }/*   else {
            dd($form->getErrors(true, false));
        } */
        $query = $entityManager->createQueryBuilder()
            ->select('k.id, k.name')
            ->from(Keyword::class, 'k')
            ->getQuery();
        $keywords = $query->getResult();

        return $this->render('announcement/new.html.twig', [
            'keywords' => $keywords,
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
        $form = $this->createForm(AnnouncementType::class, $announcement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_announcement_index', [], Response::HTTP_SEE_OTHER);
        }
        $query = $entityManager->createQueryBuilder()
            ->select('k.id, k.name')
            ->from(Keyword::class, 'k')
            ->getQuery();
        $keywords = $query->getResult();

        return $this->render('announcement/edit.html.twig', [
            'keywords' => $keywords,
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
