<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * SejourAttachment
 *
 * @ORM\Table(name="sejour_attachment", indexes={@ORM\Index(name="fk_attchment_idx", columns={"id_attchment"}), @ORM\Index(name="fk_sejour_idx", columns={"id_sejour"})})
 * @ORM\Entity(repositoryClass="App\Repository\SejourAttachmentRepository")
 */
class SejourAttachment {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_depot_attachement", type="datetime", nullable=true)
     */
    private $dateDepotAttachement;

    /**
     * @var string|null
     *
     * @ORM\Column(name="etat", type="string", length=45, nullable=true)
     */
    private $etat;

    /**
     * @var int|null
     *
     * @ORM\Column(name="nbconsomateurattch", type="integer", nullable=true)
     */
    private $nbconsomateurattch;

    /**
     * @var int|null
     *
     * @ORM\Column(name="nbpartenaireattch", type="integer", nullable=true)
     */
    private $nbpartenaireattch;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nbecoleattch", type="string", length="128", nullable=true)
     */
    private $nbecoleattch;

    /**
     * @var \Attachment
     *
     * @ORM\ManyToOne(targetEntity="Attachment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_attchment", referencedColumnName="id")
     * })
     */
    private $idAttchment;

    /**
     * @var \Sejour
     *
     * @ORM\ManyToOne(targetEntity="Sejour")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_sejour", referencedColumnName="id")
     * })
     */
    private $idSejour;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="sejourAttachments")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idParent_id", referencedColumnName="id")
     * })
     */
    private $idParent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $statut;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Photonsumeriques", mappedBy="idSejourAttachement")
     */
    private $photonsumeriques;

    public function __construct()
    {
        $this->photonsumeriques = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getDateDepotAttachement(): ?\DateTimeInterface {
        return $this->dateDepotAttachement;
    }

    public function setDateDepotAttachement(?\DateTimeInterface $dateDepotAttachement): self {
        $this->dateDepotAttachement = $dateDepotAttachement;

        return $this;
    }

    public function getEtat(): ?string {
        return $this->etat;
    }

    public function setEtat(?string $etat): self {
        $this->etat = $etat;

        return $this;
    }

    public function getIdAttchment(): ?Attachment {
        return $this->idAttchment;
    }

    public function setIdAttchment(?Attachment $idAttchment): self {
        $this->idAttchment = $idAttchment;

        return $this;
    }

    public function getIdSejour(): ?Sejour {
        return $this->idSejour;
    }

    public function setIdSejour(?Sejour $idSejour): self {
        $this->idSejour = $idSejour;

        return $this;
    }

    public function getNbconsomateurattch(): ?int {
        return $this->nbconsomateurattch;
    }

    public function setNbconsomateurattch(?int $nbconsomateurattch): self {
        $this->nbconsomateurattch = $nbconsomateurattch;

        return $this;
    }

    public function getNbpartenaireattch(): ?int {
        return $this->nbpartenaireattch;
    }

    public function setNbpartenaireattch(?int $nbpartenaireattch): self {
        $this->nbpartenaireattch = $nbpartenaireattch;

        return $this;
    }

    public function getNbecoleattch(): ?string {
        return $this->nbecoleattch;
    }

    public function setNbecoleattch(?string $nbecoleattch): self {
        $this->nbecoleattch = $nbecoleattch;

        return $this;
    }

    public function getIdParent(): ?User
    {
        return $this->idParent;
    }

    public function setIdParent(?User $idParent): self
    {
        $this->idParent = $idParent;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * @return Collection|Photonsumeriques[]
     */
    public function getPhotonsumeriques(): Collection
    {
        return $this->photonsumeriques;
    }

    public function addPhotonsumerique(Photonsumeriques $photonsumerique): self
    {
        if (!$this->photonsumeriques->contains($photonsumerique)) {
            $this->photonsumeriques[] = $photonsumerique;
            $photonsumerique->setIdSejourAttachement($this);
        }

        return $this;
    }

    public function removePhotonsumerique(Photonsumeriques $photonsumerique): self
    {
        if ($this->photonsumeriques->contains($photonsumerique)) {
            $this->photonsumeriques->removeElement($photonsumerique);
            // set the owning side to null (unless already changed)
            if ($photonsumerique->getIdSejourAttachement() === $this) {
                $photonsumerique->setIdSejourAttachement(null);
            }
        }

        return $this;
    }

}
