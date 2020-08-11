<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\User;
use App\Form\SortieType;
use App\Form\UserType;
use App\Repository\EtatRepository;
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

    /**
     * @Route("creerSortie", name="sortie_creerSortie")
     */
    public function creerSortie(Request $request, EntityManagerInterface $manager, EtatRepository $etatRepository)
    {

        $sortie = new Sortie();

        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);

        /*
        // Test de bouton pour valider lieu et afficher détail du lieu -> marche pas
        // https://symfony.com/doc/4.4/form/multiple_buttons.html

        //validation du lieu
        $validLieu = "lieu pas validé";

        if ($sortieForm->get('validerLieu')->isClicked())
        {
            // variables test pour bouton valider lieu -> marche pas
            $this->addFlash('test', "lorem");
            $validLieu = "lieu validé";
            $lorem = "Lorem Lorem";
        }
        */

        //récupération de la sortie crée
        if($sortieForm->isSubmitted() && $sortieForm->isValid())
        {
            /* à la création d'une sortie
                   - organisateur : user
                   - campus : campus du user
                   - état : Créée
            */

            $sortie->setOrganisateur($this->getUser());
            $sortie->setCampus($this->getUser()->getCampus());

            $etatCreee = $etatRepository->findOneBy(array('libelle' => 'Créée'));

            $sortie->setEtat($etatCreee);

            $manager->persist($sortie);
            $manager->flush();

            //s'affiche sur la page d'acceuil
            $this->addFlash('success', "La sortie a été créée");

            return $this->redirectToRoute('accueil');
        }

        return $this->render("sortie/creerSortie.html.twig",[
            'sortieForm'=> $sortieForm->createView()
        ]);}




}
