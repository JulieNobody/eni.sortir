<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\SearchForm;
use App\Form\SortieType;
use App\Form\UserType;
use App\Repository\SortieRepository;
use App\Repository\EtatRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function accueil(SortieRepository $repo, Request $request)
    {

        $data = new SearchData();
        $form = $this->createForm(SearchForm::class, $data);
        $form->handleRequest($request);
        $user = $this->getUser();
        $listeSorties = $repo->findSearch($data, $user);

       // $listeSorties = $repo->findBy([], ['dateHeureDebut' => 'DESC']);

        return $this->render("sortie/accueil.html.twig",[
            'listeSorties' => $listeSorties,
            'form' => $form->createView()
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

        //TODO : tester if(isset($_POST('nomDuBouton'))
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

            //organisateur
            $sortie->setOrganisateur($this->getUser());

            //campus
            $sortie->setCampus($this->getUser()->getCampus());

            //etat
            $etatCreee = $etatRepository->findOneBy(array('libelle' => 'Créée'));
            $sortie->setEtat($etatCreee);

            //durée
            $heures = $request->request->get('heures');
            $minutes = $request->get('minutes');
            $duree = 0;

            if ($heures > 1)
            {
                $duree = ($heures * 60);
            }
            $duree = $duree + $minutes;
            $sortie->setDuree($duree);

            //insertion en BDD
            $manager->persist($sortie);
            $manager->flush();

            //s'affiche sur la page d'acceuil
            $this->addFlash('success', "La sortie a été créée");

            return $this->redirectToRoute('accueil');
        }

        return $this->render("sortie/creerSortie.html.twig",[
            'sortieForm'=> $sortieForm->createView()
        ]);}

    /**
     * @Route("inscription/{id}", name="sortie_inscription", requirements={"id":"\d+"})
     */
    public function inscription(Sortie $sortie, EntityManagerInterface $manager)
    {
        //--- insertion du user dans la table sortie ---

        //récupération du user connecté
        $user = $this->getUser();

        $message = $sortie->addParticipant($user);

        $manager->persist($sortie);
        $manager->flush();

        $this->addFlash('result', $message);

        return $this->redirectToRoute('accueil');
    }
    //Fonction permettant d'afficher une sortie
    //Requirements permet de renseigner que un integer en id
    /**
     * @Route("afficheSortie/{id}", name="sortie_afficheSortie", requirements={"id":"\d+"})
     * @param SortieRepository $sortieRepo
     * @return Response
     */
    public function sortie(SortieRepository $sortieRepo, $id)
    {
        $sortie = $sortieRepo->find($id);
        $particpants = $sortieRepo->findOneBySomeParticipants($id);

            return $this->render('sortie/afficheSortie.html.twig', [
                "sortie" => $sortie, "particpants" => $particpants]);
        }


    /**
     * @Route("desinscription/{id}", name="sortie_desinscription", requirements={"id":"\d+"})
     */
    public function desinscription(Sortie $sortie, EntityManagerInterface $manager)
    {
        //--- suppression du user de la table sortie ---

        //récupération du user connecté
        $user = $this->getUser();

        $message = $sortie->removeParticipant($user);

        $manager->persist($sortie);
        $manager->flush();

        $this->addFlash('result', $message);

        return $this->redirectToRoute('accueil');
    }


}
