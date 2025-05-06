<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *@ORM\Table(name="documentpartenaire")
 * @ORM\Entity(repositoryClass="App\Repository\DocumentpartenaireRepository")
 */
class Documentpartenaire
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
    private $path;

    /**
     * @var \Etablisment
     *
     * @ORM\ManyToOne(targetEntity="Etablisment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idetablisment", referencedColumnName="id")
     * })
     */
    private $idetablisment;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nomdocument;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }



    public function getIdetablisment(): ?Etablisment
    {
        return $this->idetablisment;
    }

    public function setIdetablisment(?Etablisment $idetablisment): self
    {
        $this->idetablisment = $idetablisment;

        return $this;
    }

    public function getNomdocument(): ?string
    {
        return $this->nomdocument;
    }

    public function setNomdocument(?string $nomdocument): self
    {
        $this->nomdocument = $nomdocument;

        return $this;
    }
}
