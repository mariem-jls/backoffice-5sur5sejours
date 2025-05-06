<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ParentSejour
 *
 * @ORM\Table(name="parent_sejour", indexes={@ORM\Index(name="fk_sejour_user_idx", columns={"id_parent"}), @ORM\Index(name="Id_Sjour_idx", columns={"Id_sejour"})})
 * @ORM\Entity(repositoryClass="App\Repository\ParentSejourRepository")
 */
class ParentSejour
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
     * @ORM\Column(name="date_creation", type="date", nullable=true)
     */
    private $dateCreation;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_modification", type="date", nullable=true)
     */
    private $dateModification;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_parent", referencedColumnName="id")
     * })
     */
    private $idParent;

    /**
     * @var \Sejour
     *
     * @ORM\ManyToOne(targetEntity="Sejour")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Id_sejour", referencedColumnName="id")
     * })
     */
    private $idSejour;

    /**
     * @var int|null
     *
     * @ORM\Column(name="payment", type="integer", nullable=true)
     */
    private $payment;

    /**
     * @var int|null
     *
     * @ORM\Column(name="smsnotif", type="integer", nullable=true)
     */
    private $smsnotif;

    /**
     * @var int|null
     *
     * @ORM\Column(name="mailnotif", type="integer", nullable=true)
     */
    private $mailnotif;
    /**
     * @var int|null
     *
     * @ORM\Column(name="traiter", type="integer", nullable=true)
     */
    private $traiter;

    /**
     *
     * @ORM\Column(name="rev", type="integer", nullable=true)
     */
    private $rev;

    /**
     *
     * @ORM\Column(name="fact", type="integer", nullable=true)
     */
    private $fact;


    /**
     * @var int|null
     *
     * @ORM\Column(name="flagPrix", type="integer", nullable=true)
     */
    private $flagPrix;

    /**
     * @return int|null
     */
    public function getFlagPrix(): ?int
    {
        return $this->flagPrix;
    }

    /**
     * @param int|null $flagPrix
     */
    public function setFlagPrix(?int $flagPrix): void
    {
        $this->flagPrix = $flagPrix;
    }


    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateModification(): ?\DateTimeInterface
    {
        return $this->dateModification;
    }

    public function setDateModification(?\DateTimeInterface $dateModification): self
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    public function getIdParent(): ?User
    {
        return $this->idParent;
    }

    public function setIdParent(?User $idParent): self
    {
        $this->idParent = $idParent;

        return $this;
    }

    public function getIdSejour(): ?Sejour
    {
        return $this->idSejour;
    }

    public function setIdSejour(?Sejour $idSejour): self
    {
        $this->idSejour = $idSejour;

        return $this;
    }
    
    public function getPayment(): ?int
    {
        return $this->payment;
    }

    public function setPayment(?int $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    public function getSmsnotif(): ?int
    {
        return $this->smsnotif;
    }

    public function setSmsnotif(?int $smsnotif): self
    {
        $this->smsnotif = $smsnotif;

        return $this;
    }



    public function getMailnotif(): ?int
    {
        return $this->mailnotif;
    }

    public function setMailnotif(?int $mailnotif): self
    {
        $this->mailnotif = $mailnotif;

        return $this;
    }

    public function getRev(): ?int
    {
        return $this->rev;
    }

    public function setRev(?int $rev): self
    {
        $this->rev = $rev;

        return $this;
    }


    public function getFact(): ?int
    {
        return $this->fact;
    }

    public function setFact(?int $fact): self
    {
        $this->fact = $fact;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTraiter(): ?int
    {
        return $this->traiter;
    }

    /**
     * @param int|null $traiter
     */
    public function setTraiter(?int $traiter): void
    {
        $this->traiter = $traiter;
    }

   
}
