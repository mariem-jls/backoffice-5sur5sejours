<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Attachment
 *
 * @ORM\Table(name="attachment", indexes={@ORM\Index(name="fk_attchposition_idx", columns={"idposition"}), @ORM\Index(name="fk_userphoto_idx", columns={"iduser"}), @ORM\Index(name="fk_attchref_idx", columns={"idref"})})
 * @ORM\Entity(repositoryClass="App\Repository\AttachmentRepository")
 */
class Attachment
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
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"typeproduit:read"})
     */
    private $path;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var int|null
     *
     * @ORM\Column(name="idblog", type="integer", nullable=true)
     */
    private $idblog;

    /**
     * @var \Position
     *
     * @ORM\ManyToOne(targetEntity="Position")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idposition", referencedColumnName="id")
     * })
     */
    private $idposition;

    /**
     * @var \Ref
     *
     * @ORM\ManyToOne(targetEntity="Ref")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idref", referencedColumnName="id")
     * })
     */
    private $idref;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="iduser", referencedColumnName="id")
     * })
     */
    private $iduser;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descreption;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;

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

    public function getIdblog(): ?int
    {
        return $this->idblog;
    }

    public function setIdblog(?int $idblog): self
    {
        $this->idblog = $idblog;

        return $this;
    }

    public function getIdposition(): ?Position
    {
        return $this->idposition;
    }

    public function setIdposition(?Position $idposition): self
    {
        $this->idposition = $idposition;

        return $this;
    }

    public function getIdref(): ?Ref
    {
        return $this->idref;
    }

    public function setIdref(?Ref $idref): self
    {
        $this->idref = $idref;

        return $this;
    }

    public function getIduser(): ?User
    {
        return $this->iduser;
    }

    public function setIduser(?User $iduser): self
    {
        $this->iduser = $iduser;

        return $this;
    }

    public function getDescreption(): ?string
    {
        return $this->descreption;
    }

    public function setDescreption(?string $descreption): self
    {
        $this->descreption = $descreption;

        return $this;
    }


}
