<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="emails_sejours_en_masse")
 * @ORM\Entity(repositoryClass="App\Repository\EmailsSejoursEnMasseRepository")
 */
class EmailsSejoursEnMasse
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="id_accompagnateur",type="integer", nullable=true)
     */
    private $idAccompagnateur;

    /**
     * @ORM\Column(name="email_acco",type="string", length=255, nullable=true)
     */
    private $emailAcco;

    /**
     * @ORM\Column(name="password_acco",type="string", length=255, nullable=true)
     */
    private $passwordAcco;

    /**
     * @ORM\Column(name="id_sejour",type="integer", nullable=true)
     */
    private $idSejour;

    /**
     * @ORM\Column(name="type_sejour",type="string", length=255, nullable=true)
     */
    private $typeSejour;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Statut;

    /**
     * @ORM\Column(name="date_envoi",type="date", nullable=true)
     */
    private $DateEnvoi;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdAccompagnateur(): ?int
    {
        return $this->idAccompagnateur;
    }

    public function setIdAccompagnateur(?int $idAccompagnateur): self
    {
        $this->idAccompagnateur = $idAccompagnateur;

        return $this;
    }

    public function getEmailAcco(): ?string
    {
        return $this->emailAcco;
    }

    public function setEmailAcco(?string $emailAcco): self
    {
        $this->emailAcco = $emailAcco;

        return $this;
    }

    public function getPasswordAcco(): ?string
    {
        return $this->passwordAcco;
    }

    public function setPasswordAcco(?string $passwordAcco): self
    {
        $this->passwordAcco = $passwordAcco;

        return $this;
    }

    public function getIdSejour(): ?int
    {
        return $this->idSejour;
    }

    public function setIdSejour(?int $idSejour): self
    {
        $this->idSejour = $idSejour;

        return $this;
    }

    public function getTypeSejour(): ?string
    {
        return $this->typeSejour;
    }

    public function setTypeSejour(?string $typeSejour): self
    {
        $this->typeSejour = $typeSejour;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->Statut;
    }

    public function setStatut(?string $Statut): self
    {
        $this->Statut = $Statut;

        return $this;
    }

    public function getDateEnvoi(): ?\DateTimeInterface
    {
        return $this->DateEnvoi;
    }

    public function setDateEnvoi(?\DateTimeInterface $DateEnvoi): self
    {
        $this->DateEnvoi = $DateEnvoi;

        return $this;
    }
}
