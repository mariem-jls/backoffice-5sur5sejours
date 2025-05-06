<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="jourdescripdate", indexes={@ORM\Index(name="FK_218C35CEF2488D84", columns={"idsejour"})})
 * @ORM\Entity(repositoryClass="App\Repository\JourdescripdateRepository")
 */
class Jourdescripdate
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datejourphoto;


     /**
     * @var \Sejour
     *
     * @ORM\ManyToOne(targetEntity="Sejour")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idsejour", referencedColumnName="id")
     * })
     */
    private $idsejour;    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDatejourphoto(): ?\DateTimeInterface
    {
        return $this->datejourphoto;
    }

    public function setDatejourphoto(?\DateTimeInterface $datejourphoto): self
    {
        $this->datejourphoto = $datejourphoto;

        return $this;
    }

    public function getIdsejour(): ?Sejour
    {
        return $this->Idsejour;
    }

    public function setIdIdsejour(?Sejour $Idsejour): self
    {
        $this->idsejour = $Idsejour;

        return $this;
    }

}
