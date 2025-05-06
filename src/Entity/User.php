<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * User
 *
 * @ORM\Table(name="user", indexes={@ORM\Index(name="fk_Userref_idx", columns={"statut"})})
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
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
     * @ORM\Column(name="nbconnx", type="string", length=45, nullable=true)
     */
    private $nbconnx;

    /**
     * @var string|null
     *
     * @ORM\Column(name="username", type="string", length=45, nullable=true)
     */
    private $username;

    /**
     * @var string|null
     *
     * @ORM\Column(name="adresse", type="string", length=45, nullable=true)
     */
    private $adresse;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fonction", type="string", length=45, nullable=true)
     */
    private $fonction;

    /**
     * @var string|null
     *
     * @ORM\Column(name="etablisment", type="string", length=45, nullable=true)
     */
    private $etablisment;

    /**
     * @var string|null
     *
     * @ORM\Column(name="numMobile", type="string", nullable=true)
     */
    private $nummobile;

    /**
     * @var string|null
     *
     * @ORM\Column(name="logourl",type="text", nullable=true)
     */
    private $logourl;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="Datedepart", type="date", nullable=true)
     */
    private $datedepart;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dateFlagDepot", type="date", nullable=true)
     */
    private $dateFlagDepot;

    /**
     * @var string|null
     *
     * @ORM\Column(name="flagDepot", type="string", length=45, nullable=true)
     */
    private $flagDepot;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nom", type="string", length=45, nullable=true)
     */
    private $nom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="prenom", type="string", length=45, nullable=true)
     */
    private $prenom;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="json", nullable=true)
     */
    private $roles = [];


    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=45, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @var string|null
     *
     * @ORM\Column(name="salt", type="string", length=45, nullable=true)
     */
    private $salt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_creation", type="datetime", nullable=true)
     */
    private $dateCreation;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nometablisment", type="string", length=45, nullable=true)
     */
    private $nometablisment;

    /**
     * @var int|null
     *
     * @ORM\Column(name="randomnotice", type="integer", nullable=true)
     */
    private $randomnotice;

    /**
     * @var \Adress
     *
     * @ORM\ManyToOne(targetEntity="Adress")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="adresslivraison", referencedColumnName="id" )
     * })
     */
    private $adresslivraison;

    /**
     * @var \Adress
     *
     * @ORM\ManyToOne(targetEntity="Adress")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="adressfactoration", referencedColumnName="id" )
     * })
     */
    private $adressfactoration;

    /**
     * @var \Ref
     *
     * @ORM\ManyToOne(targetEntity="Ref")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="statut", referencedColumnName="id")
     * })
     */
    private $statut;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Commande", mappedBy="idUser")
     */
    private $mescommandes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Etablisment", mappedBy="user")
     */
    private $etablismentuser;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Blog", mappedBy="iduser")
     */
    private $mesblogs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sejour", mappedBy="idAcommp")
     */
    private $mesSejourAcc;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sejour", mappedBy="idPartenaire")
     */
    private $mesSejourPart;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="user")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usersecondaire", referencedColumnName="id")
     * })
     */
    private $usersecondaire;

    /**
     * @var \Comptebancaire
     *
     * @ORM\ManyToOne(targetEntity="Comptebancaire")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="comptebanque", referencedColumnName="id")
     * })
     */
    private $comptebanque;

    /**
     * @var string|null
     *
     * @ORM\Column(name="infocomple", type="string", length=45, nullable=true)
     */
    private $infocomple;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reponseemail", type="string", length=255, nullable=true)
     */
    private $reponseemail;

    /**
     * @ORM\Column(name="password_non_cripted",type="string", length=255, nullable=true)
     */
    private $passwordNonCripted;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SejourAttachment", mappedBy="idParent")
     */
    private $sejourAttachments;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $cnxparent;

    /**
     * @var string|null
     *
     * @ORM\Column(name="accompaplus", type="string", length=45, nullable=true)
     */
    private $accompaplus;

    /**
     * @ORM\Column(name="activatemail",type="integer", nullable=true)
     */
    private $activatemail;

    /**
     * @ORM\Column(name="Jeton",type="integer", nullable=true)
     */
    private $jeton;

    /**
     * @ORM\Column(name="showpubprod",type="integer", nullable=true)
     */
    private $showpubprod;

    /**
     * @var string|null
     *
     * @ORM\Column(name="showdetailsalbum", type="string", length=45, nullable=true)
     */
    private $showdetailsalbum;

    /**
     * @var string|null
     *
     * @ORM\Column(name="showdetailslivre", type="string", length=45, nullable=true)
     */
    private $showdetailslivre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="showdetailsphotos", type="string", length=45, nullable=true)
     */
    private $showdetailsphotos;

    /**
     * @var string|null
     *
     * @ORM\Column(name="showdetailsretros", type="string", length=45, nullable=true)
     */
    private $showdetailsretros;

    /**
     * @var string|null
     *
     * @ORM\Column(name="showdetailscal", type="string", length=45, nullable=true)
     */
    private $showdetailscal;

    /**
     * @return mixed
     */
    public function getJeton()
    {
        return $this->jeton;
    }

    /**
     * @param mixed $jeton
     */
    public function setJeton($jeton): void
    {
        $this->jeton = $jeton;
    }

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $cnxpartenaire;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Panier", mappedBy="creerPar")
     */
    private $paniers;

    /**
     * @var int|null
     *
     * @ORM\Column(name="smsnotif", type="integer", nullable=true)
     */
    private $smsnotif;

    /**
     * @var int|null
     *
     * @ORM\Column(name="mailnotif", type="integer", nullable=true)
     */
    private $mailnotif;

    /**
     * @var int|null
     *
     * @ORM\Column(name="AccoPlusToPartenaire", type="integer", nullable=true)
     */
    private $AccoPlusToPartenaire;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Etiquette", mappedBy="support")
     */
    private $etiquettes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Etiquette", mappedBy="rapporteur")
     */
    private $etiquetteRapporteur;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CommentaireEtiquette", mappedBy="createur")
     */
    private $commentaireEtiquettes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Fonctions", inversedBy="users")
     */
    private $idFonction;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $jetonAccoPlusSejourGratuit;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LogPromotions", mappedBy="idClient")
     */
    private $logPromotions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PromoParents", mappedBy="parent")
     */
    private $promoParents;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Photonsumeriques", mappedBy="idUser")
     */
    private $photonsumeriques;

    private $decryptedPassword;



    public function __construct()
    {
        $this->mesSejourPart = new ArrayCollection();
        $this->mesSejourAcc = new ArrayCollection();
        $this->mescommandes = new ArrayCollection();
        $this->mesblogs = new ArrayCollection();
        $this->sejourAttachments = new ArrayCollection();
        $this->paniers = new ArrayCollection();
        $this->etablismentuser = new ArrayCollection();
        $this->etiquettes = new ArrayCollection();
        $this->etiquetteRapporteur = new ArrayCollection();
        $this->commentaireEtiquettes = new ArrayCollection();
        $this->logPromotions = new ArrayCollection();
        $this->promoParents = new ArrayCollection();
        $this->photonsumeriques = new ArrayCollection();
    }

    public function setDecryptedPassword($decryptedPassword)
    {
        $this->decryptedPassword = $decryptedPassword;
    }

    public function getDecryptedPassword()
    {
        return $this->decryptedPassword;
    }

    public function getRandomnotice()
    {
        return $this->randomnotice;
    }

    public function setRandomnotice($randomnotice)
    {
        $this->randomnotice = $randomnotice;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEtablismentuser()
    {
        return $this->etablismentuser;
    }

    /**
     * @param mixed $mescommandes
     */
    public function setEtablismentuser($etablismentuser): void
    {
        $this->etablismentuser = $etablismentuser;
    }

    /**
     * @return mixed
     */
    public function getMescommandes()
    {

        return $this->mescommandes;
    }

    public function getMescommandesBySejour($Sejour)
    {
        $cmds = array();
        foreach ($this->mescommandes as $cmd) {
            if ($cmd->idSejour == $Sejour) {
                array_push($cmds, $cmd);
            }
        }
        return $cmds;
    }

    /**
     * @param mixed $mescommandes
     */
    public function setMescommandes($mescommandes): void
    {
        $this->mescommandes = $mescommandes;
    }



    /**
     * @return null|string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @return null|string
     */
    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function getUsername()
    {
        return $this->username;
    }

    /**
     * The public representation of the user (e.g. a username, an email address, etc.)
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoles(): array
    {
        // Ensure $this->roles is always an array
        if (is_string($this->roles)) {
            $this->roles = json_decode($this->roles, true);
        }

        // Ensure every user at least has ROLE_USER
        $roles = $this->roles ?? []; // Handle null cases
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles; // Store roles as an array

        return $this;
    }

    public function addRole(?string $role): self
    {
        // Ensure $this->roles is always an array
        if (is_string($this->roles)) {
            $this->roles = json_decode($this->roles, true);
        }

        // Handle the case when $this->roles is null
        if (is_null($this->roles)) {
            $this->roles = [];
        }

        $this->roles[] = $role;
        $this->roles = array_unique($this->roles);

        return $this;
    }

    public function hasRole(?string $role): bool
    {
        // Ensure $this->roles is always an array
        if (is_string($this->roles)) {
            $this->roles = json_decode($this->roles, true);
        }

        if ($this->roles != null) {
            return in_array($role, $this->roles);
        }

        return false;
    }


    public function getNbconnx(): ?string
    {
        return $this->nbconnx;
    }

    public function setNbconnx(?string $nbconnx): self
    {
        $this->nbconnx = $nbconnx;

        return $this;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(?string $fonction): self
    {
        $this->fonction = $fonction;

        return $this;
    }

    public function getEtablisment(): ?string
    {
        return $this->etablisment;
    }

    public function setEtablisment(?string $etablisment): self
    {
        $this->etablisment = $etablisment;

        return $this;
    }

    public function getNummobile(): ?string
    {
        return $this->nummobile;
    }

    public function setNummobile(?string $nummobile): self
    {
        $this->nummobile = $nummobile;

        return $this;
    }

    public function getLogourl(): ?string
    {
        return $this->logourl;
    }

    public function setLogourl(?string $logourl): self
    {
        $this->logourl = $logourl;

        return $this;
    }

    public function getDatedepart(): ?\DateTimeInterface
    {
        return $this->datedepart;
    }

    public function setDatedepart(?\DateTimeInterface $datedepart): self
    {
        $this->datedepart = $datedepart;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

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

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function setSalt(?string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function setStatut(?Ref $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    function getMesblogs()
    {
        return $this->mesblogs;
    }

    function setMesblogs($mesblogs)
    {
        $this->mesblogs = $mesblogs;
    }

    public function __toString()
    {
        return $this->roles;
    }

    /**
     * @return mixed
     */
    public function getMesSejourAcc()
    {
        return $this->mesSejourAcc;
    }

    /**
     * @param mixed $mesSejourAcc
     */
    public function setMesSejourAcc($mesSejourAcc): void
    {
        $this->mesSejourAcc = $mesSejourAcc;
    }

    /**
     * @return mixed
     */
    public function getMesSejourPart()
    {
        return $this->mesSejourPart;
    }

    /**
     * @param mixed $mesSejourPart
     */
    public function setMesSejourPart($mesSejourPart): void
    {
        $this->mesSejourPart = $mesSejourPart;
    }

    public function getNometablisment(): ?string
    {
        return $this->nometablisment;
    }

    public function setNometablisment(?string $nometablisment): self
    {
        $this->nometablisment = $nometablisment;

        return $this;
    }

    public function getAdresslivraison(): ?Adress
    {
        return $this->adresslivraison;
    }

    public function setAdresslivraison(?Adress $adresslivraison): self
    {
        $this->adresslivraison = $adresslivraison;

        return $this;
    }

    public function getAdressfactoration(): ?Adress
    {
        return $this->adressfactoration;
    }

    public function setAdressfactoration(?Adress $adressfactoration): self
    {
        $this->adressfactoration = $adressfactoration;

        return $this;
    }

    public function getUsersecondaire()
    {
        return $this->usersecondaire;
    }

    public function setUsersecondaire(?User $usersecondaire): self
    {
        $this->usersecondaire = $usersecondaire;

        return $this;
    }

    public function getComptebanque()
    {
        return $this->comptebanque;
    }

    public function setComptebanque(?Comptebancaire $comptebanque): self
    {
        $this->comptebanque = $comptebanque;

        return $this;
    }

    public function getInfocomple(): ?string
    {
        return $this->infocomple;
    }

    public function setInfocomple(?string $infocomple): self
    {
        $this->infocomple = $infocomple;

        return $this;
    }

    public function getReponseemail(): ?string
    {
        return $this->reponseemail;
    }

    public function setReponseemail(?string $reponseemail): self
    {
        $this->reponseemail = $reponseemail;

        return $this;
    }

    public function getPasswordNonCripted(): ?string
    {
        return $this->passwordNonCripted;
    }

    public function setPasswordNonCripted(?string $passwordNonCripted): self
    {
        $this->passwordNonCripted = $passwordNonCripted;

        return $this;
    }

    public function getCnxparent(): ?int
    {
        return $this->cnxparent;
    }

    public function setCnxparent(?int $cnxparent): self
    {
        $this->cnxparent = $cnxparent;

        return $this;
    }

    public function getActivatemail(): ?int
    {
        return $this->activatemail;
    }

    public function setActivatemail(?int $activatemail): self
    {
        $this->activatemail = $activatemail;

        return $this;
    }

    public function getCnxpartenaire(): ?int
    {
        return $this->cnxpartenaire;
    }

    public function setCnxpartenaire(?int $cnxpartenaire): self
    {
        $this->cnxpartenaire = $cnxpartenaire;
        return $this;
    }

    /**
     * @return Collection|SejourAttachment[]
     */
    public function getSejourAttachments(): Collection
    {
        return $this->sejourAttachments;
    }

    public function addSejourAttachment(SejourAttachment $sejourAttachment): self
    {
        if (!$this->sejourAttachments->contains($sejourAttachment)) {
            $this->sejourAttachments[] = $sejourAttachment;
            $sejourAttachment->setIdParent($this);
        }

        return $this;
    }

    public function removeSejourAttachment(SejourAttachment $sejourAttachment): self
    {
        if ($this->sejourAttachments->contains($sejourAttachment)) {
            $this->sejourAttachments->removeElement($sejourAttachment);
            // set the owning side to null (unless already changed)
            if ($sejourAttachment->getIdParent() === $this) {
                $sejourAttachment->setIdParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Panier[]
     */
    public function getPaniers(): Collection
    {
        return $this->paniers;
    }

    public function addPanier(Panier $panier): self
    {
        if (!$this->paniers->contains($panier)) {
            $this->paniers[] = $panier;
            $panier->setCreerPar($this);
        }

        return $this;
    }

    public function removePanier(Panier $panier): self
    {
        if ($this->paniers->contains($panier)) {
            $this->paniers->removeElement($panier);
            // set the owning side to null (unless already changed)
            if ($panier->getCreerPar() === $this) {
                $panier->setCreerPar(null);
            }
        }

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateFlagDepot(): ?\DateTime
    {
        return $this->dateFlagDepot;
    }

    /**
     * @param \DateTime|null $dateFlagDepot
     */
    public function setDateFlagDepot(?\DateTime $dateFlagDepot): void
    {
        $this->dateFlagDepot = $dateFlagDepot;
    }

    /**
     * @return null|string
     */
    public function getFlagDepot(): ?string
    {
        return $this->flagDepot;
    }

    /**
     * @param null|string $flagDepot
     */
    public function setFlagDepot(?string $flagDepot): void
    {
        $this->flagDepot = $flagDepot;
    }

    /**
     * @return null|string
     */
    public function getAccompaplus(): ?string
    {
        return $this->accompaplus;
    }

    /**
     * @param null|string $accompaplus
     */
    public function setAccompaplus(?string $accompaplus): void
    {
        $this->accompaplus = $accompaplus;
    }

    public function getSmsnotif(): ?int
    {
        return $this->smsnotif;
    }

    public function setSmsnotif(?int $smsnotif): self
    {
        $this->smsnotif = $smsnotif;

        return $this;
    }

    public function getMailnotif(): ?int
    {
        return $this->mailnotif;
    }

    public function setMailnotif(?int $mailnotif): self
    {
        $this->mailnotif = $mailnotif;

        return $this;
    }

    public function hasFreeAlbum()
    {
        foreach ($this->mescommandes as $cmd) {
            if (($cmd->getStatuts()->getId() == 33) || ($cmd->getStatuts()->getId() == 38)) {
                $recalculePrixCommande = 0;
                foreach ($cmd->getCommandesProduits() as $produitCmd) {
                    $recalculePrixCommande = $recalculePrixCommande + ($produitCmd->getIdProduit()->getIdConditionnement()->getMontantTTC() * $produitCmd->getQuantiter());
                }
                if ($cmd->getMontanenv() != null) {
                    $recalculePrixCommande = $recalculePrixCommande + $cmd->getMontanenv();
                }
                if ($recalculePrixCommande != $cmd->getMontantrth()) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @return int|null
     */
    public function getAccoPlusToPartenaire(): ?int
    {
        return $this->AccoPlusToPartenaire;
    }

    /**
     * @param int|null $AccoPlusToPartenaire
     */
    public function setAccoPlusToPartenaire(?int $AccoPlusToPartenaire): void
    {
        $this->AccoPlusToPartenaire = $AccoPlusToPartenaire;
    }

    /**
     * @return Collection|Etiquette[]
     */
    public function getEtiquettes(): Collection
    {
        return $this->etiquettes;
    }

    public function addEtiquette(Etiquette $etiquette): self
    {
        if (!$this->etiquettes->contains($etiquette)) {
            $this->etiquettes[] = $etiquette;
            $etiquette->setSupport($this);
        }

        return $this;
    }

    public function removeEtiquette(Etiquette $etiquette): self
    {
        if ($this->etiquettes->contains($etiquette)) {
            $this->etiquettes->removeElement($etiquette);
            // set the owning side to null (unless already changed)
            if ($etiquette->getSupport() === $this) {
                $etiquette->setSupport(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Etiquette[]
     */
    public function getEtiquetteRapporteur(): Collection
    {
        return $this->etiquetteRapporteur;
    }

    public function addEtiquetteRapporteur(Etiquette $etiquetteRapporteur): self
    {
        if (!$this->etiquetteRapporteur->contains($etiquetteRapporteur)) {
            $this->etiquetteRapporteur[] = $etiquetteRapporteur;
            $etiquetteRapporteur->setRapporteur($this);
        }

        return $this;
    }

    public function removeEtiquetteRapporteur(Etiquette $etiquetteRapporteur): self
    {
        if ($this->etiquetteRapporteur->contains($etiquetteRapporteur)) {
            $this->etiquetteRapporteur->removeElement($etiquetteRapporteur);
            // set the owning side to null (unless already changed)
            if ($etiquetteRapporteur->getRapporteur() === $this) {
                $etiquetteRapporteur->setRapporteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CommentaireEtiquette[]
     */
    public function getCommentaireEtiquettes(): Collection
    {
        return $this->commentaireEtiquettes;
    }

    public function addCommentaireEtiquette(CommentaireEtiquette $commentaireEtiquette): self
    {
        if (!$this->commentaireEtiquettes->contains($commentaireEtiquette)) {
            $this->commentaireEtiquettes[] = $commentaireEtiquette;
            $commentaireEtiquette->setCreateur($this);
        }

        return $this;
    }

    public function removeCommentaireEtiquette(CommentaireEtiquette $commentaireEtiquette): self
    {
        if ($this->commentaireEtiquettes->contains($commentaireEtiquette)) {
            $this->commentaireEtiquettes->removeElement($commentaireEtiquette);
            // set the owning side to null (unless already changed)
            if ($commentaireEtiquette->getCreateur() === $this) {
                $commentaireEtiquette->setCreateur(null);
            }
        }

        return $this;
    }
    public function getShowpubprod()
    {
        return $this->showpubprod;
    }

    public function setShowpubprod($showpubprod): void
    {
        $this->showpubprod = $showpubprod;
    }

    public function getShowdetailsalbum(): ?string
    {
        return $this->showdetailsalbum;
    }

    public function getShowdetailslivre(): ?string
    {
        return $this->showdetailslivre;
    }

    public function getShowdetailsphotos(): ?string
    {
        return $this->showdetailsphotos;
    }

    public function getShowdetailsretros(): ?string
    {
        return $this->showdetailsretros;
    }

    public function getShowdetailscal(): ?string
    {
        return $this->showdetailscal;
    }

    public function setShowdetailsalbum(?string $showdetailsalbum): void
    {
        $this->showdetailsalbum = $showdetailsalbum;
    }

    public function setShowdetailslivre(?string $showdetailslivre): void
    {
        $this->showdetailslivre = $showdetailslivre;
    }

    public function setShowdetailsphotos(?string $showdetailsphotos): void
    {
        $this->showdetailsphotos = $showdetailsphotos;
    }

    public function setShowdetailsretros(?string $showdetailsretros): void
    {
        $this->showdetailsretros = $showdetailsretros;
    }

    public function setShowdetailscal(?string $showdetailscal): void
    {
        $this->showdetailscal = $showdetailscal;
    }

    public function getIdFonction(): ?Fonctions
    {
        return $this->idFonction;
    }

    public function setIdFonction(?Fonctions $idFonction): self
    {
        $this->idFonction = $idFonction;

        return $this;
    }

    public function getJetonAccoPlusSejourGratuit(): ?string
    {
        return $this->jetonAccoPlusSejourGratuit;
    }

    public function setJetonAccoPlusSejourGratuit(?string $jetonAccoPlusSejourGratuit): self
    {
        $this->jetonAccoPlusSejourGratuit = $jetonAccoPlusSejourGratuit;

        return $this;
    }

    /**
     * @return Collection|LogPromotions[]
     */
    public function getLogPromotions(): Collection
    {
        return $this->logPromotions;
    }

    public function addLogPromotion(LogPromotions $logPromotion): self
    {
        if (!$this->logPromotions->contains($logPromotion)) {
            $this->logPromotions[] = $logPromotion;
            $logPromotion->setIdClient($this);
        }

        return $this;
    }

    public function removeLogPromotion(LogPromotions $logPromotion): self
    {
        if ($this->logPromotions->contains($logPromotion)) {
            $this->logPromotions->removeElement($logPromotion);
            // set the owning side to null (unless already changed)
            if ($logPromotion->getIdClient() === $this) {
                $logPromotion->setIdClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PromoParents[]
     */
    public function getPromoParents(): Collection
    {
        return $this->promoParents;
    }

    public function addPromoParent(PromoParents $promoParent): self
    {
        if (!$this->promoParents->contains($promoParent)) {
            $this->promoParents[] = $promoParent;
            $promoParent->setParent($this);
        }

        return $this;
    }

    public function removePromoParent(PromoParents $promoParent): self
    {
        if ($this->promoParents->contains($promoParent)) {
            $this->promoParents->removeElement($promoParent);
            // set the owning side to null (unless already changed)
            if ($promoParent->getParent() === $this) {
                $promoParent->setParent(null);
            }
        }

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
            $photonsumerique->setIdUser($this);
        }

        return $this;
    }

    public function removePhotonsumerique(Photonsumeriques $photonsumerique): self
    {
        if ($this->photonsumeriques->contains($photonsumerique)) {
            $this->photonsumeriques->removeElement($photonsumerique);
            // set the owning side to null (unless already changed)
            if ($photonsumerique->getIdUser() === $this) {
                $photonsumerique->setIdUser(null);
            }
        }

        return $this;
    }
}
