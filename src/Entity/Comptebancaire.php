<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *  comptebancaire
 *
 * @ORM\Table(name="comptebancaire")
 * @ORM\Entity(repositoryClass="App\Repository\ComptebancaireRepository")
 */
class Comptebancaire
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $codebnaque;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $codeguichet;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $numcompt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $clerib;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $iban;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $codebic;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $domicilation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodebnaque(): ?string
    {
        return $this->codebnaque;
    }

    public function setCodebnaque(?string $codebnaque): self
    {
        $this->codebnaque = $codebnaque;

        return $this;
    }

    public function getCodeguichet(): ?string
    {
        return $this->codeguichet;
    }

    public function setCodeguichet(?string $codeguichet): self
    {
        $this->codeguichet = $codeguichet;

        return $this;
    }

    public function getNumcompt(): ?string
    {
        return $this->numcompt;
    }

    public function setNumcompt(?string $numcompt): self
    {
        $this->numcompt = $numcompt;

        return $this;
    }

    public function getClerib(): ?string
    {
        return $this->clerib;
    }

    public function setClerib(?string $clerib): self
    {
        $this->clerib = $clerib;

        return $this;
    }

    public function getIban(): ?string
    {
        return $this->iban;
    }

    public function setIban(?string $iban): self
    {
        $this->iban = $iban;

        return $this;
    }

    public function getCodebic(): ?string
    {
        return $this->codebic;
    }

    public function setCodebic(?string $codebic): self
    {
        $this->codebic = $codebic;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDomicilation(): ?string
    {
        return $this->domicilation;
    }

    public function setDomicilation(?string $domicilation): self
    {
        $this->domicilation = $domicilation;

        return $this;
    }
    
}

