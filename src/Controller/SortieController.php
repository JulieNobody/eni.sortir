<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\User;
use App\Form\SortieType;
use App\Form\UserType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
    public function accueil(SortieRepository $repo)
    {

        $listeSorties = $repo->findBy([], ['dateHeureDebut' => 'DESC']);

        return $this->render("sortie/accueil.html.twig",[
            'listeSorties' => $listeSorties
        ]);
    }

    /**
     * @Route("creerSortie", name="sortie_creerSortie")
     */
    public function creerSortie()
    {
        $sortie = new Sortie();

        $sortieForm = $this->createForm(SortieType::class, $sortie);



        return $this->render("sortie/creerSortie.html.twig",['sortieForm'=> $sortieForm->createView()]);
    }




}
