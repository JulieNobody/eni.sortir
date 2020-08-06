<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
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

         if ($this -> isGranted('IS_AUTHENTICATED_FULLY') ){
             return $this->redirectToRoute('accueil');
         } else{
            return $this->render('user/login.html.twig');
        }


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

    /**
     * @Route("modifierProfil/{id}", name="user_modifierProfil", requirements={"id":"\d+"})
     */
    public function modifierProfil(User $user, Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, UserRepository $repository)
    {
        //création instance du formulaire
        $userForm = $this->createForm(UserType::class, $user);

        //association du formulaire avec la request
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid())
        {
            $usernameIsUnique = $repository->findOneBy(["username" => $user->getUsername()]);

            if($usernameIsUnique === null)
            {
                $hashed = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($hashed);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash('success', 'Le profil a bien été mis à jour');

            }else{
                $this->addFlash('error', 'Désolé, le pseudo est déja utilisé');
            }

        }

        return $this->render("user/modifierProfil.html.twig", ['userForm' => $userForm->createView()]);
    }

    /**
     * @Route("detailProfil", name="user_detailProfil")
     */
    public function detailProfil()
    {

        return $this->render("user/detailProfil.html.twig");
    }

}
