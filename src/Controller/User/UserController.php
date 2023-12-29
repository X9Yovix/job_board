<?php

namespace App\Controller\User;

use App\Entity\Country;
use App\Form\ChangePasswordType;
use App\Form\UserType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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

#[Route('/updatePassword', name: 'update_password')]
public function updatePassword(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response

{

    /**
 * @var \App\Entity\User $user
 */
    $user = $this->getUser();
    $formData = [
        'currentPassword' => $user->getPassword(),
        'newPassword' => '',
        'confirmPassword' => '',
    ];
    $form = $this->createForm(ChangePasswordType::class,$formData);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $formData = $form->getData();
        if (!$passwordHasher->isPasswordValid($user, $formData['currentPassword'])) {
            $this->addFlash('error', 'The current password is incorrect.');
        } elseif ($formData['newPassword'] !== $formData['confirmPassword']) {
            $this->addFlash('error', 'The new password and confirmation do not match.');
        } else {

            $encodedPassword = $passwordHasher->encodePassword($user, $formData['newPassword']);
            $user->setPassword($encodedPassword);
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Password changed successfully.');
            return $this->redirectToRoute('app_user_profile');
        }
    }
    return $this->render('user/updatePassword.html.twig', [
        'form' => $form->createView(),
    ]);
}

}
