<?php
/**
 * Created by PhpStorm.
 * User: Appsfact-02
 * Date: 15/05/2020
 * Time: 00:39
 */

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *@ORM\Table(name="CommandeFile")
 * @ORM\Entity(repositoryClass="App\Repository\CommandeFileRepository")
 */
class CommandeFile
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
private $id;
    /**
     * @ORM\Column(name="noFacture",type="string", length=255, nullable=true)
     */
private $noFacture;

    /**
     * @ORM\Column(name="noCommande",type="string", length=255, nullable=true)
     */
    private $noCommande;
    /**
     * @ORM\Column(name="civilite",type="string", length=255, nullable=true)
     */
private $civilite;
    /**
     * @ORM\Column(name="nom",type="string", length=255, nullable=true)
     */
private $nom;
    /**
     * @ORM\Column(name="prenom",type="string", length=255, nullable=true)
     */
private $prenom;
    /**
     * @ORM\Column(name="mail",type="string", length=255, nullable=true)
     */
private $mail;
    /**
     * @ORM\Column(name="adresse",type="string", length=255, nullable=true)
     */
private $adresse;
    /**
     * @ORM\Column(name="complementAdresse",type="string", length=255, nullable=true)
     */
private $complementAdresse;
    /**
     * @ORM\Column(name="codePostal",type="string", length=255, nullable=true)
     */
private $codePostal;
    /**
     * @ORM\Column(name="ville",type="string", length=255, nullable=true)
     */
private $ville;
    /**
     * @ORM\Column(name="codePays",type="string", length=255, nullable=true)
     */
private $codePays;
    /**
     * @ORM\Column(name="pays",type="string", length=255, nullable=true)
     */
private $pays;
    /**
     * @ORM\Column(name="sejour",type="string", length=255, nullable=true)
     */
private $sejour;
    /**
     * @ORM\Column(name="typeEnvoi",type="string", length=255, nullable=true)
     */
private $typeEnvoi;
    /**
     * @ORM\Column(name="fichierImpression",type="string", length=255, nullable=true)
     */
private $fichierImpression;
    /**
     * @ORM\Column(name="produit",type="string", length=255, nullable=true)
     */
private $produit;
    /**
     * @ORM\Column(name="albumPhotos",type="string", length=255, nullable=true)
     */
private $albumPhotos;
    /**
     * @ORM\Column(name="livreSouvenirs",type="string", length=255, nullable=true)
     */
private $livreSouvenirs;
    /**
     * @ORM\Column(name="calendrier",type="string", length=255, nullable=true)
     */
private $calendrier;
    /**
     * @ORM\Column(name="polaroid",type="string", length=255, nullable=true)
     */
private $polaroid;
/**
 * @ORM\Column(name="formatPolaroid",type="string", length=255, nullable=true)
 */
private $formatPolaroid;
    /**
     * @ORM\Column(name="packPhoto",type="string", length=255, nullable=true)
     */
private $packPhoto;
    /**
     * @ORM\Column(name="formatPackPhoto",type="string", length=255, nullable=true)
     */
private $formatPackPhoto;
/**
 * @ORM\Column(name="coffretRigide",type="string", length=255, nullable=true)
 */
