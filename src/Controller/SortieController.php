<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SortieController
 * @package App\Controller
 * @Route("/sortie_")
 */
class SortieController extends AbstractController
{

    /**
     * @Route("accueil", name="sortie_accueil")
     */
    public function accueil()
    {
        return $this->render("sortie/accueil.html.twig");
    }

    //fonction à déplacer dans le UserController
    /**
     * @Route("modifierProfil/{id}", name="user_modifierProfil", requirements={"id":"\d+"})
     */
    public function modifierProfil(User $user, Request $request, EntityManagerInterface $manager)
    {
        //création instance du formulaire
        $userForm = $this->createForm(UserType::class, $user);

        //association du formulaire avec la request
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid())
        {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'Le profil a bien été mis à jour');

            return $this->redirectToRoute('user_detailProfil');
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
