<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Etablisment
 *
 * @ORM\Table(name="etablisment", indexes={@ORM\Index(name="fk_Ecole_User1_idx", columns={"User_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\EtablismentRepository")
 */
class Etablisment
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
     * @ORM\Column(name="nometab", type="string", length=45, nullable=true)
     */
    private $nometab;

    /**
     * @var int|null
     *
     * @ORM\Column(name="codepostaleatb", type="integer", nullable=true)
     */
    private $codepostaleatb;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nomcon", type="string", length=45, nullable=true)
     */
    private $nomcon;

    /**
     * @var string|null
     *
     * @ORM\Column(name="typeetablisment", type="string", length=45, nullable=true)
     */
    private $typeetablisment;

    /**
     * @var string|null
     *
     * @ORM\Column(name="formatEtablisement", type="string", length=100, nullable=true)
     */
    private $formatEtablisement;

    /**
     * @var string|null
     *
     * @ORM\Column(name="numRCS", type="string", length=100, nullable=true)
     */
    private $numRCS;

    /**
     * @var string|null
     *
     * @ORM\Column(name="prenomcont", type="string", length=45, nullable=true)
     */
    private $prenomcont;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fonctioncontact", type="string", length=45, nullable=true)
     */
    private $fonctioncontact;

    /**
     * @var string|null
     *
     * @ORM\Column(name="numerotelp", type="string", length=45, nullable=true)
     */
    private $numerotelp;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=45, nullable=true)
     */
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(name="adresseetab", type="string", length=45, nullable=true)
     */
    private $adresseetab;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="User_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var string|null
     *
     * @ORM\Column(name="pays", type="string", length=255, nullable=true)
     */
    private $pays;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ville", type="string", length=45, nullable=true)
     */
     private $ville;

    /**
     * @var string|null
     *
     * @ORM\Column(name="logo", type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @return null|string
     */
    public function getLogo(): ?string
    {
        return $this->logo;
    }

    /**
     * @param null|string $logo
     */
    public function setLogo(?string $logo): void
    {
        $this->logo = $logo;
    }

    
     /**
     * @var float|null
     *
     * @ORM\Column(name="prixcnxparent", type="float", length=45, nullable=true)
     */
    private $prixcnxparent;

     /**
     * @var float|null
     *
     * @ORM\Column(name="prixcnxpartenaire", type="float", length=45, nullable=true)
     */
    private $prixcnxpartenaire;

    /**
     * @var float|null
     *
     * @ORM\Column(name="reversecnxpart", type="float", length=45, nullable=true)
     */
    private $reversecnxpart;

     /**
     * @var float|null
     *
     * @ORM\Column(name="reverseventepart", type="float", length=45, nullable=true)
     */
    private $reverseventepart;

      /**
     * @var int|null
     *
     * @ORM\Column(name="delated", type="integer", nullable=true)
     */
    private $delated;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNometab(): ?string
    {
        return $this->nometab;
    }

    public function setNometab(?string $nometab): self
    {
        $this->nometab = $nometab;

        return $this;
    }

    public function getCodepostaleatb(): ?int
    {
        return $this->codepostaleatb;
    }

    public function setCodepostaleatb(?int $codepostaleatb): self
    {
        $this->codepostaleatb = $codepostaleatb;

        return $this;
    }

    public function getNomcon(): ?string
    {
        return $this->nomcon;
    }

    public function setNomcon(?string $nomcon): self
    {
        $this->nomcon = $nomcon;

        return $this;
    }

    public function getTypeetablisment(): ?string
    {
        return $this->typeetablisment;
    }

    public function setTypeetablisment(?string $typeetablisment): self
    {
        $this->typeetablisment = $typeetablisment;

        return $this;
    }

    public function getPrenomcont(): ?string
    {
        return $this->prenomcont;
    }

    public function setPrenomcont(?string $prenomcont): self
    {
        $this->prenomcont = $prenomcont;

        return $this;
    }

    public function getFonctioncontact(): ?string
    {
        return $this->fonctioncontact;
    }

    public function setFonctioncontact(?string $fonctioncontact): self
    {
        $this->fonctioncontact = $fonctioncontact;

        return $this;
    }

    public function getNumerotelp(): ?string
    {
        return $this->numerotelp;
    }

    public function setNumerotelp(?string $numerotelp): self
    {
        $this->numerotelp = $numerotelp;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAdresseetab(): ?string
    {
        return $this->adresseetab;
    }

    public function setAdresseetab(?string $adresseetab): self
    {
        $this->adresseetab = $adresseetab;

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

     /**
     * @return null|string
     */
    public function getPays(): ?string
    {
        return $this->pays;
    }

    /**
     * @param null|string $pays
     */
    public function setPays(?string $pays): void
    {
        $this->pays = $pays;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }
    
    public function getPrixcnxparent(): ?float
    {
        return $this->prixcnxparent;
    }

    public function setPrixcnxparent(?float $prixcnxparent): self
    {
        $this->prixcnxparent = $prixcnxparent;

        return $this;
    }

    public function getPrixcnxpartenaire(): ?float
    {
        return $this->prixcnxpartenaire;
    }

    public function setPrixcnxpartenaire(?float $prixcnxpartenaire): self
    {
        $this->prixcnxpartenaire = $prixcnxpartenaire;

        return $this;
    }

    public function getReversecnxpart(): ?float
    {
        return $this->reversecnxpart;
    }

    public function setReversecnxpart(?float $reversecnxpart): self
    {
        $this->reversecnxpart = $reversecnxpart;

        return $this;
    }

    public function getReverseventepart(): ?float
    {
        return $this->reverseventepart;
    }

    public function setReverseventepart(?float $reverseventepart): self
    {
        $this->reverseventepart = $reverseventepart;

        return $this;
    }


    public function getDelated(): ?int
    {
        return $this->delated;
    }

    public function setDelated(?int $delated): self
    {
        $this->delated = $delated;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getFormatEtablisement(): ?string
    {
        return $this->formatEtablisement;
    }

    /**
     * @param null|string $formatEtablisement
     */
    public function setFormatEtablisement(?string $formatEtablisement): void
    {
        $this->formatEtablisement = $formatEtablisement;
    }

    /**
     * @return null|string
     */
    public function getNumRCS(): ?string
    {
        return $this->numRCS;
    }

    /**
     * @param null|string $numRCS
     */
    public function setNumRCS(?string $numRCS): void
    {
        $this->numRCS = $numRCS;
    }


}
