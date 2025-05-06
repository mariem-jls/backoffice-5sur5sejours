<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CommandeComptable
 *
 * @ORM\Table(name="CommandeComptable")
 * @ORM\Entity(repositoryClass="App\Repository\CommandeComptableRespository")
 */
class CommandeComptable {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="idcmd", type="integer", nullable=true)
     */
    private $idcmd;

    /**
     * @var int|null
     *
     * @ORM\Column(name="idclient", type="integer", nullable=true)
     */
    private $idclient;

    /**
     * @ORM\Column(name="client",type="string", length=255, nullable=true)
     */
    private $client ;

    /**
     * @ORM\Column(name="numfacture",type="float", nullable=true)
     */
    private $numfacture;
    
        /**
     * @ORM\Column(name="datefacture",type="date", nullable=true)
     */
    private $datefacture;
    
        /**
     * @ORM\Column(name="datecmd",type="date", nullable=true)
     */
    private $datecmd;

    /**
     * @var int|null
     *
     * @ORM\Column(name="numcmd", type="integer", nullable=true)
     */
    private $numcmd;

    /**
     * @ORM\Column(name="quantite",type="string", length=255, nullable=true)
     */
    private $quantite ;
    
    /**
     * @ORM\Column(name="produits", type="text",nullable=true)
     */
    private $produits;

    /**
     * @ORM\Column(name="montant_ttc",type="float", nullable=true)
     */
    private $montant_ttc;

    /**
     * @ORM\Column(name="frais_expedition",type="float", nullable=true)
     */
    private $frais_expedition;

    /**
     * @ORM\Column(name="montant_ht",type="float", nullable=true)
     */
    private $montant_ht;

    /**
     * @ORM\Column(name="tva",type="float", nullable=true)
     */
    private $tva;

    /**
     * @ORM\Column(name="moyen_paiement",type="string", length=255, nullable=true)
     */
    private $moyen_paiement ;
    
    function getId() {
        return $this->id;
    }

    function getIdcmd() {
        return $this->idcmd;
    }

    function getIdclient() {
        return $this->idclient;
    }

    function getClient() {
        return $this->client;
    }

    function getNumfacture() {
        return $this->numfacture;
    }

    function getDatefacture() {
        return $this->datefacture;
    }

    function getDatecmd() {
        return $this->datecmd;
    }

    function getNumcmd() {
        return $this->numcmd;
    }

    function getQuantite() {
        return $this->quantite;
    }

    function getProduits() {
        return $this->produits;
    }

    function getMontant_ttc() {
        return $this->montant_ttc;
    }

    function getFrais_expedition() {
        return $this->frais_expedition;
    }

    function getMontant_ht() {
        return $this->montant_ht;
    }

    function getTva() {
        return $this->tva;
    }

    function getMoyen_paiement() {
        return $this->moyen_paiement;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setIdcmd($idcmd) {
        $this->idcmd = $idcmd;
    }

    function setIdclient($idclient) {
        $this->idclient = $idclient;
    }

    function setClient($client) {
        $this->client = $client;
    }

    function setNumfacture($numfacture) {
        $this->numfacture = $numfacture;
    }

    function setDatefacture($datefacture) {
        $this->datefacture = $datefacture;
    }

    function setDatecmd($datecmd) {
        $this->datecmd = $datecmd;
    }

    function setNumcmd($numcmd) {
        $this->numcmd = $numcmd;
    }

    function setQuantite($quantite) {
        $this->quantite = $quantite;
    }

    function setProduits($produits) {
        $this->produits = $produits;
    }

    function setMontant_ttc($montant_ttc) {
        $this->montant_ttc = $montant_ttc;
    }

    function setFrais_expedition($frais_expedition) {
        $this->frais_expedition = $frais_expedition;
    }

    function setMontant_ht($montant_ht) {
        $this->montant_ht = $montant_ht;
    }

    function setTva($tva) {
        $this->tva = $tva;
    }

    function setMoyen_paiement($moyen_paiement) {
        $this->moyen_paiement = $moyen_paiement;
    }


}
