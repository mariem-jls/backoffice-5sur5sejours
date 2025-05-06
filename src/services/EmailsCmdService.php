<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;
use App\Entity\Ref;
use App\Entity\Emailing;
use Swift_Image;
use App\Entity\Emailrelance;
use Doctrine\Persistence\ManagerRegistry;
use Swift_Attachment;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Crypto\SMimeSigner;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\SendEmail;
use Symfony\Component\Mime\Email;

class EmailsCmdService
{
    private $em;

    private $templating;
    private $params;
    private $loginLinkHandler;
    private MessageBusInterface $messageBus;

    public function __construct(ManagerRegistry $em,
      Environment $templating,
       ParameterBagInterface $params,
        // LoginLinkHandlerInterface $loginLinkHandler,
         MessageBusInterface $messageBus)
    {
        $this->em = $em;

        $this->templating = $templating;
        $this->params = $params;
        // $this->loginLinkHandler = $loginLinkHandler;
        $this->messageBus = $messageBus;
    }

    //     function MailCommandeSuivieAccomp($sendTo, $comande, $trackingCode)
    //     {
    //         $RefEmail = $this->em->getRepository(Ref::class)->find(28);
    //         $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
    //         $message = (new \Swift_Message(' Suivie commande'))
    //             ->setFrom('contact@5sur5sejour.com')
    //             ->setTo($sendTo)
    //             ->setBcc('yousra.tlich@gmail.com');
    //         $pathImage2 = $Email->getIdImage2()->getPath();
    //         $pathImage1 = $Email->getIdImage1()->getPath();
    //         $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
    //         $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
    //         $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
    //         $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
    //         $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
    //         $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
    //         $message->setBody(
    //             $this->templating->render(
    //                 'emails/MailCommandeSuivieAccomp.html.twig',
    //                 [
    //                     "image1" => $image1,
    //                     "image2" => $image2,
    //                     "iconfooter" => $iconfooter,
    //                     "iconphoto" => $iconphoto,
    //                     "iconloca" => $iconloca,
    //                     "iconmsg" => $iconmsg,
    //                     "comande" => $comande,
    //                     'trackingCode' => $trackingCode
    //                 ]
    //             ),
    //             'text/html'
    //         );
    //         $signMail = $this->params->get('signMail');
    //         if ($signMail == 'yes') {
    //             $domainName = $this->params->get('domaine');
    //             $selector = $this->params->get('selector');
    //             $PrivateKey =  file_get_contents($this->params->get('pathDKIM'));
    //             $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
    //             $message->attachSigner($signer);
    //         }
    //         try {
    //             $done = $this->mailer->send($message);
    //             //var_dump($done);
    //             // var_dump("send it ");
    //         } catch (\Swift_SwiftException $ex) {
    //             //var_dump( $ex->getMessage());
    //         }
    //     }
    //     function MailCommandeSuivieParent($sendTo, $comande, $trackingCode)
    //     {
    //         $RefEmail = $this->em->getRepository(Ref::class)->find(28);
    //         $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
    //         $message = (new \Swift_Message(' Suivie commande'))
    //             ->setFrom('no-reply@5sur5sejour.com')
    //             ->setTo($sendTo)
    //             ->setBcc('yousra.tlich@gmail.com');
    //         $pathImage2 = $Email->getIdImage2()->getPath();
    //         $pathImage1 = $Email->getIdImage1()->getPath();
    //         $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
    //         $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
    //         $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
    //         $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
    //         $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
    //         $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
    //         $message->setBody(
    //             $this->templating->render(
    //                 'emails/MailCommandeSuivieParent.html.twig',
    //                 [
    //                     "image1" => $image1,
    //                     "image2" => $image2,
    //                     "iconfooter" => $iconfooter,
    //                     "iconphoto" => $iconphoto,
    //                     "iconloca" => $iconloca,
    //                     "iconmsg" => $iconmsg,
    //                     "comande" => $comande,
    //                     "trackingCode" => $trackingCode
    //                 ]
    //             ),
    //             'text/html'
    //         );
    //         $signMail = $this->params->get('signMail');
    //         if ($signMail == 'yes') {
    //             $domainName = $this->params->get('domaine');
    //             $selector = $this->params->get('selector');
    //             $PrivateKey =  file_get_contents($this->params->get('pathDKIM'));
    //             $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
    //             $message->attachSigner($signer);
    //         }
    //         try {
    //             $done = $this->mailer->send($message);
    //             //var_dump($done);
    //             // var_dump("send it ");
    //         } catch (\Swift_SwiftException $ex) {
    //             //var_dump( $ex->getMessage());
    //         }
    //         //  MailCommandeSuivieParent . html . twig
    //     }
    //     function MailExpCommandeSansSuivieParent($sendTo)
    //     {
    //         $RefEmail = $this->em->getRepository(Ref::class)->find(28);
    //         $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
    //         $message = (new \Swift_Message('Expédition commande'))
    //             ->setFrom('no-reply@5sur5sejour.com')
    //             ->setTo($sendTo);
    //         // ->setBcc(["contact@5sur5sejour.com"]);
    //         $pathImage2 = $Email->getIdImage2()->getPath();
    //         $pathImage1 = $Email->getIdImage1()->getPath();
    //         $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
    //         $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
    //         $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
    //         $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
    //         $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
    //         $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
    //         $message->setBody(
    //             $this->templating->render(
    //                 'emails/MailExpCommandeSansSuivieParent.html.twig',
    //                 [
    //                     "image1" => $image1,
    //                     "image2" => $image2,
    //                     "iconfooter" => $iconfooter,
    //                     "iconphoto" => $iconphoto,
    //                     "iconloca" => $iconloca,
    //                     "iconmsg" => $iconmsg,
    //                 ]
    //             ),
    //             'text/html'
    //         );
    //         $signMail = $this->params->get('signMail');
    //         if ($signMail == 'yes') {
    //             $domainName = $this->params->get('domaine');
    //             $selector = $this->params->get('selector');
    //             $PrivateKey =  file_get_contents($this->params->get('pathDKIM'));
    //             $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
    //             $message->attachSigner($signer);
    //         }
    //         try {
    //             $done = $this->mailer->send($message);
    //             //var_dump($done);
    //             // var_dump("send it ");
    //         } catch (\Swift_SwiftException $ex) {
    //             //var_dump( $ex->getMessage());
    //         }
    //         //    MailExpCommandeSansSuivieParent . html . twig
    //     }
    //     function MailExpCommandeSansSuivieAccomp($sendTo)
    //     {
    //         $RefEmail = $this->em->getRepository(Ref::class)->find(28);
    //         $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
    //         $message = (new \Swift_Message('Expédition commande'))
    //             ->setFrom('no-reply@5sur5sejour.com')
    //             ->setTo($sendTo);
    //         //->setBcc(["contact@5sur5sejour.com"]);
    //         $pathImage2 = $Email->getIdImage2()->getPath();
    //         $pathImage1 = $Email->getIdImage1()->getPath();
    //         $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
    //         $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
    //         $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
    //         $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
    //         $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
    //         $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
    //         $message->setBody(
    //             $this->templating->render(
    //                 'emails/MailExpCommandeSansSuivieAccomp.html.twig',
    //                 [
    //                     "image1" => $image1,
    //                     "image2" => $image2,
    //                     "iconfooter" => $iconfooter,
    //                     "iconphoto" => $iconphoto,
    //                     "iconloca" => $iconloca,
    //                     "iconmsg" => $iconmsg,
    //                 ]
    //             ),
    //             'text/html'
    //         );
    //         $signMail = $this->params->get('signMail');
    //         if ($signMail == 'yes') {
    //             $domainName = $this->params->get('domaine');
    //             $selector = $this->params->get('selector');
    //             $PrivateKey =  file_get_contents($this->params->get('pathDKIM'));
    //             $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
    //             $message->attachSigner($signer);
    //         }
    //         try {
    //             $done = $this->mailer->send($message);
    //             //var_dump($done);
    //             // var_dump("send it ");
    //         } catch (\Swift_SwiftException $ex) {
    //             //var_dump( $ex->getMessage());
    //         }
    //         // MailExpCommandeSansSuivieAccomp . html . twig
    //     }
    //     function MailValiderCommandeParent($sendTo, $comande)
    //     {
    //         $RefEmail = $this->em->getRepository(Ref::class)->find(28);
    //         $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
    //         /* $commandesnumeriques=$this->em->getRepository(CommandeNumerique::class)->findBy(array('idCommande'=>$comande,'etat'=>1));
    //         $links=[];
    //         if(sizeof($commandesnumeriques))
    //         {
    //             foreach($commandesnumeriques as $cmd)
    //             {
    //                 $link=$cmd->getLinkdownload();
    //                 array_push($links,$link);
    //             }
    //             $this->MailCommandeNumerique($sendTo,$links,$comande); 
    //         }
    //        */
    //         $message = (new \Swift_Message('validation commande'))
    //             ->setFrom('no-reply@5sur5sejour.com')
    //             ->setTo($sendTo);
    //         //->setBcc(["contact@5sur5sejour.com"]);
    //         $pathImage2 = $Email->getIdImage2()->getPath();
    //         $pathImage1 = $Email->getIdImage1()->getPath();
    //         $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
    //         $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
    //         $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
    //         $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
    //         $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
    //         $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
    //         $message->setBody(
    //             $this->templating->render(
    //                 'emails/MailValiderCommandeParent.html.twig',
    //                 [
    //                     "image1" => $image1,
    //                     "image2" => $image2,
    //                     "iconfooter" => $iconfooter,
    //                     "iconphoto" => $iconphoto,
    //                     "iconloca" => $iconloca,
    //                     "iconmsg" => $iconmsg,
    //                     "comande" => $comande,
    //                     //   "links"=>$links
    //                 ]
    //             ),
    //             'text/html'
    //         );
    //         $signMail = $this->params->get('signMail');
    //         if ($signMail == 'yes') {
    //             $domainName = $this->params->get('domaine');
    //             $selector = $this->params->get('selector');
    //             $PrivateKey =  file_get_contents($this->params->get('pathDKIM'));
    //             $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
    //             $message->attachSigner($signer);
    //         }
    //         try {
    //             $done = $this->mailer->send($message);
    //             //var_dump($done);
    //             // var_dump("send it ");
    //         } catch (\Swift_SwiftException $ex) {
    //             //var_dump( $ex->getMessage());
    //         }
    //         // MailValiderCommandeParent . html . twig
    //     }
    //     function MailCommandeNumerique($sendTo, $link, $comande)
    //     {
    //         $RefEmail = $this->em->getRepository(Ref::class)->find(28);
    //         $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
    //         $message = (new \Swift_Message('validation commande numérique'))
    //             ->setFrom('no-reply@5sur5sejour.com')
    //             ->setTo($sendTo);
    //         var_dump($sendTo);
    //         //->setBcc(["contact@5sur5sejour.com"]);
    //         $pathImage2 = $Email->getIdImage2()->getPath();
    //         $pathImage1 = $Email->getIdImage1()->getPath();
    //         $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
    //         $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
    //         $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
    //         $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
    //         $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
    //         $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
    //         $message->setBody(
    //             $this->templating->render(
    //                 'emails/MailValiderCommandeNumeriqueParent.html.twig',
    //                 [
    //                     "image1" => $image1,
    //                     "image2" => $image2,
    //                     "iconfooter" => $iconfooter,
    //                     "iconphoto" => $iconphoto,
    //                     "iconloca" => $iconloca,
    //                     "iconmsg" => $iconmsg,
    //                     "commande" => $comande,
    //                     "link" => $link
    //                 ]
    //             ),
    //             'text/html'
    //         );
    //         var_dump($link);
    //         $signMail = $this->params->get('signMail');
    //         if ($signMail == 'yes') {
    //             $domainName = $this->params->get('domaine');
    //             $selector = $this->params->get('selector');
    //             $PrivateKey =  file_get_contents($this->params->get('pathDKIM'));
    //             $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
    //             $message->attachSigner($signer);
    //         }
    //         try {
    //             $done =     $this->mailer->send($message);
    //             var_dump($done);
    //             var_dump("send it ");
    //         } catch (\Swift_SwiftException $ex) {
    //             var_dump("exception");
    //             var_dump($ex->getMessage());
    //         }
    //     }
    //     function MailValiderCommandeAccomp($sendTo, $comande)
    //     {
    //         $RefEmail = $this->em->getRepository(Ref::class)->find(28);
    //         $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
    //         $message = (new \Swift_Message('validation commande'))
    //             ->setFrom('no-reply@5sur5sejour.com')
    //             ->setTo($sendTo);
    //         //->setBcc(["contact@5sur5sejour.com"]);
    //         $pathImage2 = $Email->getIdImage2()->getPath();
    //         $pathImage1 = $Email->getIdImage1()->getPath();
    //         $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
    //         $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
    //         $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
    //         $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
    //         $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
    //         $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
    //         $message->setBody(
    //             $this->templating->render(
    //                 'emails/MailValiderCommandeAccomp.html.twig',
    //                 [
    //                     "image1" => $image1,
    //                     "image2" => $image2,
    //                     "iconfooter" => $iconfooter,
    //                     "iconphoto" => $iconphoto,
    //                     "iconloca" => $iconloca,
    //                     "iconmsg" => $iconmsg,
    //                     "comande" => $comande,
    //                 ]
    //             ),
    //             'text/html'
    //         );
    //         $signMail = $this->params->get('signMail');
    //         if ($signMail == 'yes') {
    //             $domainName = $this->params->get('domaine');
    //             $selector = $this->params->get('selector');
    //             $PrivateKey =  file_get_contents($this->params->get('pathDKIM'));
    //             $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
    //             $message->attachSigner($signer);
    //         }
    //         try {
    //             $done = $this->mailer->send($message);
    //             //var_dump($done);
    //             // var_dump("send it ");
    //         } catch (\Swift_SwiftException $ex) {
    //             //var_dump( $ex->getMessage());
    //         }
    //         // MailValiderCommandeAccomp . html . twig
    //     }
    //     function MailRelancePanier($sendTo)
    //     {
    //         $RefEmail = $this->em->getRepository(Ref::class)->find(28);
    //         $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
    //         $message = (new \Swift_Message('panier en attente'))
    //             ->setFrom('no-reply@5sur5sejour.com')
    //             ->setTo("contact@5sur5sejour.com")
    //             ->setBcc($sendTo);
    //         $pathImage2 = $Email->getIdImage2()->getPath();
    //         $pathImage1 = $Email->getIdImage1()->getPath();
    //         $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
    //         $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
    //         $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
    //         $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
    //         $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
    //         $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
    //         $message->setBody(
    //             $this->templating->render(
    //                 'emails/MailRelancePanier.html.twig',
    //                 [
    //                     "image1" => $image1,
    //                     "image2" => $image2,
    //                     "iconfooter" => $iconfooter,
    //                     "iconphoto" => $iconphoto,
    //                     "iconloca" => $iconloca,
    //                     "iconmsg" => $iconmsg,
    //                 ]
    //             ),
    //             'text/html'
    //         );
    //         $signMail = $this->params->get('signMail');
    //         if ($signMail == 'yes') {
    //             $domainName = $this->params->get('domaine');
    //             $selector = $this->params->get('selector');
    //             $PrivateKey =  file_get_contents($this->params->get('pathDKIM'));
    //             $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
    //             $message->attachSigner($signer);
    //         }
    //         try {
    //             $done = $this->mailer->send($message);
    //             //var_dump($done);
    //             // var_dump("send it ");
    //         } catch (\Swift_SwiftException $ex) {
    //             //var_dump( $ex->getMessage());
    //         }
    //         //  MailRelancePanier . html . twig
    //     }
    //     function SaveMailRelancePanier($sendTo)
    //     {
    //         $mailRelance = new Emailrelance();
    //         $mailRelance->setDateCreation(new \Datetime());
    //         $mailRelance->setDateSend(null);
    //         $mailRelance->setFlagDepot(null);
    //         $mailRelance->setSendTo($sendTo);
    //         $this->em->getManager()->persist($mailRelance);
    //         $this->em->getManager()->flush();
    //         return "done";
    //     }
    //     function MailFinSejour($sendTo)
    //     {
    //         $RefEmail = $this->em->getRepository(Ref::class)->find(28);
    //         $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
    //         $message = (new \Swift_Message('Fin de séjour'))
    //             ->setFrom('no-reply@5sur5sejour.com')
    //             ->setTo($sendTo);
    //         // ->setBcc(["contact@5sur5sejour.com"]);
    //         $pathImage2 = $Email->getIdImage2()->getPath();
    //         $pathImage1 = $Email->getIdImage1()->getPath();
    //         $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
    //         $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
    //         $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
    //         $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
    //         $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
    //         $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
    //         $message->setBody(
    //             $this->templating->render(
    //                 'emails/MailFinSejour.html.twig',
    //                 [
    //                     "image1" => $image1,
    //                     "image2" => $image2,
    //                     "iconfooter" => $iconfooter,
    //                     "iconphoto" => $iconphoto,
    //                     "iconloca" => $iconloca,
    //                     "iconmsg" => $iconmsg,
    //                 ]
    //             ),
    //             'text/html'
    //         );
    //         $signMail = $this->params->get('signMail');
    //         if ($signMail == 'yes') {
    //             $domainName = $this->params->get('domaine');
    //             $selector = $this->params->get('selector');
    //             $PrivateKey =  file_get_contents($this->params->get('pathDKIM'));
    //             $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
    //             $message->attachSigner($signer);
    //         }
    //         try {
    //             $done = $this->mailer->send($message);
    //             //var_dump($done);
    //             // var_dump("send it ");
    //         } catch (\Swift_SwiftException $ex) {
    //             //var_dump( $ex->getMessage());
    //         }
    //         //    MailFinSejour . html . twig
    //     }
    //     function MailFinSejourRapelleTemps($sendTo)
    //     {
    //         $RefEmail = $this->em->getRepository(Ref::class)->find(28);
    //         $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
    //         $message = (new \Swift_Message('Fin de séjour'))
    //             ->setFrom('no-reply@5sur5sejour.com')
    //             ->setTo($sendTo);
    //         //->setBcc(["contact@5sur5sejour.com"]);
    //         $pathImage2 = $Email->getIdImage2()->getPath();
    //         $pathImage1 = $Email->getIdImage1()->getPath();
    //         $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
    //         $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
    //         $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
    //         $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
    //         $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
    //         $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
    //         $message->setBody(
    //             $this->templating->render(
    //                 'emails/MailFinSejourRapelleTemps.html.twig',
    //                 [
    //                     "image1" => $image1,
    //                     "image2" => $image2,
    //                     "iconfooter" => $iconfooter,
    //                     "iconphoto" => $iconphoto,
    //                     "iconloca" => $iconloca,
    //                     "iconmsg" => $iconmsg,
    //                 ]
    //             ),
    //             'text/html'
    //         );
    //         $signMail = $this->params->get('signMail');
    //         if ($signMail == 'yes') {
    //             $domainName = $this->params->get('domaine');
    //             $selector = $this->params->get('selector');
    //             $PrivateKey =  file_get_contents($this->params->get('pathDKIM'));
    //             $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
    //             $message->attachSigner($signer);
    //         }
    //         try {
    //             $done = $this->mailer->send($message);
    //             //var_dump($done);
    //             // var_dump("send it ");
    //         } catch (\Swift_SwiftException $ex) {
    //             //var_dump( $ex->getMessage());
    //         }
    //         //      MailFinSejourRapelleTemps . html . twig
    //     }
    //     function MailRemercAccApresPremDepot1($sendTo)
    //     {
    //         $RefEmail = $this->em->getRepository(Ref::class)->find(28);
    //         $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
    //         $message = (new \Swift_Message('Remerciement fin de séjour'))
    //             ->setFrom('no-reply@5sur5sejour.com')
    //             ->setTo($sendTo)
    //             ->setBcc("5sur5sejour2020@gmail.com");
    //         $pathImage2 = $Email->getIdImage2()->getPath();
    //         $pathImage1 = $Email->getIdImage1()->getPath();
    //         $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
    //         $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
    //         $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
    //         $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
    //         $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
    //         $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
    //         $message->setBody(
    //             $this->templating->render(
    //                 'emails/MailRemercAccApresPremDepot1.html.twig',
    //                 [
    //                     "image1" => $image1,
    //                     "image2" => $image2,
    //                     "iconfooter" => $iconfooter,
    //                     "iconphoto" => $iconphoto,
    //                     "iconloca" => $iconloca,
    //                     "iconmsg" => $iconmsg,
    //                 ]
    //             ),
    //             'text/html'
    //         );
    //         $signMail = $this->params->get('signMail');
    //         if ($signMail == 'yes') {
    //             $domainName = $this->params->get('domaine');
    //             $selector = $this->params->get('selector');
    //             $PrivateKey =  file_get_contents($this->params->get('pathDKIM'));
    //             $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
    //             $message->attachSigner($signer);
    //         }
    //         try {
    //             $done = $this->mailer->send($message);
    //             //var_dump($done);
    //             // var_dump("send it ");
    //         } catch (\Swift_SwiftException $ex) {
    //             //var_dump( $ex->getMessage());
    //         }
    //         //        MailRemercAccApresPremDepot1 . html . twig
    //     }
    //     function MailRemercAccApresPremDepot2($sendTo)
    //     {
    //         $RefEmail = $this->em->getRepository(Ref::class)->find(28);
    //         $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
    //         $message = (new \Swift_Message('Remerciement '))
    //             ->setFrom('no-reply@5sur5sejour.com')
    //             ->setTo($sendTo);
    //         //->setBcc(["contact@5sur5sejour.com"]);
    //         $pathImage2 = $Email->getIdImage2()->getPath();
    //         $pathImage1 = $Email->getIdImage1()->getPath();
    //         $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
    //         $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
    //         $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
    //         $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
    //         $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
    //         $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
    //         $message->setBody(
    //             $this->templating->render(
    //                 'emails/MailRemercAccApresPremDepot2.html.twig',
    //                 [
    //                     "image1" => $image1,
    //                     "image2" => $image2,
    //                     "iconfooter" => $iconfooter,
    //                     "iconphoto" => $iconphoto,
    //                     "iconloca" => $iconloca,
    //                     "iconmsg" => $iconmsg,
    //                 ]
    //             ),
    //             'text/html'
    //         );
    //         $signMail = $this->params->get('signMail');
    //         if ($signMail == 'yes') {
    //             $domainName = $this->params->get('domaine');
    //             $selector = $this->params->get('selector');
    //             $PrivateKey =  file_get_contents($this->params->get('pathDKIM'));
    //             $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
    //             $message->attachSigner($signer);
    //         }
    //         try {
    //             $done = $this->mailer->send($message);
    //             //var_dump($done);
    //             // var_dump("send it ");
    //         } catch (\Swift_SwiftException $ex) {
    //             //var_dump( $ex->getMessage());
    //         }
    //         // MailRemercAccApresPremDepot2 . html . twig
    //     }
    //     function RappelAcomp($sendTo)
    //     {
    //         $RefEmail = $this->em->getRepository(Ref::class)->find(28);
    //         $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
    //         $message = (new \Swift_Message('Rappel des fonctionnalités '))
    //             ->setFrom('no-reply@5sur5sejour.com')
    //             ->setTo($sendTo)
    //             ->setBcc(["ramzi.benlarbi@gmail.com", "5sur5sejour2020@gmail.com"]);
    //         $pathImage2 = $Email->getIdImage2()->getPath();
    //         $pathImage1 = $Email->getIdImage1()->getPath();
    //         $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
    //         $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
    //         $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
    //         $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
    //         $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
    //         $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
    //         $message->setBody(
    //             $this->templating->render(
    //                 'emails/RappelAcomp.html.twig',
    //                 [
    //                     "image1" => $image1,
    //                     "image2" => $image2,
    //                     "iconfooter" => $iconfooter,
    //                     "iconphoto" => $iconphoto,
    //                     "iconloca" => $iconloca,
    //                     "iconmsg" => $iconmsg,
    //                 ]
    //             ),
    //             'text/html'
    //         );
    //         $signMail = $this->params->get('signMail');
    //         if ($signMail == 'yes') {
    //             $domainName = $this->params->get('domaine');
    //             $selector = $this->params->get('selector');
    //             $PrivateKey =  file_get_contents($this->params->get('pathDKIM'));
    //             $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
    //             $message->attachSigner($signer);
    //         }
    //         try {
    //             $done = $this->mailer->send($message);
    //             //var_dump($done);
    //             // var_dump("send it ");
    //         } catch (\Swift_SwiftException $ex) {
    //             //var_dump( $ex->getMessage());
    //         }
    //         //  RappelAcomp . html . twig
    //     }
    //     function RappelAcompAcecPartenaire($sendTo, $part)
    //     {
    //         $logo = '';
    //         $nom = '';
    //         $logo = $part->getLogourl();
    //         $nom = $part->getNometablisment();
    //         $RefEmail = $this->em->getRepository(Ref::class)->find(28);
    //         $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
    //         $message = (new \Swift_Message('Rappel des fonctionnalités'))
    //             ->setFrom('no-reply@5sur5sejour.com')
    //             ->setTo($sendTo)
    //             ->setBcc("5sur5sejour2020@gmail.com");
    //         $pathImage2 = $Email->getIdImage2()->getPath();
    //         $pathImage1 = $Email->getIdImage1()->getPath();
    //         $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
    //         $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
    //         $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
    //         $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
    //         $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
    //         $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
    //         $message->setBody(
    //             $this->templating->render(
    //                 'emails/RappelAcompAcecPartenaire.html.twig',
    //                 [
    //                     "image1" => $image1,
    //                     "image2" => $image2,
    //                     "iconfooter" => $iconfooter,
    //                     "iconphoto" => $iconphoto,
    //                     "iconloca" => $iconloca,
    //                     "iconmsg" => $iconmsg,
    //                     'logo' => $logo,
    //                     'nom' => $nom,
    //                 ]
    //             ),
    //             'text/html'
    //         );
    //         $signMail = $this->params->get('signMail');
    //         if ($signMail == 'yes') {
    //             $domainName = $this->params->get('domaine');
    //             $selector = $this->params->get('selector');
    //             $PrivateKey =  file_get_contents($this->params->get('pathDKIM'));
    //             $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
    //             $message->attachSigner($signer);
    //         }
    //         try {
    //             $done = $this->mailer->send($message);
    //             //var_dump($done);
    //             // var_dump("send it ");
    //         } catch (\Swift_SwiftException $ex) {
    //             //var_dump( $ex->getMessage());
    //         }
    //         //   RappelAcompAcecPartenaire . html . twig
    //     }
    //     function RelancePasDeContenueDepose($sendTo)
    //     {
    //         $RefEmail = $this->em->getRepository(Ref::class)->find(28);
    //         $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
    //         $message = (new \Swift_Message('Pensez à déposer !'))
    //             ->setFrom('no-reply@5sur5sejour.com')
    //             ->setTo($sendTo)
    //             ->setBcc(["yousra.tlich@gmail.com"]);

