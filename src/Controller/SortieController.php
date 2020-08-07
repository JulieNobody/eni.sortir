<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\User;
use App\Form\UserType;
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
    public function accueil()
    {
        $sortie1 = new Sortie();
        $sortie1->setNom('la mer');
        $sortie1->setInfosSortie('Sortie à la mer');
        $sortie1->setNbInscriptionsMax(6);

        $sortie2 = new Sortie();
        $sortie2->setNom('Escalade');
        $sortie2->setInfosSortie('Sortie escalade');
        $sortie2->setNbInscriptionsMax(10);

        $sortie3 = new Sortie();
        $sortie3->setNom('ciné');
        $sortie3->setInfosSortie('Sortie ciné');
        $sortie3->setNbInscriptionsMax(8);

        //array_push($listeSorties, $sortie1, $sortie2, $sortie3);

        $listeSorties = array($sortie1, $sortie2, $sortie3);

        return $this->render("sortie/accueil.html.twig",[
            'listeSorties' => $listeSorties
        ]);
    }

    //fonction à déplacer dans le UserController




}
