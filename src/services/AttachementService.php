<?php

namespace App\Service;

use App\Entity\Attachment;
use App\Entity\Likephoto;
use App\Entity\Ref;
use App\Entity\SejourAttachment;
use App\Entity\Typeproduit;
use App\Entity\TypeProduitPhoto;
use Doctrine\Persistence\ManagerRegistry;

class AttachementService
{
    private $em;
    public function __construct(ManagerRegistry $em)
    {
        $this->em = $em;
    }
    function creationAttachement($file, $folder, $port, $host, $user)
    {
        $em = $this->em->getManager();
        $attachement = new Attachment();
        $originalName = $file->getClientOriginalName();
        $size = $file->getSize();
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        //$name = $this->GenerateNeme($user->getIdUser());
        $arrayext = explode('.', $originalName);
        //move_uploaded_file($file, 'img/' . $folder . '/' . $name . '-' . $originalName);
        //$attachement->setPath('http://' . $host . ':' . $port . '/img/' . $folder . '/' . $name . '-' . $originalName);
        //$attachement->setSize($size);
        //$attachement->setDateaction(new \DateTime());
        //$attachement->setExtension($arrayext[sizeof($arrayext) - 1]);
        $em->persist($attachement);
        $em->flush();
        //return $attachement->getIdAttachment();
    }
    function creationAttachementS($path, $type, $date = "")
    {
        $em = $this->em->getManager();
        $attach = $this->em->getRepository(Attachment::class)->findOneBy(array("path" => $path));
        if ($attach == null || $attach == "") {
            $attachement = new Attachment();
            $attachement->setPath($path);
            $ref = $this->em->getRepository(Ref::class)->findOneBy(array("libiller" => $type));
            $attachement->setIdref($ref);
            if ($date == "") {
                $attachement->setDate(new \DateTime());
            } else {
                $dateFormat = date_create_from_format('d/m/Y', $date);
                $attachement->setDate($dateFormat);
            }
            $em->persist($attachement);
            $em->flush();
            return $attachement;
        }
    }
    function creationUrlImprimAlbum($path, $type,$descriptAlbm, $date = "")
    {
        $em = $this->em->getManager();
        //$attach = $this->em->getRepository(Attachment::class)->findOneBy(array());
        $imprimAlbum=  $this->em->getRepository(Attachment::class)->findOneBy(array("descreption" =>$descriptAlbm,"path" => $path));
       
        if ( $imprimAlbum == null || $imprimAlbum == "") {
            $attachement = new Attachment();
            $attachement->setPath($path);
            $attachement->setDescreption($descriptAlbm);
            $ref = $this->em->getRepository(Ref::class)->findOneBy(array("libiller" => $type));
            $attachement->setIdref($ref);
            if ($date == "") {
                $attachement->setDate(new \DateTime());
            } else {
                $dateFormat = date_create_from_format('d/m/Y', $date);
                $attachement->setDate($dateFormat);
            }
            $em->persist($attachement);
            $em->flush();
            return $attachement;
        }
    }
    public function SupprimerImagesProduit($idProd, $path)
    {
        $id = '';
        $em = $this->em->getManager();
        $produit = $this->em->getRepository(Typeproduit::class)->find($idProd);
        $Attachement = $this->em->getRepository(Attachment::class)->find($id);
        $ArticlePhotos = $this->em->getRepository(TypeProduitPhoto::class)->findOneBy(array('id_typep' => $produit, 'id_attachement' => $Attachement));
        $em->remove($ArticlePhotos);
        $em->flush();
    }
    function RemovePhoto($user, $idAttachement, $idSejour)
    {
        $SejourAttachement = $this->em->getRepository(SejourAttachment::class)->findOneBy(array('idSejour' => $idSejour, 'idAttchment' => $idAttachement));
        $favoris = $this->em->getRepository(Likephoto::class)->findOneBy(array('idUser' => $user, 'idSejourAttchment' => $SejourAttachement->getId()));
        if ($favoris) {
            $this->em->getManager()->remove($favoris);
        }
        if ($SejourAttachement && $SejourAttachement->getStatut() == 'private') {
            //$Attachement = $this->em->getRepository(Attachment::class)->find($idAttachement);
            //$this->em->getManager()->remove($SejourAttachement);
            //$this->em->getManager()->remove($Attachement);
        }
        $this->em->getManager()->flush();
        return "Done";
    }
    function creationLogoSejour($user, $path, $type)
    {
        $em = $this->em->getManager();
        $attachement = new Attachment();
        $attachement->setPath($path);
        $ref = $this->em->getRepository(Ref::class)->findOneBy(array("libiller" => $type));
        $attachement->setIdref($ref);
        $attachement->setDate(new \DateTime());
        $em->persist($attachement);
        $em->flush();
        $user->setLogourl($path);
        $em->persist($user);
        $em->flush();
        return $user;
    }
}
