<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;


class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        TokenGeneratorInterface $tokenGenInterface,
        MailerService $mailerService
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->beginTransaction();

            $tokenRegistration = $tokenGenInterface->generateToken();
            $user->setRegistrationToken($tokenRegistration);

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $res = $mailerService->sendEmail(
                $user->getEmail(),
                'Please confirm your account',
                'confirmation_email.html.twig',
                [
                    'token' => $tokenRegistration,
                    'user' => $user,
                    'lifeTimeToken' => $user->getRegistrationTokenLifeTime()->format('H:i:s d-m-Y')
                ]
            );

            if (!$res) {
                $entityManager->rollback();
                $this->addFlash('error', 'An error occurred while sending the email, please try again later.');
                return $this->redirectToRoute('app_register');
            }

            $entityManager->commit();
            $this->addFlash('success', 'Your account has been created, please check your email to activate it.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/account_verification', name: 'account_verification')]
    public function account_verification(): Response
    {
        return $this->render('registration/account_verification.html.twig');
    }
}
