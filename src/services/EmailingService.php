<?php

namespace App\Service;

use App\Entity\Emailing;
use App\Entity\Ref;
use App\Entity\Attachment;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

class EmailingService
{
    private $em;
    public function __construct(ManagerRegistry $em)
    {
        $this->em = $em;
    }
    function getAllMail()
    {
        $Mails = $this->em->getRepository(Emailing::class)->findAll();
        return $Mails;
    }
    function imageemail($id, $path)
    {
        $email = $this->em->getRepository(Emailing::class)->find($id);
        $attachment1 = new Attachment();
        $attachment1->setDate(new \DateTime());
        $attachment1->setPath($path);
        $this->em->getManager()->persist($attachment1);
        $this->em->getManager()->flush();
        $email->setIdImage1($attachment1);
        $this->em->getManager()->persist($email);
        $this->em->getManager()->flush();
        return $email;
    }
    function imageemail2($id, $path)
    {
        $email = $this->em->getRepository(Emailing::class)->find($id);
        $attachment1 = new Attachment();
        $attachment1->setDate(new \DateTime());
        $attachment1->setPath($path);
        $this->em->getManager()->persist($attachment1);
        $this->em->getManager()->flush();
        $email->setIdImage2($attachment1);
        $this->em->getManager()->persist($email);
        $this->em->getManager()->flush();
        return $email;
    }
    function miseEnAvant($idEmail, $idtypeemail)
    {
        $this->em->getRepository(Emailing::class)->UpdatestatutAllemailbytype($idtypeemail);
        $mail = $this->em->getRepository(Emailing::class)->find($idEmail);
        $statut = $this->em->getRepository(Ref::class)->findOneBy(array('libiller' => "mise en avant"));
        $mail->setStatut($statut);
        $this->em->getManager()->persist($mail);
        $this->em->getManager()->flush();
        return new response("site est mise en avant");
    }
}