private $coffretRigide;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getNoFacture()
    {
        return $this->noFacture;
    }

    /**
     * @param mixed $noFacture
     */
    public function setNoFacture($noFacture): void
    {
        $this->noFacture = $noFacture;
    }

    /**
     * @return mixed
     */
    public function getNoCommande()
    {
        return $this->noCommande;
    }

    /**
     * @param mixed $noCommande
     */
    public function setNoCommande($noCommande): void
    {
        $this->noCommande = $noCommande;
    }

    /**
     * @return mixed
     */
    public function getCivilite()
    {
        return $this->civilite;
    }

    /**
     * @param mixed $civilite
     */
    public function setCivilite($civilite): void
    {
        $this->civilite = $civilite;
    }

    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @return mixed
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * @param mixed $prenom
     */
    public function setPrenom($prenom): void
    {
        $this->prenom = $prenom;
    }

    /**
     * @return mixed
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param mixed $mail
     */
    public function setMail($mail): void
    {
        $this->mail = $mail;
    }

    /**
     * @return mixed
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * @param mixed $adresse
     */
    public function setAdresse($adresse): void
    {
        $this->adresse = $adresse;
    }

    /**
     * @return mixed
     */
    public function getComplementAdresse()
    {
        return $this->complementAdresse;
    }

    /**
     * @param mixed $complementAdresse
     */
    public function setComplementAdresse($complementAdresse): void
    {
        $this->complementAdresse = $complementAdresse;
    }

    /**
     * @return mixed
     */
    public function getCodePostal()
    {
        return $this->codePostal;
    }

    /**
     * @param mixed $codePostal
     */
    public function setCodePostal($codePostal): void
    {
        $this->codePostal = $codePostal;
    }

    /**
     * @return mixed
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * @param mixed $ville
     */
    public function setVille($ville): void
    {
        $this->ville = $ville;
    }

    /**
     * @return mixed
     */
    public function getCodePays()
    {
        return $this->codePays;
    }

    /**
     * @param mixed $codePays
     */
    public function setCodePays($codePays): void
    {
        $this->codePays = $codePays;
    }

    /**
     * @return mixed
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * @param mixed $pays
     */
    public function setPays($pays): void
    {
        $this->pays = $pays;
    }

    /**
     * @return mixed
     */
    public function getSejour()
    {
        return $this->sejour;
    }

    /**
     * @param mixed $sejour
     */
    public function setSejour($sejour): void
    {
        $this->sejour = $sejour;
    }

    /**
     * @return mixed
     */
    public function getTypeEnvoi()
    {
        return $this->typeEnvoi;
    }

    /**
     * @param mixed $typeEnvoi
     */
    public function setTypeEnvoi($typeEnvoi): void
    {
        $this->typeEnvoi = $typeEnvoi;
    }

    /**
     * @return mixed
     */
    public function getFichierImpression()
    {
        return $this->fichierImpression;
    }

    /**
     * @param mixed $fichierImpression
     */
    public function setFichierImpression($fichierImpression): void
    {
        $this->fichierImpression = $fichierImpression;
    }

    /**
     * @return mixed
     */
    public function getProduit()
    {
        return $this->produit;
    }

    /**
     * @param mixed $produit
     */
    public function setProduit($produit): void
    {
        $this->produit = $produit;
    }

    /**
     * @return mixed
     */
    public function getAlbumPhotos()
    {
        return $this->albumPhotos;
    }

    /**
     * @param mixed $albumPhotos
     */
    public function setAlbumPhotos($albumPhotos): void
    {
        $this->albumPhotos = $albumPhotos;
    }

    /**
     * @return mixed
     */
    public function getLivreSouvenirs()
    {
        return $this->livreSouvenirs;
    }

    /**
     * @param mixed $livreSouvenirs
     */
    public function setLivreSouvenirs($livreSouvenirs): void
    {
        $this->livreSouvenirs = $livreSouvenirs;
    }

    /**
     * @return mixed
     */
    public function getCalendrier()
    {
        return $this->calendrier;
    }

    /**
     * @param mixed $calendrier
     */
    public function setCalendrier($calendrier): void
    {
        $this->calendrier = $calendrier;
    }

    /**
     * @return mixed
     */
    public function getPolaroid()
    {
        return $this->polaroid;
    }

    /**
     * @param mixed $polaroid
     */
    public function setPolaroid($polaroid): void
    {
        $this->polaroid = $polaroid;
    }

    /**
     * @return mixed
     */
    public function getFormatPolaroid()
    {
        return $this->formatPolaroid;
    }

    /**
     * @param mixed $formatPolaroid
     */
    public function setFormatPolaroid($formatPolaroid): void
    {
        $this->formatPolaroid = $formatPolaroid;
    }

    /**
     * @return mixed
     */
    public function getPackPhoto()
    {
        return $this->packPhoto;
    }

    /**
     * @param mixed $packPhoto
     */
    public function setPackPhoto($packPhoto): void
    {
        $this->packPhoto = $packPhoto;
    }

    /**
     * @return mixed
     */
    public function getFormatPackPhoto()
    {
        return $this->formatPackPhoto;
    }

    /**
     * @param mixed $formatPackPhoto
     */
    public function setFormatPackPhoto($formatPackPhoto): void
    {
        $this->formatPackPhoto = $formatPackPhoto;
    }

    /**
     * @return mixed
     */
    public function getCoffretRigide()
    {
        return $this->coffretRigide;
    }

    /**
     * @param mixed $coffretRigide
     */
    public function setCoffretRigide($coffretRigide): void
    {
        $this->coffretRigide = $coffretRigide;
    }


}