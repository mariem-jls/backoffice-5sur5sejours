<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="Element",indexes={@ORM\Index(name="fk_blocktext_element_idx", columns={"idtext"})},indexes={@ORM\Index(name="fk_clipart_element_idx", columns={"idclipart"})},indexes={@ORM\Index(name="fk_pagecliparttext_element_idx", columns={"idpage"})})
 * @ORM\Entity(repositoryClass="App\Repository\ElementRepository")
 */
class Element
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
    private $higth;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $width;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $x;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $y;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rotation;

     /**
     * @var \Clipart
     *
     * @ORM\ManyToOne(targetEntity="Clipart")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idclipart", referencedColumnName="id")
     * })
     */
    private $idclipart;

     /**
     * @var \Blocktext
     *
     * @ORM\ManyToOne(targetEntity="Blocktext")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idtext", referencedColumnName="id")
     * })
     */
    private $idtext;

    /**
     * @var \Page
     *
     * @ORM\ManyToOne(targetEntity="Page")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idpage", referencedColumnName="id")
     * })
     */
    private $idpage;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHigth(): ?string
    {
        return $this->higth;
    }

    public function setHigth(?string $higth): self
    {
        $this->higth = $higth;

        return $this;
    }

    public function getWidth(): ?string
    {
        return $this->width;
    }

    public function setWidth(?string $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getX(): ?string
    {
        return $this->x;
    }

    public function setX(?string $x): self
    {
        $this->x = $x;

        return $this;
    }

    public function getY(): ?string
    {
        return $this->y;
    }

    public function setY(?string $y): self
    {
        $this->y = $y;

        return $this;
    }

    public function getRotation(): ?string
    {
        return $this->rotation;
    }

    public function setRotation(?string $rotation): self
    {
        $this->rotation = $rotation;

        return $this;
    }

    public function getIdclipart(): ?Clipart
    {
        return $this->idclipart;
    }

    public function setIdclipart(?Clipart $idclipart): self
    {
        $this->idclipart = $idclipart;

        return $this;
    }

    public function getIdtext(): ?Blocktext
    {
        return $this->idtext;
    }

    public function setIdtext(?Blocktext $idtext): self
    {
        $this->idtext = $idtext;

        return $this;
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

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): self
    {
        $this->type = $type;

        return $this;
    }
}
