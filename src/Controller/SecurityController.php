<?php

namespace App\Controller;

use App\Form\RedefinePasswordType;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/redefine-password', name: 'app_redefine_password')]
    public function redefinePassword(Request $request, EntityManagerInterface $manager, AppAuthenticator $appAuthenticator,
    UserPasswordHasherInterface $hasher){

        $user = $this->getUser();
        $email = $user->getEmail();
        //dd($user);

        if($request->getMethod() == 'POST'){

            $user->setPassword($request->get('redefine_password')['plainPassword']['first']);
        }


        $form = $this->createForm(RedefinePasswordType::class, $user);
        $form->handleRequest($request);

        $pass = $form->get('plainPassword')->getData();




        if($form->isSubmitted() && $form->isValid()){
            $user->setFirstConnexion(false);
            $user->setPassword($hasher->hashPassword($user, $user->getPassword()));
            $manager->flush();

            $request->getSession()->set(Security::LAST_USERNAME, $email);

             new Passport(
                new UserBadge($email),
                new PasswordCredentials($request->request->get('password', $pass)),
                [
                    new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                ]
            );



            return  $this->redirectToRoute('app_employee_index');


        }

        return $this->render('security/redefine-password.html.twig', [
            'form'=> $form->createView()
        ]);

    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
