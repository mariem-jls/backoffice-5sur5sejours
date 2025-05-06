<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Site
 *
 * @ORM\Table(name="site", indexes={@ORM\Index(name="fk_site_slide1", columns={"slide1"}), @ORM\Index(name="fk_site_slide3", columns={"slide3"}), @ORM\Index(name="fk_site_user", columns={"user"}), @ORM\Index(name="fk_site_slide2", columns={"slide2"}),@ORM\Index(name="fk_site_statut", columns={"statut"})})
 * @ORM\Entity(repositoryClass="App\Repository\SiteRepository")
 */
class Site
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
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string|null $code
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }
    /**
     * @var string|null
     *
     * @ORM\Column(name="code", type="string", length=125, nullable=true)
     */
    private $code;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="date", nullable=false)
     */

    private $datecreation;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var \Slide
     *
     * @ORM\ManyToOne(targetEntity="Slide")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="slide1", referencedColumnName="id")
     * })
     */
    private $slide1;

    /**
     * @var \Slide
     *
     * @ORM\ManyToOne(targetEntity="Slide")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="slide2", referencedColumnName="id")
     * })
     */
    private $slide2;

    /**
     * @var \Slide
     *
     * @ORM\ManyToOne(targetEntity="Slide")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="slide3", referencedColumnName="id")
     * })
     */
    private $slide3;
     /**
     * @var \Ref
     *
     * @ORM\ManyToOne(targetEntity="Ref")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="statut", referencedColumnName="id")
     * })
     */
    private $statut;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatecreation(): ?\DateTimeInterface
    {
        return $this->datecreation;
    }

    public function setDatecreation(\DateTimeInterface $datecreation): self
    {
        $this->datecreation = $datecreation;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getSlide1(): ?Slide
    {
        return $this->slide1;
    }

    public function setSlide1(?Slide $slide1): self
    {
        $this->slide1 = $slide1;

        return $this;
    }

    public function getSlide2(): ?Slide
    {
        return $this->slide2;
    }

    public function setSlide2(?Slide $slide2): self
    {
        $this->slide2 = $slide2;

        return $this;
    }

    public function getSlide3(): ?Slide
    {
        return $this->slide3;
    }

    public function setSlide3(?Slide $slide3): self
    {
        $this->slide3 = $slide3;

        return $this;
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
