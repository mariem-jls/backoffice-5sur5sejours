<?php

namespace App\Service;


use App\Entity\Attachment;
use App\Entity\Site;
use App\Entity\Slide;
use App\Entity\User;
use App\Entity\Ref;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

class SiteService
{
    private $em;
    public function __construct(ManagerRegistry $em)
    {
        $this->em = $em;
    }
    function CreationNewSite($image1, $image2, $image3, $titre1, $titre2, $titre3, $desc1, $desc2, $desc3, $iduser)
    {
        $slide1 = $this->creationNewSlide($titre1, $desc1, $image1);
        $slide2 = $this->creationNewSlide($titre2, $desc2, $image2);
        $slide3 = $this->creationNewSlide($titre3, $desc3, $image3);
        $site = new Site();
        $site->setDatecreation(new \DateTime());
        $user = $this->em->getRepository(User::class)->find($iduser);
        $site->setCode($this->GenerateCodeSite($user->getId()));
        $site->setUser($user);
        $site->setSlide1($slide1);
        $site->setSlide2($slide2);
        $site->setSlide3($slide3);
        $statut = $this->em->getRepository(Ref::class)->findOneBy(array('libiller' => "desactiver"));
        $site->setStatut($statut);
        $this->em->getManager()->persist($site);
        $this->em->getManager()->flush();
        return $site;
    }
    function creationNewSlide($titre, $desc, $image)
    {
        $attachment = new Attachment();
        $attachment->setPath($image);
        $attachment->setDate(new \DateTime());
        $this->em->getManager()->persist($attachment);
        $this->em->getManager()->flush();
        $slide = new Slide();
        $slide->setLibele($titre);
        $slide->setDescription($desc);
        // $Att=$this->em->getRepository(Attachment::class)->findOneBy(array('id' => $idAttachement));
        $slide->setImage($attachment);
        $this->em->getManager()->persist($slide);
        $this->em->getManager()->flush();
        return ($slide);
    }
    function getAllSite($user = null)
    {
        $Sites = $this->em->getRepository(Site::class)->findAll();
        return $Sites;
    }
    function GenerateCodeSite($userID)
    {
        $date = new \Datetime();
        $Milliseconde = $date->format('u');
        $code = "Site" . $userID . "-" . $Milliseconde;
        return $code;
    }
    function miseEnAvant($idSite)
    {
        $this->em->getRepository(Site::class)->UpdatestatutAllsite();
        $site = $this->em->getRepository(Site::class)->find($idSite);
        $statut = $this->em->getRepository(Ref::class)->findOneBy(array('libiller' => "mise en avant"));
        $site->setStatut($statut);
        $this->em->getManager()->persist($site);
        $this->em->getManager()->flush();
        return new response("site est mise en avant");
    }
    function getActiveSite()
    {
        $statut = $this->em->getRepository(Ref::class)->findOneBy(array('libiller' => "mise en avant"));
        $Site = $this->em->getRepository(Site::class)->findOneBy(array('statut' => $statut));
        return $Site;
    }
}
