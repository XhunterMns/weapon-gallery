<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;

final class RegistrationController extends AbstractController
{
    #[Route('/registration', name: 'app_registration')]
    public function index(
        UserPasswordHasherInterface
        $hashedPassword,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response { // 1) build the form
        $user = new User();
        $form = $this->createForm("App\Form\UserType", $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $user->getPlainPassword();
            if ($plainPassword) {


                $hashedPassword = $hashedPassword->hashPassword(
                    $user,
                    $plainPassword
                );
            }
            $user->setPassword($hashedPassword);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }
        return $this->render(
            'registration/form.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    #[Route("/show/{id}", name: "show")]
    public function show2(EntityManagerInterface $entityManager, $id): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
        return $this->render('registration/show.html.twig', [
            'user' => $user,
        ]);
    }
    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName
    ): ?Response {
        $user = $token->getUser();
        // Récupérer les rôles
        $roles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $roles)) {
            return new RedirectResponse('/admin/dashboard');
        }
        return new RedirectResponse('/home');
    }

    #[Route('/login', name: 'app_login')]
public function login(AuthenticationUtils $authenticationUtils): Response
{
// Récupère les erreurs de login
$error = $authenticationUtils->getLastAuthenticationError();
$lastUsername = $authenticationUtils->getLastUsername();
return $this->render('registration/login.html.twig', [
'last_username' => $lastUsername,
'error' => $error
]);
}
#[Route('/logout', name: 'app_logout')]
public function logout() {}
}
