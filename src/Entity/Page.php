<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="page", indexes={@ORM\Index(name="fk_typpage_idx", columns={"idtypepage"}), @ORM\Index(name="fk_produit_page_idx", columns={"idproduit"}), @ORM\Index(name="fk_pagesuivante_idx", columns={"pagesuivante"}) , @ORM\Index(name="fk_pageprecedente_idx", columns={"pageprecedente"})})
 * @ORM\Entity(repositoryClass="App\Repository\PageRepository")
 */
class Page
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

     /**
     * @var \Typepage
     *
     * @ORM\ManyToOne(targetEntity="Typepage")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idtypepage", referencedColumnName="id")
     * })
     */
    private $idtypepage;

    /**
     * @var \Produit
     *
     * @ORM\ManyToOne(targetEntity="Produit")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idproduit", referencedColumnName="id")
     * })
     */
    private $idproduit;

    /**
     * @var \Page
     *
     * @ORM\ManyToOne(targetEntity="Page")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="pagesuivante", referencedColumnName="id")
     * })
     */
    private $pagesuivante;

     /**
     * @var \Page
     *
     * @ORM\ManyToOne(targetEntity="Page")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="pageprecedente", referencedColumnName="id")
     * })
     */
    private $pageprecedente;

    /**
     * @ORM\Column(type="text", length=255, nullable=true)
     */
    private $couleurbordure;

    



    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdtypepage(): ?Typepage
    {
        return $this->idtypepage;
    }

    public function setIdtypepage(?Typepage $idtypepage): self
    {
        $this->idtypepage = $idtypepage;

        return $this;
    }

    public function getIdproduit(): ?Produit
    {
        return $this->idproduit;
    }

    public function setIdproduit(?Produit $idproduit): self
    {
        $this->idproduit = $idproduit;

        return $this;
    }

    public function getPagesuivante(): ?Page
    {
        return $this->pagesuivante;
    }

    public function setPagesuivante(?Page $pagesuivante): self
    {
        $this->pagesuivante = $pagesuivante;

        return $this;
    }

    public function getPageprecedente(): ?Page
    {
        return $this->pageprecedente;
    }

    public function setPageprecedente(?Page $pageprecedente): self
    {
        $this->pageprecedente = $pageprecedente;

        return $this;
    }

    public function getCouleurbordure()
    {
        return $this->couleurbordure;
    }

    public function setCouleurbordure( $couleurbordure): self
    {
        $this->couleurbordure = $couleurbordure;

        return $this;
    }

    
}
