<?php

namespace App\Service;
use App\Entity\AttachementEtiquette;
use App\Entity\CommentaireEtiquette;
use App\Entity\Etiquette;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Twig\Environment;
use Psr\Container\ContainerInterface;

class SupportService
{
    private $em;
    private $templating;
    private $container;
    public function __construct(ManagerRegistry $em,Environment  $templating,ContainerInterface $container)
    {
        $this->em = $em;
        $this->templating = $templating;
        $this->container = $container;
    }
    //Cette fct permet de créer une etiquette dans l'espace support
    function creationEtiquette($idUser,$titre,$description,$etat,$type,$listeSejour,$listePartenaire,$listeCommande,$listeLimite,$listeFiles)
    {
        $user=$this->em->getRepository(User::class)->find($idUser);
        $Etiquette=new Etiquette();
        $Etiquette->setTitre($titre);
        $Etiquette->setDescription($description);
        $Etiquette->setDateCreation(new \DateTime());
        $Etiquette->setRapporteur($user);
        $Etiquette->setEtat($etat);
        $Etiquette->setStatut($type);
        $this->em->getManager()->persist($Etiquette);
        $this->em->getManager()->flush();
        if(isset($listeSejour))
        {
            foreach ($listeSejour as $sejour)
            {
                $attachement=new AttachementEtiquette();
                $attachement->setPath($sejour);
                $attachement->setType("sejour");
                $attachement->setIdEtiquette($Etiquette);
                $this->em->getManager()->persist($attachement);
                $this->em->getManager()->flush();
            }
        }
        if(isset($listePartenaire))
        {
            foreach ($listePartenaire as $partenaire)
            {
                $attachement=new AttachementEtiquette();
                $attachement->setPath($partenaire);
                $attachement->setType("partenaire");
                $attachement->setIdEtiquette($Etiquette);
                $this->em->getManager()->persist($attachement);
                $this->em->getManager()->flush();
            }
        }
        if(isset($listeCommande))
        {
            foreach ($listeCommande as $commande)
            {
                $attachement=new AttachementEtiquette();
                $attachement->setPath($commande);
                $attachement->setType("commande");
                $attachement->setIdEtiquette($Etiquette);
                $this->em->getManager()->persist($attachement);
                $this->em->getManager()->flush();
            }
        }
        if(isset($listeLimite))
        {
            foreach ($listeLimite as $dateLimte)
            {
                $attachement=new AttachementEtiquette();
                $attachement->setPath($dateLimte);
                $attachement->setType("dateLimite");
                $attachement->setIdEtiquette($Etiquette);
                $this->em->getManager()->persist($attachement);
                $this->em->getManager()->flush();
            }
        }
        if(isset($listeFiles))
        {
            foreach ($listeFiles as $file)
            {
                $file=$this->em->getRepository(AttachementEtiquette::class)->find(intval($file));
                $file->setType("pieceJointe");
                $file->setIdEtiquette($Etiquette);
                $this->em->getManager()->persist($file);
                $this->em->getManager()->flush();
            }
        }
//        if(isset($listeCommentaire))
//        {
//            foreach ($listeCommentaire as $com)
//            {
//                $comment=$this->em->getRepository(CommentaireEtiquette::class)->find(intval($com));
//                $comment->setEtiquette($Etiquette);
//                $this->em->getManager()->persist($comment);
//                $this->em->getManager()->flush();
//            }
//
//        }
        return($Etiquette);
    }
    //cette fct permet d'ajouter commentaire dans l'etiquette
    function ajouterCommentaire($idUser,$text,$idTicket,$type)
    {
//        $Etiquette=$this->em->getRepository(Etiquette::class)->find($idEtiquette);
        $user=$this->em->getRepository(User::class)->find($idUser);
        $Ticket=$this->em->getRepository(Etiquette::class)->find($idTicket);
        $CommentaireEtiq=new CommentaireEtiquette();
        $CommentaireEtiq->setDateCreation(new \DateTime());
        $CommentaireEtiq->setText($text);
        $CommentaireEtiq->setCreateur($user);
        $CommentaireEtiq->setTypecommentaire($type);
        $CommentaireEtiq->setEtiquette($Ticket);
        $this->em->getManager()->persist($CommentaireEtiq);
        $this->em->getManager()->flush();
        return($CommentaireEtiq->getId());
    }
    //cette fct permet de modifier  commentaire dans l'etiquette
    function modifierCommentaire($text,$idCommentaire)
    {
        $CommentaireEtiq=$this->em->getRepository(CommentaireEtiquette::class)->find($idCommentaire);
        $CommentaireEtiq->setText($text);
        $this->em->getManager()->persist($CommentaireEtiq);
        $this->em->getManager()->flush();
        return ($CommentaireEtiq->getId());
    }
    //cette fct permet de changer le statut de l'etiquette(en cours,terminé,colturée,archivée)
    function changerStatut($idEtiquette,$statut)
    {
        $Etiquette=$this->em->getRepository(Etiquette::class)->find(intval($idEtiquette));
        $Etiquette->setEtat($statut);
        $this->em->getManager()->persist($Etiquette);
        $this->em->getManager()->flush();
        return($Etiquette);
    }
    //cette fct permet des envoyées des mails pour le support ou le rapporteur(Admin)
    function envoiAlert()
    {
    }
    //cette fct permet de récupperer la liste des étiquettes sauf colturée
    function listerEtiquette()
    {
        $Etiquettes= $this->em->getRepository(Etiquette::class)->findAll();
        return $Etiquettes;
    }
    //cette fct permet de find etiquette
    function voirEtiquette($idEtiquette)
    {
        $Etiquette= $this->em->getRepository(Etiquette::class)->find($idEtiquette);
        return $Etiquette;
    }
    function creationAttachement($file,$port,$host)
    {
        $attachement=new AttachementEtiquette();
        $milliseconds = round(microtime(true) * 1000);
        $dateJour=date('dmy');
        $codeZip=$dateJour.'-'.$milliseconds;
         $originalName = $file->getClientOriginalName();
        $size=$file->getSize();
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        $name="PJ"."_".$codeZip;
//                $file->move($this->container->get('kernel')->getProjectDir() . '/public/pdfDocs/'.$name);
//                  $attachement->setPath( 'http://'.$host.':'.$port.'/pdfDocs/'.$name);
        $arrayext=explode('.',$originalName);
        $folder='pieceJointe';
        move_uploaded_file($file, 'pdfDocs/'.$folder.'/' . $name.'-'.$originalName);
        $attachement->setPath( 'https://'.$host.'/pdfDocs/'.$folder.'/' . $name.'-'.$originalName);
        $attachement->setSize($size);
        $attachement->setDateCreation(new \DateTime());
        $attachement->setExtension($arrayext[sizeof($arrayext)-1]);
        $attachement->setType('pieceJointe');
        $this->em->getManager()->persist($attachement);
        $this->em->getManager()->flush();
        return $attachement;
    }
    //cette fct permet de modifier l'etiquette
    //Cette fct permet de créer une etiquette dans l'espace support
    function modifierTicket($idEtiquette,$titre,$description,$etat,$type,$listeSejour,$listePartenaire,$listeCommande,$listeLimite,$listeFiles)
    {
        $Etiquette=$this->em->getRepository(Etiquette::class)->find(intval($idEtiquette));
        $Etiquette->setTitre($titre);
        $Etiquette->setDescription($description);
        $Etiquette->setDateCreation(new \DateTime());
        $Etiquette->setEtat($etat);
        $Etiquette->setStatut($type);
        $this->em->getManager()->persist($Etiquette);
        $this->em->getManager()->flush();
        if(isset($listeSejour))
        {
            foreach ($listeSejour as $sejour)
            {
                $attachement=new AttachementEtiquette();
                $attachement->setPath($sejour);
                $attachement->setType("sejour");
                $attachement->setIdEtiquette($Etiquette);
                $this->em->getManager()->persist($attachement);
                $this->em->getManager()->flush();
            }
        }
        if(isset($listePartenaire))
        {
            foreach ($listePartenaire as $partenaire)
            {
                $attachement=new AttachementEtiquette();
                $attachement->setPath($partenaire);
                $attachement->setType("partenaire");
                $attachement->setIdEtiquette($Etiquette);
                $this->em->getManager()->persist($attachement);
                $this->em->getManager()->flush();
            }
        }
        if(isset($listeCommande))
        {
            foreach ($listeCommande as $commande)
            {
                $attachement=new AttachementEtiquette();
                $attachement->setPath($commande);
                $attachement->setType("commande");
                $attachement->setIdEtiquette($Etiquette);
                $this->em->getManager()->persist($attachement);
                $this->em->getManager()->flush();
            }
        }
        if(isset($listeLimite))
        {
            $oldDateLimite=$this->em->getRepository(AttachementEtiquette::class)->findBy(array('type'=>'dateLimite','idEtiquette'=>$Etiquette));
           if($oldDateLimite!=null)
           {
               foreach ($oldDateLimite as $oldDate)
               {
                   $oldDate->setStatut("deleted");
                   $this->em->getManager()->persist($oldDate);
                   $this->em->getManager()->flush();
               }
           }
            foreach ($listeLimite as $dateLimte)
            {
                $attachement=new AttachementEtiquette();
                $attachement->setPath($dateLimte);
                $attachement->setType("dateLimite");
                $attachement->setIdEtiquette($Etiquette);
                $this->em->getManager()->persist($attachement);
                $this->em->getManager()->flush();
            }
        }
        if(isset($listeFiles))
        {
            foreach ($listeFiles as $file)
            {
                $file=$this->em->getRepository(AttachementEtiquette::class)->find(intval($file));
                $file->setType("pieceJointe");
                $file->setIdEtiquette($Etiquette);
                $this->em->getManager()->persist($file);
                $this->em->getManager()->flush();
            }
        }
        return($Etiquette);
    }
    function SupprimerAttachementTicket($idAttachement)
    {
        $file=$this->em->getRepository(AttachementEtiquette::class)->find($idAttachement);
        $file->setStatut('deleted');
        $this->em->getManager()->persist($file);
        $this->em->getManager()->flush();
        return $file;
    }
    function creerUserSupport()
    {
    }
    function supprimerCommentaire($id)
    {
        $commentaire=$this->em->getRepository(CommentaireEtiquette::class)->find($id);
        $commentaire->setStatut('deleted');
        $this->em->getManager()->persist($commentaire);
        $this->em->getManager()->flush();
        return $commentaire;
    }
}