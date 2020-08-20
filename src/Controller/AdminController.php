<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserAdminType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AdminController
 * @package App\Controller
 * @Route("/admin_")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("menu", name="admin_menu")
     */
    public function index()
    {
        return $this->render('admin/menu-admin.html.twig', []);
    }

    /**
     * @Route("inscription", name="admin_inscription")
     */
    public function register(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, UserRepository $repository)
    {
        $user = new User();
        $registerForm = $this->createForm(UserAdminType::class, $user);

        $registerForm->handleRequest($request);
        if($registerForm->isSubmitted() && $registerForm->isValid()){

            //USERNAME
                $username = $user->getPrenom()."-".$user->getNom()."-".date('yy') ;
                $usernameIsUnique = $repository->findOneBy(["username" => $username]);

                $i = 1;
                //vérification que le username n'existe pas déja, si il existe, on ajoute un nombre (incrément) à la fin
                while ($usernameIsUnique !== null) {
                    $i ++;
                    $username = $user->getPrenom()."-".$user->getNom()."-".date('yy')."-".$i;
                    $usernameIsUnique = $repository->findOneBy(["username" => $username]);
                }

                $user->setUsername($username);

            //PASSWORD
                //hasher le mot de passe (mot de passe : Pa$$w0rd)
                $hashed = $encoder->encodePassword($user, "Pa$\$w0rd");
                $user->setPassword($hashed);

            //PHOTO
                $user->setPhoto('pp.png');

            $em-> persist($user);
            $em->flush();

            $this->addFlash('success', 'L\'utilisateur à bien été enregistré');
            return $this->render("admin/menu-admin.html.twig", []);
        }

        return $this->render('admin/inscription.html.twig', [
            "registerForm" => $registerForm->createView()
        ]);
    }


}
