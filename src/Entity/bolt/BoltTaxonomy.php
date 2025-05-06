<?php

namespace App\Entity\bolt;

use Doctrine\ORM\Mapping as ORM;

/**
 * BoltTaxonomy
 *
 * @ORM\Table(name="bolt_taxonomy", indexes={@ORM\Index(name="IDX_ABAA120084A0A3ED", columns={"content_id"}), @ORM\Index(name="IDX_ABAA1200FE2A268F", columns={"taxonomytype"}), @ORM\Index(name="IDX_ABAA1200FEA3B3F9", columns={"sortorder"}), @ORM\Index(name="IDX_ABAA1200745E1826", columns={"contenttype"}), @ORM\Index(name="IDX_ABAA1200989D9B62", columns={"slug"})})
 * @ORM\Entity
 */
class BoltTaxonomy
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
     * @var int
     *
     * @ORM\Column(name="content_id", type="integer", nullable=false)
     */
    private $contentId;

    /**
     * @var string
     *
     * @ORM\Column(name="contenttype", type="string", length=32, nullable=false)
     */
    private $contenttype;

    /**
     * @var string
     *
     * @ORM\Column(name="taxonomytype", type="string", length=32, nullable=false)
     */
    private $taxonomytype;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=64, nullable=false)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="sortorder", type="integer", nullable=false)
     */
    private $sortorder = '0';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContentId(): ?int
    {
        return $this->contentId;
    }

    public function setContentId(int $contentId): self
    {
        $this->contentId = $contentId;

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

    public function getTaxonomytype(): ?string
    {
        return $this->taxonomytype;
    }

    public function setTaxonomytype(string $taxonomytype): self
    {
        $this->taxonomytype = $taxonomytype;

        return $this;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSortorder(): ?int
    {
        return $this->sortorder;
    }

    public function setSortorder(int $sortorder): self
    {
        $this->sortorder = $sortorder;

        return $this;
    }


}
