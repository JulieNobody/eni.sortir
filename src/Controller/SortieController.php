<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{

    /**
     * @Route("/accueil", name="accueil")
     */
    public function accueil(){
        return $this->render("sortie/accueil.html.twig");
    }






}
