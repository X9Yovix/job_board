<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\User;
use App\Entity\State;
use App\Entity\Country;
use App\Service\MailerService;
use App\Form\RegistrationFormType;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\Validator\Validation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Validator\Constraints\IsFalse;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $hash,
        EntityManagerInterface $entityManager,
        TokenGeneratorInterface $tokenGenInterface,
        MailerService $mailerService,
        ValidatorInterface $validator
    ): Response {
        $query = $entityManager->createQueryBuilder()
            ->select('c.id, c.name')
            ->from(Country::class, 'c')
            ->getQuery();

        $countries = $query->getResult();
        $errors = [];
        if ($request->isMethod(Request::METHOD_POST)) {
            $data = $request->request->all();

            $constraints = new Collection([
                'firstName' => [new NotBlank(['message' => 'Please enter your first name',])],
                'lastName' => [new NotBlank(['message' => 'Please enter your last name',])],
                'email' => [
                    new NotBlank(['message' => 'Please enter your email address',]),
                    new Email(['message' => 'The email address {{ value }} is not a valid email',])
                ],
                'plainPassword' => [
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                ],
                'birthday' => [
                    new NotBlank(['message' => 'Please enter your birthday',]),
                ],
                'country' => [new NotBlank(['message' => 'Please select your country',])],
                'state' => [new NotBlank(['message' => 'Please select your state',])],
                'city' => [new NotBlank(['message' => 'Please select your city',])],
                'phoneNumber' => [new NotBlank(['message' => 'Please enter your phone number',])],
                'phoneCode' => [new NotBlank()],
                'gender' => [
                    new NotBlank(['message' => 'Please select your gender']),
                    new Choice(['choices' => ['male', 'female'], 'message' => 'Please select your gender'])
                ],
            ]);

            $errors = $validator->validate($data, $constraints);

            foreach ($errors as $key => $error) {
                if ($error->getPropertyPath() === '[phoneCode]') {
                    $errors->remove($key);
                }
            }

            if (count($errors) > 0) {
                return $this->render('registration/register.html.twig', [
                    'countries' => $countries,
                    'errors' => $errors,
                ]);
            }

            $user = new User();
            $user->setFirstName($data['firstName']);
            $user->setLastName($data['lastName']);
            $user->setEmail($data['email']);
            $user->setBirthday(new \DateTime($data['birthday']));
            $user->setCountry($data['country']);
            $user->setState($data['state']);
            $user->setCity($data['city']);
            $user->setPhoneNumber('(' . $data['phoneCode'] . ') ' . $data['phoneNumber']);
            $user->setGender($data['gender']);
            $user->setPassword(
                $hash->hashPassword(
                    $user,
                    $data['plainPassword']
                )
            );
            $tokenRegistration = $tokenGenInterface->generateToken();
            $user->setRegistrationToken($tokenRegistration);

            $entityManager->beginTransaction();
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
                $this->addFlash('danger', 'An error occurred while sending the email, please try again later');
                return $this->redirectToRoute('app_register');
            }

            $entityManager->commit();
            $this->addFlash('success', 'Your account has been created, please check your email to activate it');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'countries' => $countries,
            'errors' => $errors,
        ]);
    }

    #[Route('/verify/{token}/{id<\d+>}', name: 'account_verification')]
    public function account_verification(string $token, User $user, EntityManagerInterface $em): Response
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

    #[Route('/states/{country}', name: 'get_states')]
    public function getRegions($country, EntityManagerInterface $entityManager): JsonResponse
    {
        $states = $entityManager->getRepository(State::class)->findBy(['country' => $country]);
        $res = [];
        foreach ($states as $state) {
            array_push($res, [
                'id' => $state->getId(),
                'name' => $state->getName(),
            ]);
        }

        return new JsonResponse($res);
    }

    #[Route('/cities/{state}', name: 'get_cities')]
    public function getCities($state, EntityManagerInterface $entityManager): JsonResponse
    {
        $cities = $entityManager->getRepository(City::class)->findBy(['state' => $state]);
        $res = [];
        foreach ($cities as $city) {
            array_push($res, [
                'id' => $city->getId(),
                'name' => $city->getName(),
            ]);
        }

        return new JsonResponse($res);
    }

    #[Route('/country/flag/{id}', name: 'get_flag_emoji')]
    public function getFlagEmoji($id, EntityManagerInterface $entityManager): JsonResponse
    {

        $query = $entityManager->createQueryBuilder()
            ->select('c.phonecode, c.emoji')
            ->from(Country::class, 'c')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        try {
            $res = $query->getSingleResult();
        } catch (NoResultException $e) {
            return new JsonResponse(['error' => 'Country not found'], 404);
        }

        $response = [
            'phoneCode' => $res['phonecode'],
            'emoji' => $res['emoji'],
        ];

        return new JsonResponse($response);
    }
}
