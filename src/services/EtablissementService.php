<?php

namespace App\Service;

use App\Entity\Ref;
use App\Entity\Page;
use App\Entity\User;
use App\Entity\Sejour;
use App\Entity\Produit;
use App\Entity\Commande;
use App\Entity\Likephoto;
use App\Entity\Attachment;
use App\Entity\Etablisment;
use App\Entity\Typeproduit;
use App\Entity\ParentSejour;
use App\Entity\ComandeProduit;
use App\Entity\Photonsumeriques;
use App\Entity\SejourAttachment;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\TypeProduitConditionnement;
use Doctrine\Persistence\ManagerRegistry;


class EtablissementService
{
    private $em;
    public function __construct(ManagerRegistry $em)
    {
        $this->em = $em;
    }
    function CreationNouveauxEtablissement($nomEtab, $adressEtab, $codePostale, $nomContact, $prenomContact, $Type, $numTel, $fonctionTel, $email, $prixcnxparent, $prixcnxpartenaire, $reversecnxpart, $reverseventepart)
    {
        $etablissement = new Etablisment();
        $etablissement->setNometab($nomEtab);
        $etablissement->setAdresseetab($adressEtab);
        $etablissement->setCodepostaleatb($codePostale);
        $etablissement->setNomcon($nomContact);
        $etablissement->setPrenomcont($prenomContact);
        $etablissement->setTypeetablisment($Type);
        $etablissement->setNumerotelp($numTel);
        $etablissement->setFonctioncontact($fonctionTel);
        $etablissement->setEmail(trim($email));
        $etablissement->setPrixcnxparent($prixcnxparent);
        $etablissement->setPrixcnxpartenaire($prixcnxpartenaire);
        $etablissement->setReversecnxpart($reversecnxpart);
        $etablissement->setReverseventepart($reverseventepart);
        $this->em->getManager()->persist($etablissement);
        $this->em->getManager()->flush();
        return ($etablissement);
    }
    function CreationEtablissement($type, $nomEtab, $adressEtab, $codePostale, $fonctionTel, $ville, $pays, $prixcnxparent, $prixcnxpartenaire, $reversecnxpart, $reverseventepart, $user)
    {
        if ($prixcnxparent == "") {
            $prixcnxparent = 0;
        }
        if ($prixcnxpartenaire == "") {
            $prixcnxpartenaire = 0;
        }
        if ($reversecnxpart == "") {
            $reversecnxpart = 0;
        }
        if ($reverseventepart == "") {
            $reverseventepart = 0;
        }
        $etablissement = new Etablisment();
        $etablissement->setTypeetablisment($type);
        $etablissement->setNometab($nomEtab);
        $etablissement->setAdresseetab($adressEtab);
        $etablissement->setCodepostaleatb($codePostale);
        $etablissement->setFonctioncontact($fonctionTel);
        $etablissement->setVille($ville);
        $etablissement->setPays($pays);
        $etablissement->setPrixcnxparent($prixcnxparent);
        $etablissement->setPrixcnxpartenaire($prixcnxpartenaire);
        $etablissement->setReversecnxpart($reversecnxpart);
        $etablissement->setReverseventepart($reverseventepart);
        $etablissement->setUser($user);
        $this->em->getManager()->persist($etablissement);
        $this->em->getManager()->flush();
        return ($etablissement);
    }
    function CreationEtablissementAccoPlus($etablisment, $adressetablisment,  $nom, $prenom, $phone, $email)
    {
        $prixcnxparent = 2.9;
        $prixcnxpartenaire = 0;
        $reversecnxpart = 0;
        $reverseventepart = 0;
        $type = "ECOLES/AUTRES";
        $etablissement = new Etablisment();
        $etablissement->setTypeetablisment($type);
        $etablissement->setNometab($etablisment);
        $etablissement->setNomcon($nom);
        $etablissement->setPrenomcont($prenom);
        $etablissement->setEmail($email);
        $etablissement->setNumerotelp($phone);
        $etablissement->setAdresseetab($adressetablisment);
        $etablissement->setPrixcnxparent($prixcnxparent);
        $etablissement->setPrixcnxpartenaire($prixcnxpartenaire);
        $etablissement->setReversecnxpart($reversecnxpart);
        $etablissement->setReverseventepart($reverseventepart);
        $this->em->getManager()->persist($etablissement);
        $this->em->getManager()->flush();
        return ($etablissement);
    }
    function updateEtablissement($id, $type, $nomEtab, $fonctionTel, $adressEtab, $codePostale, $ville, $pays, $prixcnxparent, $prixcnxpartenaire, $reversecnxpart, $reverseventepart, $logo)
    {
        //$partn = $this->em->getRepository(User::class)->find($id);
        //        dd($id);
        $etablissement = $this->em->getRepository(Etablisment::class)->find($id);
        $etablissement->setTypeetablisment($type);
        $etablissement->setNometab($nomEtab);
        $etablissement->setFonctioncontact($fonctionTel);
        $etablissement->setAdresseetab($adressEtab);
        $etablissement->setCodepostaleatb($codePostale);
        $etablissement->setVille($ville);
        $etablissement->setPays($pays);
        $etablissement->setLogo($logo);
        $etablissement->setPrixcnxparent($prixcnxparent);
        $etablissement->setPrixcnxpartenaire($prixcnxpartenaire);
        $etablissement->setReversecnxpart($reversecnxpart);
        $etablissement->setReverseventepart($reverseventepart);
        //$etablissement->setUser($user);
        $this->em->getManager()->persist($etablissement);
        $this->em->getManager()->flush();
        return ($etablissement);
    }
    function updateEtablissementp($id, $type, $nomEtab, $fonctionTel, $adressEtab, $codePostale, $ville)
    {
        //$partn = $this->em->getRepository(User::class)->find($id);
        $etablissement = $this->em->getRepository(Etablisment::class)->find($id);
        $etablissement->setTypeetablisment($type);
        $etablissement->setNometab($nomEtab);
        $etablissement->setFonctioncontact($fonctionTel);
        $etablissement->setAdresseetab($adressEtab);
        $etablissement->setCodepostaleatb($codePostale);
        $etablissement->setVille($ville);
        //$etablissement->setUser($user);
        $this->em->getManager()->persist($etablissement);
        $this->em->getManager()->flush();
        return ($etablissement);
    }
    function affecterRepresentant($etablissement, $user)
    {
        $etablissement->setUser($user);
        $this->em->getManager()->persist($etablissement);
        $this->em->getManager()->flush();
    }
    function getEtablissement($id)
    {
        $etablissement = $this->em->getRepository(Etablisment::class)->find($id);
        return $etablissement;
    }
    function getEtablissementAll()
    {
        $etablissement = $this->em->getRepository(Etablisment::class)->findNotDeletedEtab();
        return $etablissement;
    }
    function getEtablissementPartenaire()
    {
        $etablissement = $this->em->getRepository(Etablisment::class)->findByOrder();
        return $etablissement;
    }
    function getEtablissementPartenaireFiltre()
    {
        $etablissement = $this->em->getRepository(Etablisment::class)->findFiltredEtab();
        return $etablissement;
    }
    function getEtablissementPartenaire1($user)
    {
        $etablissement = $this->em->getRepository(Etablisment::class)->findBy(array("user" => $user));
        return $etablissement;
    }
    function supprisionimageadminfon($id)
    {
        $atach = $this->em->getRepository(SejourAttachment::class)->findBy(array('idAttchment' => $id));
        foreach ($atach as $e) {
            $like = $this->em->getRepository(Likephoto::class)->findBy(array('idSejourAttchment' => $e->getId()));
            foreach ($like as $l) {
                $this->em->getManager()->remove($l);
                $this->em->getManager()->flush();
            }
            $this->em->getManager()->remove($e);
            $this->em->getManager()->flush();
        }
        $photo = $this->em->getRepository(Attachment::class)->find($id);
        $this->em->getManager()->remove($photo);
        $this->em->getManager()->flush();
        return $atach;
    }
    /********** ENREGISTRER LE LIVRE DE SEJOUR  ****************************/
    function saveLivreSejour($page, $user, $sejour, $prodid, $stat, $nomprod, $versionalbm = null)
    {
         $sejourobj = $this->em->getRepository(Sejour::class)->find($sejour);
        //        dd($prodid);
        if ($stat == "saved") {
            $Alllivres = $this->em->getRepository(Produit::class)->findby(["idsjour" => $sejourobj->getId(), "iduser" => $user->getId(), "statut" => "saved", "type" => 2]);
            foreach ($Alllivres as $albm) {
                $albm->setStatut("draft");
                $this->em->getManager()->persist($albm);
                $this->em->getManager()->flush();
            }
        }
        if ($stat == "Livre_sejour") {
            $Alllivres = $this->em->getRepository(Produit::class)->findby(["idsjour" => $sejourobj->getId(), "iduser" => 1, "statut" => "Livre_sejour"]);
            foreach ( $Alllivres  as $albm) {
                $albm->setStatut("draft");
                $this->em->getManager()->persist($albm);
                $this->em->getManager()->flush();
            }
        }
        
        if ($prodid != null && $prodid != "") {
            $Produit = $this->em->getRepository(Produit::class)->find($prodid);
            $Produit->setIduser($user);
            $Produit->setStatut("Livre_sejour");
            $Produit->setIdsjour($sejourobj);
            $Produit->setLabele($nomprod);
            $Produit->setVersion($versionalbm);
            $this->em->getManager()->persist($Produit);
            $this->em->getManager()->flush();
            $AllPages = $this->em->getRepository(Page::class)->findBy(array('idproduit' => $Produit), array('id' => 'ASC'));
            //dd($AllPages);
            if (sizeof($AllPages) != 0) {
                foreach ($AllPages as $key1 => $pag) {
                    foreach ($page as  $key2 => $e) {
                        if ($key1 == $key2) {
                            $pag->setIdproduit($Produit);
                            $pag->setCouleurbordure(json_encode($e));
                            $this->em->getManager()->persist($pag);
                            $this->em->getManager()->flush();
                        }
                    }
                }
            } else {
                foreach ($page as $e) {
                    $x = new Page();
                    $x->setIdproduit($Produit);
                    $x->setCouleurbordure(json_encode($e));
                    $this->em->getManager()->persist($x);
                    $this->em->getManager()->flush();
                }
            }
        } else {
            $Conditionnement = $this->em->getRepository(TypeProduitConditionnement::class)->find(5);
            $prodct = $this->em->getRepository(Typeproduit::class)->find(4);
            $Produit = new Produit();
            $Produit->setIduser($user);
            $Produit->setIdsjour($sejourobj);
            $Produit->setDate(new \DateTime());
            $Produit->setStatut($stat);
            $Produit->setIdConditionnement($Conditionnement);
            $Produit->setLabele("Livre du séjour");
            $Produit->setType($prodct);
            $Produit->setVersion("Livre_V24");
            $this->em->getManager()->persist($Produit);
            $this->em->getManager()->flush();
            foreach ($page as $e) {
                $page = new Page();
                $page->setIdproduit($Produit);
                $page->setCouleurbordure(json_encode($e));
                $this->em->getManager()->persist($page);
                $this->em->getManager()->flush();
            }
        }
        return ($Produit);
    }
    function savealbumAcc($page, $user, $sejour, $prodid, $stat, $nomprod, $versionalbm = null)
    {
        $AlbumGtrauie = $this->em->getRepository(Produit::class)->findOneBy(["idsjour" => $sejour, "iduser" => $user]);
        $Typeproduit = $this->em->getRepository(Typeproduit::class)->find(2);
        $version = $this->em->getRepository(Produit::class)->SearchVersionproduit($user->getId());
        $sejourobj = $this->em->getRepository(Sejour::class)->find($sejour);
        $version = $this->em->getRepository(Produit::class)->SearchVersionproduit($user->getId());
        //        dd($prodid);
        if ($stat == "saved") {
            $Allabum = $this->em->getRepository(Produit::class)->findby(["idsjour" => $sejourobj->getId(), "iduser" => $user->getId(), "statut" => "saved", "type" => 2]);
            foreach ($Allabum as $albm) {
                $albm->setStatut("draft");
                $this->em->getManager()->persist($albm);
                $this->em->getManager()->flush();
            }
        }
        if ($stat == "Album_sejour") {
            $Allabum = $this->em->getRepository(Produit::class)->findby(["idsjour" => $sejourobj->getId(), "iduser" => 1, "statut" => "Album_sejour", "type" => 2]);
            foreach ($Allabum as $albm) {
                $albm->setStatut("draft");
                $this->em->getManager()->persist($albm);
                $this->em->getManager()->flush();
            }
        }
        
        if ($prodid != null && $prodid != "") {
            $Produit = $this->em->getRepository(Produit::class)->find($prodid);
            $Produit->setIduser($user);
            $Produit->setStatut($stat);
            $Produit->setIdsjour($sejourobj);
             $Produit->setLabele($nomprod);
             $Produit->setVersion($versionalbm);
            $this->em->getManager()->persist($Produit);
            $this->em->getManager()->flush();
            $AllPages = $this->em->getRepository(Page::class)->findBy(array('idproduit' => $Produit), array('id' => 'ASC'));
            //dd($AllPages);
            if (sizeof($AllPages) != 0) {
                foreach ($AllPages as $key1 => $pag) {
                    foreach ($page as  $key2 => $e) {
                        if ($key1 == $key2) {
                            $pag->setIdproduit($Produit);
                            $pag->setCouleurbordure(json_encode($e));
                            $this->em->getManager()->persist($pag);
                            $this->em->getManager()->flush();
                        }
                    }
                }
            } else {
                foreach ($page as $e) {
                    $x = new Page();
                    $x->setIdproduit($Produit);
                    $x->setCouleurbordure(json_encode($e));
                    $this->em->getManager()->persist($x);
                    $this->em->getManager()->flush();
                }
            }
        } else {
            $Conditionnement = $this->em->getRepository(TypeProduitConditionnement::class)->find(4);
            $prodct = $this->em->getRepository(Typeproduit::class)->find(2);
            $Produit = new Produit();
            $Produit->setIduser($user);
            $Produit->setIdsjour($sejourobj);
            $Produit->setDate(new \DateTime());
            $Produit->setStatut($stat);
            $Produit->setIdConditionnement($Conditionnement);
            $Produit->setLabele($nomprod);
            $Produit->setType($prodct);
            $Produit->setVersion($versionalbm);
            $this->em->getManager()->persist($Produit);
            $this->em->getManager()->flush();
            foreach ($page as $e) {
                $page = new Page();
                $page->setIdproduit($Produit);
                $page->setCouleurbordure(json_encode($e));
                $this->em->getManager()->persist($page);
                $this->em->getManager()->flush();
            }
        }
        return ($Produit);
    }
    
