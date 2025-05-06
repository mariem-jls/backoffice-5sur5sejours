<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Position
 *
 * @ORM\Table(name="position", indexes={@ORM\Index(name="fk_Positioncart_idx", columns={"idcart"}), @ORM\Index(name="fk_Position_1_idx", columns={"Iduser"})})
 * @ORM\Entity(repositoryClass="App\Repository\PositionRepository")
 */
class Position
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
     * @ORM\Column(name="nom_position", type="string", length=45, nullable=true)
     */
    private $nomPosition;

    /**
     * @var string|null
     *
     * @ORM\Column(name="rue_position", type="string", length=45, nullable=true)
     */
    private $ruePosition;
    /**
     * @var string|null
     *
     * @ORM\Column(name="num_position", type="string", length=45, nullable=true)
     */
    private $numPosition;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ville_position", type="string", length=45, nullable=true)
     */
    private $villPosition;

    /**
     * @var string|null
     *
     * @ORM\Column(name="lat_position", type="string", length=45, nullable=true)
     */
    private $latPosition;
    /**
     * @var string|null
     *
     * @ORM\Column(name="lng_position", type="string", length=45, nullable=true)
     */
    private $lngPosition;
    /**
     * @var string|null
     *
     * @ORM\Column(name="postal", type="string", length=45, nullable=true)
     */
    private $postalPosition;

    /**
     * @var string|null
     *
     * @ORM\Column(name="pays_position", type="string", length=45, nullable=true)
     */
    private $paysPosition;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_position", type="date", nullable=true)
     */
    private $datePosition;

    /**
     * @var \Cart
     *
     * @ORM\ManyToOne(targetEntity="Cart")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idcart", referencedColumnName="id")
     * })
     */
    private $idcart;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Iduser", referencedColumnName="id")
     * })
     */
    private $iduser;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomPosition(): ?string
    {
        return $this->nomPosition;
    }

    public function setNomPosition(?string $nomPosition): self
    {
        $this->nomPosition = $nomPosition;

        return $this;
    }

    public function getRuePosition(): ?string
    {
        return $this->ruePosition;
    }

    public function setRuePosition(?string $ruePosition): self
    {
        $this->ruePosition = $ruePosition;

        return $this;
    }

    public function getPaysPosition(): ?string
    {
        return $this->paysPosition;
    }

    public function setPaysPosition(?string $paysPosition): self
    {
        $this->paysPosition = $paysPosition;

        return $this;
    }

    public function getDatePosition(): ?\DateTimeInterface
    {
        return $this->datePosition;
    }

    public function setDatePosition(?\DateTimeInterface $datePosition): self
    {
        $this->datePosition = $datePosition;

        return $this;
    }

    public function getIdcart(): ?Cart
    {
        return $this->idcart;
    }

    public function setIdcart(?Cart $idcart): self
    {
        $this->idcart = $idcart;

        return $this;
    }

    public function getIduser(): ?User
    {
        return $this->iduser;
    }

    public function setIduser(?User $iduser): self
    {
        $this->iduser = $iduser;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getVillPosition(): ?string
    {
        return $this->villPosition;
    }

    /**
     * @param null|string $villPosition
     */
    public function setVillPosition(?string $villPosition): void
    {
        $this->villPosition = $villPosition;
    }

    /**
     * @return null|string
     */
    public function getPayPosition(): ?string
    {
        return $this->payPosition;
    }

    /**
     * @param null|string $payPosition
     */
    public function setPayPosition(?string $payPosition): void
    {
        $this->payPosition = $payPosition;
    }

    /**
     * @return null|string
     */
    public function getLatPosition(): ?string
    {
        return $this->latPosition;
    }

    /**
     * @param null|string $latPosition
     */
    public function setLatPosition(?string $latPosition): void
    {
        $this->latPosition = $latPosition;
    }

    /**
     * @return null|string
     */
    public function getLngPosition(): ?string
    {
        return $this->lngPosition;
    }

    /**
     * @param null|string $lngPosition
     */
    public function setLngPosition(?string $lngPosition): void
    {
        $this->lngPosition = $lngPosition;
    }

    /**
     * @return null|string
     */
    public function getPostalPosition(): ?string
    {
        return $this->postalPosition;
    }

    /**
     * @param null|string $postalPosition
     */
    public function setPostalPosition(?string $postalPosition): void
    {
        $this->postalPosition = $postalPosition;
    }

    /**
     * @return null|string
     */
    public function getNumPosition(): ?string
    {
        return $this->numPosition;
    }

    /**
     * @param null|string $numPosition
     */
    public function setNumPosition(?string $numPosition): void
    {
        $this->numPosition = $numPosition;
    }

}
