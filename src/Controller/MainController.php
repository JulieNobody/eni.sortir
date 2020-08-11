<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="accueil")
     */
    public function index()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->redirectToRoute('sortie_accueil');
    }

    /**
     * @Route("aVenir", name="main_aVenir")
     */
    public function aVenir()
    {
       //commentaire test
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render("aVenir.html.twig",[]);
    }




}
