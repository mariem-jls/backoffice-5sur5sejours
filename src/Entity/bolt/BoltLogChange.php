<?php

namespace App\Entity\bolt;

use Doctrine\ORM\Mapping as ORM;

/**
 * BoltLogChange
 *
 * @ORM\Table(name="bolt_log_change", indexes={@ORM\Index(name="IDX_946F972AA9E377A", columns={"date"}), @ORM\Index(name="IDX_946F972745E1826", columns={"contenttype"}), @ORM\Index(name="IDX_946F972B0AEEF39", columns={"mutation_type"}), @ORM\Index(name="IDX_946F97275DAD987", columns={"ownerid"}), @ORM\Index(name="IDX_946F972E625AE99", columns={"contentid"})})
 * @ORM\Entity
 */
class BoltLogChange
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ownerid", type="integer", nullable=true)
     */
    private $ownerid;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=256, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="contenttype", type="string", length=128, nullable=false)
     */
    private $contenttype;

    /**
     * @var int
     *
     * @ORM\Column(name="contentid", type="integer", nullable=false)
     */
    private $contentid;

    /**
     * @var string
     *
     * @ORM\Column(name="mutation_type", type="string", length=16, nullable=false)
     */
    private $mutationType;

    /**
     * @var json
     *
     * @ORM\Column(name="diff", type="json", nullable=false)
     */
    private $diff;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comment", type="string", length=150, nullable=true)
     */
    private $comment;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContenttype(): ?string
    {
        return $this->contenttype;
    }

    public function setContenttype(string $contenttype): self
    {
        $this->contenttype = $contenttype;

        return $this;
    }

    public function getContentid(): ?int
    {
        return $this->contentid;
    }

    public function setContentid(int $contentid): self
    {
        $this->contentid = $contentid;

        return $this;
    }

    public function getMutationType(): ?string
    {
        return $this->mutationType;
    }

    public function setMutationType(string $mutationType): self
    {
        $this->mutationType = $mutationType;

        return $this;
    }

    public function getDiff(): ?array
    {
        return $this->diff;
    }

    public function setDiff(array $diff): self
    {
        $this->diff = $diff;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }


}
