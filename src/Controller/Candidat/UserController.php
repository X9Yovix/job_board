<?php

namespace App\Controller\Candidat;

use App\Entity\Country;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\CandidatForm;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/updateProfile/{id}', name:'updateProfile' , methods: ['GET', 'POST'] )]
    public function UpdateUser(UserRepository $ur, EntityManagerInterface $em, Request $request, $id): Response
    { $query = $em->createQueryBuilder()
        ->select('c.id, c.name')
        ->from(Country::class, 'c')
        ->getQuery();

        $countries = $query->getResult();

        $user = $ur->find($id);
        $editForm = $this->createForm(CandidatForm::class, $user);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_index');
        }

        return $this->render('Candidat/updateCandidat.html.twig', ['editFormCandidat' => $editForm->createView(),'user' => $user,'countries'=>$countries]);
    }
}