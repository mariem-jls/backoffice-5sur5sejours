<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Ref
 *
 * @ORM\Table(name="ref", indexes={@ORM\Index(name="fk_reftype_idx", columns={"typeref"})})
 * @ORM\Entity
 */
class Ref
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
     * @ORM\Column(name="libiller", type="string", length=45, nullable=true)
     */
    private $libiller;

    /**
     * @var string|null
     *
     * @ORM\Column(name="code", type="string", length=45, nullable=true)
     */
    private $code;

    /**
     * @var \Typeref
     *
     * @ORM\ManyToOne(targetEntity="Typeref")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="typeref", referencedColumnName="id")
     * })
     */
    private $typeref;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Panier", mappedBy="statut")
     */
    private $paniers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Promotions", mappedBy="statut")
     */
    private $promotions;

    public function __construct()
    {
        $this->paniers = new ArrayCollection();
        $this->promotions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibiller(): ?string
    {
        return $this->libiller;
    }

    public function setLibiller(?string $libiller): self
    {
        $this->libiller = $libiller;

        return $this;
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

    public function getTyperef(): ?Typeref
    {
        return $this->typeref;
    }

    public function setTyperef(?Typeref $typeref): self
    {
        $this->typeref = $typeref;

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
            $panier->setStatut($this);
        }

        return $this;
    }

    public function removePanier(Panier $panier): self
    {
        if ($this->paniers->contains($panier)) {
            $this->paniers->removeElement($panier);
            // set the owning side to null (unless already changed)
            if ($panier->getStatut() === $this) {
                $panier->setStatut(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Promotions[]
     */
    public function getPromotions(): Collection
    {
        return $this->promotions;
    }

    public function addPromotion(Promotions $promotion): self
    {
        if (!$this->promotions->contains($promotion)) {
            $this->promotions[] = $promotion;
            $promotion->setStatut($this);
        }

        return $this;
    }

    public function removePromotion(Promotions $promotion): self
    {
        if ($this->promotions->contains($promotion)) {
            $this->promotions->removeElement($promotion);
            // set the owning side to null (unless already changed)
            if ($promotion->getStatut() === $this) {
                $promotion->setStatut(null);
            }
        }

        return $this;
    }


}
