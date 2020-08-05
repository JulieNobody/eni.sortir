<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{

    /**
     * @Route("/login", name="user_login")
     */
    public function login() {

        return $this->render('user/login.html.twig');
    }

    /**
     * Symfony gère entièrement cette route
     * @Route("/logout", name="user_logout")
     */
    public function logout() {}

    /**
     * @Route("/register", name="user_register")
     */
    public function register(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $registerForm = $this->createForm(UserType::class, $user);

        $registerForm->handleRequest($request);
        if($registerForm->isSubmitted() && $registerForm->isValid()){
            //hasher le mot de passe
            $hashed = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hashed);

            $user->setRole('ROLE_USER');
            $user->setActif(1);

            $em-> persist($user);
            $em->flush();
        }

        return $this->render('user/register.html.twig', [
            "registerForm" => $registerForm->createView()
        ]);
    }





}
