<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Sejour
 *
 * @ORM\Table(name="sejour", indexes={@ORM\Index(name="fk_statu_ref_idx", columns={"statut"}), @ORM\Index(name="fk_userAcomp_idx", columns={"Id_acommp"}), @ORM\Index(name="fk_age_ref_idx", columns={"age_group"}), @ORM\Index(name="fk_userpartenaire_sejour_idx", columns={"id_partenaire"}), @ORM\Index(name="fk_ecole_sejour_idx", columns={"id_etablisment"})})
 * @ORM\Entity(repositoryClass="App\Repository\SejourRepository")
 */
class Sejour
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
     * @var string|null
     *
     * @ORM\Column(name="code_sejour", type="string", length=45, nullable=true)
     */
    private $codeSejour;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_creation_code", type="date", nullable=true)
     */
    private $dateCreationCode;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_fin_code", type="date", nullable=true)
     */
    private $dateFinCode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="them_sejour", type="string", length=45, nullable=true)
     */
    private $themSejour;

    /**
     * @var string|null
     *
     * @ORM\Column(name="adresse_sejour", type="string", length=45, nullable=true)
     */
    private $adresseSejour;

    /**
     * @return null|string
     */
    public function getPays(): ?string
    {
        return $this->pays;
    }

    /**
     * @param null|string $pays
     */
    public function setPays(?string $pays): void
    {
        $this->pays = $pays;
    }

    /**
     * @return null|string
     */
    public function getVille(): ?string
    {
        return $this->ville;
    }

    /**
     * @param null|string $ville
     */
    public function setVille(?string $ville): void
    {
        $this->ville = $ville;
    }

    /**
     * @var string|null
     *
     * @ORM\Column(name="pays", type="string", length=255, nullable=true)
     */
    private $pays;
    /**
     * @var string|null
     *
     * @ORM\Column(name="ville", type="string", length=255, nullable=true)
     */
    private $ville;

    /**
     * @var int|null
     *
     * @ORM\Column(name="code_postal", type="integer", nullable=true)
     */
    private $codePostal;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_debut_sejour", type="date", nullable=true)
     */
    private $dateDebutSejour;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_fin_sejour", type="date", nullable=true)
     */
    private $dateFinSejour;

    /**
     * @var int|null
     *
     * @ORM\Column(name="nb_photo_diposer", type="integer", nullable=true)
     */
    private $nbPhotoDiposer;



     /**
     * @var int|null
     *
     * @ORM\Column(name="paym", type="integer", nullable=true)
     */
    private $paym;



    /**
     * @var int|null
     *
     * @ORM\Column(name="albumgratuie", type="integer", nullable=true)
     */
    private $albumgratuie;

    /**
     * @var int|null
     *
     * @ORM\Column(name="nb_message", type="integer", nullable=true)
     */
    private $nbMessage;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="etat_acomp_Album", type="boolean", nullable=true)
     */
    private $etatAcompAlbum;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="etat_adresse_carte", type="boolean", nullable=true)
     */
    private $etatAdresseCarte;

    /**
     * @var \Etablisment
     *
     * @ORM\ManyToOne(targetEntity="Etablisment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_etablisment", referencedColumnName="id")
     * })
     */
    private $idEtablisment;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Id_acommp", referencedColumnName="id")
     * })
     */
    private $idAcommp;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_partenaire", referencedColumnName="id")
     * })
     */
    private $idPartenaire;

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
     * @var int|null
     *
     * @ORM\Column(name="nbenfan", type="integer", nullable=true)
     */
    private $nbenfan;

    /**
     * @var int|null
     *
     * @ORM\Column(name="nbenfantencours", type="integer", nullable=true)
     */
    private $nbenfantencours;
    /**
     * @return int|null
     */
    public function getNbenfan(): ?int
    {
        return $this->nbenfan;
    }

    /**
     * @param int|null $nbenfan
     */
    public function setNbenfan(?int $nbenfan): void
    {
        $this->nbenfan = $nbenfan;
    }

    /**
     * @var \Ref
     *
     * @ORM\ManyToOne(targetEntity="Ref")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="age_group", referencedColumnName="id")
     * })
     */
    private $ageGroup;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Produit", mappedBy="idsjour")
     */
    private $produits;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Commande", mappedBy="idSejour")
     */
    private $commandes;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Cart", mappedBy="idsejour")
     */
    private $cartes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SejourAttachment", mappedBy="idSejour")
     */
    private $attachements;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Jourdescripdate", mappedBy="idsejour")
     */
    private $jourdescripdate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $cd;
    
    /**
     * @var float|null
     *
     * @ORM\Column(name="prixcnxparent", type="float", length=45, nullable=true)
     */
    private $prixcnxparent;

     /**
     * @var float|null
     *
     * @ORM\Column(name="prixcnxpartenaire", type="float", length=45, nullable=true)
     */
    private $prixcnxpartenaire;

    /**
     * @var float|null
     *
     * @ORM\Column(name="reversecnxpart", type="float", length=45, nullable=true)
     */
    private $reversecnxpart;

     /**
     * @var float|null
     *
     * @ORM\Column(name="reverseventepart", type="float", length=45, nullable=true)
     */
    private $reverseventepart;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Referenc;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $cp;



    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Panier", mappedBy="idSejour")
     */
    private $paniers;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ParentSejour", mappedBy="idSejour")
     */
    private $parentSejour;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PromoSejour", mappedBy="sejour")
     */
    private $promoSejours;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Photonsumeriques", mappedBy="idSejour")
     */
    private $photonsumeriques;

    public function __construct()
    {
        $this->attachements = new ArrayCollection();
        $this->cartes = new ArrayCollection();
        $this->produits = new ArrayCollection();
        $this->commandes = new ArrayCollection();
        $this->jourdescripdate = new ArrayCollection();
        $this->paniers = new ArrayCollection();
        $this->parentSejour = new ArrayCollection();
        $this->promoSejours = new ArrayCollection();
        $this->photonsumeriques = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeSejour(): ?string
    {
        return $this->codeSejour;
    }

    public function setCodeSejour(?string $codeSejour): self
    {
        $this->codeSejour = $codeSejour;

        return $this;
    }




 



    public function getDateCreationCode(): ?\DateTimeInterface
    {
        return $this->dateCreationCode;
    }

    public function setDateCreationCode(?\DateTimeInterface $dateCreationCode): self
    {
        $this->dateCreationCode = $dateCreationCode;

        return $this;
    }

    public function getDateFinCode(): ?\DateTimeInterface
    {
        return $this->dateFinCode;
    }

    public function setDateFinCode(?\DateTimeInterface $dateFinCode): self
    {
        $this->dateFinCode = $dateFinCode;

        return $this;
    }

    public function getThemSejour(): ?string
    {
        return $this->themSejour;
    }

    public function setThemSejour(?string $themSejour): self
    {
        $this->themSejour = $themSejour;

        return $this;
    }

    public function getAdresseSejour(): ?string
    {
        return $this->adresseSejour;
    }

    public function setAdresseSejour(?string $adresseSejour): self
    {
        $this->adresseSejour = $adresseSejour;

        return $this;
    }

    public function getCodePostal(): ?int
    {
        return $this->codePostal;
    }

    public function setCodePostal(?int $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getDateDebutSejour(): ?\DateTimeInterface
    {
        return $this->dateDebutSejour;
    }

    public function setDateDebutSejour(?\DateTimeInterface $dateDebutSejour): self
    {
        $this->dateDebutSejour = $dateDebutSejour;

        return $this;
    }

    public function getDateFinSejour(): ?\DateTimeInterface
    {
        return $this->dateFinSejour;
    }

    public function setDateFinSejour(?\DateTimeInterface $dateFinSejour): self
    {
        $this->dateFinSejour = $dateFinSejour;

        return $this;
    }

    public function getNbPhotoDiposer(): ?int
    {
        return $this->nbPhotoDiposer;
    }

    public function setNbPhotoDiposer(?int $nbPhotoDiposer): self
    {
        $this->nbPhotoDiposer = $nbPhotoDiposer;

        return $this;
    }

    public function getNbMessage(): ?int
    {
        return $this->nbMessage;
    }

    public function setNbMessage(?int $nbMessage): self
    {
        $this->nbMessage = $nbMessage;

        return $this;
    }

    public function getEtatAcompAlbum(): ?bool
    {
        return $this->etatAcompAlbum;
    }

    public function setEtatAcompAlbum(?bool $etatAcompAlbum): self
    {
        $this->etatAcompAlbum = $etatAcompAlbum;

        return $this;
    }

    public function getEtatAdresseCarte(): ?bool
    {
        return $this->etatAdresseCarte;
    }

    public function setEtatAdresseCarte(?bool $etatAdresseCarte): self
    {
        $this->etatAdresseCarte = $etatAdresseCarte;

        return $this;
    }

    public function getIdEtablisment(): ?Etablisment
    {
        return $this->idEtablisment;
    }

    public function setIdEtablisment(?Etablisment $idEtablisment): self
    {
        $this->idEtablisment = $idEtablisment;

        return $this;
    }

    public function getIdAcommp(): ?User
    {
        return $this->idAcommp;
    }

    public function setIdAcommp(?User $idAcommp): self
    {
        $this->idAcommp = $idAcommp;

        return $this;
    }

    public function getIdPartenaire(): ?User
    {
        return $this->idPartenaire;
    }

    public function setIdPartenaire(?User $idPartenaire): self
    {
        $this->idPartenaire = $idPartenaire;

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

    public function getAgeGroup(): ?Ref
    {
        return $this->ageGroup;
    }

    public function setAgeGroup(?Ref $ageGroup): self
    {
        $this->ageGroup = $ageGroup;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProduits()
    {
        return $this->produits;
    }

    /**
     * @param mixed $produits
     */
    public function setProduits($produits): void
    {
        $this->produits = $produits;
    }

    public function getNbConnexion(): ?int
    {
        $cpt=0;
       foreach ($this->produits as $pdt){
           if ($pdt->getType()==null){
               $cpt++;
           }
       }
       return $cpt;
    }

    /**
     * @return mixed
     */
    public function getCommandes()
    {
        return $this->commandes;
    }


    public function getMnttotale()
    {
        $cpt=0;
        if($this->reversecnxpart != null){
    
        foreach ($this->commandes as $pdt){
            
                $cpt=$pdt->getMoantantTtcregl()+$cpt ;
            
        }
     }

        return $cpt;

    }
    /**
     * @param mixed $commandes
     */
    public function setCommandes($commandes): void
    {
        $this->commandes = $commandes;
    }

    public function getCommandesMontantTotal()
    {
        $SUM=0;
        foreach ($this->commandes as $cmd){

                $SUM=$SUM+$cmd->getMontantht();

        }
        return $SUM;
    }
    public function getCommandesMontantTotalReversements(){
        $SUM=0;
        foreach ($this->commandes as $cmd){
            foreach($cmd->getCommandesProduits() as $cmdPrd)
            $SUM=$SUM+$cmdPrd->getReversement();

        }
        return $SUM;
    }

    /**
     * @return mixed
     */
    public function getCartes()
    {
        return $this->cartes;
    }

    /**
     * @param mixed $cartes
     */
    public function setCartes($cartes): void
    {
        $this->cartes = $cartes;
    }

    /**
     * @return mixed
     */
    public function getAttachements()
    {
        return $this->attachements;
    }

    /**
     * @param mixed $attachements
     */
    public function setAttachements($attachements): void
    {
        $this->attachements = $attachements;
    }

    public function getNbrAttachementsByType()
    {
        $arrayFinal=['Photo'=>0,'Audio'=>0,"Video"=>0];
        foreach($this->attachements as $attach){
            //Photo
            //6 7 30
            if($attach->getIdAttchment()->getIdref()->getId()==6){
                $arrayFinal['Photo']=$arrayFinal['Photo']+1;
            }
            //message
            elseif($attach->getIdAttchment()->getIdref()->getId()==7){
                $arrayFinal['Audio']=$arrayFinal['Audio']+1;
            }
            //Video
            elseif($attach->getIdAttchment()->getIdref()->getId()==30){
                $arrayFinal['Video']=$arrayFinal['Video']+1;
        }
        }
    }


     /**
     * @return mixed
     */
    public function getJourdescripdate()
    {
        return $this->jourdescripdate;
    }

    /**
     * @param mixed $jourdescripdate
     */
    public function setJourdescripdate($jourdescripdate): void
    {
        $this->jourdescripdate = $jourdescripdate;
    }



    public function getPaym(): ?int
    {
        return $this->paym;
    }

    public function setPaym(?int $paym): self
    {
        $this->paym = $paym;

        return $this;
    }


    
    public function getAlbumgratuie(): ?int
    {
        return $this->albumgratuie;
    }

    public function setAlbumgratuie(?int $albumgratuie): self
    {
        $this->albumgratuie = $albumgratuie;

        return $this;
    }



    public function getCd(): ?int
    {
        return $this->cd;
    }

    public function setCd(?int $cd): self
    {
        $this->cd = $cd;

        return $this;
    }
    
    public function getPrixcnxparent(): ?float
    {
        return $this->prixcnxparent;
    }

    public function setPrixcnxparent(?float $prixcnxparent): self
    {
        $this->prixcnxparent = $prixcnxparent;

        return $this;
    }

    public function getPrixcnxpartenaire(): ?float
    {
        return $this->prixcnxpartenaire;
    }

    public function setPrixcnxpartenaire(?float $prixcnxpartenaire): self
    {
        $this->prixcnxpartenaire = $prixcnxpartenaire;

        return $this;
    }

    public function getReversecnxpart(): ?float
    {
        return $this->reversecnxpart;
    }

    public function setReversecnxpart(?float $reversecnxpart): self
    {
        $this->reversecnxpart = $reversecnxpart;

        return $this;
    }

    public function getReverseventepart(): ?float
    {
        return $this->reverseventepart;
    }

    public function setReverseventepart(?float $reverseventepart): self
    {
        $this->reverseventepart = $reverseventepart;

        return $this;
    }

    public function getReferenc(): ?string
    {
        return $this->Referenc;
    }

    public function setReferenc(?string $Referenc): self
    {
        $this->Referenc = $Referenc;

        return $this;
    }
    
    public function getCp(): ?int
    {
        return $this->cp;
    }

    public function setCp(?int $cp): self
    {
        $this->cp = $cp;

        return $this;
    }

    /**
     * @return Collection|Panier[]
     */
    public function getPaniers(): Collection
    {
        return $this->paniers;
    }

    public function addPanier(Panier $panier): self
    {
        if (!$this->paniers->contains($panier)) {
            $this->paniers[] = $panier;
            $panier->setIdSejour($this);
        }

        return $this;
    }

    public function removePanier(Panier $panier): self
    {
        if ($this->paniers->contains($panier)) {
            $this->paniers->removeElement($panier);
            // set the owning side to null (unless already changed)
            if ($panier->getIdSejour() === $this) {
                $panier->setIdSejour(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getParentSejour()
    {
        return $this->parentSejour;
    }

    /**
     * @param mixed $parentSejour
     */
    public function setParentSejour($parentSejour): void
    {
        $this->parentSejour = $parentSejour;
    }

    public function getCountParentSejour()
    {
        $count=0;

        foreach($this->parentSejour as $cnc){
            #$count=0;
            if(substr($this->codeSejour, 1, 1)=="P")
            {
                if ($cnc->getPayment()==1){
                    $count=$count+1;
                }

            }else{
                $count=$count+1;
            }
        }
        return  $count;
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
    public function hasCommandesProduit(){

        foreach ($this->commandes as $cmd){
            foreach($cmd->getCommandesProduits() as $cmdPrd)
               if($cmdPrd->getIdProduit()->getIdConditionnement()->getDescriptionCommande()!="Connexion"){
                   return true;
               }

        }
        return false;
    }

    /**
     * @return Collection|PromoSejour[]
     */
    public function getPromoSejours(): Collection
    {
        return $this->promoSejours;
    }

    public function addPromoSejour(PromoSejour $promoSejour): self
    {
        if (!$this->promoSejours->contains($promoSejour)) {
            $this->promoSejours[] = $promoSejour;
            $promoSejour->setSejour($this);
        }

        return $this;
    }

    public function removePromoSejour(PromoSejour $promoSejour): self
    {
        if ($this->promoSejours->contains($promoSejour)) {
            $this->promoSejours->removeElement($promoSejour);
            // set the owning side to null (unless already changed)
            if ($promoSejour->getSejour() === $this) {
                $promoSejour->setSejour(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Photonsumeriques[]
     */
    public function getPhotonsumeriques(): Collection
    {
        return $this->photonsumeriques;
    }

    public function addPhotonsumerique(Photonsumeriques $photonsumerique): self
    {
        if (!$this->photonsumeriques->contains($photonsumerique)) {
            $this->photonsumeriques[] = $photonsumerique;
            $photonsumerique->setIdSejour($this);
        }

        return $this;
    }

    public function removePhotonsumerique(Photonsumeriques $photonsumerique): self
    {
        if ($this->photonsumeriques->contains($photonsumerique)) {
            $this->photonsumeriques->removeElement($photonsumerique);
            // set the owning side to null (unless already changed)
            if ($photonsumerique->getIdSejour() === $this) {
                $photonsumerique->setIdSejour(null);
            }
        }

        return $this;
    }


}
