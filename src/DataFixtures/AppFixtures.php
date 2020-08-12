<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\UserRepository;
use App\Repository\VilleRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker;

class AppFixtures extends Fixture
{
    // Choisissez le nombre d'exemples voulus pour chaque entités :
    private  $nbUsers = 10;
    private  $nbSorties = 20;
    private  $nbLieux = 20;
    private  $nbVilles = 5;



    private $encoder;
    private $repoVille;
    private $repoCampus;
    private $repoEtat;
    private $repoLieu;
    private $repoUser;

    public function __construct(UserPasswordEncoderInterface $encoder, VilleRepository $repoVille, CampusRepository $repoCampus,
                                EtatRepository $repoEtat, LieuRepository $repoLieu, UserRepository $repoUser)
    {
        $this-> encoder = $encoder;
        $this-> repoVille = $repoVille;
        $this-> repoCampus = $repoCampus;
        $this-> repoEtat = $repoEtat;
        $this-> repoLieu = $repoLieu;
        $this-> repoUser = $repoUser;
    }


    public function GetRandomVille(){
        return $this->repoVille->find(random_int(1, $this->nbVilles));
    }
    public function GetRandomCampus(){
        return $this->repoCampus->find(random_int(1, 3));
    }
    public function GetRandomEtat(){
        return $this->repoEtat->find(random_int(1, 6));
    }
    public function GetRandomLieu(){
        return $this->repoLieu->find(random_int(1, $this->nbLieux));
    }
    public function GetRandomUser(){
        return $this->repoUser->find(random_int(1, $this->nbUsers));
    }
    
    public function load(ObjectManager $manager)
    {
        // On configure dans quelles langues nous voulons nos données
        $faker = Faker\Factory::create('fr_FR');


        //LES CAMPUS
        $campus1 = new Campus();
        $campus1->setNom('SAINT-HERBLAIN');

        $campus2 = new Campus();
        $campus2->setNom('CHARTRES DE BRETAGNE');

        $campus3 = new Campus();
        $campus3->setNom('LA ROCHE SUR YON');

        $manager->persist($campus1);
        $manager->persist($campus2);
        $manager->persist($campus3);
        $manager->flush();


        //LES ETATS
        $etat1 = new Etat();
        $etat2 = new Etat();
        $etat3 = new Etat();
        $etat4 = new Etat();
        $etat5 = new Etat();
        $etat6 = new Etat();

        $etat1 ->setLibelle('Créée');
        $etat2 ->setLibelle('Ouverte');
        $etat3 ->setLibelle('Clôturée');
        $etat4 ->setLibelle('Activité en cours');
        $etat5 ->setLibelle('passée');
        $etat6 ->setLibelle('Annulée');

        $manager->persist($etat1);
        $manager->persist($etat2);
        $manager->persist($etat3);
        $manager->persist($etat4);
        $manager->persist($etat5);
        $manager->persist($etat6);
        $manager->flush();

        // LES VILLES
        for ($i = 0; $i < $this->nbVilles; $i++) {
            $ville = new Ville();

            $ville->setNom($faker->city);
            $ville->setCodePostal($faker->postcode);

            $manager->persist($ville);
        }
        $manager->flush();


        // LES LIEUX
        for ($i = 0; $i < $this->nbLieux; $i++) {
            $lieu = new Lieu();

            $lieu->setNom($faker->company);
            $lieu->setRue($faker->streetAddress);
            $lieu->setLatitude($faker->latitude);
            $lieu->setLongitude($faker->longitude);

            //Récupération d'une ville existante pour l'associer au lieu
            $maVille = $this->GetRandomVille();
            $lieu->setVille($maVille);

            $manager->persist($lieu);
        }
        $manager->flush();



        // LES USERS
        for ($i = 0; $i < $this->nbUsers; $i++) {
            $user = new User();

            $user->setNom($faker->lastName);
            $user->setPrenom($faker->firstNameFemale);
            $user->setUsername($user->getPrenom().$faker->biasedNumberBetween($min = 10, $max = 20, $function = 'sqrt'));

            $email = $user->getPrenom().".".$user->getNom()."@".$faker->freeEmailDomain;
            $email = trim($email);

            //FIXME : strtolower ne marche pas + enlever les accents
            //$email = strtolower($email); passe les caractères en minuscule

            $user->setEmail($email);
            $user->setTelephone($faker->e164PhoneNumber);
            $user->setPhoto('/img.pp.png');
            $user->setActif(true);
            $user->setRole('ROLE_USER');

            //hashage du mot de passe
            $passwordEncode = $this->encoder->encodePassword($user, $user->getPrenom());
            $user->setPassword($passwordEncode);

            //Récupération d'un campus existant pour l'associer au user
            $monCampus = $this->GetRandomCampus();
            $user->setCampus($monCampus);

            $manager->persist($user);
        }
        $manager->flush();


        // LES SORTIES
        for ($i = 0; $i < $this->nbSorties; $i++){
            $sortie = new Sortie();

            $sortie->setNom($faker->company);
            $sortie->setDateHeureDebut($faker->dateTimeThisYear($max='now', $timezone = 'Europe/Paris'));
            $sortie->setDuree($faker->numberBetween($min = 15, $max = 1000));

            $maDateLimite = $sortie->getDateHeureDebut();
            $sortie->setDateLimiteInscription($maDateLimite);

            $sortie->setNbInscriptionsMax($faker->numberBetween($min = 5, $max = 20));
            $sortie->setInfosSortie($faker->text($maxNbChars = 200));

            //Récupération d'un campus existant pour l'associer à la sortie
            $monCampus = $this->GetRandomCampus();
            $sortie->setCampus($monCampus);

            //Récupération d'un état existant pour l'associer à la sortie
            $monEtat = $this->GetRandomEtat();
            $sortie->setEtat($monEtat);

            //Récupération d'un lieu existant pour l'associer à la sortie
            $monLieu = $this->GetRandomLieu();
            $sortie->setLieu($monLieu);

            //Récupération d'un user existant pour l'associer à la sortie
            $monOrganisateur = $this->GetRandomUser();
            $sortie->setOrganisateur($monOrganisateur);

            $manager->persist($sortie);
        }

        $manager->flush();
    }
}
