<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Blog
 *
 * @ORM\Table(name="blog", indexes={@ORM\Index(name="fk_blog_user_idx", columns={"iduser"}),@ORM\Index(name="fk_blog_ref", columns={"statut"})})
 * @ORM\Entity(repositoryClass="App\Repository\BlogRepository")
 */
class Blog
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
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=500, nullable=true)
     */
    private $description;
    /**
     * @var string|null
     *
     * @ORM\Column(name="categorie", type="string", length=500, nullable=true)
     */
    private $categorie;
    /**
     * @var \Ref
     *
     * @ORM\ManyToOne(targetEntity="Ref")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="statut", referencedColumnName="id" )
     * })
     */
    private $statut;


    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="iduser", referencedColumnName="id")
     * })
     */
    private $iduser;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BlogAttachement", mappedBy="idblog")
     */
    private $mesattachments;

    public function __construct()
    {
        $this->mesattachments = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function mesattachments()
    {
        
        return $this->mesattachments;
    }

    /**
     * @param mixed $mesattachments
     */
    public function setMesattachments($mesattachments): void
    {
        $this->mesattachments = $mesattachments;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    /**
     * @return null|string
     */
    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    /**
     * @param null|string $categorie
     */
    public function setCategorie(?string $categorie): void
    {
        $this->categorie = $categorie;
    }

    public function getStatut(): ?Ref
    {
        return $this->statut;
    }

    public function setStatut(?Ref $statut): self
    {
        $this->statut = $statut;

        return $this;
    }


}