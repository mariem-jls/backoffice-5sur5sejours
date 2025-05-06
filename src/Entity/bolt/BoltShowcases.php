<?php

namespace App\Entity\bolt;

use Doctrine\ORM\Mapping as ORM;

/**
 * BoltShowcases
 *
 * @ORM\Table(name="bolt_showcases", indexes={@ORM\Index(name="IDX_C5F751E8989D9B62", columns={"slug"}), @ORM\Index(name="IDX_C5F751E8BE74E59A", columns={"datechanged"}), @ORM\Index(name="IDX_C5F751E8B7805520", columns={"datedepublish"}), @ORM\Index(name="IDX_C5F751E8FD4718AE", columns={"integerfield"}), @ORM\Index(name="IDX_C5F751E8AFBA6FD8", columns={"datecreated"}), @ORM\Index(name="IDX_C5F751E8A5131421", columns={"datepublish"}), @ORM\Index(name="IDX_C5F751E87B00651C", columns={"status"})})
 * @ORM\Entity
 */
class BoltShowcases
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
     * @ORM\Column(name="html", type="text", length=0, nullable=true)
     */
    private $html;

    /**
     * @var string|null
     *
     * @ORM\Column(name="textarea", type="text", length=0, nullable=true)
     */
    private $textarea;

    /**
     * @var string|null
     *
     * @ORM\Column(name="markdown", type="text", length=0, nullable=true)
     */
    private $markdown;

    /**
     * @var json|null
     *
     * @ORM\Column(name="geolocation", type="json", nullable=true)
     */
    private $geolocation;

    /**
     * @var json|null
     *
     * @ORM\Column(name="embed", type="json", nullable=true)
     */
    private $embed;

    /**
     * @var json|null
     *
     * @ORM\Column(name="video", type="json", nullable=true)
     */
    private $video;

    /**
     * @var json|null
     *
     * @ORM\Column(name="image", type="json", nullable=true)
     */
    private $image;

    /**
     * @var json|null
     *
     * @ORM\Column(name="imagelist", type="json", nullable=true)
     */
    private $imagelist;

    /**
     * @var string|null
     *
     * @ORM\Column(name="file", type="string", length=256, nullable=true)
     */
    private $file;

    /**
     * @var json|null
     *
     * @ORM\Column(name="filelist", type="json", nullable=true)
     */
    private $filelist;

    /**
     * @var bool
     *
     * @ORM\Column(name="checkbox", type="boolean", nullable=false)
     */
    private $checkbox = '0';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="datetime", type="datetime", nullable=true)
     */
    private $datetime;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(name="integerfield", type="integer", nullable=false)
     */
    private $integerfield = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="floatfield", type="float", precision=10, scale=0, nullable=false)
     */
    private $floatfield = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="selectfield", type="text", length=0, nullable=true)
     */
    private $selectfield;

    /**
     * @var json|null
     *
     * @ORM\Column(name="multiselect", type="json", nullable=true)
     */
    private $multiselect;

    /**
     * @var string|null
     *
     * @ORM\Column(name="selectentry", type="text", length=0, nullable=true)
     */
    private $selectentry;

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

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function setHtml(?string $html): self
    {
        $this->html = $html;

        return $this;
    }

    public function getTextarea(): ?string
    {
        return $this->textarea;
    }

    public function setTextarea(?string $textarea): self
    {
        $this->textarea = $textarea;

        return $this;
    }

    public function getMarkdown(): ?string
    {
        return $this->markdown;
    }

    public function setMarkdown(?string $markdown): self
    {
        $this->markdown = $markdown;

        return $this;
    }

    public function getGeolocation(): ?array
    {
        return $this->geolocation;
    }

    public function setGeolocation(?array $geolocation): self
    {
        $this->geolocation = $geolocation;

        return $this;
    }

    public function getEmbed(): ?array
    {
        return $this->embed;
    }

    public function setEmbed(?array $embed): self
    {
        $this->embed = $embed;

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

    public function getImage(): ?array
    {
        return $this->image;
    }

    public function setImage(?array $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getImagelist(): ?array
    {
        return $this->imagelist;
    }

    public function setImagelist(?array $imagelist): self
    {
        $this->imagelist = $imagelist;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getFilelist(): ?array
    {
        return $this->filelist;
    }

    public function setFilelist(?array $filelist): self
    {
        $this->filelist = $filelist;

        return $this;
    }

    public function getCheckbox(): ?bool
    {
        return $this->checkbox;
    }

    public function setCheckbox(bool $checkbox): self
    {
        $this->checkbox = $checkbox;

        return $this;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(?\DateTimeInterface $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
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

    public function getIntegerfield(): ?int
    {
        return $this->integerfield;
    }

    public function setIntegerfield(int $integerfield): self
    {
        $this->integerfield = $integerfield;

        return $this;
    }

    public function getFloatfield(): ?float
    {
        return $this->floatfield;
    }

    public function setFloatfield(float $floatfield): self
    {
        $this->floatfield = $floatfield;

        return $this;
    }

    public function getSelectfield(): ?string
    {
        return $this->selectfield;
    }

    public function setSelectfield(?string $selectfield): self
    {
        $this->selectfield = $selectfield;

        return $this;
    }

    public function getMultiselect(): ?array
    {
        return $this->multiselect;
    }

    public function setMultiselect(?array $multiselect): self
    {
        $this->multiselect = $multiselect;

        return $this;
    }

    public function getSelectentry(): ?string
    {
        return $this->selectentry;
    }

    public function setSelectentry(?string $selectentry): self
    {
        $this->selectentry = $selectentry;

        return $this;
    }


}
