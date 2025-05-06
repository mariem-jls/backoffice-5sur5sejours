<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * Typeproduit
 *
 * @ORM\Table(name="typeproduit", indexes={@ORM\Index(name="fk_admin_idx", columns={"iduser"})})
 * @ORM\Entity(repositoryClass="App\Repository\TypeproduitRepository")
 */
class Typeproduit
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
     * @ORM\Column(name="labeleType", type="string", length=255, nullable=true)
     */
    private $labeletype;

    /**
     *
     * @ORM\Column(name="caracteristiques", type="text",  nullable=true)
     */
    private $caracteristiques;

    /**
     *
     * @ORM\Column(name="delais", type="text",  nullable=true)
     */
    private $delais;

    /**
     *
     * @ORM\Column(name="tarifs", type="text",  nullable=true)
     */
    private $tarifs;
    /**
     * @var string|null
     *
     * @ORM\Column(name="traif", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $traif;
    /**
     * @var string|null
     *
     * @ORM\Column(name="fraisDePort", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $fraisDePort;


    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="iduser", referencedColumnName="id")
     * })
     */
    private $iduser;

     /**
     * @ORM\OneToMany(targetEntity="App\Entity\TypeProduitPhoto", mappedBy="idTypep", fetch="EAGER")
     * @Groups({"typeproduit:read"})
     */
    private $attachements;



    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Reversement", mappedBy="idTypeproduit")
     */
    private $reversment;

    public function __construct()
    {

        $this->attachements = new ArrayCollection();
        $this->mesproduits = new ArrayCollection();
        $this->reversment = new ArrayCollection();
        $this->typeProduitConditionnements = new ArrayCollection();

    }



    /**
     * @param ArrayCollection $reversment
     */
    public function setReversment(ArrayCollection $reversment): void
    {
        $this->reversment = $reversment;
    }


    /**
     * @return ArrayCollection
     */
    public function getReversment()
    {
        return $this->reversment;
    }

    /**
     * @return ArrayCollection
     */
    public function getAttachements()
    {
        return $this->attachements;
    }

    /**
     * @param ArrayCollection $attachements
     */
    public function setAttachements(ArrayCollection $attachements): void
    {
        $this->attachements = $attachements;
    }

    // public function addAttachment(Attachment $attachment): self
    // {
    //     if (!$this->attachements->contains($attachment)) {
    //         $this->attachements[] = $attachment;
    //         $attachment->setTypeproduit($this);
    //     }

    //     return $this;
    // }

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Produit", mappedBy="type")
     */
    private $mesproduits;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(name ="plusDescription", type="text", nullable=true)
     */
    private $plusDescription;

    /**
     * @ORM\Column(name ="MontantHt",type="integer", nullable=true)
     */
    private $MontantHt;

    /**
     * @ORM\Column(name ="MontantTTC",type="integer", nullable=true)
     */
    private $MontantTTC;

    /**
     * @ORM\Column(name ="PoindsEnGrContenant",type="integer", nullable=true)
     */
    private $PoindsEnGrContenant;

    /**
     * @ORM\Column(name="PoidsEnGrProduit",type="integer", nullable=true)
     */
    private $PoidsEnGrProduit;

    /**
     * @ORM\Column(name="PoidsTotalEnGrHorsPochette",type="integer", nullable=true)
     */
    private $PoidsTotalEnGrHorsPochette;

    /**
     * @ORM\Column(name="PochetteEnvoi",type="integer", nullable=true)
     */
    private $PochetteEnvoi;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TypeProduitConditionnement", mappedBy="idTypeProduit", cascade={"persist"})
     */
    private $typeProduitConditionnements;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $statut;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $reversement;

    

    /**
     * @return mixed
     */
    public function mesproduits()
    {
        
        return $this->mesproduits;
    }


    /**
     * @param mixed $mesproduits
     */
    public function setMesproduits($mesproduits): void
    {
        $this->mesproduits = $mesproduits;
    }

    /**
     * @return mixed
     */
    public function mesproduitsBySejour($id)
    {
        $arrayres=array();
        foreach ($this->mesproduits as $prdt){
            if ($prdt->getIdsjour()!=null)
            if($prdt->getIdsjour()->getId()==$id)
             array_push($arrayres,$prdt);
        }
        return $arrayres;
    }




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabeletype(): ?string
    {
        return $this->labeletype;
    }

    public function setLabeletype(?string $labeletype): self
    {
        $this->labeletype = $labeletype;

        return $this;
    }

    public function getCaracteristiques(): ?string
    {
        return $this->caracteristiques;
    }

    public function setCaracteristiques(?string $caracteristiques): self
    {
        $this->caracteristiques = $caracteristiques;

        return $this;
    }

    public function getDelais(): ?string
    {
        return $this->delais;
    }

    public function setDelais(?string $delais): self
    {
        $this->delais = $delais;

        return $this;
    }

    public function getTarifs(): ?string
    {
        return $this->tarifs;
    }

    public function setTarifs(?string $tarifs): self
    {
        $this->tarifs = $tarifs;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getIduser(): ?User
    {
        return $this->iduser;
    }

    /**
     * @return string|null
     */
    public function getTraif(): ?int
    {
        return $this->traif;
    }

    /**
     * @param string|null $traif
     */
    public function setTraif(?string $traif): void
    {
        $this->traif = $traif;
    }

    /**
     * @return string|null
     */
    public function getFraisDePort(): ?string
    {
        return $this->fraisDePort;
    }

    /**
     * @param string|null $fraisDePort
     */
    public function setFraisDePort(?string $fraisDePort): void
    {
        $this->fraisDePort = $fraisDePort;
    }

    public function setIduser(?User $iduser): self
    {
        $this->iduser = $iduser;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPlusDescription(): ?string
    {
        return $this->plusDescription;
    }

    public function setPlusDescription(?string $plusDescription): self
    {
        $this->plusDescription = $plusDescription;

        return $this;
    }

    public function getMontantHt(): ?int
    {
        return $this->MontantHt;
    }

    public function setMontantHt(?int $MontantHt): self
    {
        $this->MontantHt = $MontantHt;

        return $this;
    }

    public function getMontantTTC(): ?int
    {
        return $this->MontantTTC;
    }

    public function setMontantTTC(?int $MontantTTC): self
    {
        $this->MontantTTC = $MontantTTC;

        return $this;
    }

    public function getPoindsEnGrContenant(): ?int
    {
        return $this->PoindsEnGrContenant;
    }

    public function setPoindsEnGrContenant(?int $PoindsEnGrContenant): self
    {
        $this->PoindsEnGrContenant = $PoindsEnGrContenant;

        return $this;
    }

    public function getPoidsEnGrProduit(): ?int
    {
        return $this->PoidsEnGrProduit;
    }

    public function setPoidsEnGrProduit(?int $PoidsEnGrProduit): self
    {
        $this->PoidsEnGrProduit = $PoidsEnGrProduit;

        return $this;
    }

    public function getPoidsTotalEnGrHorsPochette(): ?int
    {
        return $this->PoidsTotalEnGrHorsPochette;
    }

    public function setPoidsTotalEnGrHorsPochette(?int $PoidsTotalEnGrHorsPochette): self
    {
        $this->PoidsTotalEnGrHorsPochette = $PoidsTotalEnGrHorsPochette;

        return $this;
    }

    public function getPochetteEnvoi(): ?int
    {
        return $this->PochetteEnvoi;
    }

    public function setPochetteEnvoi(?int $PochetteEnvoi): self
    {
        $this->PochetteEnvoi = $PochetteEnvoi;

        return $this;
    }

    /**
     * @return Collection|TypeProduitConditionnement[]
     */
    public function getTypeProduitConditionnements(): Collection
    {
        return $this->typeProduitConditionnements;
    }

    public function addTypeProduitConditionnement(TypeProduitConditionnement $typeProduitConditionnement): self
    {
        if (!$this->typeProduitConditionnements->contains($typeProduitConditionnement)) {
            $this->typeProduitConditionnements[] = $typeProduitConditionnement;
            $typeProduitConditionnement->setIdTypeProduit($this);
        }

        return $this;
    }

    public function removeTypeProduitConditionnement(TypeProduitConditionnement $typeProduitConditionnement): self
    {
        if ($this->typeProduitConditionnements->contains($typeProduitConditionnement)) {
            $this->typeProduitConditionnements->removeElement($typeProduitConditionnement);
            // set the owning side to null (unless already changed)
            if ($typeProduitConditionnement->getIdTypeProduit() === $this) {
                $typeProduitConditionnement->setIdTypeProduit(null);
            }
        }

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getReversement(): ?float
    {
        return $this->reversement;
    }

    public function setReversement(?float $reversement): self
    {
        $this->reversement = $reversement;

        return $this;
    }

   


}
