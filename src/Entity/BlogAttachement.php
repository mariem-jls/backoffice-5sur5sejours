<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BlogAttachement
 *
 * @ORM\Table(name="blog_attachement", indexes={@ORM\Index(name="fk_at_blog_idx", columns={"idattachement"}), @ORM\Index(name="fk_blog_at_idx", columns={"idblog"})})
 * @ORM\Entity
 */
class BlogAttachement
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var \Blog
     *
     * @ORM\ManyToOne(targetEntity="Blog")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idblog", referencedColumnName="id")
     * })
     */
    private $idblog;

    /**
     * @var \Attachment
     *
     * @ORM\ManyToOne(targetEntity="Attachment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idattachement", referencedColumnName="id")
     * })
     */
    private $idattachement;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getIdblog(): ?Blog
    {
        return $this->idblog;
    }

    public function setIdblog(?Blog $idblog): self
    {
        $this->idblog = $idblog;

        return $this;
    }

    public function getIdattachement(): ?Attachment
    {
        return $this->idattachement;
    }

    public function setIdattachement(?Attachment $idattachement): self
    {
        $this->idattachement = $idattachement;

        return $this;
    }


}
