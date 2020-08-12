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
       //commentaire test
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render("aVenir.html.twig",[]);
    }

    //FIXME déplacer dans sortieController
    /**
     * @Route("inscription/{id}", name="sortie_inscription", requirements={"id":"\d+"})
     */
    public function inscription(Sortie $sortie, EntityManagerInterface $manager)
    {
        //--- insertion du user dans la table sortie ---

        //récupération du user connecté
        $user = $this->getUser();

        //récuperation tableau de participant de la sortie
        $tableauParticipant[] = $sortie->getParticipants();

        //ajout du user au tableau de sortie
        array_push($tableauParticipant, $user);

        //renvoi du tableau completé dans la sortie
        $sortie->setParticipants($tableauParticipant);

        $manager->persist($sortie);
        $manager->flush();

        $this->addFlash('success', "Vous êtes inscrits à la sortie");

        return $this->redirectToRoute('accueil');


    }

    //FIXME déplacer dans sortieController
    /**
     * @Route("desinscription/{id}", name="sortie_desinscription", requirements={"id":"\d+"})
     */
    public function desinscription(Sortie $sortie, EntityManagerInterface $manager)
    {
        //--- suppression du user de la table sortie ---

        //récupération du user connecté
        $user = $this->getUser();

        //récuperation tableau de participant de la sortie
        $tableauParticipant[] = $sortie->getParticipants();

        //recherche puis suppression du user dans le tableau de participants
        unset($tableauParticipant[array_search($user, $tableauParticipant)]);

        //renvoi du tableau completé dans la sortie
        $sortie->setParticipants($tableauParticipant);

        $manager->persist($sortie);
        $manager->flush();

        $this->addFlash('success', "Vous êtes désinscrit de la sortie");

        return $this->redirectToRoute('accueil');
    }


    /**
     * @Route("test", name="main_test")
     */
    public function test()
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

        return $this->render("main/test.html.twig",['tableau'=> $tableau, 'tableau2'=> $tableau2]);
    }




}
