<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function accueil(){
        return $this->render("sortie/accueil.html.twig");
    }





}
