<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="PageBlocktexte", indexes={@ORM\Index(name="fk_block_page_idx", columns={"idpage"}) , @ORM\Index(name="fk_page_block_idx", columns={"idblocktext"})})
 * @ORM\Entity(repositoryClass="App\Repository\PageBlocktexteRepository")
 */
class PageBlocktexte
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
     * @var \Blocktext
     *
     * @ORM\ManyToOne(targetEntity="Blocktext")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idblocktext", referencedColumnName="id")
     * })
     */
    private $idblocktext;

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

    public function getIdblocktext(): ?Blocktext
    {
        return $this->idblocktext;
    }

    public function setIdblocktext(?Blocktext $idblocktext): self
    {
        $this->idblocktext = $idblocktext;

        return $this;
    }
}
