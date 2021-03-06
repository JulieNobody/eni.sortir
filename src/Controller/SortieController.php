<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;
use App\Form\LieuType;
use App\Form\MotifSortieType;
use App\Form\SearchForm;
use App\Form\SortieType;
use App\Form\UserType;
use App\Form\VilleType;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Repository\EtatRepository;
use App\Repository\VilleRepository;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $campusUser = $this->getUser()->getCampus();
        $data = new SearchData();
        $data->campus = $campusUser;
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
            /*
                        //durée
                        $heures = $request->request->get('heures');
                        $minutes = $request->get('minutes');
                        $duree = 0;
                        if ($heures > 1)
                        {
                            $duree = ($heures * 60);
                        }
                        $duree = $duree + $minutes;
                        $sortie->setDuree($duree);*/


            //insertion en BDD
            $manager->persist($sortie);
            $manager->flush();

            //s'affiche sur la page d'acceuil
            $this->addFlash('success', "La sortie a été créée");

            return $this->redirectToRoute('accueil');
        }



        return $this->render("sortie/creerSortie.html.twig",[
            'sortieForm'=> $sortieForm->createView(),

        ]);
    }



    /**
     * @Route("getVille",methods={"POST", "GET"}, name="getVille")
     */
    public function getVilleController(Request $request, LieuRepository $lieuRepository)
    {
        $idVille = $_POST['idVille'];
        //dd($idVille);
        $listeLieux = $lieuRepository->findLieuTriVille($idVille);

        //dd($listeLieux);
        //$toto = 5;

        $retour = [];
        foreach ($listeLieux as $item){
            $retour[] = [
                'id' => $item->getId(),
                'nom' => $item->getNom()
            ];
        }

        return $this->json($retour);

    }





    /**
     * @Route("getLieu",methods={"POST"}, name="getLieu")
     */
            public function getLieu(Request $request, LieuRepository $lieuRepository)
            {
                   $monId = $_POST['id'];

                   $monLieu = $lieuRepository->findOneBy(array('id' => $monId));


                   $response = new Response("<li class='lieuAdresse'>Adresse : </li>".
                                                    "<li class='lieuRue'>".$monLieu->getRue()."</li>".
                                                    "<li class='lieuVille'>".$monLieu->getVille()."</li>".
                                                    "<li>Latitude : ".$monLieu->getLatitude()."</li>".
                                                    "<li>Longitude : ".$monLieu->getLongitude()."</li>"
                   );
                   return $response;

            }





    //Fonction permettant d'afficher une sortie
    //Requirements permet de renseigner que un integer en id
    //"SELECT * FROM lieu WHERE id = :id";






    /**
     * @Route("afficheSortie/{id}", name="sortie_afficheSortie", requirements={"id":"\d+"})
     * @return Response
     */
    public function sortie(Sortie $sortie)
    {
        return $this->render('sortie/afficheSortie.html.twig', [
            "sortie" => $sortie
        ]);
    }

    //Fonction permettant d'afficher la page d'annulation d'une sortie
    //Requirements permet de renseigner que un integer en id
    /**
     * @Route("afficheAnnulationSortie/{id}", name="sortie_afficheAnnulationSortie", requirements={"id":"\d+"})
     * @return Response
     */
    public function afficherAnnulationSortie(Sortie $sortie, Request $request, EntityManagerInterface $manager)
    {

        $sortieMotifForm = $this->createForm(MotifSortieType::class, $sortie);
        $sortieMotifForm->handleRequest($request);

        if($sortieMotifForm->isSubmitted() and $sortieMotifForm->isValid()){

            $manager->persist($sortie);
            $manager->flush();

            return $this->redirectToRoute('sortie_annulation',[
                'sortie'=> $sortie,
                'id' => $sortie->getId()
            ]);
        }

        return $this->render('sortie/annulationSortie.html.twig', [
            "sortie" => $sortie,
            'form' => $sortieMotifForm->createView()
        ]);
    }

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

    /**
     * @param Sortie $sortie
     * @param EntityManagerInterface $manager
     * @Route("annulation/{id}", name="sortie_annulation", requirements={"id":"\d+"})
     */
    public function annulerSortie(Sortie $sortie, EntityManagerInterface $manager, EtatRepository $repoEtat)
    {
        $etat = $repoEtat->find(6);

        if($sortie->getEtat()->getId() == 4){
            $this->addFlash('result', 'Vous ne pouvez pas annuler une sortie en cours !');
            return $this->redirectToRoute('accueil');
        }elseif ($sortie->getEtat()->getId() == 5){
            $this->addFlash('result', 'Vous ne pouvez pas annuler une sortie passée !');
            return $this->redirectToRoute('accueil');
        }
        $message = $sortie->annulerSortie($this->getUser(), $etat);
        $manager->persist($sortie);
        $manager->flush();

        $this->addFlash('result', $message);
        return $this->redirectToRoute('accueil');
    }

    /**
     * @param Sortie $sortie
     * @param EntityManagerInterface $manager
     * @Route("publication/{id}", name="sortie_publication", requirements={"id":"\d+"})
     */
    public function publierSortie(Sortie $sortie, EntityManagerInterface $manager, EtatRepository $repoEtat)
    {
        $etat = $repoEtat->find(2);

        if($sortie->getEtat()->getId() != 1){
            $this->addFlash('result', 'Vôtre sortie a déjà été publiée !');
            return $this->redirectToRoute('accueil');
        }
        $message = $sortie->publierSortie($this->getUser(), $etat);
        $manager->persist($sortie);
        $manager->flush();

        $this->addFlash('result', $message);
        return $this->redirectToRoute('accueil');
    }

    /**
     * @Route("creerLieu", name="sortie_creerLieu")
     */
    public function creerLieu(Request $request, EntityManagerInterface $manager, VilleRepository $villeRepository)
    {
        //Création de 2 formulaire pour insertion dans 2 tables
        $lieu = new Lieu();
        $ville = new  Ville();

        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $villeForm = $this->createForm(VilleType::class, $ville);

        $villeForm->handleRequest($request);
        $lieuForm->handleRequest($request);


        //vérification
        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {

            //Si le lieu n'existe pas, insertion BDD
            //liaison des 2 BDD
            if (!$villeRepository->findOneBy(array('nom' => $ville->getNom()))){

                $lieu->setVille($ville);
                $manager->persist($ville);
                $manager->persist($lieu);

                $manager->flush();

            }else{

                //Si lieu existe, chercher le nom de la ville
                //récupération 'ville-id' dans 'lieu' et insertion BDD
                $maVille = $villeRepository->findOneBy(array('nom' => $ville->getNom()));

                $lieu->setVille($maVille);
                $manager->persist($lieu);
                $manager->flush();

            }


            return $this->redirectToRoute('sortie_creerSortie');

        }


        return $this->render("sortie/creerLieu.html.twig", [
            "lieuForm" => $lieuForm->createView(),
            "villeForm" => $villeForm->createView()
        ]);
    }

    /**
     * @Route("modifierSortie/{id}", name="sortie_modifierSortie", requirements={"id":"\d+"})
     */
    public function modifierSortie(Sortie $sortie, Request $request, EntityManagerInterface $manager, SortieRepository $repository)
    {
        //création instance du formulaire
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        //association du formulaire avec la request
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid())
        {

            $manager->persist($sortie);
            $manager->flush();

            $this->addFlash('success', 'La sortie a bien été modifiée');

            return $this->render("sortie/afficheSortie.html.twig",['sortie'=>$sortie]);

        }

        return $this->render("sortie/modifierSortie.html.twig", [
            'sortieForm'    => $sortieForm->createView(),
            'sortie'        => $sortie
        ]);
    }



}