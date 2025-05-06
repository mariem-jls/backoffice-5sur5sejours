<?php

namespace App\Service;
use App\Entity\Produit;
use App\Entity\SejourAttachment;
use App\Entity\Etablisment;
use App\Entity\ParentSejour;
use App\Entity\Commande;
use App\Entity\Cart;
use App\Entity\Ref;
use App\Entity\Sejour;
use App\Entity\Attachment;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Twig\Environment;

class HomeService
{
    private ManagerRegistry $doctrine;
    private FormFactoryInterface $formFactory;
    private Environment $templating;
    
    public function __construct(ManagerRegistry $doctrine, FormFactoryInterface $formFactory, Environment  $templating)
    {
        $this->doctrine = $doctrine;
        $this->formFactory = $formFactory;
        $this->templating = $templating;
    }
    //liste des sjour Creré
    public function listesejourcrer()
    {
        $list = $this->doctrine->getRepository(Sejour::class)->findAll();
        return $list;
    }
    //liste des sjour Active
    public function sejouractive($dateTimeDebut = null, $dateTimeFin = null)
    {
        $sta = 'ACtive';
        //dd($date);
        //  $id= $this->doctrine->getRepository(Ref::class)->findby(array('libiller' =>$sta ));
        if ($dateTimeDebut == null || $dateTimeFin == null) {
            $list = $this->doctrine->getRepository(Sejour::class)->searshTypeAndesansDate();
        } else {
            $list = $this->doctrine->getRepository(Sejour::class)->searshTypeAndeDate($dateTimeDebut, $dateTimeFin);
        }
        return $list;
    }
    //Nb de connxtion*
    public function nombreConnxtion($date = null)
    {
        if ($date == null) {
            $NBCnxx = $this->doctrine->getRepository(ParentSejour::class)->searshNbconnxionSejourAllnodate();
        } else {
            $NBCnxx = $this->doctrine->getRepository(ParentSejour::class)->searshNbconnxionSejourAvecnodate($date);
        }
        return $NBCnxx;
    }
    //pie graphe sejour(ecole,partenaire Cse)
    public function sejourpie($type, $dateTimeDebut = null, $dateTimeFin = null)
    {
        // a faire
        // $type= $this->doctrine->getRepository(Etablisment::class)->findby(array('typeetablisment' =>  $ecole ));
        //   liste= $this->doctrine->getRepository(Sejour::class)->findby(array('idEtablisment' => $type ));
        if ($dateTimeDebut == null || $dateTimeFin == null) {
            return $this->doctrine->getRepository(Sejour::class)->searshTypeEtab($type);
        } else {
            return $this->doctrine->getRepository(Sejour::class)->searshTypeEtabByDate($type, $dateTimeDebut, $dateTimeFin);
        }
    }
    public function sejourpierep($type, $dateTimeDebut = null, $dateTimeFin = null)
    {
        if ($dateTimeDebut == null || $dateTimeFin == null) {
            return $this->doctrine->getRepository(Sejour::class)->searshTypeEtabRep($type);
        } else {
            return $this->doctrine->getRepository(Sejour::class)->searshTypeEtabRepByDate($type, $dateTimeDebut, $dateTimeFin);
        }
    }
    public function comandehtp($dateTimeDebut = null, $dateTimeFin = null)
    {
        if ($dateTimeDebut == null || $dateTimeFin == null) {
            return $this->doctrine->getRepository(Commande::class)->searshTypeEmth();
        } else {
            return $this->doctrine->getRepository(Commande::class)->searshTypeEmthByDate($dateTimeDebut, $dateTimeFin);
        }
    }
    public function comandetrth($dateTimeDebut = null, $dateTimeFin = null)
    {
        if ($dateTimeDebut == null || $dateTimeFin == null) {
            return $this->doctrine->getRepository(Commande::class)->searshTypeEmthh();
        } else {
            return $this->doctrine->getRepository(Commande::class)->searshTypeEmthhByDate($dateTimeDebut, $dateTimeFin);
        }
    }
    public function comandehtreversment($dateTimeDebut = null, $dateTimeFin = null)
    {
        if ($dateTimeDebut == null || $dateTimeFin == null) {
            return $this->doctrine->getRepository(Commande::class)->searshTypeEmthreversment();
        } else {
            return $this->doctrine->getRepository(Commande::class)->searshTypeEmthreversmentByDate($dateTimeDebut, $dateTimeFin);
        }
    }
    public function cartvisite($dateTimeDebut = null, $dateTimeFin = null)
    {
        if ($dateTimeDebut == null || $dateTimeFin == null) {
            return $this->doctrine->getRepository(Cart::class)->searshTypeCarteecole();
        } else {
            return $this->doctrine->getRepository(Cart::class)->searshTypeCarteecoleByDate($dateTimeDebut, $dateTimeFin);
        }
    }
    public function cartvisitecse($date = null)
    {
        if ($date == null) {
            return $this->doctrine->getRepository(Cart::class)->searshTypeCartcse();
        } else {
            return $this->doctrine->getRepository(Cart::class)->searshTypeCartcseByDate($date);
        }
    }
    public function cartvisitpart($date = null)
    {
        if ($date == null) {
            return $this->doctrine->getRepository(Cart::class)->searshTypeCartpart();
        } else {
            return $this->doctrine->getRepository(Cart::class)->searshTypeCartpartByDate($date);
        }
    }
    public function messagevisitpart($date = null)
    {
        if ($date == null) {
            return $this->doctrine->getRepository(SejourAttachment::class)->searshTypeMessagtpart();
        } else {
            return $this->doctrine->getRepository(SejourAttachment::class)->searshTypeMessagtpartByDate($date);
        }
    }
    public function messgvisitecole($date = null)
    {
        if ($date == null) {
            return $this->doctrine->getRepository(SejourAttachment::class)->searshTypeMessagtecole();
        } else {
            return $this->doctrine->getRepository(SejourAttachment::class)->searshTypeMessagtecoleByDate($date);
        }
    }
    public function messgvisitcse($date = null)
    {
        if ($date == null) {
            return $this->doctrine->getRepository(SejourAttachment::class)->searshTypeMessagtcse();
        } else {
            return $this->doctrine->getRepository(SejourAttachment::class)->searshTypeMessagtcseByDate($date);
        }
    }
    public function photovisitcse($date = null)
    {
        if ($date == null) {
            return $this->doctrine->getRepository(SejourAttachment::class)->searshTypePhotogtcse();
        } else {
            return $this->doctrine->getRepository(SejourAttachment::class)->searshTypePhotogtcseByDate($date);
        }
    }
    public function photovisitecole($date = null)
    {
        if ($date == null) {
            return $this->doctrine->getRepository(SejourAttachment::class)->searshTypePhotogtecole();
        } else {
            return $this->doctrine->getRepository(SejourAttachment::class)->searshTypePhotogtecoleByDate($date);
        }
    }
    public function photovisitepart($date = null)
    {
        if ($date == null) {
            return $this->doctrine->getRepository(SejourAttachment::class)->searshTypePhotogtpart();
        } else {
            return $this->doctrine->getRepository(SejourAttachment::class)->searshTypePhotogtpartByDate($date);
        }
    }
}
