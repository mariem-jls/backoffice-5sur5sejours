<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BlocktextRepository")
 */
class Blocktext
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
    private $labelle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $styletexte;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabelle(): ?string
    {
        return $this->labelle;
    }

    public function setLabelle(?string $labelle): self
    {
        $this->labelle = $labelle;

        return $this;
    }

    public function getStyletexte(): ?string
    {
        return $this->styletexte;
    }

    public function setStyletexte(?string $styletexte): self
    {
        $this->styletexte = $styletexte;

        return $this;
    }
}
