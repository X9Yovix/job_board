<?php

namespace App\Controller\User;

use App\Entity\Country;
use App\Form\UserType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]
#[Route('/user')]
class UserController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/profile', name: 'app_user_profile')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        FileUploader $fileUploader
    ): Response {
        /**
         * @var \App\Entity\User $user
         */
        $user = $this->getUser();

        $query = $this->entityManager
            ->getRepository(Country::class)
            ->createQueryBuilder('c')
            ->select('c.id, c.name')
            ->getQuery();

        $countries = $query->getResult();
        $roles = ['RECRUITER', 'CANDIDATE'];

        $formData = [
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'birthday' => $user->getBirthday(),
            'phoneNumber' => $user->getPhoneNumber(),
            'gender' => $user->getGender(),
            'imgUrl' => null,

        ];

        $form = $this->createForm(UserType::class, $formData);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $user->setFirstName($formData['firstName']);
            $user->setLastName($formData['lastName']);
            $user->setBirthday(($formData['birthday']));
            $user->setGender($formData['gender']);
            $user->setPhoneNumber($formData['phoneNumber']);
            $user->setJobTitle($formData['jobTitle'] ? $formData['jobTitle'] : "");
            if ($formData['imgUrl']) {
                $fileName = $fileUploader->upload($formData['imgUrl']);
                $user->setImgUrl($fileName);
            }
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profile updated successfully.');

            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('user/profile.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
