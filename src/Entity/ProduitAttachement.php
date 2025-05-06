<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProduitAttachement
 *
 * @ORM\Table(name="produit_attachement", indexes={@ORM\Index(name="fk_attachement_pr_idx", columns={"id_attachement"}), @ORM\Index(name="fk_produit_at_idx", columns={"id_produit"})})
 * @ORM\Entity
 */
class ProduitAttachement
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
     * @var \DateTime|null
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

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
     * @var \Attachment
     *
     * @ORM\ManyToOne(targetEntity="Attachment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_attachement", referencedColumnName="id")
     * })
     */
    private $idAttachement;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIdProduit(): ?Produit
    {
        return $this->idProduit;
    }

    public function setIdProduit(?Produit $idProduit): self
    {
        $this->idProduit = $idProduit;

        return $this;
    }

    public function getIdAttachement(): ?Attachment
    {
        return $this->idAttachement;
    }

    public function setIdAttachement(?Attachment $idAttachement): self
    {
        $this->idAttachement = $idAttachement;

        return $this;
    }


}
