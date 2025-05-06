<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="cliparpage", indexes={@ORM\Index(name="fk_clipart_page_idx", columns={"idclipart"}), @ORM\Index(name="fk_photo_clipart_idx", columns={"idpage"})})
 * @ORM\Entity(repositoryClass="App\Repository\CliparpageRepository")
 */
class Cliparpage
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var \Page
     *
     * @ORM\ManyToOne(targetEntity="Page")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idpage", referencedColumnName="id")
     * })
     */
    private $idpage;

    /**
     * @var \Clipart
     *
     * @ORM\ManyToOne(targetEntity="Clipart")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idclipart", referencedColumnName="id")
     * })
     */
    private $idclipart;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $styleclipart;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $taille;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdpage(): ?Page
    {
        return $this->idpage;
    }

    public function setIdpage(?Page $idpage): self
    {
        $this->idpage = $idpage;

        return $this;
    }

    public function getIdclipart(): ?Clipart
    {
        return $this->idclipart;
    }

    public function setIdclipart(?Clipart $idclipart): Clipart
    {
        $this->idclipart = $idclipart;

        return $this;
    }

    public function getStyleclipart(): ?string
    {
        return $this->styleclipart;
    }

    public function setStyleclipart(?string $styleclipart): self
    {
        $this->styleclipart = $styleclipart;

        return $this;
    }

    public function getTaille(): ?string
    {
        return $this->taille;
    }

    public function setTaille(?string $taille): self
    {
        $this->taille = $taille;

        return $this;
    }
}
