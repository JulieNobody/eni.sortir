<?php

namespace App\Controller;

use App\Entity\Campus;
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

        return $this->render("main/aVenir.html.twig",[]);
    }



    /**
     * @Route("test", name="main_test")
     */
    public function test(Request $request)
    {
        //$tableau = array("orange", "banana");
        //array_push($tableau, "apple", "raspberry");

        $campus1 = new Campus();
        $campus1->setNom("test1");
        $campus2 = new Campus();
        $campus2->setNom("test2");
        $campus3 = new Campus();
        $campus3->setNom("test3");

        $tableau[] = $campus1;
        array_push($tableau, $campus2);

        $tableau2 = array("Lorem1", "Lorem2", "Lorem3", "Lorem4", "Lorem5", "Lorem6", "Lorem7");

        unset($tableau2[array_search("Lorem4", $tableau2)]);


        //DUREE


            $heures = $request->request->get('heures');
            $minutes = $request->get('minutes');
            $duree = 0;

            if ($heures > 1) {
                $duree = ($heures * 60);
            }
            $duree = $duree + $minutes;




        return $this->render("main/test.html.twig",['tableau'=> $tableau, 'tableau2'=> $tableau2]);
    }




}
