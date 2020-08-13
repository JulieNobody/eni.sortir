<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SortieRepository::class)
 */
class Sortie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateHeureDebut;

    /**
     * @ORM\Column(type="integer")
     */
    private $duree;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateLimiteInscription;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbInscriptionsMax;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $infosSortie;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Lieu", inversedBy="sorties")
     */
    private $lieu;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Campus", inversedBy="sorties")
     */
    private $campus;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User")
     */
    private $participants;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $organisateur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Etat", inversedBy="sorties")
     */
    private $etat;

    public function __construct(){
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateHeureDebut(): ?\DateTimeInterface
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(\DateTimeInterface $dateHeureDebut): self
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDuree()
    {
        return $this->duree;
    }

    /**
     * @param mixed $duree
     */
    public function setDuree($duree): void
    {
        $this->duree = $duree;
    }



    public function getDateLimiteInscription(): ?\DateTimeInterface
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(\DateTimeInterface $dateLimiteInscription): self
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getNbInscriptionsMax(): ?int
    {
        return $this->nbInscriptionsMax;
    }

    public function setNbInscriptionsMax(int $nbInscriptionsMax): self
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;

        return $this;
    }

    public function getInfosSortie(): ?string
    {
        return $this->infosSortie;
    }

    public function setInfosSortie(string $infosSortie): self
    {
        $this->infosSortie = $infosSortie;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLieu()
    {
        return $this->lieu;
    }

    /**
     * @param mixed $lieu
     */
    public function setLieu($lieu): void
    {
        $this->lieu = $lieu;
    }

    /**
     * @return mixed
     */
    public function getCampus()
    {
        return $this->campus;
    }

    /**
     * @param mixed $campus
     */
    public function setCampus($campus): void
    {
        $this->campus = $campus;
    }

    /**
     * @return mixed
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * @param mixed $participants
     */
    public function setParticipants($participants): void
    {
        $this->participants = $participants;
    }

    public function addParticipant(User $user){
        if(sizeof($this->participants) >= $this->nbInscriptionsMax){
            return $message = 'Vous ne pouvez plus vous inscrire à la sortie '.$this->getNom().' : L\'activité est complète !';
        }else if($this->participants->contains($user)){
            return $message = 'Vous êtes déjà inscrit à la sortie '.$this->getNom().'. Amusez-vous bien !';
        }else if($this->etat->getId() == 1){
            return $message = 'La sortie '.$this->getNom().' n\'est pas encore ouverte aux inscriptions !';
        }else if($this->etat->getId() == 3){
            return $message = 'Vous ne pouvez plus vous inscrire à la sortie '.$this->getNom().' : Les inscriptions sont clôturées !';
        }else if($this->etat->getId() == 4){
            return $message = 'La sortie '.$this->getNom().' est en cours. Vous ne pouvez plus vous y inscrire !';
        }else if($this->etat->getId() == 5){
            return $message = 'La sortie '.$this->getNom().' a déjà eu lieu. Vous ne pouvez pas vous inscrire à une sortie passée !';
        }else if($this->etat->getId() == 6){
            return $message = 'La sortie '.$this->getNom().' a été annulée et n\'est plus ouverte aux inscriptions !';
        }else {
            $this->participants[] = $user;
            return $message = 'Vôtre demande d\'inscription à la sortie '.$this->getNom().' a bien été prise en compte. Amusez-vous bien !';
        }
    }

    public function removeParticipant(User $user){
        if($this->participants->contains($user) and ($this->etat ->getId() == 2 or $this->etat->getId() == 3)){
            $this->participants->removeElement($user);
            return $message = 'Vôtre demande de désistement à la sortie '.$this->getNom().' a bien été prise en compte.';
        }else if($this->etat ->getId() == 4){
            return $message = 'La sortie '.$this->getNom().' est en cours. Vous ne pouvez plus vous désinscrire !';
        }else if($this->etat ->getId() == 5){
            return $message = 'La sortie '.$this->getNom().' a déjà eu lieu. Vous ne pouvez pas vous désinscrire d\'une sortie passée !';
        }else if($this->etat ->getId() == 6){
            return $message = 'La sortie '.$this->getNom().' a été annulée et n\'est plus sujette aux inscriptions/désinscriptions !';
        }else {
            return $message = 'La désinscription est impossible à réaliser sur cette sortie !';
        }

    }

    /**
     * @return mixed
     */
    public function getOrganisateur()
    {
        return $this->organisateur;
    }

    /**
     * @param mixed $organisateur
     */
    public function setOrganisateur($organisateur): void
    {
        $this->organisateur = $organisateur;
    }

    /**
     * @return mixed
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * @param mixed $etat
     */
    public function setEtat($etat): void
    {
        $this->etat = $etat;
    }


}