    //         $pathImage2 = $Email->getIdImage2()->getPath();
    //         $pathImage1 = $Email->getIdImage1()->getPath();
    //         $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
    //         $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
    //         $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
    //         $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
    //         $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
    //         $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
    //         $message->setBody(
    //             $this->templating->render(
    //                 'emails/RelancePasDeContenueDepose.html.twig',
    //                 [
    //                     "image1" => $image1,
    //                     "image2" => $image2,
    //                     "iconfooter" => $iconfooter,
    //                     "iconphoto" => $iconphoto,
    //                     "iconloca" => $iconloca,
    //                     "iconmsg" => $iconmsg,
    //                 ]
    //             ),
    //             'text/html'
    //         );
    //         $signMail = $this->params->get('signMail');
    //         if ($signMail == 'yes') {
    //             $domainName = $this->params->get('domaine');
    //             $selector = $this->params->get('selector');
    //             $PrivateKey =  file_get_contents($this->params->get('pathDKIM'));
    //             $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
    //             $message->attachSigner($signer);
    //         }
    //         try {
    //             $done = $this->mailer->send($message);
    //         } catch (\Swift_SwiftException $ex) {
    //             var_dump($ex->getMessage());
    //         }
    //         //     RelancePasDeContenueDepose . html . twig
    //     }
    //     function RelanceJMOINS1Mail($sendTo, $extraData = array())
    //     {
    //         $RefEmail = $this->em->getRepository(Ref::class)->find(28);
    //         $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
    //         $message = (new \Swift_Message('Pensez à déposer !'))
    //             ->setFrom('no-reply@5sur5sejour.com')
    //             ->setTo($sendTo);
    //         //->setBcc(["contact@5sur5sejour.com"]);
    //         $pathImage2 = $Email->getIdImage2()->getPath();
    //         $pathImage1 = $Email->getIdImage1()->getPath();
    //         $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
    //         $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
    //         $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
    //         $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
    //         $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
    //         $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
    //         $message->setBody(
    //             $this->templating->render(
    //                 'emails/RelanceJMoins1.html.twig',
    //                 [
    //                     "image1" => $image1,
    //                     "image2" => $image2,
    //                     "iconfooter" => $iconfooter,
    //                     "iconphoto" => $iconphoto,
    //                     "iconloca" => $iconloca,
    //                     "iconmsg" => $iconmsg,
    //                     "extra" => $extraData,
    //                 ]
    //             ),
    //             'text/html'
    //         );
    //         $signMail = $this->params->get('signMail');
    //         if ($signMail == 'yes') {
    //             $domainName = $this->params->get('domaine');
    //             $selector = $this->params->get('selector');
    //             $PrivateKey =  file_get_contents($this->params->get('pathDKIM'));
    //             $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
    //             $message->attachSigner($signer);
    //         }
    //         try {
    //             $done = $this->mailer->send($message);
    //         } catch (\Swift_SwiftException $ex) {
    //             var_dump($ex->getMessage());
    //         }
    //         //     RelancePasDeContenueDepose . html . twig
    //     }
    //     function AlerteCreationAlbumSejour($sendTo, $extraData = array())
    //     {
    //         $RefEmail = $this->em->getRepository(Ref::class)->find(28);
    //         $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
    //         $message = (new \Swift_Message("L'album du séjour est fin prêt !"))
    //             ->setFrom('no-reply@5sur5sejour.com')
    //             ->setTo($sendTo);
    //         //->setBcc(["contact@5sur5sejour.com"]);
    //         $pathImage2 = $Email->getIdImage2()->getPath();
    //         $pathImage1 = $Email->getIdImage1()->getPath();
    //         $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
    //         $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
    //         $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
    //         $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
    //         $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
    //         $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
    //         $message->setBody(
    //             $this->templating->render(
    //                 'emails/RelanceDernierJour.html.twig',
    //                 [
    //                     "image1" => $image1,
    //                     "image2" => $image2,
    //                     "iconfooter" => $iconfooter,
    //                     "iconphoto" => $iconphoto,
    //                     "iconloca" => $iconloca,
    //                     "iconmsg" => $iconmsg,
    //                     "extra" => $extraData,
    //                 ]
    //             ),
    //             'text/html'
    //         );
    //         $signMail = $this->params->get('signMail');
    //         if ($signMail == 'yes') {
    //             $domainName = $this->params->get('domaine');
    //             $selector = $this->params->get('selector');
    //             $PrivateKey =  file_get_contents($this->params->get('pathDKIM'));
    //             $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
    //             $message->attachSigner($signer);
    //         }
    //         try {
    //             $done = $this->mailer->send($message);
    //         } catch (\Swift_SwiftException $ex) {
    //             var_dump($ex->getMessage());
    //         }
    //         //     RelancePasDeContenueDepose . html . twig
    //     }
    //     function RelanceDernierJourMail($sendTo, $extraData = array())
    //     {
    //         $RefEmail = $this->em->getRepository(Ref::class)->find(28);
    //         $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
    //         $message = (new \Swift_Message('Pensez à déposer !'))
    //             ->setFrom('no-reply@5sur5sejour.com')
    //             ->setTo($sendTo);
    //         //->setBcc(["contact@5sur5sejour.com"]);
    //         $pathImage2 = $Email->getIdImage2()->getPath();
    //         $pathImage1 = $Email->getIdImage1()->getPath();
    //         $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
    //         $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
    //         $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
    //         $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
    //         $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
    //         $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
    //         $message->setBody(
    //             $this->templating->render(
    //                 'emails/RelanceDernierJour.html.twig',
    //                 [
    //                     "image1" => $image1,
    //                     "image2" => $image2,
    //                     "iconfooter" => $iconfooter,
    //                     "iconphoto" => $iconphoto,
    //                     "iconloca" => $iconloca,
    //                     "iconmsg" => $iconmsg,
    //                     "extra" => $extraData,
    //                 ]
    //             ),
    //             'text/html'
    //         );
    //         $signMail = $this->params->get('signMail');
    //         if ($signMail == 'yes') {
    //             $domainName = $this->params->get('domaine');
    //             $selector = $this->params->get('selector');
    //             $PrivateKey =  file_get_contents($this->params->get('pathDKIM'));
    //             $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
    //             $message->attachSigner($signer);
    //         }
    //         try {
    //             $done = $this->mailer->send($message);
    //         } catch (\Swift_SwiftException $ex) {
    //             var_dump($ex->getMessage());
    //         }
    //         //     RelancePasDeContenueDepose . html . twig
    //     }
    //     public function envoyerAlbumAccoToParent($parent, $sejour, $autoLogintoAlbumAcco)
    //     {
    //         $RefEmail = $this->em->getRepository(Ref::class)->find(28);
    //         $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
    //         $sendTo = $parent->getEmail();
    //         $message = (new \Swift_Message('album accompagnateur'))
    //             ->setFrom('no-reply@5sur5sejour.com')
    //             ->setTo($sendTo);
    //         $pathImage2 = $Email->getIdImage2()->getPath();
    //         $pathImage1 = $Email->getIdImage1()->getPath();
    //         $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
    //         $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
    //         $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
    //         $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
    //         $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
    //         $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
    //         $message->setBody(
    //             $this->templating->render(
    //                 'emails/mailParentAlbumAcco.html.twig',
    //                 [
    //                     "image1" => $image1,
    //                     "image2" => $image2,
    //                     "iconfooter" => $iconfooter,
    //                     "iconphoto" => $iconphoto,
    //                     "iconloca" => $iconloca,
    //                     "iconmsg" => $iconmsg,
    //                     "link" => $autoLogintoAlbumAcco
    //                 ]
    //             ),
    //             'text/html'
    //         );
    //         $signMail = $this->params->get('signMail');
    //         if ($signMail == 'yes') {
    //             $domainName = $this->params->get('domaine');
    //             $selector = $this->params->get('selector');
    //             $PrivateKey =  file_get_contents($this->params->get('pathDKIM'));
    //             $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
    //             $message->attachSigner($signer);
    //         }
    //         try {
    //             $done =     $this->mailer->send($message);
    //         } catch (\Swift_SwiftException $ex) {
    //         }
    //     }
    //    /**
    //      * Service that handle sending mails using Mailer
    //      * @author Firas Belhadj firasbelhadj686@gmail.com
    //      * @param string $sendTo The receiving mail adress
    //      * @param string $object The mail object
    //      * @param string $twig The name of mail twig without .html.twig
    //      * @param string $extraData Parameters to be sent to twig (optional)
    //      * @return int 0
    //      */
    //     public function sendMailAlbum($sendTo, $object, $twig, $extraData = array())
    //     {
    //         $from = 'contact@5sur5sejour.com';
    //        // $RefEmail = $this->em->getRepository(Ref::class)->find(28);
    //         //$Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));

