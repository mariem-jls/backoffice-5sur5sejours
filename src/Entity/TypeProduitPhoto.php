<?php

namespace App\Entity;

use App\Entity\Attachment;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * TypeProduitPhoto
 *
 * @ORM\Table(name="type_produit_photo", indexes={@ORM\Index(name="fk_typep_at_idx", columns={"id_attachement", "id_typep"}), @ORM\Index(name="fk_at_typpr", columns={"id_typep"}), @ORM\Index(name="IDX_54C654B9880ED496", columns={"id_attachement"})})
 * @ORM\Entity
 */
class TypeProduitPhoto
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Attachment", fetch="EAGER")
     * @ORM\JoinColumn(name="id_attachement", referencedColumnName="id")
     * @Groups({"typeproduit:read"})
     */
    private $idAttachement;

    /**
     * @var \Typeproduit
     *
     * @ORM\ManyToOne(targetEntity="Typeproduit")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_typep", referencedColumnName="id")
     * })
     */
    private $idTypep;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TypeProduitConditionnement", inversedBy="typeProduitPhotos")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="idProduitConditionnement_id", referencedColumnName="id")
     * })
     */
    private $idProduitConditionnement;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $statut;

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

    public function getIdAttachement(): ?Attachment
    {
        return $this->idAttachement;
    }

    public function setIdAttachement(?Attachment $idAttachement): self
    {
        $this->idAttachement = $idAttachement;

        return $this;
    }

    public function getIdTypep(): ?Typeproduit
    {
        return $this->idTypep;
    }

    public function setIdTypep(?Typeproduit $idTypep): self
    {
        $this->idTypep = $idTypep;

        return $this;
    }

    public function getIdProduitConditionnement(): ?TypeProduitConditionnement
    {
        return $this->idProduitConditionnement;
    }

    public function setIdProduitConditionnement(?TypeProduitConditionnement $idProduitConditionnement): self
    {
        $this->idProduitConditionnement = $idProduitConditionnement;

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



}
