<?php

namespace App\Entity\bolt;

use Doctrine\ORM\Mapping as ORM;

/**
 * BoltEntries
 *
 * @ORM\Table(name="bolt_entries", indexes={@ORM\Index(name="IDX_2696ADF0989D9B62", columns={"slug"}), @ORM\Index(name="IDX_2696ADF0BE74E59A", columns={"datechanged"}), @ORM\Index(name="IDX_2696ADF0B7805520", columns={"datedepublish"}), @ORM\Index(name="IDX_2696ADF0AFBA6FD8", columns={"datecreated"}), @ORM\Index(name="IDX_2696ADF0A5131421", columns={"datepublish"}), @ORM\Index(name="IDX_2696ADF07B00651C", columns={"status"})})
 * @ORM\Entity
 */
class BoltEntries
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
     * @var string|null
     *
     * @ORM\Column(name="teaser", type="text", length=0, nullable=true)
     */
    private $teaser;

    /**
     * @var string|null
     *
     * @ORM\Column(name="body", type="text", length=0, nullable=true)
     */
    private $body;

    /**
     * @var json|null
     *
     * @ORM\Column(name="image", type="json", nullable=true)
     */
    private $image;

    /**
     * @var json|null
     *
     * @ORM\Column(name="video", type="json", nullable=true)
     */
    private $video;

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

    public function getTeaser(): ?string
    {
        return $this->teaser;
    }

    public function setTeaser(?string $teaser): self
    {
        $this->teaser = $teaser;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): self
    {
        $this->body = $body;

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

    public function getVideo(): ?array
    {
        return $this->video;
    }

    public function setVideo(?array $video): self
    {
        $this->video = $video;

        return $this;
    }


}
