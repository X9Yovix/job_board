<?php

namespace App\Controller\Recruiter;

use App\Entity\Company;
use App\Form\CompanyType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[IsGranted("ROLE_RECRUITER")]
#[Route('/recruiter/company')]
class CompanyController extends AbstractController
{
    #[Route('/', name: 'app_company_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $companies = $entityManager->getRepository(Company::class)->findCompaniesByUser($user);
        return $this->render('recruiter/company/index.html.twig', [
            'companies' => $companies,
        ]);
    }

    #[Route('/new', name: 'app_company_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $company = new Company();
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $logo */
            $logo = $form->get('logo')->getData();
            if ($logo) {
                $fileName = $fileUploader->upload($logo);
                $company->setLogo($fileName);
            }

            $user = $this->getUser();
            $company->addUser($user);
            $entityManager->persist($company);
            $entityManager->flush();

            return $this->redirectToRoute('app_company_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('recruiter/company/new.html.twig', [
            'company' => $company,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_company_show', methods: ['GET'])]
    public function show(Company $company): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if (!$user->getCompanies()->contains($company)) {
            throw new AccessDeniedException();
        }

        return $this->render('recruiter/company/show.html.twig', [
            'company' => $company,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_company_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Company $company, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if (!$user->getCompanies()->contains($company)) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $logo */
            $logo = $form->get('logo')->getData();
            if ($logo) {
                $fileName = $fileUploader->upload($logo);
                $company->setLogo($fileName);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_company_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('recruiter/company/edit.html.twig', [
            'company' => $company,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_company_delete', methods: ['POST'])]
    public function delete(Request $request, Company $company, EntityManagerInterface $entityManager): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if (!$user->getCompanies()->contains($company)) {
            throw new AccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete' . $company->getId(), $request->request->get('_token'))) {
            $user = $this->getUser();
            $company->addUser($user);
            $entityManager->remove($company);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_company_index', [], Response::HTTP_SEE_OTHER);
    }
}
