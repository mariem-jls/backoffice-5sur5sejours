<?php

namespace App\Entity\bolt;

use Doctrine\ORM\Mapping as ORM;

/**
 * BoltCron
 *
 * @ORM\Table(name="bolt_cron", indexes={@ORM\Index(name="IDX_CD38E123615F8869", columns={"interim"})})
 * @ORM\Entity
 */
class BoltCron
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
     * @ORM\Column(name="interim", type="string", length=16, nullable=false)
     */
    private $interim;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastrun", type="datetime", nullable=false)
     */
    private $lastrun;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInterim(): ?string
    {
        return $this->interim;
    }

    public function setInterim(string $interim): self
    {
        $this->interim = $interim;

        return $this;
    }

    public function getLastrun(): ?\DateTimeInterface
    {
        return $this->lastrun;
    }

    public function setLastrun(\DateTimeInterface $lastrun): self
    {
        $this->lastrun = $lastrun;

        return $this;
    }


}
