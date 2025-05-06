<?php

namespace App\Service;

use App\Entity\SejourAttachment;
use App\Entity\Likephoto;
use App\Entity\Attachment;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class PhotosFavorisService
{
    private $em;
    public function __construct(ManagerRegistry $em)
    {
        $this->em = $em;
    }
    function ListephotosFavoris($user, $idSejour)
    {
        $idAttachement = '';
        $favoris = $this->em->getRepository(Likephoto::class)->findBy(array('idUser' => $user));
        if ($favoris == null) {
            $SejourAttachement = $this->em->getRepository(SejourAttachment::class)->findOneBy(array('idSejour' => $idSejour, 'idAttchment' => $idAttachement));
            $favoris = new Likephoto();
            $favoris->setIdUser($user);
            $favoris->setIdSejourAttchment($SejourAttachement);
            $favoris->setDate(new \DateTime());
            $this->em->getManager()->persist($favoris);
            $this->em->getManager()->flush();
        }
        return "Done";
    }
    function AddFavoris($user, $idAttachement, $idSejour)
    {
        $favoris = $this->em->getRepository(Likephoto::class)->findOneBy(array('idUser' => $user, 'idSejourAttchment' => $idAttachement));
        if ($favoris == null) {
            $favoris = new Likephoto();
            $SejourAttachement = $this->em->getRepository(SejourAttachment::class)->findOneBy(array('idSejour' => $idSejour, 'idAttchment' => $idAttachement));
            //            $user =$this->em->getRepository(User::class)->find($iduser);
            $favoris->setIdUser($user);
            $favoris->setIdSejourAttchment($SejourAttachement);
            $favoris->setIdSejour($idSejour);
            $favoris->setDate(new \DateTime());
            $this->em->getManager()->persist($favoris);
            $this->em->getManager()->flush();
        }
        return "Done";
    }
    function RemoveFavoris($user, $idAttachement, $idSejour)
    {
        $SejourAttachement = $this->em->getRepository(SejourAttachment::class)->findOneBy(array('idSejour' => $idSejour, 'idAttchment' => $idAttachement));
        $favoris = $this->em->getRepository(Likephoto::class)->findOneBy(array('idUser' => $user, 'idSejourAttchment' => $SejourAttachement->getId()));
        if ($favoris != null) {
            $this->em->getManager()->remove($favoris);
            $this->em->getManager()->flush();
        }
        return "Done";
    }
    //Yosra a faire ==> Supprimer complètement une photo perso de 5sur5 !!!!
    function RemovePhotoPerso($user, $idAttachement, $idSejour)
    {
        $SejourAttachement = $this->em->getRepository(SejourAttachment::class)->findOneBy(array('idSejour' => $idSejour, 'idAttchment' => $idAttachement));
        $favoris = $this->em->getRepository(Likephoto::class)->findOneBy(array('idUser' => $user, 'idSejourAttchment' => $SejourAttachement->getId()));
        if ($favoris != null) {
            $this->em->getManager()->remove($favoris);
            $this->em->getManager()->flush();
        }
        return "Done";
    }
    function ALLFavoris($user, $Attachements, $idSejour)
    {
        foreach ($Attachements as $key => $Photo) {
            $Attachement = $this->em->getRepository(Attachment::class)->find($Photo['id_attchment']);
            $SejourAttachement = $this->em->getRepository(SejourAttachment::class)->findOneBy(array('idSejour' => $idSejour, 'idAttchment' => $Attachement));
            $favoris = $this->em->getRepository(Likephoto::class)->findOneBy(array('idUser' => $user, 'idSejourAttchment' => $SejourAttachement));
            if ($favoris == null) {
                $favoris = new Likephoto();
                //            $user =$this->em->getRepository(User::class)->find($iduser);
                $favoris->setIdUser($user);
                $favoris->setIdSejourAttchment($SejourAttachement);
                $favoris->setIdSejour($idSejour);
                $favoris->setDate(new \DateTime());
                $this->em->getManager()->persist($favoris);
                $this->em->getManager()->flush();
            }
        }
        return "Done";
    }
    function RemoveALLFavoris($user, $Attachements, $idSejour)
    {
        foreach ($Attachements as $key => $Photo) {
            $Attachement = $this->em->getRepository(Attachment::class)->find($Photo['id_attchment']);
            $SejourAttachement = $this->em->getRepository(SejourAttachment::class)->findOneBy(array('idSejour' => $idSejour, 'idAttchment' => $Attachement));
            $favoris = $this->em->getRepository(Likephoto::class)->findOneBy(array('idUser' => $user, 'idSejourAttchment' => $SejourAttachement));
            if ($favoris != null) {
                $this->em->getManager()->remove($favoris);
                $this->em->getManager()->flush();
            }
        }
        return "Done";
    }
}
