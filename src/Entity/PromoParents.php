<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PromoParentsRepository")
 */
class PromoParents
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Promotions", inversedBy="promoParents")
     */
    private $promotion;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="promoParents")
     */
    private $parent;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateCreation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPromotion(): ?Promotions
    {
        return $this->promotion;
    }

    public function setPromotion(?Promotions $promotion): self
    {
        $this->promotion = $promotion;

        return $this;
    }

    public function getParent(): ?User
    {
        return $this->parent;
    }

    public function setParent(?User $parent): self
    {
        $this->parent = $parent;

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
}