    //         $message = (new TemplatedEmail())
    //             ->from($from)
    //             ->to($sendTo)
    //             ->subject($object)
    //             ->htmlTemplate('emails/' . $twig . '.html.twig')
    //             ->context($extraData);
    //         $this->newMailer->send($message);
    //     }
    //     /**
    //      * Service that handle sending mails using Mailer
    //      * @author Firas Belhadj firasbelhadj686@gmail.com
    //      * @param string $sendTo The receiving mail adress
    //      * @param string $object The mail object
    //      * @param string $twig The name of mail twig without .html.twig
    //      * @param string $extraData Parameters to be sent to twig (optional)
    //      * @return int 0
    //      */
    //     public function sendMail($sendTo, $object, $twig, $extraData = array())
    //     {
    //         $from = 'no-reply@5sur5sejour.com';
    //        // $RefEmail = $this->em->getRepository(Ref::class)->find(28);
    //         //$Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));

    //         $message = (new TemplatedEmail())
    //             ->from($from)
    //             ->to($sendTo)
    //             ->subject($object)
    //             ->htmlTemplate('emails/' . $twig . '.html.twig')
    //             ->context($extraData);
    //         $this->newMailer->send($message);
    //     }
    //     public function sendMailSansImages($sendTo, $object, $twig, $extraData = array())
    //     {
    //         $from = 'no-reply@5sur5sejour.com';
    //         $RefEmail = $this->em->getRepository(Ref::class)->find(28);
    //         $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));

