<?php

namespace App\Entity\bolt;

use Doctrine\ORM\Mapping as ORM;

/**
 * BoltRelations
 *
 * @ORM\Table(name="bolt_relations", indexes={@ORM\Index(name="IDX_4C524BC3EA11294378CED90B", columns={"from_contenttype", "from_id"}), @ORM\Index(name="IDX_4C524BC35ACD264530354A65", columns={"to_contenttype", "to_id"})})
 * @ORM\Entity
 */
class BoltRelations
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
     * @ORM\Column(name="from_contenttype", type="string", length=32, nullable=false)
     */
    private $fromContenttype;

    /**
     * @var int
     *
     * @ORM\Column(name="from_id", type="integer", nullable=false)
     */
    private $fromId;

    /**
     * @var string
     *
     * @ORM\Column(name="to_contenttype", type="string", length=32, nullable=false)
     */
    private $toContenttype;

    /**
     * @var int
     *
     * @ORM\Column(name="to_id", type="integer", nullable=false)
     */
    private $toId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFromContenttype(): ?string
    {
        return $this->fromContenttype;
    }

    public function setFromContenttype(string $fromContenttype): self
    {
        $this->fromContenttype = $fromContenttype;

        return $this;
    }

    public function getFromId(): ?int
    {
        return $this->fromId;
    }

    public function setFromId(int $fromId): self
    {
        $this->fromId = $fromId;

        return $this;
    }

    public function getToContenttype(): ?string
    {
        return $this->toContenttype;
    }

    public function setToContenttype(string $toContenttype): self
    {
        $this->toContenttype = $toContenttype;

        return $this;
    }

    public function getToId(): ?int
    {
        return $this->toId;
    }

    public function setToId(int $toId): self
    {
        $this->toId = $toId;

        return $this;
    }


}
