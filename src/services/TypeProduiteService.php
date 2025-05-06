<?php

namespace App\Service;
use App\Entity\ComandeProduit;
use App\Entity\Ref;
use App\Entity\Reversement;
use App\Entity\Typeproduit;
use App\Entity\Produit;
use App\Entity\TypeProduitConditionnement;
use App\Entity\TypeProduitPhoto;
use App\Entity\Attachment;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
class TypeProduiteService
{
    private $em;
    private $formFactory;
    private $templating;
    public function __construct(ManagerRegistry $em, FormFactoryInterface $formFactory, Environment  $templating)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->templating = $templating;
    }
    public function new($labeletype, $caracteristiques, $tarifsFraisPort, $plusproduit, $path, $user, $condition, $imagesCondition, $reversement)
    {
        $typeAttach = "photo";
        $Typeproduit = new Typeproduit();
        $Typeproduit->setLabeletype($labeletype);
        $Typeproduit->setDescription($caracteristiques);
        $Typeproduit->setTarifs($tarifsFraisPort);
        $Typeproduit->setPlusDescription($plusproduit);
        $Typeproduit->setReversement($reversement);
        $Typeproduit->setIduser($user);
        $Typeproduit->setDate(new \DateTime());
        $this->em->getManager()->persist($Typeproduit);
        $this->em->getManager()->flush();
        //$idnewatach = $attachment->getId(); $idprod = $Typeproduit->getId();
        //$Att=$this->em->getRepository(Attachment::class)->findOneBy(array('id' => $idAttachement));
        if (!empty($path)) {
            foreach ($path as $key => $pathPhoto) {
                $attachment = new Attachment();
                $attachment->setPath($pathPhoto['path']);
                $ref = $this->em->getRepository(Ref::class)->findOneBy(array("libiller" => $typeAttach));
                $attachment->setIdref($ref);
                $attachment->setDate(new \DateTime());
                $this->em->getManager()->persist($attachment);
                $this->em->getManager()->flush();
                $TypeProduitPhoto = new TypeProduitPhoto();
                $TypeProduitPhoto->setIdAttachement($attachment);
                $TypeProduitPhoto->setIdTypep($Typeproduit);
                $TypeProduitPhoto->setIdTypep($Typeproduit);
                $TypeProduitPhoto->setDate(new \DateTime());
                $TypeProduitPhoto->setStatut("pageProduit");
                $this->em->getManager()->persist($TypeProduitPhoto);
                $this->em->getManager()->flush();
            }
        }
        if (!empty($condition)) {
            foreach ($condition as $key => $conditionProduit) {
                $typeProduitConditionnement = new TypeProduitConditionnement();
                $typeProduitConditionnement->setDescriptionCommande($conditionProduit['description']);
                $typeProduitConditionnement->setSousTitre($conditionProduit['sousTitre']);
                $typeProduitConditionnement->setMontantHT($conditionProduit['montantHT']);
                $typeProduitConditionnement->setTVA($conditionProduit['tva']);
                $MontantTTC = $conditionProduit['montantHT'] * (1 + ($conditionProduit['tva'] / 100));
                $typeProduitConditionnement->setMontantTTC($MontantTTC);
                //            $typeProduitConditionnement->setR($conditionProduit['reversement']);
                $typeProduitConditionnement->setPoidsProduit($conditionProduit['poidsProduit']);
                $typeProduitConditionnement->setPoidsContenant($conditionProduit['poidsContenant']);
                $totalProduit = $conditionProduit['poidsProduit'] + $conditionProduit['poidsContenant'];
                $typeProduitConditionnement->setPoidsTotal($totalProduit);
                $typeProduitConditionnement->setPochetteEnvoi($conditionProduit['Pochette']);
                $typeProduitConditionnement->setIdTypeProduit($Typeproduit);
                $this->em->getManager()->persist($typeProduitConditionnement);
                $this->em->getManager()->flush();
                if (!empty($imagesCondition)) {
                    foreach ($imagesCondition as $key => $images) {
                        if ($conditionProduit['idCondition'] == $images['id']) {
                            $attachment = new Attachment();
                            $attachment->setPath($images['path']);
                            $ref = $this->em->getRepository(Ref::class)->findOneBy(array("libiller" => $typeAttach));
                            $attachment->setIdref($ref);
                            $attachment->setDate(new \DateTime());
                            $this->em->getManager()->persist($attachment);
                            $this->em->getManager()->flush();
                            $TypeProduitPhoto = new TypeProduitPhoto();
                            $TypeProduitPhoto->setIdAttachement($attachment);
                            $TypeProduitPhoto->setIdTypep($Typeproduit);
                            $TypeProduitPhoto->setIdProduitConditionnement($typeProduitConditionnement);
                            $TypeProduitPhoto->setDate(new \DateTime());
                            $this->em->getManager()->persist($TypeProduitPhoto);
                            $this->em->getManager()->flush();
                        }
                    }
                }
            }
        }
        return $Typeproduit;
    }
    public function produitlist()
    {
        $Produit = $this->em->getRepository(TypeProduitPhoto::class)->findBy(array('statut' => 'pageProduit'));
        return $Produit;
    }
    public function produitlistType()
    {
        $Produit = $this->em->getRepository(Typeproduit::class)->findBy(array('statut' => 'publier'));
        return $Produit;
    }
    public function produitlistTypeParDate($date1, $date2)
    {
        $Produit = $this->em->getRepository(Typeproduit::class)->findByTypeParDate('publier', $date1, $date2);
        return $Produit;
    }
    public function produitlistAll()
    {
        $Produit = $this->em->getRepository(TypeProduitPhoto::class)->findAllWithRelatedData();
        return $Produit;
    }
    public function produitlistTypeConditionnement()
    {
        //$Produit = $this->em->getRepository(Typeproduit::class)->findBy(array('statut' => 'publier'));
        //$ProduitConditionnement = $this->em->getRepository(TypeProduitConditionnement::class)->findBy(array('idTypeProduit' => $Produit), array('orderprod' => 'ASC'));
        $ProduitConditionnement = $this->em->getRepository(TypeProduitConditionnement::class)->produitlistTypeConditionnement();
        return $ProduitConditionnement;
    }
    public function produitlistTypeAll()
    {
        $Produit = $this->em->getRepository(Typeproduit::class)->findALL();
        return $Produit;
    }

    public function findBySearchQuery($query)
    {
        $Produit = $this->em->getRepository(Typeproduit::class)->findBySearchQuery($query);
        return $Produit;
    }

    public function affecterphoto($id, $IdAttachement)
    {
        $produit = $this->em->getRepository(Typeproduit::class)->find($id);
        $Attach = $this->em->getRepository(Attachment::class)->findBy($IdAttachement);
        $TypeProduitPhoto = new TypeProduitPhoto();
        $TypeProduitPhoto->setIdAttachement($Attach->getId());
        $TypeProduitPhoto->setIdTypep($produit->getId());
        $this->em->getManager()->persist($TypeProduitPhoto);
        $this->em->getManager()->flush();
        return ('photo has been affected to product');
    }
    public function suprimerphoto($id, $IdAttachement)
    {
        $produit = $this->em->getRepository(Typeproduit::class)->find($id);
        $Attachement = $this->em->getRepository(Attachment::class)->find($IdAttachement);
        $TypeProduitPhoto = $this->em->getRepository(TypeProduitPhoto::class)->findOneBy(array('idTypep' => $id, 'idAttachement' => $IdAttachement));
        $this->em->getManager()->remove($TypeProduitPhoto);
        $this->em->getManager()->flush();
        return ('photo has been delated from product');
    }
    public function showProduit($id)
    {
        $produit = $this->em->getRepository(Typeproduit::class)->find($id);
        return  $produit;
        return ('affichage du produit');
    }
    public function updateproduit($id, $caracteristiques, $delais, $tarifs, $tarif, $fraisDeport, $labeletype, $path, $user, $reversement)
    {
        $typeAttach = "photo";
        $produit = $this->em->getRepository(Typeproduit::class)->find($id);
        $produit->setDescription($caracteristiques);
        $produit->setPlusDescription($delais);
        $produit->setTarifs($tarifs);
        $produit->setFraisDePort($fraisDeport);
        $produit->setTraif($tarif);
        $produit->setIduser($user);
        $produit->setLabeletype($labeletype);
        $this->em->getManager()->persist($produit);
        $this->em->getManager()->flush();
        $rever = $this->em->getRepository(Reversement::class)->findOneBy(array('idTypeproduit' => $id));
        if ($rever != null) {
            $rever->setReversement($reversement);
            $rever->setIdTypeproduit($produit);
            $this->em->getManager()->persist($rever);
            $this->em->getManager()->flush();
        }
        foreach ($path as $key => $pathPhoto) {
            $attachment = new Attachment();
            $attachment->setPath($pathPhoto['path']);
            $ref = $this->em->getRepository(Ref::class)->findOneBy(array("libiller" => $typeAttach));
            $attachment->setIdref($ref);
            $attachment->setDate(new \DateTime());
            $this->em->getManager()->persist($attachment);
            $this->em->getManager()->flush();
            $TypeProduitPhoto = new TypeProduitPhoto();
            $TypeProduitPhoto->setIdAttachement($attachment);
            $TypeProduitPhoto->setIdTypep($produit);
            $TypeProduitPhoto->setDate(new \DateTime());
            $this->em->getManager()->persist($TypeProduitPhoto);
            $this->em->getManager()->flush();
        }
        return $produit;
    }
    public function updateproduitAvecNewCondition($id, $caracteristiques, $delais, $tarifs, $labeletype, $path, $user, $conditionOld, $condition, $imagesCondition, $imagesOldCondition, $reversement)
    {
        $typeAttach = "photo";
        $produit = $this->em->getRepository(Typeproduit::class)->find($id);
        $produit->setDescription($caracteristiques);
        $produit->setPlusDescription($delais);
        $produit->setTarifs($tarifs);
        $produit->setIduser($user);
        $produit->setLabeletype($labeletype);
        $produit->setReversement($reversement);
        $this->em->getManager()->persist($produit);
        $this->em->getManager()->flush();
        if (!empty($path)) {
            foreach ($path as $key => $pathPhoto) {
                $attachment = new Attachment();
                $attachment->setPath($pathPhoto['path']);
                $ref = $this->em->getRepository(Ref::class)->findOneBy(array("libiller" => $typeAttach));
                $attachment->setIdref($ref);
                $attachment->setDate(new \DateTime());
                $this->em->getManager()->persist($attachment);
                $this->em->getManager()->flush();
                $TypeProduitPhoto = new TypeProduitPhoto();
                $TypeProduitPhoto->setIdAttachement($attachment);
                $TypeProduitPhoto->setIdTypep($produit);
                $TypeProduitPhoto->setDate(new \DateTime());
                $TypeProduitPhoto->setStatut("pageProduit");
                $this->em->getManager()->persist($TypeProduitPhoto);
                $this->em->getManager()->flush();
            }
        }
        if (!empty($condition)) {
            foreach ($condition as $key => $conditionProduit) {
                $typeProduitConditionnement = new TypeProduitConditionnement();
                $typeProduitConditionnement->setDescriptionCommande($conditionProduit['description']);
                $typeProduitConditionnement->setSousTitre($conditionProduit['sousTitre']);
                $typeProduitConditionnement->setMontantHT($conditionProduit['montantHT']);
                $typeProduitConditionnement->setTVA($conditionProduit['tva']);
                $MontantTTC = $conditionProduit['montantHT'] * (1 + ($conditionProduit['tva'] / 100));
                $typeProduitConditionnement->setMontantTTC($MontantTTC);
                //            $typeProduitConditionnement->setR($conditionProduit['reversement']);
                $typeProduitConditionnement->setPoidsProduit($conditionProduit['poidsProduit']);
                $typeProduitConditionnement->setPoidsContenant($conditionProduit['poidsContenant']);
                $totalProduit = $conditionProduit['poidsProduit'] + $conditionProduit['poidsContenant'];
                $typeProduitConditionnement->setPoidsTotal($totalProduit);
                $typeProduitConditionnement->setPochetteEnvoi($conditionProduit['Pochette']);
                $typeProduitConditionnement->setIdTypeProduit($produit);
                $this->em->getManager()->persist($typeProduitConditionnement);
                $this->em->getManager()->flush();
                if (!empty($imagesCondition)) {
                    foreach ($imagesCondition as $key => $images) {
                        if ($conditionProduit['idCondition'] == $images['id']) {
                            $attachment = new Attachment();
                            $attachment->setPath($images['path']);
                            $ref = $this->em->getRepository(Ref::class)->findOneBy(array("libiller" => $typeAttach));
                            $attachment->setIdref($ref);
                            $attachment->setDate(new \DateTime());
                            $this->em->getManager()->persist($attachment);
                            $this->em->getManager()->flush();
                            $TypeProduitPhoto = new TypeProduitPhoto();
                            $TypeProduitPhoto->setIdAttachement($attachment);
                            $TypeProduitPhoto->setIdTypep($produit);
                            $TypeProduitPhoto->setIdProduitConditionnement($typeProduitConditionnement);
                            $TypeProduitPhoto->setDate(new \DateTime());
                            $this->em->getManager()->persist($TypeProduitPhoto);
                            $this->em->getManager()->flush();
                        }
                    }
                }
            }
        }
        if (!empty($conditionOld)) {
            foreach ($conditionOld as $key => $conditionProduit) {
                $Conditionnement = $this->em->getRepository(TypeProduitConditionnement::class)->find($conditionProduit['idCondition']);
                $Conditionnement->setDescriptionCommande($conditionProduit['description']);
                $Conditionnement->setSousTitre($conditionProduit['sousTitre']);
                $Conditionnement->setMontantHT($conditionProduit['montantHT']);
                $Conditionnement->setTVA($conditionProduit['tva']);
                $MontantTTC = $conditionProduit['montantHT'] * (1 + ($conditionProduit['tva'] / 100));
                $Conditionnement->setMontantTTC($MontantTTC);
                $Conditionnement->setPoidsProduit($conditionProduit['poidsProduit']);
                $Conditionnement->setPoidsContenant($conditionProduit['poidsContenant']);
                $totalProduit = $conditionProduit['poidsProduit'] + $conditionProduit['poidsContenant'];
                $Conditionnement->setPoidsTotal($totalProduit);
                $Conditionnement->setPochetteEnvoi($conditionProduit['Pochette']);
                $Conditionnement->setIdTypeProduit($produit);
                $this->em->getManager()->persist($Conditionnement);
                $this->em->getManager()->flush();
                if (!empty($imagesOldCondition)) {
                    foreach ($imagesOldCondition as $key => $images) {
                        if ($conditionProduit['idCondition'] == $images['id']) {
                            $attachment = new Attachment();
                            $attachment->setPath($images['path']);
                            $ref = $this->em->getRepository(Ref::class)->findOneBy(array("libiller" => $typeAttach));
                            $attachment->setIdref($ref);
                            $attachment->setDate(new \DateTime());
                            $this->em->getManager()->persist($attachment);
                            $this->em->getManager()->flush();
                            $TypeProduitPhoto = new TypeProduitPhoto();
                            $TypeProduitPhoto->setIdAttachement($attachment);
                            $TypeProduitPhoto->setIdTypep($produit);
                            $TypeProduitPhoto->setIdProduitConditionnement($Conditionnement);
                            $TypeProduitPhoto->setDate(new \DateTime());
                            $this->em->getManager()->persist($TypeProduitPhoto);
                            $this->em->getManager()->flush();
                        }
                    }
                }
            }
        }
        return $produit;
    }
    public function updateproduitSansNewCondition($id, $caracteristiques, $delais, $tarifs, $labeletype, $path, $user, $conditionOld, $imagesOldCondition, $reversement)
    {
        $typeAttach = "photo";
        $produit = $this->em->getRepository(Typeproduit::class)->find($id);
        $produit->setDescription($caracteristiques);
        $produit->setPlusDescription($delais);
        $produit->setTarifs($tarifs);
        $produit->setIduser($user);
        $produit->setLabeletype($labeletype);
        $produit->setReversement($reversement);
        $this->em->getManager()->persist($produit);
        $this->em->getManager()->flush();
        if (!empty($path)) {
            foreach ($path as $key => $pathPhoto) {
                $attachment = new Attachment();
                $attachment->setPath($pathPhoto['path']);
                $ref = $this->em->getRepository(Ref::class)->findOneBy(array("libiller" => $typeAttach));
                $attachment->setIdref($ref);
                $attachment->setDate(new \DateTime());
                $this->em->getManager()->persist($attachment);
                $this->em->getManager()->flush();
                $TypeProduitPhoto = new TypeProduitPhoto();
                $TypeProduitPhoto->setIdAttachement($attachment);
                $TypeProduitPhoto->setIdTypep($produit);
                $TypeProduitPhoto->setDate(new \DateTime());
                $TypeProduitPhoto->setStatut("pageProduit");
                $this->em->getManager()->persist($TypeProduitPhoto);
                $this->em->getManager()->flush();
            }
        }
        foreach ($conditionOld as $key => $conditionProduit) {
            $Conditionnement = $this->em->getRepository(TypeProduitConditionnement::class)->find($conditionProduit['idCondition']);
            $Conditionnement->setDescriptionCommande($conditionProduit['description']);
            $Conditionnement->setSousTitre($conditionProduit['sousTitre']);
            $Conditionnement->setMontantHT($conditionProduit['montantHT']);
            $Conditionnement->setTVA($conditionProduit['tva']);
            $MontantTTC = $conditionProduit['montantHT'] * (1 + ($conditionProduit['tva'] / 100));
            $Conditionnement->setMontantTTC($MontantTTC);
            $Conditionnement->setPoidsProduit($conditionProduit['poidsProduit']);
            $Conditionnement->setPoidsContenant($conditionProduit['poidsContenant']);
            $totalProduit = $conditionProduit['poidsProduit'] + $conditionProduit['poidsContenant'];
            $Conditionnement->setPoidsTotal($totalProduit);
            $Conditionnement->setPochetteEnvoi($conditionProduit['Pochette']);
            $Conditionnement->setIdTypeProduit($produit);
            $this->em->getManager()->persist($Conditionnement);
            $this->em->getManager()->flush();
            if (!empty($imagesOldCondition)) {
                foreach ($imagesOldCondition as $key => $images) {
                    if ($conditionProduit['idCondition'] == $images['id']) {
                        $attachment = new Attachment();
                        $attachment->setPath($images['path']);
                        $ref = $this->em->getRepository(Ref::class)->findOneBy(array("libiller" => $typeAttach));
                        $attachment->setIdref($ref);
                        $attachment->setDate(new \DateTime());
                        $this->em->getManager()->persist($attachment);
                        $this->em->getManager()->flush();
                        $TypeProduitPhoto = new TypeProduitPhoto();
                        $TypeProduitPhoto->setIdAttachement($attachment);
                        $TypeProduitPhoto->setIdTypep($produit);
                        $TypeProduitPhoto->setIdProduitConditionnement($Conditionnement);
                        $TypeProduitPhoto->setDate(new \DateTime());
                        $this->em->getManager()->persist($TypeProduitPhoto);
                        $this->em->getManager()->flush();
                    }
                }
            }
        }
        return $produit;
    }
    function produitPublier($idProd)
    {
        $Pro = $this->em->getRepository(Typeproduit::class)->find($idProd);
        $statut = $this->em->getRepository(Ref::class)->findOneBy(array('libiller' => "publiée"));
        $Pro->setStatut($statut);
        $this->em->getManager()->persist($Pro);
        $this->em->getManager()->flush();
        return new response("produit a publiée");
    }
    public function nombrcnnxparsejour($id)
    {
        $liste = $this->em->getRepository(Produit::class)->searshNbconnxionParSejour($id);
        return  $liste;
        return ('affichage du produit');
    }
    public function produitparent()
    {
        $Produit = $this->em->getRepository(Typeproduit::class)->searshProduitParent();
        return $Produit;
    }
    function StatutTypeProduit($idProd, $statut)
    {
        $Pro = $this->em->getRepository(Typeproduit::class)->find($idProd);
        $Pro->setStatut($statut);
        $this->em->getManager()->persist($Pro);
        $this->em->getManager()->flush();
        return $Pro;
    }
    public function ProduitPlusVente($type, $dateTimeDebut, $dateTimeFin)
    {
        if ($dateTimeDebut == null) {
            $Produit = $this->em->getRepository(ComandeProduit::class)->PRoduitsENOrDREdEvENTE($type);
        } else {
            $Produit = $this->em->getRepository(ComandeProduit::class)->PRoduitsENOrDREdEvENTEFiltrePardate($type, $dateTimeDebut, $dateTimeFin);
        }
        return $Produit;
    }
    public function LaSommeDesProduitsVendus($dateTimeDebut, $dateTimeFin)
    {
        if ($dateTimeDebut == null) {
            $Produit = $this->em->getRepository(ComandeProduit::class)->LaSommeDesProduitsVendus();
        } else {
            $Produit = $this->em->getRepository(ComandeProduit::class)->LaSommeDesProduitsVendusParDate($dateTimeDebut, $dateTimeFin);
        }
        // $somme = count($Produit);
        return $Produit;
    }
}