    function saveCopyalbumParent($page, $user, $sejour, $prodid, $typproduit, $x, $nomprod, $versionalbm)
    {
        $sejourobj = $this->em->getRepository(Sejour::class)->find($sejour);
        $version = $this->em->getRepository(Produit::class)->SearchVersionproduit($user->getId());
        $prodct = $this->em->getRepository(Typeproduit::class)->find(2);
        if ($prodid != null && $prodid != "") {
            $ProduitOriginal = $this->em->getRepository(Produit::class)->find($prodid);
            $Produit = clone $ProduitOriginal;
            $Produit->setIduser($user);
            $Produit->setIdsjour($sejourobj);
            //     $Produit->setLabele($nomprod);
            //    $Produit->setVersion($versionalbm);
            // $Produit->setLabele($prodct->getLabeletype()." ".$version);
            //$Produit->setType($prodct);
            $this->em->getManager()->persist($Produit);
            $this->em->getManager()->flush();
            $AllPages = $this->em->getRepository(Page::class)->findBy(array('idproduit' => $Produit), array('id' => 'ASC'));
            //dd($AllPages);
            if (sizeof($AllPages) != 0) {
                foreach ($AllPages as $key1 => $pag) {
                    foreach ($page as  $key2 => $e) {
                        if ($key1 == $key2) {
                            $pag->setIdproduit($Produit);
                            $pag->setCouleurbordure(json_encode($e));
                            $this->em->getManager()->persist($pag);
                            $this->em->getManager()->flush();
                        }
                    }
                }
            } else {
                foreach ($page as $e) {
                    $x = new Page();
                    $x->setIdproduit($Produit);
                    $x->setCouleurbordure(json_encode($e));
                    $this->em->getManager()->persist($x);
                    $this->em->getManager()->flush();
                }
            }
        } else {
            $Conditionnement = $this->em->getRepository(TypeProduitConditionnement::class)->find(4);
            $Produit = new Produit();
            $Produit->setIduser($user);
            $Produit->setIdsjour($sejourobj);
            $Produit->setIdConditionnement($Conditionnement);
            $Produit->setLabele($nomprod);
            $Produit->setDate(new \DateTime());
            $Produit->setType($prodct);
            $Produit->setVersion($versionalbm);
            $this->em->getManager()->persist($Produit);
            $this->em->getManager()->flush();
            foreach ($page as $e) {
                $page = new Page();
                $page->setIdproduit($Produit);
                $page->setCouleurbordure(json_encode($e));
                $this->em->getManager()->persist($page);
                $this->em->getManager()->flush();
            }
        }
        return ($Produit);
    }
    function savealbumParent($page, $user, $sejour, $prodid, $typproduit, $x, $nomprod, $versionalbm)
    {
        $sejourobj = $this->em->getRepository(Sejour::class)->find($sejour);
        $version = $this->em->getRepository(Produit::class)->SearchVersionproduit($user->getId());
        $prodct = $this->em->getRepository(Typeproduit::class)->find(2);
        if ($prodid != null && $prodid != "") {
            $Produit = $this->em->getRepository(Produit::class)->find($prodid);
            $Produit->setIduser($user);
            $Produit->setIdsjour($sejourobj);
            //     $Produit->setLabele($nomprod);
            //    $Produit->setVersion($versionalbm);
            // $Produit->setLabele($prodct->getLabeletype()." ".$version);
            //$Produit->setType($prodct);
            $this->em->getManager()->persist($Produit);
            $this->em->getManager()->flush();
            $AllPages = $this->em->getRepository(Page::class)->findBy(array('idproduit' => $Produit), array('id' => 'ASC'));
            //dd($AllPages);
            if (sizeof($AllPages) != 0) {
                foreach ($AllPages as $key1 => $pag) {
                    foreach ($page as  $key2 => $e) {
                        if ($key1 == $key2) {
                            $pag->setIdproduit($Produit);
                            $pag->setCouleurbordure(json_encode($e));
                            $this->em->getManager()->persist($pag);
                            $this->em->getManager()->flush();
                        }
                    }
                }
            } else {
                foreach ($page as $e) {
                    $x = new Page();
                    $x->setIdproduit($Produit);
                    $x->setCouleurbordure(json_encode($e));
                    $this->em->getManager()->persist($x);
                    $this->em->getManager()->flush();
                }
            }
        } else {
            $Conditionnement = $this->em->getRepository(TypeProduitConditionnement::class)->find(4);
            $Produit = new Produit();
            $Produit->setIduser($user);
            $Produit->setIdsjour($sejourobj);
            $Produit->setIdConditionnement($Conditionnement);
            $Produit->setLabele($nomprod);
            $Produit->setDate(new \DateTime());
            $Produit->setType($prodct);
            $Produit->setVersion($versionalbm);
            $this->em->getManager()->persist($Produit);
            $this->em->getManager()->flush();
            foreach ($page as $e) {
                $page = new Page();
                $page->setIdproduit($Produit);
                $page->setCouleurbordure(json_encode($e));
                $this->em->getManager()->persist($page);
                $this->em->getManager()->flush();
            }
        }
        return ($Produit);
    }
    function SavePhotosPochetteParent($page, $user, $sejour, $prodid, $typproduit, $x, $nbr, $nomprod = null)
    {
        $sejourobj = $this->em->getRepository(Sejour::class)->find($sejour);
        $version = $this->em->getRepository(Produit::class)->SearchVersionproduit($user->getId());
        $prodct = $this->em->getRepository(Typeproduit::class)->find(10);
        if ($prodid != null && $prodid != "") {
            $Produit = $this->em->getRepository(Produit::class)->find($prodid);
            $Produit->setIduser($user);
            $Produit->setIdsjour($sejourobj);
            $Produit->setLabele($nomprod);
            // $Produit->setLabele($prodct->getLabeletype()." ".$version);
            //$Produit->setType($prodct);
            $this->em->getManager()->persist($Produit);
            $this->em->getManager()->flush();
            $AllPages = $this->em->getRepository(Page::class)->findBy(array('idproduit' => $Produit), array('id' => 'ASC'));
            //dd($AllPages);
            if (sizeof($AllPages) != 0) {
                foreach ($AllPages as $key1 => $pag) {
                    foreach ($page as  $key2 => $e) {
                        if ($key1 == $key2) {
                            $pag->setIdproduit($Produit);
                            $pag->setCouleurbordure(json_encode($e));
                            $this->em->getManager()->persist($pag);
                            $this->em->getManager()->flush();
                        }
                    }
                }
            } else {
                foreach ($page as $e) {
                    $x = new Page();
                    $x->setIdproduit($Produit);
                    $x->setCouleurbordure(json_encode($e));
                    $this->em->getManager()->persist($x);
                    $this->em->getManager()->flush();
                }
            }
        } else {
            if (($nbr) == '12') {
                $Conditionnement = $this->em->getRepository(TypeProduitConditionnement::class)->find(1);
            } elseif (($nbr) == '24') {
                $Conditionnement = $this->em->getRepository(TypeProduitConditionnement::class)->find(2);
            } elseif (($nbr) == '36') {
                $Conditionnement = $this->em->getRepository(TypeProduitConditionnement::class)->find(3);
            }
            $Produit = new Produit();
            $Produit->setIduser($user);
            $Produit->setIdsjour($sejourobj);
            $Produit->setIdConditionnement($Conditionnement);
            $Produit->setLabele($nomprod);
            $Produit->setDate(new \DateTime());
            $Produit->setType($prodct);
            $this->em->getManager()->persist($Produit);
            $this->em->getManager()->flush();
            foreach ($page as $e) {
                $page = new Page();
                $page->setIdproduit($Produit);
                $page->setCouleurbordure(json_encode($e));
                $this->em->getManager()->persist($page);
                $this->em->getManager()->flush();
            }
        }
        return ($Produit);
    }
    function SavePhotosRetrosParents($page, $user, $sejour, $prodid, $typproduit, $x, $nbr, $nomprod)
    {
        $sejourobj = $this->em->getRepository(Sejour::class)->find($sejour);
        $version = $this->em->getRepository(Produit::class)->SearchVersionproduit($user->getId());
        if ($prodid != null && $prodid != "") {
            $Produit = $this->em->getRepository(Produit::class)->find($prodid);
            $Produit->setIduser($user);
            $Produit->setIdsjour($sejourobj);
            $Produit->setLabele($nomprod);
            $this->em->getManager()->persist($Produit);
            $this->em->getManager()->flush();
            $AllPages = $this->em->getRepository(Page::class)->findBy(array('idproduit' => $Produit), array('id' => 'ASC'));
            //dd($AllPages);
            if (sizeof($AllPages) != 0) {
                foreach ($AllPages as $key1 => $pag) {
                    foreach ($page as  $key2 => $e) {
                        if ($key1 == $key2) {
                            $pag->setIdproduit($Produit);
                            $pag->setCouleurbordure(json_encode($e));
                            $this->em->getManager()->persist($pag);
                            $this->em->getManager()->flush();
                        }
                    }
                }
            } else {
                foreach ($page as $e) {
                    $x = new Page();
                    $x->setIdproduit($Produit);
                    $x->setCouleurbordure(json_encode($e));
                    $this->em->getManager()->persist($x);
                    $this->em->getManager()->flush();
                }
            }
        } else {
            if (($nbr) == '12') {
                $prodct = $this->em->getRepository(Typeproduit::class)->find(18);
                $Conditionnement = $this->em->getRepository(TypeProduitConditionnement::class)->find(9);
            } elseif (($nbr) == '24') {
                $prodct = $this->em->getRepository(Typeproduit::class)->find(18);
                $Conditionnement = $this->em->getRepository(TypeProduitConditionnement::class)->find(10);
            } elseif (($nbr) == '36') {
                $prodct = $this->em->getRepository(Typeproduit::class)->find(17);
                $Conditionnement = $this->em->getRepository(TypeProduitConditionnement::class)->find(8);
            }
            $Produit = new Produit();
            $Produit->setIduser($user);
            $Produit->setIdsjour($sejourobj);
            $Produit->setIdConditionnement($Conditionnement);
            $Produit->setLabele($nomprod);
            $Produit->setDate(new \DateTime());
            $Produit->setType($prodct);
            $this->em->getManager()->persist($Produit);
            $this->em->getManager()->flush();
            foreach ($page as $e) {
                $page = new Page();
                $page->setIdproduit($Produit);
                $page->setCouleurbordure(json_encode($e));
                $this->em->getManager()->persist($page);
                $this->em->getManager()->flush();
            }
        }
        return ($Produit);
    }
    function SaveCalendrierParent($page, $user, $sejour, $prodid, $typproduit, $x, $nbr, $nomprod)
    {
        $sejourobj = $this->em->getRepository(Sejour::class)->find($sejour);
        $version = $this->em->getRepository(Produit::class)->SearchVersionproduit($user->getId());
        $prodct = $this->em->getRepository(Typeproduit::class)->find(16);
        if ($prodid != null && $prodid != "") {
            $Produit = $this->em->getRepository(Produit::class)->find($prodid);
            $Produit->setIduser($user);
            $Produit->setIdsjour($sejourobj);
            $Produit->setLabele($nomprod);
            // $Produit->setLabele($prodct->getLabeletype()." ".$version);
            //$Produit->setType($prodct);
            $this->em->getManager()->persist($Produit);
            $this->em->getManager()->flush();
            $AllPages = $this->em->getRepository(Page::class)->findBy(array('idproduit' => $Produit), array('id' => 'ASC'));
            //dd($AllPages);
            if (sizeof($AllPages) != 0) {
                foreach ($AllPages as $key1 => $pag) {
                    foreach ($page as  $key2 => $e) {
                        if ($key1 == $key2) {
                            $pag->setIdproduit($Produit);
                            $pag->setCouleurbordure(json_encode($e));
                            $this->em->getManager()->persist($pag);
                            $this->em->getManager()->flush();
                        }
                    }
                }
            } else {
                foreach ($page as $e) {
                    $x = new Page();
                    $x->setIdproduit($Produit);
                    $x->setCouleurbordure(json_encode($e));
                    $this->em->getManager()->persist($x);
                    $this->em->getManager()->flush();
                }
            }
        } else {
            $Conditionnement = $this->em->getRepository(TypeProduitConditionnement::class)->find(7);
            $Produit = new Produit();
            $Produit->setIduser($user);
            $Produit->setIdsjour($sejourobj);
            $Produit->setIdConditionnement($Conditionnement);
            $Produit->setLabele($nomprod);
            $Produit->setType($prodct);
            $Produit->setDate(new \DateTime());
            $this->em->getManager()->persist($Produit);
            $this->em->getManager()->flush();
            foreach ($page as $e) {
                $page = new Page();
                $page->setIdproduit($Produit);
                $page->setCouleurbordure(json_encode($e));
                $this->em->getManager()->persist($page);
                $this->em->getManager()->flush();
            }
        }
        return ($Produit);
    }
    function savelivreParent($page, $user, $sejour, $prodid, $x, $nomprod, $versionalbm)
    {
        $sejourobj = $this->em->getRepository(Sejour::class)->find($sejour);
        $version = $this->em->getRepository(Produit::class)->SearchVersionproduit($user->getId());
        $prodct = $this->em->getRepository(Typeproduit::class)->findOneBy(array('id' => '4'));
        if ($prodid != null && $prodid != "") {
            $Produit = $this->em->getRepository(Produit::class)->find($prodid);
            $Produit->setIduser($user);
            $Produit->setIdsjour($sejourobj);
            $Produit->setLabele($nomprod);
            $Produit->setType($prodct);
            $Produit->setVersion($versionalbm);
            $this->em->getManager()->persist($Produit);
            $this->em->getManager()->flush();
            $AllPages = $this->em->getRepository(Page::class)->findBy(array('idproduit' => $Produit), array('id' => 'ASC'));
            //dd($AllPages);
            if (sizeof($AllPages) != 0) {
                foreach ($AllPages as $key1 => $pag) {
                    foreach ($page as  $key2 => $e) {
                        if ($key1 == $key2) {
                            $pag->setIdproduit($Produit);
                            $pag->setCouleurbordure(json_encode($e));
                            $this->em->getManager()->persist($pag);
                            $this->em->getManager()->flush();
                        }
                    }
                }
            } else {
                foreach ($page as $e) {
                    $x = new Page();
                    $x->setIdproduit($Produit);
                    $x->setCouleurbordure(json_encode($e));
                    $this->em->getManager()->persist($x);
                    $this->em->getManager()->flush();
                }
            }
        } else {
            $Conditionnnement = $this->em->getRepository(TypeProduitConditionnement::class)->find(5);
            $Produit = new Produit();
            $Produit->setIduser($user);
            $Produit->setIdsjour($sejourobj);
            $Produit->setIdConditionnement($Conditionnnement);
            $Produit->setLabele($nomprod);
            $Produit->setDate(new \DateTime());
            $Produit->setType($prodct);
            $Produit->setVersion($versionalbm);
            $this->em->getManager()->persist($Produit);
            $this->em->getManager()->flush();
            foreach ($page as $e) {
                $page = new Page();
                $page->setIdproduit($Produit);
                $page->setCouleurbordure(json_encode($e));
                $this->em->getManager()->persist($page);
                $this->em->getManager()->flush();
            }
        }
        return ($Produit);
    }
    function  getNombreconnxtionPartenaire($id)
    {
        $Connx = $this->em->getRepository(ParentSejour::class)->SreachNombreConnxtionpourPartenaire($id);
        return $Connx;
    }
    function  getNombreconnxtionPartenaireV2($id)
    {
        $Connx = $this->em->getRepository(ParentSejour::class)->SreachNombreConnxtionpourPartenaireV2($id);
        return $Connx;
    }
    function  lesProduitsplusvenduPart($id)
    {
        $produit = $this->em->getRepository(ComandeProduit::class)->SreachNombreproduitspart($id);
        return $produit;
    }
    function comandePartesnaireEtablisment($id)
    {
        $comandes = $this->em->getRepository(Commande::class)->SreachComandespart($id);
        return $comandes;
    }
    function addfacture_partenaire($partenaire, $sejour, $periode)
    {
        $part =  $this->em->getRepository(User::class)->find($partenaire);
        $partenaire_Facture = $this->em->getRepository(Ref::class)->find(31);
        $year_month = date("ym");
        $nbCmd = $this->em->getRepository(Commande::class)->findBy(array('statut' => $partenaire_Facture));
        $nb = count($nbCmd) + 1;
        $pass =  $this->generationNumFacture($nb, "Facture_Cnx");
        $Commande = new Commande();
        $s = $this->em->getRepository(Sejour::class)->find($sejour["idSejour"]);
        $Commande->setNumComande($pass);
        $Commande->setDateCreateCommande(new \DateTime());
        $Commande->setIdSejour($s);
        $Commande->setIdUser($part);
        $Commande->setNbconnx($sejour["nombreConnexion"]);
        $Commande->setNbenfantencours($sejour["nbEnfantsEnCours"]);
        $Commande->setMoantantTtcregl($sejour["montantTotal"]);
        $Commande->setDateCreateCommande(new \Datetime());
        $Commande->setDateFacture(new \Datetime());
        $Commande->setNumfacture($pass);
        $Commande->setStatut($partenaire_Facture);
        $Commande->setPeriode($periode);
        //        $Commande->setNumfacture(substr($Commande->getNumfacture(), '0', 6) . $Commande->getId());
        $this->em->getManager()->persist($Commande);
        $this->em->getManager()->flush();
    }
    function addreversementpartenaire($partenaire, $sejour, $montantTotalRev, $nbr_cnxx, $ym)
    {
        $part =  $this->em->getRepository(User::class)->find($partenaire);
        $partenaire_Facture = $this->em->getRepository(Ref::class)->find(32);
        $year_month = date("ym");
        $nbCmd = $this->em->getRepository(Commande::class)->findBy(array('statut' => $partenaire_Facture));
        //        $nbCmd=count($cmds);
        $nb = count($nbCmd) + 1;
        $valx1 = $this->generationNumFacture($nb, "Appel_Facture");
        $Commande = new Commande();
        $s = $this->em->getRepository(Sejour::class)->find($sejour);
        $Commande->setNumComande($valx1);
        $Commande->setDateCreateCommande(new \DateTime());
        $Commande->setIdSejour($s);
        $Commande->setIdUser($part);
        $Commande->setNbconnx($nbr_cnxx);
        $Commande->setMoantantTtcregl($montantTotalRev);
        $Commande->setDateCreateCommande(new \Datetime());
        $Commande->setNumfacture($valx1);
        $Commande->setStatut($partenaire_Facture);
        $Commande->setDateFacture(new \Datetime());
        $Commande->setPeriode($ym);
        $this->em->getManager()->persist($Commande);
        $this->em->getManager()->flush();
    }
    function addreversement_Connex_partenaire($Nb_factuer, $sejour)
    {
        $partenaire_reversementConnx = $this->em->getRepository(Ref::class)->find(32);
        $bytes = random_int(100, 9999);
        $pass = bin2hex($bytes);
        $Commande = new Commande();
        $Commande->setIdSejour($sejour);
        $Commande->setNumComande($pass);
        $Commande->setDateCreateCommande(new \DateTime());
        $Commande->setNbconnx($Nb_factuer);
        $prixConnx = $sejour->getPrixcnxparent() + $sejour->getPrixcnxpartenaire();
        $totale = $prixConnx * $Nb_factuer;
        $Commande->setMoantantTtcregl($totale * $sejour->getReversecnxpart() / 100);
        $Commande->setStatut($partenaire_reversementConnx);
        $this->em->getManager()->persist($Commande);
        $this->em->getManager()->flush();
    }
    function reversment_Connextion($id)
    {
        $comandes = $this->em->getRepository(Commande::class)->Sreachreversment_Connextion($id);
        return $comandes;
    }
    function reversment_Produits($id)
    {
        $comandes = $this->em->getRepository(ComandeProduit::class)->Sreachreversment_ProduitsPART($id);
        return $comandes;
    }
    function  generationNumFacture($nbFacture, $type)
    {
        $numFacture = "";
        $year_month = date("ym");
        if ($nbFacture < 10000) {
            if ($nbFacture < 10) {
                if ($type == "Appel_Facture") {
                    $numFacture = $year_month . "3" . "000" . $nbFacture;
                }
                if ($type == "Facture_Cnx") {
                    $numFacture = $year_month . "2" . "000" . $nbFacture;
                }
            } elseif ($nbFacture < 100) {
                if ($type == "Appel_Facture") {
                    $numFacture = $year_month . "3" . "00" . $nbFacture;
                }
                if ($type == "Facture_Cnx") {
                    $numFacture = $year_month . "2" . "00" . $nbFacture;
                }
            } elseif ($nbFacture < 1000) {
                if ($type == "Appel_Facture") {
                    $numFacture = $year_month . "3" . "0" . $nbFacture;
                }
                if ($type == "Facture_Cnx") {
                    $numFacture = $year_month . "2" . "0" . $nbFacture;
                }
            } else {
                if ($type == "Appel_Facture") {
                    $numFacture = $year_month . "3" . $nbFacture;
                }
                if ($type == "Facture_Cnx") {
                    $numFacture = $year_month . "2" . $nbFacture;
                }
            }
        } else {
            $val = $nbFacture - 10000;
            $nbFacture2 = $val + 1;
            if ($nbFacture < 10) {
                if ($type == "Appel_Facture") {
                    $numFacture = $year_month . "3" . "000" . $nbFacture2;
                }
                if ($type == "Facture_Cnx") {
                    $numFacture = $year_month . "2" . "000" . $nbFacture2;
                }
            } elseif ($nbFacture < 100) {
                if ($type == "Appel_Facture") {
                    $numFacture = $year_month . "3" . "00" . $nbFacture2;
                }
                if ($type == "Facture_Cnx") {
                    $numFacture = $year_month . "2" . "00" . $nbFacture2;
                }
            } elseif ($nbFacture < 1000) {
                if ($type == "Appel_Facture") {
                    $numFacture = $year_month . "3" . "0" . $nbFacture2;
                }
                if ($type == "Facture_Cnx") {
                    $numFacture = $year_month . "2" . "0" . $nbFacture2;
                }
            } else {
                if ($type == "Appel_Facture") {
                    $numFacture = $year_month . "3" . $nbFacture2;
                }
                if ($type == "Facture_Cnx") {
                    $numFacture = $year_month . "2" . $nbFacture2;
                }
            }
        }
        return (intval($numFacture));
    }
    function SavePackPhotosNumerique($page, $user, $sejour, $prodid, $typproduit, $x, $nbr, $nomprod,$refname = null)
    {
        $Conditionnement = $this->em->getRepository(TypeProduitConditionnement::class)->find(12);
        $sejourobj = $this->em->getRepository(Sejour::class)->find($sejour);
        $prodct = $this->em->getRepository(Typeproduit::class)->find(19);
        //modifier produit
        if ($prodid != null && $prodid != "") {
            $Produit = $this->em->getRepository(Produit::class)->find($prodid);
            $Produit->setIduser($user);
            $Produit->setIdsjour($sejourobj);
            $Produit->setLabele($nomprod);
            $Produit->setPathpdf($refname);
            // $Produit->setLabele($prodct->getLabeletype()." ".$version);
            //$Produit->setType($prodct);
            $this->em->getManager()->persist($Produit);
            $this->em->getManager()->flush();
            $AllPages = $this->em->getRepository(Page::class)->findBy(array('idproduit' => $Produit), array('id' => 'ASC'));
            //dd($AllPages);
            if (sizeof($AllPages) != 0) {
                foreach ($AllPages as $key1 => $pag) {
                    foreach ($page as  $key2 => $e) {
                        if ($key1 == $key2) {
                            $pag->setIdproduit($Produit);
                            $pag->setCouleurbordure(json_encode($e));
                            $this->em->getManager()->persist($pag);
                            $this->em->getManager()->flush();
                        }
                    }
                }
            } else {
                foreach ($page as $e) {
                    $x = new Page();
                    $x->setIdproduit($Produit);
                    $x->setCouleurbordure(json_encode($e));
                    $this->em->getManager()->persist($x);
                    $this->em->getManager()->flush();
                }
            }
        } else {
            if (($nbr) == '20') {
                $Conditionnement = $this->em->getRepository(TypeProduitConditionnement::class)->find(12);
            } elseif (($nbr) == '30') {
                $Conditionnement = $this->em->getRepository(TypeProduitConditionnement::class)->find(13);
            } elseif (($nbr) == '50') {
                $Conditionnement = $this->em->getRepository(TypeProduitConditionnement::class)->find(14);
            }
            elseif (($nbr) == '15') {
                $Conditionnement = $this->em->getRepository(TypeProduitConditionnement::class)->find(16);
            }
            $Produit = new Produit();
            $Produit->setIduser($user);
            $Produit->setIdsjour($sejourobj);
            $Produit->setIdConditionnement($Conditionnement);
            $Produit->setLabele($nomprod);
            $Produit->setDate(new \DateTime());
            $Produit->setType($prodct);
            $Produit->setPathpdf($refname);
            $this->em->getManager()->persist($Produit);
            $this->em->getManager()->flush();
            foreach ($page as $e) {
                $page = new Page();
                $page->setIdproduit($Produit);
                $page->setCouleurbordure(json_encode($e));
                $this->em->getManager()->persist($page);
                $this->em->getManager()->flush();
            }
        }
        return ($Produit);
    }
    function SavePackPhotosNumeriquefree($page, $user, $sejour, $prodid, $typproduit, $x, $nbr, $nomprod,$refname = null)
    {
        $Conditionnement = $this->em->getRepository(TypeProduitConditionnement::class)->find(12);
        $sejourobj = $this->em->getRepository(Sejour::class)->find($sejour);
        $prodct = $this->em->getRepository(Typeproduit::class)->find(20);
        //modifier produit
        if ($prodid != null && $prodid != "") {
            $Produit = $this->em->getRepository(Produit::class)->find($prodid);
            $Produit->setIduser($user);
            $Produit->setIdsjour($sejourobj);
            $Produit->setLabele($nomprod);
            $Produit->setPathpdf($refname);
            // $Produit->setLabele($prodct->getLabeletype()." ".$version);
            //$Produit->setType($prodct);
            $this->em->getManager()->persist($Produit);
            $this->em->getManager()->flush();
            $AllPages = $this->em->getRepository(Page::class)->findBy(array('idproduit' => $Produit), array('id' => 'ASC'));
            //dd($AllPages);
            if (sizeof($AllPages) != 0) {
                foreach ($AllPages as $key1 => $pag) {
                    foreach ($page as  $key2 => $e) {
                        if ($key1 == $key2) {
                            $pag->setIdproduit($Produit);
                            $pag->setCouleurbordure(json_encode($e));
                            $this->em->getManager()->persist($pag);
                            $this->em->getManager()->flush();
                        }
                    }
                }
            } else {
                foreach ($page as $e) {
                    $x = new Page();
                    $x->setIdproduit($Produit);
                    $x->setCouleurbordure(json_encode($e));
                    $this->em->getManager()->persist($x);
                    $this->em->getManager()->flush();
                }
            }
        } else {
            if (($nbr) == '20') {
                $Conditionnement = $this->em->getRepository(TypeProduitConditionnement::class)->find(12);
            } elseif (($nbr) == '30') {
                $Conditionnement = $this->em->getRepository(TypeProduitConditionnement::class)->find(13);
            } elseif (($nbr) == '50') {
                $Conditionnement = $this->em->getRepository(TypeProduitConditionnement::class)->find(14);
            }
            elseif (($nbr) == '15') {
                $Conditionnement = $this->em->getRepository(TypeProduitConditionnement::class)->find(16);
            }
            $Produit = new Produit();
            $Produit->setIduser($user);
            $Produit->setIdsjour($sejourobj);
            $Produit->setIdConditionnement($Conditionnement);
            $Produit->setLabele($nomprod);
            $Produit->setDate(new \DateTime());
            $Produit->setType($prodct);
            $Produit->setPathpdf($refname);
            $this->em->getManager()->persist($Produit);
            $this->em->getManager()->flush();
            foreach ($page as $e) {
                $page = new Page();
                $page->setIdproduit($Produit);
                $page->setCouleurbordure(json_encode($e));
                $this->em->getManager()->persist($page);
                $this->em->getManager()->flush();
            }
        }
        return ($Produit);
    }
    public function commandepackphotoNum($attachements,$idsejour,$user,$packphotonum)
    {
       foreach($attachements as $idAttchment)
       {
            $photonum=new Photonsumeriques();
            $photonum->setDateCreation(new \DateTime());
            $sejour=$this->em->getRepository(Sejour::class)->find($idsejour);
            $photonum->setIdSejour($sejour);
            $photonum->setIdUser($user);
            $photonum->setIdProduit($packphotonum);
            $sejourAttachment=$this->em->getRepository(SejourAttachment::class)->findOneBy(array('idAttchment'=>$idAttchment,'idSejour'=>$sejour));
            $photonum->setIdSejourAttachement($sejourAttachment);
            $this->em->getManager()->persist($photonum);
            $this->em->getManager()->flush(); 
       }
       return 'bingo';
    }
}
