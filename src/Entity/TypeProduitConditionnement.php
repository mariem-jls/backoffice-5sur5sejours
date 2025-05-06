<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="typeproduitconditionnement")
 * @ORM\Entity(repositoryClass="App\Repository\TypeProduitConditionnementRepository")
 */
class TypeProduitConditionnement
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="montantHT", type="float", nullable=true)
     */
    private $montantHT;

    /**
     * @ORM\Column(name="montantTTC",type="float", nullable=true)
     */
    private $montantTTC;

    /**
     * @ORM\Column(name="TVA",type="float", nullable=true)
     */
    private $TVA;

    /**
     * @ORM\Column(name="poidsContenant",type="integer", nullable=true)
     */
    private $poidsContenant;

    /**
     * @ORM\Column(name="poidsProduit" ,type="integer", nullable=true)
     */
    private $poidsProduit;

    /**
     * @ORM\Column(name="poidsTotal" ,type="integer", nullable=true)
     */
    private $poidsTotal;

    /**
     * @ORM\Column(name="pochetteEnvoi",type="integer", nullable=true)
     */
    private $pochetteEnvoi;

    /**
     * @ORM\Column(name="descriptionCommande",type="string", length=255, nullable=true)
     */
    private $descriptionCommande;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Typeproduit", inversedBy="typeProduitConditionnements")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="idTypeProduit_id", referencedColumnName="id")
     * })
     */
    private $idTypeProduit;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TypeProduitPhoto", mappedBy="idProduitConditionnement")
     */
    private $typeProduitPhotos;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Produit", mappedBy="idConditionnement")
     */
    private $produits;

    /**
     * @ORM\Column(name="sousTitre",type="string", length=255, nullable=true)
     */
    private $sousTitre;
    
     /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $orderprod;

    public function __construct()
    {
        $this->typeProduitPhotos = new ArrayCollection();
        $this->produits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontantHT(): ?float
    {
        return $this->montantHT;
    }

    public function setMontantHT(?float $montantHT): self
    {
        $this->montantHT = $montantHT;

        return $this;
    }

    public function getMontantTTC(): ?float
    {
        return $this->montantTTC;
    }

    public function setMontantTTC(?float $montantTTC): self
    {
        $this->montantTTC = $montantTTC;

        return $this;
    }

    public function getTVA(): ?float
    {
        return $this->TVA;
    }

    public function setTVA(float $TVA): self
    {
        $this->TVA = $TVA;

        return $this;
    }

    public function getPoidsContenant(): ?int
    {
        return $this->poidsContenant;
    }

    public function setPoidsContenant(?int $poidsContenant): self
    {
        $this->poidsContenant = $poidsContenant;

        return $this;
    }

    public function getPoidsProduit(): ?int
    {
        return $this->poidsProduit;
    }

    public function setPoidsProduit(?int $poidsProduit): self
    {
        $this->poidsProduit = $poidsProduit;

        return $this;
    }

    public function getPoidsTotal(): ?int
    {
        return $this->poidsTotal;
    }

    public function setPoidsTotal(?int $poidsTotal): self
    {
        $this->poidsTotal = $poidsTotal;

        return $this;
    }

    public function getPochetteEnvoi(): ?int
    {
        return $this->pochetteEnvoi;
    }

    public function setPochetteEnvoi(?int $pochetteEnvoi): self
    {
        $this->pochetteEnvoi = $pochetteEnvoi;

        return $this;
    }

    public function getDescriptionCommande(): ?string
    {
        return $this->descriptionCommande;
    }

    public function setDescriptionCommande(?string $descriptionCommande): self
    {
        $this->descriptionCommande = $descriptionCommande;

        return $this;
    }

    public function getIdTypeProduit(): ?Typeproduit
    {
        return $this->idTypeProduit;
    }

    public function setIdTypeProduit(?Typeproduit $idTypeProduit): self
    {
        $this->idTypeProduit = $idTypeProduit;

        return $this;
    }

    /**
     * @return Collection|TypeProduitPhoto[]
     */
    public function getTypeProduitPhotos(): Collection
    {
        return $this->typeProduitPhotos;
    }

    public function addTypeProduitPhoto(TypeProduitPhoto $typeProduitPhoto): self
    {
        if (!$this->typeProduitPhotos->contains($typeProduitPhoto)) {
            $this->typeProduitPhotos[] = $typeProduitPhoto;
            $typeProduitPhoto->setIdProduitConditionnement($this);
        }

        return $this;
    }

    public function removeTypeProduitPhoto(TypeProduitPhoto $typeProduitPhoto): self
    {
        if ($this->typeProduitPhotos->contains($typeProduitPhoto)) {
            $this->typeProduitPhotos->removeElement($typeProduitPhoto);
            // set the owning side to null (unless already changed)
            if ($typeProduitPhoto->getIdProduitConditionnement() === $this) {
                $typeProduitPhoto->setIdProduitConditionnement(null);
            }
        }

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|Produit[]
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): self
    {
        if (!$this->produits->contains($produit)) {
            $this->produits[] = $produit;
            $produit->setIdConditionnement($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): self
    {
        if ($this->produits->contains($produit)) {
            $this->produits->removeElement($produit);
            // set the owning side to null (unless already changed)
            if ($produit->getIdConditionnement() === $this) {
                $produit->setIdConditionnement(null);
            }
        }

        return $this;
    }
 
    /**
     * @return mixed
     */
    public function mesconditionnementsBySejour($id)
    {
        $arrayres=array();
        foreach ($this->produits as $prdt){
            if ($prdt->getIdsjour()!=null)
            if($prdt->getIdsjour()->getId()==$id)
             array_push($arrayres,$prdt);
        }
        return $arrayres;
    }

    public function getSousTitre(): ?string
    {
        return $this->sousTitre;
    }

    public function setSousTitre(?string $sousTitre): self
    {
        $this->sousTitre = $sousTitre;

        return $this;
    }

    public function getOrderprod(): ?int
    {
        return $this->orderprod;
    }

    public function setOrderprod(?int $orderprod): self
    {
        $this->orderprod = $orderprod;

        return $this;
    }
}
