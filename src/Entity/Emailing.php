<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Emailing
 *
 * @ORM\Table(name="emailing", indexes={@ORM\Index(name="fk_emailing_creator", columns={"id_user_creation"}), @ORM\Index(name="fk_emailing_image2", columns={"id_image2"}), @ORM\Index(name="fk_emailing_typeemail", columns={"typeemail"}), @ORM\Index(name="fk_email_user", columns={"id_destinataire"}), @ORM\Index(name="fk_emailing_image1", columns={"id_image1"}), @ORM\Index(name="fk_emailing_statut", columns={"statut"})})
 * @ORM\Entity(repositoryClass="App\Repository\EmailingRepository")
 */
class Emailing
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
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="date", nullable=false)
     */
    private $dateCreation;

    /**
     * @var \Ref
     *
     * @ORM\ManyToOne(targetEntity="Ref")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="typeemail", referencedColumnName="id")
     * })
     */
    private $typeemail;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_user_creation", referencedColumnName="id")
     * })
     */
    private $idUserCreation;

    /**
     * @var \Attachment
     *
     * @ORM\ManyToOne(targetEntity="Attachment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_image1", referencedColumnName="id")
     * })
     */
    private $idImage1;

    /**
     * @var \Attachment
     *
     * @ORM\ManyToOne(targetEntity="Attachment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_image2", referencedColumnName="id")
     * })
     */
    private $idImage2;

    /**
     * @var \Ref
     *
     * @ORM\ManyToOne(targetEntity="Ref")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="statut", referencedColumnName="id")
     * })
     */
    private $statut;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_destinataire", referencedColumnName="id")
     * })
     */
    private $idDestinataire;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTypeemail(): ?Ref
    {
        return $this->typeemail;
    }

    public function setTypeemail(?Ref $typeemail): self
    {
        $this->typeemail = $typeemail;

        return $this;
    }

    public function getIdUserCreation(): ?User
    {
        return $this->idUserCreation;
    }

    public function setIdUserCreation(?User $idUserCreation): self
    {
        $this->idUserCreation = $idUserCreation;

        return $this;
    }

    public function getIdImage1(): ?Attachment
    {
        return $this->idImage1;
    }

    public function setIdImage1(?Attachment $idImage1): self
    {
        $this->idImage1 = $idImage1;

        return $this;
    }

    public function getIdImage2(): ?Attachment
    {
        return $this->idImage2;
    }

    public function setIdImage2(?Attachment $idImage2): self
    {
        $this->idImage2 = $idImage2;

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

    public function getIdDestinataire(): ?User
    {
        return $this->idDestinataire;
    }

    public function setIdDestinataire(?User $idDestinataire): self
    {
        $this->idDestinataire = $idDestinataire;

        return $this;
    }


}