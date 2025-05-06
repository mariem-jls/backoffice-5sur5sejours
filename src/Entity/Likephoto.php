<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Likephoto
 *
 * @ORM\Table(name="likephoto", indexes={@ORM\Index(name="fk_likeprodiut_idx", columns={"id_produit"}), @ORM\Index(name="fk_userlike_idx", columns={"id_user"}), @ORM\Index(name="fk_likesejourAttach_idx", columns={"id_sejour_attchment"})})
 * @ORM\Entity(repositoryClass="App\Repository\LikephotoRepository")
 */
class Likephoto
{
    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

     /**
     * @var \SejourAttachment
     *
     * @ORM\ManyToOne(targetEntity="SejourAttachment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_sejour_attchment", referencedColumnName="id")
     * })
     */
    private $idSejourAttchment;

    /**
     * @var string|null
     *
     * @ORM\Column(name="etat", type="string", length=45, nullable=true)
     */
    private $etat;

     /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var \Produit
     *
     * @ORM\ManyToOne(targetEntity="Produit")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_produit", referencedColumnName="id")
     * })
     */
    private $idProduit;

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
     * @var string|null
     *
     * @ORM\Column(name="id_sejour", type="integer", length=11, nullable=true)
     */
    private $idSejour;

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getIdSejourAttchment(): ?SejourAttachment
    {
        return $this->idSejourAttchment;
    }

    public function setIdSejourAttchment(?SejourAttachment $idSejourAttchment): self
    {
        $this->idSejourAttchment = $idSejourAttchment;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getIdProduit(): ?Produit
    {
        return $this->idProduit;
    }

    public function setIdProduit(?Produit $idProduit): self
    {
        $this->idProduit = $idProduit;

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

    /**
     * Get the value of idSejour
     *
     * @return  string|null
     */ 
    public function getIdSejour()
    {
        return $this->idSejour;
    }

    /**
     * Set the value of idSejour
     *
     * @param  string|null  $idSejour
     *
     * @return  self
     */ 
    public function setIdSejour($idSejour)
    {
        $this->idSejour = $idSejour;

        return $this;
    }
}
