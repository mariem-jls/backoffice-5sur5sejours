<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cart
 *
 * @ORM\Table(name="cart", indexes={@ORM\Index(name="fk_sejou_idx", columns={"idsejour"})})
 * @ORM\Entity(repositoryClass="App\Repository\CartRepository")
 */
class Cart
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
     * @ORM\Column(name="nbconsomateur", type="integer", nullable=true)
     */
    private $nbconsomateur;

    /**
     * @var int|null
     *
     * @ORM\Column(name="nbpartenaire", type="integer", nullable=true)
     */
    private $nbpartenaire;

    /**
     * @var int|null
     *
     * @ORM\Column(name="nbecole", type="integer", nullable=true)
     */
    private $nbecole;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var \Sejour
     *
     * @ORM\ManyToOne(targetEntity="Sejour")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idsejour", referencedColumnName="id")
     * })
     */
    private $idsejour;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Position", mappedBy="idcart")
     */
    private $positions;


    public function __construct()
    {
        $this->positions = new ArrayCollection();}
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbconsomateur(): ?int
    {
        return $this->nbconsomateur;
    }

    public function setNbconsomateur(?int $nbconsomateur): self
    {
        $this->nbconsomateur = $nbconsomateur;

        return $this;
    }

    public function getNbpartenaire(): ?int
    {
        return $this->nbpartenaire;
    }

    public function setNbpartenaire(?int $nbpartenaire): self
    {
        $this->nbpartenaire = $nbpartenaire;

        return $this;
    }

    public function getNbecole(): ?int
    {
        return $this->nbecole;
    }

    public function setNbecole(?int $nbecole): self
    {
        $this->nbecole = $nbecole;

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

    public function getIdsejour(): ?Sejour
    {
        return $this->idsejour;
    }

    public function setIdsejour(?Sejour $idsejour): self
    {
        $this->idsejour = $idsejour;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPositions()
    {
        return $this->positions;
    }

    /**
     * @param mixed $positions
     */
    public function setPositions($positions): void
    {
        $this->positions = $positions;
    }


}
