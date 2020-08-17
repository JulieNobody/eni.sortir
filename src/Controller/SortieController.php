<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;
use App\Form\LieuType;
use App\Form\SearchForm;
use App\Form\SortieType;
use App\Form\UserType;
use App\Form\VilleType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormTypeInterface;
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
    public function accueil(SortieRepository $repo, Request $request)
    {

        $data = new SearchData();
        $form = $this->createForm(SearchForm::class, $data);
        $form->handleRequest($request);
        $listeSorties = $repo->findSearch($data);

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

    /**
     * @Route("creerLieu", name="sortie_creerLieu")
     */
    public function creerLieu(Request $request, EntityManagerInterface $manager, VilleRepository $villeRepository)
    {
        $lieu = new Lieu();
        $ville = new  Ville();

        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $villeForm = $this->createForm(VilleType::class, $ville);

        $villeForm->handleRequest($request);
        $lieuForm->handleRequest($request);



        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {

            if (!$villeRepository->findOneBy(array('nom' => $ville->getNom(), 'codePostal' => $ville->getCodePostal()))){

                $lieu->setVille($ville);
                $manager->persist($ville);
                $manager->persist($lieu);

                $manager->flush();

            }else{
                $maVille = $villeRepository->findOneBy(array('nom' => $ville->getNom(),  'codePostal' => $ville->getCodePostal()));

                $lieu->setVille($maVille);
                $manager->persist($lieu);
                $manager->flush();

            }

            return $this->redirectToRoute('sortie_creerLieu');

        }


        return $this->render("sortie/creerLieu.html.twig", [
            "lieuForm" => $lieuForm->createView(),
            "villeForm" => $villeForm->createView()
        ]);
    }

}





