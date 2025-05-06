<?php

namespace App\Entity\bolt;

use Doctrine\ORM\Mapping as ORM;

/**
 * BoltHomepage
 *
 * @ORM\Table(name="bolt_homepage", indexes={@ORM\Index(name="IDX_9D9C7FD3989D9B62", columns={"slug"}), @ORM\Index(name="IDX_9D9C7FD3BE74E59A", columns={"datechanged"}), @ORM\Index(name="IDX_9D9C7FD3B7805520", columns={"datedepublish"}), @ORM\Index(name="IDX_9D9C7FD3AFBA6FD8", columns={"datecreated"}), @ORM\Index(name="IDX_9D9C7FD3A5131421", columns={"datepublish"}), @ORM\Index(name="IDX_9D9C7FD37B00651C", columns={"status"})})
 * @ORM\Entity
 */
class BoltHomepage
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
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=128, nullable=false)
     */
    private $slug;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datecreated", type="datetime", nullable=false)
     */
    private $datecreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datechanged", type="datetime", nullable=false)
     */
    private $datechanged;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="datepublish", type="datetime", nullable=true)
     */
    private $datepublish;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="datedepublish", type="datetime", nullable=true)
     */
    private $datedepublish;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ownerid", type="integer", nullable=true)
     */
    private $ownerid;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=32, nullable=false)
     */
    private $status;

    /**
     * @var json|null
     *
     * @ORM\Column(name="templatefields", type="json", nullable=true)
     */
    private $templatefields;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", length=256, nullable=true)
     */
    private $title;

    /**
     * @var json|null
     *
     * @ORM\Column(name="image", type="json", nullable=true)
     */
    private $image;

    /**
     * @var string|null
     *
     * @ORM\Column(name="teaser", type="text", length=0, nullable=true)
     */
    private $teaser;

    /**
     * @var string|null
     *
     * @ORM\Column(name="content", type="text", length=0, nullable=true)
     */
    private $content;

    /**
     * @var string|null
     *
     * @ORM\Column(name="contentlink", type="string", length=256, nullable=true)
     */
    private $contentlink;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDatecreated(): ?\DateTimeInterface
    {
        return $this->datecreated;
    }

    public function setDatecreated(\DateTimeInterface $datecreated): self
    {
        $this->datecreated = $datecreated;

        return $this;
    }

    public function getDatechanged(): ?\DateTimeInterface
    {
        return $this->datechanged;
    }

    public function setDatechanged(\DateTimeInterface $datechanged): self
    {
        $this->datechanged = $datechanged;

        return $this;
    }

    public function getDatepublish(): ?\DateTimeInterface
    {
        return $this->datepublish;
    }

    public function setDatepublish(?\DateTimeInterface $datepublish): self
    {
        $this->datepublish = $datepublish;

        return $this;
    }

    public function getDatedepublish(): ?\DateTimeInterface
    {
        return $this->datedepublish;
    }

    public function setDatedepublish(?\DateTimeInterface $datedepublish): self
    {
        $this->datedepublish = $datedepublish;

        return $this;
    }

    public function getOwnerid(): ?int
    {
        return $this->ownerid;
    }

    public function setOwnerid(?int $ownerid): self
    {
        $this->ownerid = $ownerid;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getTemplatefields(): ?array
    {
        return $this->templatefields;
    }

    public function setTemplatefields(?array $templatefields): self
    {
        $this->templatefields = $templatefields;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getImage(): ?array
    {
        return $this->image;
    }

    public function setImage(?array $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getTeaser(): ?string
    {
        return $this->teaser;
    }

    public function setTeaser(?string $teaser): self
    {
        $this->teaser = $teaser;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getContentlink(): ?string
    {
        return $this->contentlink;
    }

    public function setContentlink(?string $contentlink): self
    {
        $this->contentlink = $contentlink;

        return $this;
    }


}
