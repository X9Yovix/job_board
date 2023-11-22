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
                $this->addFlash('danger', 'An error occurred while sending the email, please try again later.');
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

    #[Route('/verify/{token}/{id<\d+>}', name: 'account_verification')]
    public function account_verification(string $token, int $id, User $user, EntityManagerInterface $em): Response
    {
        if ($user->getRegistrationToken() !== $token) {
            $this->addFlash('danger', 'The token is invalid.');
            return $this->redirectToRoute('app_register');
        }

        if ($user->getRegistrationToken() === null) {
            $this->addFlash('danger', 'Your account is already activated.');
            return $this->redirectToRoute('app_login');
        }

        if ($user->getRegistrationTokenLifeTime() < new \DateTime()) {
            $this->addFlash('danger', 'The token has expired.');
            return $this->redirectToRoute('app_register');
        }

        $user->setVerified(true);
        $user->setRegistrationToken(null);
        $em->flush();
        $this->addFlash('success', 'Registration completed successfully.');
        return $this->redirectToRoute('app_login');
    }
}
