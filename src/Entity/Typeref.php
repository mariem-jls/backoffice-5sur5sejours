<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Typeref
 *
 * @ORM\Table(name="typeref")
 * @ORM\Entity
 */
class Typeref
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
     * @var string|null
     *
     * @ORM\Column(name="libiler", type="string", length=45, nullable=true)
     */
    private $libiler;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibiler(): ?string
    {
        return $this->libiler;
    }

    public function setLibiler(?string $libiler): self
    {
        $this->libiler = $libiler;

        return $this;
    }


}
