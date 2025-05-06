<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *@ORM\Table(name="adress")
 * @ORM\Entity(repositoryClass="App\Repository\AdressRepository")
 */
class Adress
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
    private $numadress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ruevoi;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $codepostal;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ville;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pays;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nomadrres;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $prenomadress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $organism;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumadress(): ?string
    {
        return $this->numadress;
    }

    public function setNumadress(?string $numadress): self
    {
        $this->numadress = $numadress;

        return $this;
    }

    public function getRuevoi(): ?string
    {
        return $this->ruevoi;
    }

    public function setRuevoi(?string $ruevoi): self
    {
        $this->ruevoi = $ruevoi;

        return $this;
    }

    public function getCodepostal(): ?string
    {
        return $this->codepostal;
    }

    public function setCodepostal(?string $codepostal): self
    {
        $this->codepostal = $codepostal;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(?string $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getNomadrres(): ?string
    {
        return $this->nomadrres;
    }

    public function setNomadrres(?string $nomadrres): self
    {
        $this->nomadrres = $nomadrres;

        return $this;
    }

    public function getPrenomadress(): ?string
    {
        return $this->prenomadress;
    }

    public function setPrenomadress(?string $prenomadress): self
    {
        $this->prenomadress = $prenomadress;

        return $this;
    }

    public function getOrganism(): ?string
    {
        return $this->organism;
    }

    public function setOrganism(?string $organism): self
    {
        $this->organism = $organism;

        return $this;
    }
}
