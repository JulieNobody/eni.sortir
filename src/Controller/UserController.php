<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{

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
        //récupération du pseudo du user
        $oldUsername = $user->getUsername();

        //création instance du formulaire
        $userForm = $this->createForm(UserType::class, $user);

        //association du formulaire avec la request
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid())
        {
            $usernameIsUnique = $repository->findOneBy(["username" => $user->getUsername()]);

            if($usernameIsUnique === null || $user->getUsername() === $oldUsername)
            {
                $hashed = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($hashed);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash('success', 'Le profil a bien été mis à jour');


                //$this->redirectToRoute('accueil' );


                return $this->render("user/detailProfil.html.twig",['user'=>$user]);
                //$this->redirectToRoute('user_detailProfil', array('id'=> $this->getUser()->getId() ) );

                //$this->redirectToRoute('user_detailProfil', array('id' => 9));

            }else{
                $this->addFlash('error', 'Désolé, le pseudo est déja utilisé');
            }

        }

        return $this->render("user/modifierProfil.html.twig", ['userForm' => $userForm->createView()]);
    }

    /**
     * @Route("detailProfil/{id}", name="user_detailProfil", requirements={"id":"\d+"})
     * @ParamConverter()
     */
    public function detailProfil(User $user)
    {


        return $this->render("user/detailProfil.html.twig",['user'=>$user]);
    }




}
