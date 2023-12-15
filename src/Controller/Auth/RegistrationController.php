<?php

namespace App\Controller\Auth;

use App\Entity\City;
use App\Entity\User;
use App\Entity\State;
use App\Entity\Country;
use App\Service\MailerService;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class RegistrationController extends AbstractController
{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $hash,

        TokenGeneratorInterface $tokenGenInterface,
        MailerService $mailerService,
        ValidatorInterface $validator
    ): Response {

        if ($this->getUser()) {
            return $this->redirectToRoute('app_index');
        }

        $query = $this->entityManager->createQueryBuilder()
            ->select('c.id, c.name')
            ->from(Country::class, 'c')
            ->getQuery();

        $countries = $query->getResult();
        $errors = [];
        $roles = ['RECRUITER', 'CANDIDATE'];

        $formData = [
            'firstName' => null,
            'lastName' => null,
            'email' =>  null,
            'birthday' =>  null,
            'country' => null,
            'state' =>  null,
            'city' =>  null,
            'role' =>null,
            'phoneNumber' => null,
            'phoneCode' =>  null,
            'gender' =>  null,
        ];

        if ($request->isMethod(Request::METHOD_POST)) {
            $data = $request->request->all();
            $formData = [
                'firstName' => $data['firstName'] ?? null,
                'lastName' => $data['lastName'] ?? null,
                'email' => $data['email'] ?? null,
                'birthday' => $data['birthday'] ?? null,
                'country' => $data['country'] ?? null,
                'state' => $data['state'] ?? null,
                'city' => $data['city'] ?? null,
                'role' => $data['role'] ?? null,
                'phoneNumber' => $data['phoneNumber'] ?? null,
                'phoneCode' => $data['phoneCode'] ?? null,
                'gender' => $data['gender'] ?? null,
            ];

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
                'role' => [new NotBlank(['message' => 'Please select your role',])],
            ]);

            $errors = $validator->validate($data, $constraints);

            foreach ($errors as $key => $error) {
                if ($error->getPropertyPath() === '[phoneCode]') {
                    $errors->remove($key);
                }
            }

            if (count($errors) > 0) {
                return $this->render('auth/registration/register.html.twig', [
                    'formData' => $formData,
                    'countries' => $countries,
                    'errors' => $errors,
                    'roles' => $roles
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
            $user->setRegistrationTokenLifeTime(new \DateTime('+1 min'));
            $user->setGender($data['gender']);

            if ($data['role'] === 'RECRUITER') {
                $user->setRoles(['ROLE_RECRUITER']);
            }
            if ($data['role'] === 'CANDIDATE') {
                $user->setRoles(['ROLE_CANDIDATE']);
            }

            $user->setPassword(
                $hash->hashPassword(
                    $user,
                    $data['plainPassword']
                )
            );
            $tokenRegistration = $tokenGenInterface->generateToken();
            $user->setRegistrationToken($tokenRegistration);

            $this->entityManager->beginTransaction();
            $this->entityManager->persist($user);
            $this->entityManager->flush();

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
                $this->entityManager->rollback();
                $this->addFlash('danger', 'An error occurred while sending the email, please try again later');
                return $this->redirectToRoute('app_register');
            }

            $this->entityManager->commit();
            $this->addFlash('success', 'Your account has been created, please check your email to activate it');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('auth/registration/register.html.twig', [
            'formData' => $formData,
            'countries' => $countries,
            'errors' => $errors,
            'roles' => $roles
        ]);
    }

    #[Route('/verify/{token}/{id<\d+>}', name: 'account_verification')]
    public function account_verification(
        string $token,
        User $user,
        TokenGeneratorInterface $tokenGenInterface,
        MailerService $mailerService
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_index');
        }

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
            $tokenRegistration = $tokenGenInterface->generateToken();
            $user->setRegistrationToken($tokenRegistration);
            $user->setRegistrationTokenLifeTime(new \DateTime($_ENV['REGISTRATION_TOKEN_LIFETIME']));
            $this->entityManager->flush();

            $mailerService->sendEmail(
                $user->getEmail(),
                'Please confirm your account',
                'confirmation_email.html.twig',
                [
                    'token' => $tokenRegistration,
                    'user' => $user,
                    'lifeTimeToken' => $user->getRegistrationTokenLifeTime()->format('H:i:s d-m-Y')
                ]
            );

            $this->addFlash('info', 'A new token has been sent to your email.');
            return $this->redirectToRoute('app_login');
        }

        $user->setVerified(true);
        $user->setRegistrationToken(null);
        $this->entityManager->flush();
        $this->addFlash('success', 'Registration completed successfully.');
        return $this->redirectToRoute('app_login');
    }

    #[Route('/states/{country}', name: 'get_states')]
    public function getRegions($country): JsonResponse
    {
        $states = $this->entityManager->getRepository(State::class)->findBy(['country' => $country]);
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
    public function getCities($state): JsonResponse
    {
        $cities = $this->entityManager->getRepository(City::class)->findBy(['state' => $state]);
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
    public function getFlagEmoji($id): JsonResponse
    {

        $query = $this->entityManager->createQueryBuilder()
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