    //   /*      $message = (new TemplatedEmail())
    //             ->from($from)
    //             ->to($sendTo)
    //             ->subject($object)

    //             ->htmlTemplate('emails/' . $twig . '.html.twig')
    //             ->context($extraData);
    //            */
    //             $message = new SendEmail($sendTo, $object ,  $twig , $extraData);
    //             $this->messageBus->dispatch($message);
    //           //  $this->newMailer->send($message);
    //     }
    //     public function sendMailSansAsync($sendTo, $object, $twig, $extraData = array())
    //     {
    //         $from = 'no-reply@5sur5sejour.com';
    //         $RefEmail = $this->em->getRepository(Ref::class)->find(28);
    //         $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));

    //         $message = (new TemplatedEmail())
    //             ->from($from)
    //             ->to($sendTo)
    //             ->subject($object)

    //             ->htmlTemplate('emails/' . $twig . '.html.twig')
    //             ->context($extraData);

    //             $message = new SendEmail($sendTo, $object ,  $twig , $extraData);

    //           // $this->newMailer->send($message);
    //     }
    //     /**
    //      * Generate a passwordless login link from user object
    //      * @author Firas Belhadj firasbelhadj686@gmail.com
    //      * @param UserInterface $user The user object
    //      * @return string The login link
    //      */
    //     public function requestLoginLink(UserInterface $user): string
    //     {
    //         $loginLinkDetails = $this->loginLinkHandler->createLoginLink($user);
    //         $loginLink = $loginLinkDetails->getUrl();
    //         return $loginLink;
    //     }
}
