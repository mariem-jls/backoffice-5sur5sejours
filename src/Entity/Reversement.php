<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reversement
 *
 * @ORM\Table(name="reversement", indexes={@ORM\Index(name="fk_reversement_typeproduit", columns={"id_typeproduit"}), @ORM\Index(name="fk_reversement_partenaire", columns={"id_partenaire"})})
 * @ORM\Entity(repositoryClass="App\Repository\ReversementRepository")
 */
class Reversement
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
     * @var int
     *
     * @ORM\Column(name="reversement", type="integer", nullable=false)
     */
    private $reversement;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="date", nullable=false)
     */
    private $dateCreation;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_partenaire", referencedColumnName="id",nullable=true)
     * })
     */
    private $idPartenaire;

    /**
     * @var \Typeproduit
     *
     * @ORM\ManyToOne(targetEntity="Typeproduit")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_typeproduit", referencedColumnName="id" ,nullable=true)
     * })
     */
    private $idTypeproduit;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReversement(): ?int
    {
        return $this->reversement;
    }

    public function setReversement(int $reversement): self
    {
        $this->reversement = $reversement;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

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

    public function getIdTypeproduit(): ?Typeproduit
    {
        return $this->idTypeproduit;
    }

    public function setIdTypeproduit(?Typeproduit $idTypeproduit): self
    {
        $this->idTypeproduit = $idTypeproduit;

        return $this;
    }


}
