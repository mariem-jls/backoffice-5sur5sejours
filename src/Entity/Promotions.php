<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PromotionsRepository")
 */
class Promotions
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateCreation;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateDebut;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateFin;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbreMaxGeneral;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbreMaxParUser;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ref", inversedBy="promotions")
     */
    private $statut;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LogPromotions", mappedBy="idPromotion")
     */
    private $logPromotions;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $pourcentage;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Commande", mappedBy="idPromotion")
     */
    private $commandes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PromoSejour", mappedBy="promotion")
     */
    private $promoSejours;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PromoParents", mappedBy="promotion")
     */
    private $promoParents;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $strategie;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbreApplicable;

    public function __construct()
    {
        $this->logPromotions = new ArrayCollection();
        $this->commandes = new ArrayCollection();
        $this->promoSejours = new ArrayCollection();
        $this->promoParents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getNbreMaxGeneral(): ?int
    {
        return $this->nbreMaxGeneral;
    }

    public function setNbreMaxGeneral(?int $nbreMaxGeneral): self
    {
        $this->nbreMaxGeneral = $nbreMaxGeneral;

        return $this;
    }

    public function getNbreMaxParUser(): ?int
    {
        return $this->nbreMaxParUser;
    }

    public function setNbreMaxParUser(?int $nbreMaxParUser): self
    {
        $this->nbreMaxParUser = $nbreMaxParUser;

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

    public function getEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(?bool $etat): self
    {
        $this->etat = $etat;

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
            $logPromotion->setIdPromotion($this);
        }

        return $this;
    }

    public function removeLogPromotion(LogPromotions $logPromotion): self
    {
        if ($this->logPromotions->contains($logPromotion)) {
            $this->logPromotions->removeElement($logPromotion);
            // set the owning side to null (unless already changed)
            if ($logPromotion->getIdPromotion() === $this) {
                $logPromotion->setIdPromotion(null);
            }
        }

        return $this;
    }

    public function getPourcentage(): ?int
    {
        return $this->pourcentage;
    }

    public function setPourcentage(?int $pourcentage): self
    {
        $this->pourcentage = $pourcentage;

        return $this;
    }

    /**
     * @return Collection|Commande[]
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): self
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes[] = $commande;
            $commande->setIdPromotion($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commandes->contains($commande)) {
            $this->commandes->removeElement($commande);
            // set the owning side to null (unless already changed)
            if ($commande->getIdPromotion() === $this) {
                $commande->setIdPromotion(null);
            }
        }

        return $this;
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
            $promoSejour->setPromotion($this);
        }

        return $this;
    }

    public function removePromoSejour(PromoSejour $promoSejour): self
    {
        if ($this->promoSejours->contains($promoSejour)) {
            $this->promoSejours->removeElement($promoSejour);
            // set the owning side to null (unless already changed)
            if ($promoSejour->getPromotion() === $this) {
                $promoSejour->setPromotion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PromoParents[]
     */
    public function getPromoParents(): Collection
    {
        return $this->promoParents;
    }

    public function addPromoParent(PromoParents $promoParent): self
    {
        if (!$this->promoParents->contains($promoParent)) {
            $this->promoParents[] = $promoParent;
            $promoParent->setPromotion($this);
        }

        return $this;
    }

    public function removePromoParent(PromoParents $promoParent): self
    {
        if ($this->promoParents->contains($promoParent)) {
            $this->promoParents->removeElement($promoParent);
            // set the owning side to null (unless already changed)
            if ($promoParent->getPromotion() === $this) {
                $promoParent->setPromotion(null);
            }
        }

        return $this;
    }

    public function getStrategie(): ?string
    {
        return $this->strategie;
    }

    public function setStrategie(?string $strategie): self
    {
        $this->strategie = $strategie;

        return $this;
    }

    public function getNbreApplicable(): ?int
    {
        return $this->nbreApplicable;
    }

    public function setNbreApplicable(?int $nbreApplicable): self
    {
        $this->nbreApplicable = $nbreApplicable;

        return $this;
    }
}
