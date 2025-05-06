<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Commentaire
 *
 * @ORM\Table(name="commentaire", indexes={@ORM\Index(name="fk_usr_idx", columns={"Id_user"}), @ORM\Index(name="fk_attch_sej", columns={"id_attchment_sejour"})})
 * @ORM\Entity
 */
class Commentaire
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
     * @ORM\Column(name="labele_commtaire", type="string", length=45, nullable=true)
     */
    private $labeleCommtaire;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_com", type="date", nullable=true)
     */
    private $dateCom;

    /**
     * @var \SejourAttachment
     *
     * @ORM\ManyToOne(targetEntity="SejourAttachment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_attchment_sejour", referencedColumnName="id")
     * })
     */
    private $idAttchmentSejour;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Id_user", referencedColumnName="id")
     * })
     */
    private $idUser;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabeleCommtaire(): ?string
    {
        return $this->labeleCommtaire;
    }

    public function setLabeleCommtaire(?string $labeleCommtaire): self
    {
        $this->labeleCommtaire = $labeleCommtaire;

        return $this;
    }

    public function getDateCom(): ?\DateTimeInterface
    {
        return $this->dateCom;
    }

    public function setDateCom(?\DateTimeInterface $dateCom): self
    {
        $this->dateCom = $dateCom;

        return $this;
    }

    public function getIdAttchmentSejour(): ?SejourAttachment
    {
        return $this->idAttchmentSejour;
    }

    public function setIdAttchmentSejour(?SejourAttachment $idAttchmentSejour): self
    {
        $this->idAttchmentSejour = $idAttchmentSejour;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }


}
