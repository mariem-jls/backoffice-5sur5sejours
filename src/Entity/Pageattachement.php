<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="Pageattachement", indexes={@ORM\Index(name="fk_attach_page_idx", columns={"idattachment"}), @ORM\Index(name="fk_page_attach_idx", columns={"idpage"})})
 * @ORM\Entity(repositoryClass="App\Repository\PageattachementRepository")
 */
class Pageattachement
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

     /**
     * @var \Attachment
     *
     * @ORM\ManyToOne(targetEntity="Attachment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idattachment", referencedColumnName="id")
     * })
     */
    private $idattachment;

     /**
     * @var \Page
     *
     * @ORM\ManyToOne(targetEntity="Page")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idpage", referencedColumnName="id")
     * })
     */
    private $idpage;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdattachment(): ?Attachment
    {
        return $this->idattachment;
    }

    public function setIdattachment(?Attachment $idattachment): self
    {
        $this->idattachment = $idattachment;

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
}
