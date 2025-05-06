<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Ref;
use App\Entity\Sejour;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Commande
 *
 * @ORM\Table(name="commande", indexes={@ORM\Index(name="fk_Commandestatut_idx", columns={"statut"}), @ORM\Index(name="fkuser_idx", columns={"id_user"})})
 * @ORM\Entity(repositoryClass="App\Repository\CommandeRepository")
 */
class Commande
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var int|null
     *
     * @ORM\Column(name="num_comande", type="integer",length=200,nullable=true)
     */
    private $numComande;

    /**
     *
     * @ORM\Column(name="montantHt" ,type="float", nullable=true)
     */
    private $montantht;


    /**
     *
     * @ORM\Column(name="montanenv" ,type="float", nullable=true)
     */
    private $montanenv;

    /**
     *
     * @ORM\Column(name="montantRth", type="float", nullable=true)
     */
    private $montantrth;
    /**
     * @var int|null
     *
     * @ORM\Column(name="traiter", type="integer", nullable=true)
     */
    private $traiter;

    /**
     *
     * @ORM\Column(name="moantantTtcregl", type="float", nullable=true)
     */
    private $moantantTtcregl;

    /**
     *
     * @ORM\Column(name="tva", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $tva;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="Sejour")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_sejour", referencedColumnName="id")
     * })
     */
    private $idSejour;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_user", referencedColumnName="id")
     * })
     */
    private $idUser;

    /**
     * @var \Ref
     *
     * @ORM\ManyToOne(targetEntity="Ref")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="statut", referencedColumnName="id")
     * })
     */
    private $statut;
    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_create_commande", type="date", nullable=true)
     */
    private $dateCreateCommande;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_facture", type="date", nullable=true)
     */
    private $dateFacture;

    /**
     * @var \Adress
     *
     * @ORM\ManyToOne(targetEntity="Adress")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="adresslivraison", referencedColumnName="id" )
     * })
     */
    private $adresslivraison;


    /**
     * @ORM\Column(name="Jeton",type="integer", nullable=true)
     */
    private $jeton;

    /**
     * @return mixed
     */
    public function getJeton()
    {
        return $this->jeton;
    }

    /**
     * @param mixed $jeton
     */
    public function setJeton($jeton): void
    {
        $this->jeton = $jeton;
    }


    /**
     * @var \Adress
     *
     * @ORM\ManyToOne(targetEntity="Adress")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="adressfactoration", referencedColumnName="id" )
     * })
     */
    private $adressfactoration;

    /**
     * @ORM\Column(name="payement_type", type="string", length=255, nullable=true)
     */
    private $paymentType;

    /**
     * @ORM\Column(name="periode", type="string", length=255, nullable=true)
     */
    private $periode;
    /**
     * @return mixed
     */
    public function getDupligetStatut()
    {
        return $this->dupligetStatut;
    }

    /**
     * @param mixed $dupligetStatut
     */
    public function setDupligetStatut($dupligetStatut): void
    {
        //ready
        //generated
        //ready to send
        //suspended
        //sent
        //dispatched

        $this->dupligetStatut = $dupligetStatut;
    }
    /**
     * @ORM\Column(name="dupligetStatut", type="string", length=255, nullable=true)
     */
    private $dupligetStatut;

    public function getPaymentType(): ?string
    {
        return $this->paymentType;

    }

    public function setPaymentType( $paymentType):void
    {
        $this->paymentType = $paymentType;

    }


    /**
     * @return \DateTime|null
     */
    public function getDateCreateCommande(): ?\DateTime
    {
        return $this->dateCreateCommande;
    }

    /**
     * @param \DateTime|null $dateCreateCommande
     */
    public function setDateCreateCommande(?\DateTime $dateCreateCommande): void
    {
        $this->dateCreateCommande = $dateCreateCommande;
    }

    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ComandeProduit", mappedBy="idComande")
     */
    private $commandesProduits;


    /**
     * @ORM\Column(name="revesmentpart",type="float", nullable=true)
     */
    private $revesmentpart;

       /**
     * @ORM\Column(name="remisecodepromo",type="float", nullable=true)
     */
    private $remisecodepromo;

        /**
     * @ORM\Column(name="totalBlackFriday",type="float", nullable=true)
     */
    private $totalBlackFriday;

    /**
     * @ORM\Column(name="nbconnx",type="integer", nullable=true)
     */
    private $nbconnx;

    /**
     * @ORM\Column(name="rev",type="integer", nullable=true)
     */
    private $rev;

    /**
     * @ORM\Column(name="NumSuivi",type="string", length=255, nullable=true)
     */
    private $NumSuivi;

    /**
     * @ORM\Column(name="dateLivraison",type="date", nullable=true)
     */
    private $dateLivraison;

    /**
     * @ORM\Column(name="dateExpidition",type="date", nullable=true)
     */
    private $dateExpidition;

    /**
     * @ORM\Column(name="lettreSuivi",type="string", length=255, nullable=true)
     */
    private $lettreSuivi;

    /**
     * @ORM\Column(name="addresseLivraison",type="string", length=255, nullable=true)
     */
    private $addresseLivraison;

    /**
     * @var int|null
     *
     * @ORM\Column(name="nbenfantencours", type="integer", nullable=true)
     */
    private $nbenfantencours;
    


    /**
     * @ORM\Column(name="numfacture",type="integer", length=200, nullable=true)
     */
    private $numfacture;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Panier", inversedBy="commandes")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="idPanier_id", referencedColumnName="id")
     * })   
     */
    private $idPanier;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LogPromotions", mappedBy="idCommande")
     */
    private $logPromotions;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Promotions", inversedBy="commandes")
     */
    private $idPromotion;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\LogPromotions", inversedBy="commandes")
     */
    private $logPromo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CommandeNumerique", mappedBy="idCommande")
     */
    private $commandeNumeriques;


    public function __construct()
    {

        $this->commandesProduits = new ArrayCollection();
        $this->logPromotions = new ArrayCollection();
        $this->commandeNumeriques = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getCommandesProduits()
    {
        return $this->commandesProduits;
    }

    /**
     * @param mixed $commandesProduits
     */
    public function setCommandesProduits($commandesProduits): void
    {
        $this->commandesProduits = $commandesProduits;
    }
    public function getNumComande(): ?int
    {
        return $this->numComande;
    }

    public function setNumComande(?int $numComande): self
    {
        $this->numComande = $numComande;

        return $this;
    }

    public function getMontantht(): ?float
    {
        return $this->montantht;
    }

    public function setMontantht(?float $montantht): self
    {
        $this->montantht = $montantht;

        return $this;
    }




    public function getMontanenv(): ?float
    {
        return $this->montanenv;
    }

    public function setMontanenv(?float $montanenv): self
    {
        $this->montanenv = $montanenv;

        return $this;
    }

    

    public function getMontantrth(): ?float
    {
        return $this->montantrth;
    }

    public function setMontantrth(?float $montantrth): self
    {
        $this->montantrth = $montantrth;

        return $this;
    }

    public function getMoantantTtcregl(): ?float
    {
        return $this->moantantTtcregl;
    }

    public function setMoantantTtcregl(?float $moantantTtcregl): self
    {
        $this->moantantTtcregl = $moantantTtcregl;

        return $this;
    }

    public function getTva(): ?string
    {
        return $this->tva;
    }

    public function setTva(?string $tva): self
    {
        $this->tva = $tva;

        return $this;
    }

    public function getIdSejour(): ?Sejour
    {
        return $this->idSejour;
    }

    public function setIdSejour(?Sejour $idSejour): self
    {
        $this->idSejour = $idSejour;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getStatut(): ?Ref
    {
        return $this->statut;
    }

    public function setStatut(?Ref $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    // public function setId(?int $id): self
    // {
    //     $this->id = $id;

    //     return $this;
    // }

    public function getRevesmentpart(): ?float
    {
        return $this->revesmentpart;
    }

    public function setRevesmentpart(?float $revesmentpart): self
    {
        $this->revesmentpart = $revesmentpart;

        return $this;
    }

        public function getRemisecodepromo(): ?float
    {
        return $this->remisecodepromo;
    }

    public function setRemisecodepromo(?float $remisecodepromo): self
    {
        $this->remisecodepromo = $remisecodepromo;

        return $this;
    }

    public function getNbconnx(): ?int
    {
        return $this->nbconnx;
    }

    public function setNbconnx(?int $nbconnx): self
    {
        $this->nbconnx = $nbconnx;

        return $this;
    }


    public function getRev(): ?int
    {
        return $this->rev;
    }

    public function setRev(?int $rev): self
    {
        $this->rev = $rev;

        return $this;
    }

    public function getNumSuivi(): ?string
    {
        return $this->NumSuivi;
    }

    public function setNumSuivi(?string $NumSuivi): self
    {
        $this->NumSuivi = $NumSuivi;

        return $this;
    }

    public function getDateLivraison(): ?\DateTimeInterface
    {
        return $this->dateLivraison;
    }

    public function setDateLivraison(?\DateTimeInterface $dateLivraison): self
    {
        $this->dateLivraison = $dateLivraison;

        return $this;
    }

    public function getDateExpidition(): ?\DateTimeInterface
    {
        return $this->dateExpidition;
    }

    public function setDateExpidition(?\DateTimeInterface $dateExpidition): self
    {
        $this->dateExpidition = $dateExpidition;

        return $this;
    }

    public function getLettreSuivi(): ?string
    {
        return $this->lettreSuivi;
    }

    public function setLettreSuivi(?string $lettreSuivi): self
    {
        $this->lettreSuivi = $lettreSuivi;

        return $this;
    }

    public function getAddresseLivraison(): ?string
    {
        return $this->addresseLivraison;
    }

    public function setAddresseLivraison(?string $addresseLivraison): self
    {
        $this->addresseLivraison = $addresseLivraison;

        return $this;
    }




    public function getNumfacture(): ?string
    {
        return $this->numfacture;
    }

    public function setNumfacture(?string $numfacture): self
    {
        $this->numfacture = $numfacture;

        return $this;
    }



    public function getIdPanier(): ?Panier
    {
        return $this->idPanier;
    }

    public function setIdPanier(?Panier $idPanier): self
    {
        $this->idPanier = $idPanier;

        return $this;
    }



    public function getAdresslivraison(): ?Adress
    {
        return $this->adresslivraison;
    }

    public function setAdresslivraison(?Adress $adresslivraison): self
    {
        $this->adresslivraison = $adresslivraison;

        return $this;
    }




    public function getAdressfactoration(): ?Adress
    {
        return $this->adressfactoration;
    }

    public function setAdressfactoration(?Adress $adressfactoration): self
    {
        $this->adressfactoration = $adressfactoration;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateFacture(): ?\DateTime
    {
        return $this->dateFacture;
    }

    /**
     * @param \DateTime|null $dateFacture
     */
    public function setDateFacture(?\DateTime $dateFacture): void
    {
        $this->dateFacture = $dateFacture;
    }

    /**
     * @return int|null
     */
    public function getTraiter(): ?int
    {
        return $this->traiter;
    }

    /**
     * @param int|null $traiter
     */
    public function setTraiter(?int $traiter): void
    {
        $this->traiter = $traiter;
    }

    /**
     * @return mixed
     */
    public function getPeriode()
    {
        return $this->periode;
    }

    /**
     * @param mixed $periode
     */
    public function setPeriode($periode): void
    {
        $this->periode = $periode;
    }

    /**
     * @return int|null
     */
    public function getNbenfantencours(): ?int
    {
        return $this->nbenfantencours;
    }

    /**
     * @param int|null $nbenfantencours
     */
    public function setNbenfantencours(?int $nbenfantencours): void
    {
        $this->nbenfantencours = $nbenfantencours;
    }


    



    /**
     * Get the value of totalBlackFriday
     */ 
    public function getTotalBlackFriday()
    {
        return $this->totalBlackFriday;
    }

    /**
     * Set the value of totalBlackFriday
     *
     * @return  self
     */ 
    public function setTotalBlackFriday($totalBlackFriday)
    {
        $this->totalBlackFriday = $totalBlackFriday;

        return $this;
    }

    /**
     * @return Collection|LogPromotions[]
     */
    public function getLogPromotions(): Collection
    {
        return $this->logPromotions;
    }

    public function addLogPromotion(LogPromotions $logPromotion): self
    {
        if (!$this->logPromotions->contains($logPromotion)) {
            $this->logPromotions[] = $logPromotion;
            $logPromotion->setIdCommande($this);
        }

        return $this;
    }

    public function removeLogPromotion(LogPromotions $logPromotion): self
    {
        if ($this->logPromotions->contains($logPromotion)) {
            $this->logPromotions->removeElement($logPromotion);
            // set the owning side to null (unless already changed)
            if ($logPromotion->getIdCommande() === $this) {
                $logPromotion->setIdCommande(null);
            }
        }

        return $this;
    }

    public function getIdPromotion(): ?Promotions
    {
        return $this->idPromotion;
    }

    public function setIdPromotion(?Promotions $idPromotion): self
    {
        $this->idPromotion = $idPromotion;

        return $this;
    }

    public function getLogPromo(): ?LogPromotions
    {
        return $this->logPromo;
    }

    public function setLogPromo(?LogPromotions $logPromo): self
    {
        $this->logPromo = $logPromo;

        return $this;
    }

    /**
     * @return Collection|CommandeNumerique[]
     */
    public function getCommandeNumeriques(): Collection
    {
        return $this->commandeNumeriques;
    }

    public function addCommandeNumerique(CommandeNumerique $commandeNumerique): self
    {
        if (!$this->commandeNumeriques->contains($commandeNumerique)) {
            $this->commandeNumeriques[] = $commandeNumerique;
            $commandeNumerique->setIdCommande($this);
        }

        return $this;
    }

    public function removeCommandeNumerique(CommandeNumerique $commandeNumerique): self
    {
        if ($this->commandeNumeriques->contains($commandeNumerique)) {
            $this->commandeNumeriques->removeElement($commandeNumerique);
            // set the owning side to null (unless already changed)
            if ($commandeNumerique->getIdCommande() === $this) {
                $commandeNumerique->setIdCommande(null);
            }
        }

        return $this;
    }
}
