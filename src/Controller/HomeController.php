<?php

namespace App\Controller;

use Exception;
use ZipArchive;
use Swift_Image;
use DateInterval;
use Dompdf\Dompdf;
use App\Entity\Ref;
use Dompdf\Options;
use App\Entity\Page;
use App\Entity\User;
use Twig\Environment;
use App\Entity\Panier;
use App\Entity\Sejour;
use App\Entity\Produit;
use setasign\Fpdi\Fpdi;
use App\Entity\Commande;
use App\Entity\Emailing;
use App\Entity\Etiquette;
use App\Entity\Attachment;
use App\Entity\Promotions;
use App\Message\SendEmail;
use App\Entity\Etablisment;
use App\Entity\PromoSejour;
use App\Entity\Typeproduit;
use App\Entity\ParentSejour;
use App\Entity\PromoParents;
use App\Service\HomeService;
use App\Service\UserService;
use setasign\Fpdi\PdfReader;
use App\Entity\PanierProduit;
use App\Service\JetonService;
use App\Entity\ComandeProduit;
use App\Entity\Comptebancaire;
use App\Service\SejourService;
use App\Entity\CommandeProduit;
use App\Service\ComandeService;
use App\Entity\SejourAttachment;
use App\Service\DashbordService;
use App\Service\EmailingService;
use App\Entity\CommandeComptable;
use App\Entity\Documentpartenaire;
use App\Service\StatistiqueService;
use App\Entity\AttachementEtiquette;
use App\Entity\CommentaireEtiquette;
use App\Entity\EmailsSejoursEnMasse;
use App\Service\TypeProduiteService;
use App\Service\EtablissementService;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Doctrine\Persistence\ManagerRegistry;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Entity\TypeProduitConditionnement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Qipsius\TCPDFBundle\Controller\TCPDFController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use PhpOffice\PhpSpreadsheet\Shared\Date as PHPExcel_Shared_Date;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class HomeController extends AbstractController
{
    private $em;
    private $templating;
    private $params;
    private $sejourService;
    private $homeService;
    private $statistique;
    private $typeproduitsevice;
    private $userService;
    private $comandeService;
    private SessionInterface $session;
    private $messageBus;
    private $etablissementService;

    public function __construct(
        ManagerRegistry $em,  
        ParameterBagInterface $params, 
        SejourService $sejourService,      
        HomeService $homeService,
        StatistiqueService $statistique,
          TypeProduiteService $typeproduitsevice, 
          EtablissementService $etablissementService,
          UserService $userService, 
              ComandeService $comandeService, 
                 Environment $templating,
                   SessionInterface $session,
                     MessageBusInterface $messageBus
    )
    {
        $this->em = $em;
        $this->templating = $templating;
        $this->sejourService = $sejourService;
        $this->homeService = $homeService;
        $this->statistique = $statistique;
        $this->typeproduitsevice = $typeproduitsevice;
        $this->userService = $userService;
        $this->comandeService = $comandeService;
        $this->etablissementService = $etablissementService;
        $this->params = $params;
        $this->session = $session;
        $this->messageBus = $messageBus;
    }
    
    

    #[Route('/', name: 'home')]
    public function index(): Response
    {

        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('app_login');
        }
        
      // First day of the month.
      $datedebut = date('01/m/Y');
      // Last day of the month.
      $datefin = date('t/m/Y');
      // First day of last month.
      $firstdayprevmonth = date('01/m/Y', strtotime('-1 months'));
      // Last day of last month.
      $lastdayprevmonth = date('t/m/Y', strtotime('-1 months'));
      $datedebut = \DateTime::createFromFormat('d/m/Y', $datedebut)->setTime(0, 0);
      $datefin = \DateTime::createFromFormat('d/m/Y', $datefin)->setTime(0, 0);
      $firstdayprevmonth = \DateTime::createFromFormat('d/m/Y', $firstdayprevmonth)->setTime(0, 0);
      $lastdayprevmonth = \DateTime::createFromFormat('d/m/Y', $lastdayprevmonth)->setTime(0, 0);
        //   $session = $this->get('session');
        //   $session->set('datedebut', '');
        //   $session->set('datefin', '');
        //   $session->set('part', '');
        //   $session->set('typePart', '');
        //   $session->set('firstdayprevmonth', '');
        //   $session->set('lastdayprevmonth', '');
        //   $session->set('page', 'stat');
      //        SEJOURS EN COURS TOTAL MOIS
      $sejourEncour = $this->em->getRepository(Sejour::class)->findSejourMonthEncour($datefin, $datedebut);
      $sejourEncourLast = $this->em->getRepository(Sejour::class)->findSejourMonthEncour($lastdayprevmonth, $firstdayprevmonth);
      //        SEJOURS EN COURS GRATUITS MOIS
      $sejourEncourFree = $this->em->getRepository(Sejour::class)->findSejourEncourMonthFree($datefin, $datedebut);
      //EF
      $sejourEncourFree_EF = $this->em->getRepository(Sejour::class)->findSejourEncourMonthFreeEcole($datefin, $datedebut);
      //PF
      $sejourEncourFree_PF = $this->em->getRepository(Sejour::class)->findSejourEncourMonthFreePartenaire($datefin, $datedebut);
      //        SEJOURS EN COURS PAYANT MOIS
      $sejourEncourPayent = $this->em->getRepository(Sejour::class)->findSejourEncourMonthPayant($datefin, $datedebut);
      //    SEJOURS EN COURS GRATUITS DERNIER MOIS
      $sejourEncourFreeLast = $this->em->getRepository(Sejour::class)->findSejourEncourMonthFree($lastdayprevmonth, $firstdayprevmonth);
      //        SEJOURS EN COURS PAYANT DERNIER MOIS
      $sejourEncourPayentLast = $this->em->getRepository(Sejour::class)->findSejourEncourMonthPayant($lastdayprevmonth, $firstdayprevmonth);
      //        SEJOURS EN COURS ACTIFS MOIS
      $sejourEncourHaveAttach = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttach($datefin, $datedebut);
      $sejourEncourHaveAttachLast = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttach($lastdayprevmonth, $firstdayprevmonth);
      //        SEJOURS EN COURS ACTIFS MOIS
      $sejourEncourHaveAttachFree = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachFree($datefin, $datedebut);
      //EF
      $sejourEncourHaveAttachFree_EF = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachFree_ECOLE($datefin, $datedebut);
      //PF
      $sejourEncourHaveAttachFree_PF = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachFree_PARTENAIRE($datefin, $datedebut);
      $sejourEncourHaveAttachFreeLast = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachFree($lastdayprevmonth, $firstdayprevmonth);
      //        SEJOURS EN COURS ACTIFS MOIS
      $sejourEncourHaveAttachPay = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachPay($datefin, $datedebut);
      $sejourEncourHaveAttachPayLast = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachPay($lastdayprevmonth, $firstdayprevmonth);
      //        dd($sejourEncourHaveAttachPayLast);
      //        var_dump($sejourEncourPayent); var_dump($sejourEncourHaveAttachPayLast);dd($sejourEncourHaveAttachPay);
      if (intval($sejourEncourPayentLast[0][1]) != 0) {
          $pourcentEncourPay = (((intval($sejourEncourPayent[0][1]) - intval($sejourEncourPayentLast[0][1])) / intval($sejourEncourPayentLast[0][1])) * 100);
      } else {
          $pourcentEncourPay = 0;
      }
      if (intval($sejourEncourFreeLast[0][1]) != 0) {
          $pourcentEncourFree = (((intval($sejourEncourFree[0][1]) - intval($sejourEncourFreeLast[0][1])) / intval($sejourEncourFreeLast[0][1])) * 100);
      } else {
          $pourcentEncourFree = 0;
      }
      if (sizeOf($sejourEncourHaveAttachLast) != 0) {
          $pourcentActif = (((sizeOf($sejourEncourHaveAttach) - sizeOf($sejourEncourHaveAttachLast)) / sizeOf($sejourEncourHaveAttachLast)) * 100);
      } else {
          $pourcentActif = 0;
      }
      if (sizeOf($sejourEncourHaveAttachFreeLast) != 0) {
          $pourcentActifFree = (((sizeOf($sejourEncourHaveAttachFree) - sizeOf($sejourEncourHaveAttachFreeLast)) / sizeOf($sejourEncourHaveAttachFreeLast)) * 100);
      } else {
          $pourcentActifFree = 0;
      }
      if (sizeOf($sejourEncourHaveAttachPayLast) != 0) {
          $pourcentActifPay = (((sizeOf($sejourEncourHaveAttachPay) - sizeOf($sejourEncourHaveAttachPayLast)) / sizeOf($sejourEncourHaveAttachPayLast)) * 100);
      } else {
          $pourcentActifPay = 0;
      }
      $findSejourEncourParent = $this->em->getRepository(Sejour::class)->findSejourEncourParent();
      $findSejourEncourPayantParent = $this->em->getRepository(Sejour::class)->findSejourEncourPayantParent();
      $findSejourEncourFreeParent = $this->em->getRepository(Sejour::class)->findSejourEncourFreeParentBetween($datefin, $datedebut);
      //EF
      $findSejourEncourFreeParent_EF = $this->em->getRepository(Sejour::class)->findSejourEncourFreeParentBetween_ECOLE($datefin, $datedebut);
      //PF
      $findSejourEncourFreeParent_PF = $this->em->getRepository(Sejour::class)->findSejourEncourFreeParentBetween_PARTENAIRE($datefin, $datedebut);
      $findSejourEncourPayantParentPaye = $this->em->getRepository(Sejour::class)->findSejourEncourPayantParentPayeBetween($datefin, $datedebut);
      $findSejourEncourFreeParentLast = $this->em->getRepository(Sejour::class)->findSejourEncourFreeParentBetween($lastdayprevmonth, $firstdayprevmonth);
      $findSejourEncourPayantParentPayeLast = $this->em->getRepository(Sejour::class)->findSejourEncourPayantParentPayeBetween($lastdayprevmonth, $firstdayprevmonth);
      //var_dump($findSejourEncourPayantParentPaye);dd($findSejourEncourPayantParentPayeLast);
      if ($findSejourEncourPayantParentPayeLast[0][1] != 0) {
          $pourcentuserCnxPay = ((($findSejourEncourPayantParentPaye[0][1] - $findSejourEncourPayantParentPayeLast[0][1]) / $findSejourEncourPayantParentPayeLast[0][1]) * 100);
      } else {
          $pourcentuserCnxPay = 0;
      }
      if ($findSejourEncourFreeParentLast[0][1] != 0) {
          $pourcentuserCnxFree = ((($findSejourEncourFreeParent[0][1] - $findSejourEncourFreeParentLast[0][1]) / $findSejourEncourFreeParentLast[0][1]) * 100);
      } else {
          $pourcentuserCnxFree = 0;
      }
      $totalCnxParent = $findSejourEncourFreeParent[0][1] + $findSejourEncourPayantParentPaye[0][1];
      $totalCnxParentLast = $findSejourEncourFreeParentLast[0][1] + $findSejourEncourPayantParentPayeLast[0][1];
      if ($totalCnxParentLast != 0) {
          $pourcentuserCnx = ((($totalCnxParent - $totalCnxParentLast) / $totalCnxParentLast) * 100);
      } else {
          $pourcentuserCnx = 0;
      }
      $SEjourServiceB = $this->sejourService;
      $commndeService = $this->statistique;
      $serviceuser = $this->etablissementService;
      $listeEtablissement = $serviceuser->getEtablissementPartenaireFiltre();
      $getNbrSejourCree = $SEjourServiceB->getNbrSejourCree($datedebut, $datefin);
      $getNbrSejourCreeLast = $SEjourServiceB->getNbrSejourCree($firstdayprevmonth, $lastdayprevmonth);
      if (sizeOf($getNbrSejourCreeLast) != 0) {
          $pourcentCreer = (((sizeOf($getNbrSejourCree) - sizeOf($getNbrSejourCreeLast)) / sizeOf($getNbrSejourCreeLast)) * 100);
      } else {
          $pourcentCreer = 0;
      }
      if (sizeof($getNbrSejourCree) != 0) {
          $tauxSej = (sizeOf($sejourEncourHaveAttach) / sizeOf($getNbrSejourCree)) * 100;
      } else {
          $tauxSej = 0;
      }
      $FindeUSerParentBetween = $SEjourServiceB->FindeUSerParentBetween($datedebut, $datefin);
      $FindeUSerParentActiveBetween = $SEjourServiceB->FindeUSerParentActiveBetween($datedebut, $datefin);
      $FindeUSerParentBetweenLast = $SEjourServiceB->FindeUSerParentBetween($firstdayprevmonth, $lastdayprevmonth);
      $FindeUSerParentActiveBetweenLast = $SEjourServiceB->FindeUSerParentActiveBetween($firstdayprevmonth, $lastdayprevmonth);
      if (sizeOf($FindeUSerParentBetweenLast) != 0) {
          $pourcentuserCreate = (((sizeOf($FindeUSerParentBetween) - sizeOf($FindeUSerParentBetweenLast)) / sizeOf($FindeUSerParentBetweenLast)) * 100);
      } else {
          $pourcentuserCreate = 0;
      }
      if (sizeOf($FindeUSerParentActiveBetweenLast) != 0) {
          $pourcentuserActive = (((sizeOf($FindeUSerParentActiveBetween) - sizeOf($FindeUSerParentActiveBetweenLast)) / sizeOf($FindeUSerParentActiveBetweenLast)) * 100);
      } else {
          $pourcentuserActive = 0;
      }
      $EnfantDeclare = $SEjourServiceB->FormaterarraydateBetweennbrEnfant($sejourEncourHaveAttach, 'getDateDebutSejour', 'getDateFinSejour', $datedebut, $datefin);
      $EnfantDeclareLast = $SEjourServiceB->FormaterarraydateBetweennbrEnfant($sejourEncourHaveAttachLast, 'getDateDebutSejour', 'getDateFinSejour', $firstdayprevmonth, $lastdayprevmonth);
      if (sizeOf($sejourEncourHaveAttach) != 0) {
          $EnfantDeclare = intval($EnfantDeclare['totenf']) / sizeOf($sejourEncourHaveAttach);
      } else {
          $EnfantDeclare = 0;
      }
      if (sizeOf($sejourEncourHaveAttachLast) != 0) {
          $EnfantDeclareLast = intval($EnfantDeclareLast['totenf']) / sizeOf($sejourEncourHaveAttachLast);
      } else {
          $EnfantDeclareLast = 0;
      }
      if ($EnfantDeclareLast != 0) {
          $pourcentEnfant = ((($EnfantDeclare - $EnfantDeclareLast) / $EnfantDeclareLast) * 100);
      } else {
          $pourcentEnfant = 0;
      }
      //dd($EnfantDeclare);
      $findParentDateBetween = $SEjourServiceB->findParentDateBetween($datedebut, $datefin);
      $parentcnct = $SEjourServiceB->FormaterarraydateBetweennbrPArentCncte($findParentDateBetween, 'getDateCreation', $datedebut, $datefin);
      if (sizeOf($FindeUSerParentBetween) != 0) {
          //  $TauxCmpt = ($totalCnxParent/sizeOf($FindeUSerParentBetween))*100;
          //04-09-2021 => Touhemi cette tâche demandée par Ramzi 
          if (sizeOf($sejourEncourHaveAttach)) {
              $TauxCmpt = (($totalCnxParent) / (sizeOf($sejourEncourHaveAttach) * $EnfantDeclare)) * 100;
          } else {
              $TauxCmpt = 0;
          }
      } else {
          $TauxCmpt = 0;
      }
      $ListDesCommande = $this->em->getRepository(Commande::class)->ListDesCommande($datedebut, $datefin);
    //   dd($ListDesCommande);
      $ListDesCommandeLast = $this->em->getRepository(Commande::class)->ListDesCommande($firstdayprevmonth, $lastdayprevmonth);
      $nbrCmdParSejour = $commndeService->CommandePArSejour($datedebut, $datefin);
      $nbrCmdParSejourLast = $commndeService->CommandePArSejour($firstdayprevmonth, $lastdayprevmonth);
      $nbrProduit = $this->em->getRepository(Commande::class)->nbrDesProduit($datedebut, $datefin);
    //   dd($nbrProduit);
      $nbrProduitLast = $this->em->getRepository(Commande::class)->nbrDesProduit($firstdayprevmonth, $lastdayprevmonth);
      if (sizeof($ListDesCommande) == 0) {
          $nbrProduitPartCommande = 0;
      } else {
          $nbrProduitPartCommande = $nbrProduit[0][1] / sizeof($ListDesCommande);
      }
      if (sizeof($ListDesCommandeLast) == 0) {
          $nbrProduitPartCommandeLast = 0;
      } else {
          $nbrProduitPartCommandeLast = $nbrProduitLast[0][1] / sizeof($ListDesCommandeLast);
      }
      if (sizeof($ListDesCommandeLast) != 0) {
          $pourcentNbrCmd = (((sizeOf($ListDesCommande) - sizeOf($ListDesCommandeLast)) / sizeOf($ListDesCommandeLast)) * 100);
      } else {
          $pourcentNbrCmd = 0;
      }
      if ($nbrCmdParSejourLast != 0) {
          $pourcentNbrCmdSej = ((($nbrCmdParSejour - $nbrCmdParSejourLast) / $nbrCmdParSejourLast) * 100);
      } else {
          $pourcentNbrCmdSej = 0;
      }
      if ($nbrProduitLast[0][1] != 0) {
          $pourcentNbrProd = ((($nbrProduit[0][1] - $nbrProduitLast[0][1]) / $nbrProduitLast[0][1]) * 100);
      } else {
          $pourcentNbrProd = 0;
      }
      if ($nbrProduitPartCommandeLast != 0) {
          $pourcentProdCmd = ((($nbrProduitPartCommande - $nbrProduitPartCommandeLast) / $nbrProduitPartCommandeLast) * 100);
      } else {
          $pourcentProdCmd = 0;
      }
      $ConnexionsCa = $this->em->getRepository(ParentSejour::class)->caConnexionTotal($datedebut, $datefin);
      $ConnexionsCaLast = $this->em->getRepository(ParentSejour::class)->caConnexionTotal($firstdayprevmonth, $lastdayprevmonth);
      $caConnexion = $commndeService->CalculeCaConnexion($ConnexionsCa, 'getDateCreation', $datedebut, $datefin);
      $caConnexionLast = $commndeService->CalculeCaConnexion($ConnexionsCaLast, 'getDateCreation', $firstdayprevmonth, $lastdayprevmonth);
      $caConnexion['reverCnx'] = $caConnexion['reverCnx'];
      $caConnexionLast['reverCnx'] = $caConnexionLast['reverCnx'];
      $reversementProduit = $commndeService->getReversementProduit($ListDesCommande);
      $reversementProduitLast = $commndeService->getReversementProduit($ListDesCommandeLast);
      $reversementTotal = $reversementProduit + $caConnexion["reverCnx"];
      $reversementTotalLast = $reversementProduitLast + $caConnexionLast["reverCnx"];
      //var_dump($ConnexionsCa);
      $caProduits = $commndeService->getSommeProduit($ListDesCommande)['totalCaProduit'];
      $totalEnv = $commndeService->getSommeProduit($ListDesCommande)['totalEnv'];
      $nbrCmds = $commndeService->getSommeProduit($ListDesCommande)['nbrCmd'];
      $nbrCmdsLast = $commndeService->getSommeProduit($ListDesCommandeLast)['nbrCmd'];
      $caProduitsLast = $commndeService->getSommeProduit($ListDesCommandeLast)['totalCaProduit'];
      //dd($caProduit);
      if ($nbrCmds != 0) {
          $PanierMoyen = $caProduits / $nbrCmds;
      } else {
          $PanierMoyen = 0;
      }
      if ($nbrCmdsLast != 0) {
          $PanierMoyenLast = $caProduitsLast / $nbrCmdsLast;
      } else {
          $PanierMoyenLast = 0;
      }
      if ($caConnexionLast['cacnxtotal'] != 0) {
          $pourcentCaCnx = ((($caConnexion['cacnxtotal'] - $caConnexionLast['cacnxtotal']) / $caConnexionLast['cacnxtotal']) * 100);
      } else {
          $pourcentCaCnx = 0;
      }
      if ($caConnexionLast['cacnxPay'] != 0) {
          $pourcentCaCnxPay = ((($caConnexion['cacnxPay'] - $caConnexionLast['cacnxPay']) / $caConnexionLast['cacnxPay']) * 100);
      } else {
          $pourcentCaCnxPay = 0;
      }
      if ($caConnexionLast['cacnxfree'] != 0) {
          $pourcentCaCnxFree = ((($caConnexion['cacnxfree'] - $caConnexionLast['cacnxfree']) / $caConnexionLast['cacnxfree']) * 100);
      } else {
          $pourcentCaCnxFree = 0;
      }
      if ($caConnexionLast['reverCnx'] != 0) {
          $pourcentCaRevCnx = ((($caConnexion['reverCnx'] - $caConnexionLast['reverCnx']) / $caConnexionLast['reverCnx']) * 100);
      } else {
          $pourcentCaRevCnx = 0;
      }
      if ($caProduitsLast != 0) {
          $pourcentCaProd = ((($caProduits - $caProduitsLast) / $caProduitsLast) * 100);
      } else {
          $pourcentCaProd = 0;
      }
      if ($PanierMoyenLast != 0) {
          $pourcentCaPan = ((($PanierMoyen - $PanierMoyenLast) / $PanierMoyenLast) * 100);
      } else {
          $pourcentCaPan = 0;
      }
      if ($reversementTotalLast != 0) {
          $pourcentCaRev = ((($reversementTotal - $reversementTotalLast) / $reversementTotalLast) * 100);
      } else {
          $pourcentCaRev = 0;
      }
      if ($reversementProduitLast != 0) {
          $pourcentCaRevProd = ((($reversementProduit - $reversementProduitLast) / $reversementProduitLast) * 100);
      } else {
          $pourcentCaRevProd = 0;
      }
      if ($reversementProduitLast != 0) {
          $pourcentCaRevProd = ((($reversementProduit - $reversementProduitLast) / $reversementProduitLast) * 100);
      } else {
          $pourcentCaRevProd = 0;
      }
      $tauxCa = (floatval($caConnexion['cacnxtotal']) + floatval($caProduits)) - (floatval($reversementTotal));
      if (($totalCnxParent) != 0) {
          $tauCmd = (sizeof($ListDesCommande) / ($totalCnxParent)) * 100;
      } else {
          $tauCmd = 0;
      }
      $sejourActifsTotal = $this->em->getRepository(Sejour::class)->findBy(array('statut' => '5'));
      $NbsejourActifsTotal = sizeof($sejourActifsTotal);
      return $this->render('index.html.twig', [
          'sejourEncourFree_EF' => $sejourEncourFree_EF[0][1], 'sejourEncourFree_PF' => $sejourEncourFree_PF[0][1],
          'sejourEncourHaveAttachFree_EF' => $sejourEncourHaveAttachFree_EF, 'sejourEncourHaveAttachFree_PF' => $sejourEncourHaveAttachFree_PF,
          'findSejourEncourFreeParent_EF' => $findSejourEncourFreeParent_EF[0][1], 'findSejourEncourFreeParent_PF' => $findSejourEncourFreeParent_PF[0][1],
          'caConnexions' => $caConnexion, 'caConnexionsLast' => $caConnexionLast, 'pourcentCaCnx' => $pourcentCaCnx,
          'pourcentCaCnxPay' => $pourcentCaCnxPay, 'pourcentCaCnxFree' => $pourcentCaCnxFree, 'tauxCa' => $tauxCa,
          'caProduits' => $caProduits, 'caProduitsLast' => $caProduitsLast, 'pourcentCaProd' => $pourcentCaProd,
          'PanierMoyen' => $PanierMoyen, 'PanierMoyenLast' => $PanierMoyenLast, 'pourcentCaPan' => $pourcentCaPan,
          'reversementTotal' => $reversementTotal, 'reversementTotalLast' => $reversementTotalLast, 'pourcentCaRev' => $pourcentCaRev,
          'reversementProduit' => $reversementProduit, 'reversementProduitLast' => $reversementProduitLast, 'pourcentCaRevProd' => $pourcentCaRevProd,
          'nbrDesCommande' => sizeof($ListDesCommande), 'pourcentNbrCmd' => $pourcentNbrCmd, 'pourcentCaRevCnx' => $pourcentCaRevCnx,
          'nbrDesCommandeLast' => sizeof($ListDesCommandeLast), 'tauCmd' => $tauCmd,
          'nbrCmdParSejour' => $nbrCmdParSejour,
          'nbrCmdParSejourLast' => $nbrCmdParSejourLast, 'pourcentNbrCmdSej' => $pourcentNbrCmdSej, 'pourcentNbrProd' => $pourcentNbrProd,
          'nbrProduitPartCommande' => $nbrProduitPartCommande, 'nbrDesProduit' => $nbrProduit[0][1], 'nbrDesProduitLast' => $nbrProduitLast[0][1],
          'nbrProduitPartCommandeLast' => $nbrProduitPartCommandeLast, 'pourcentProdCmd' => $pourcentProdCmd,
          'sejourEncour' => $sejourEncour,
          'NbsejourActifsTotal ' => $NbsejourActifsTotal,
          'sejourEncourLast' => $sejourEncourLast,
          'pourcentCreer' => $pourcentCreer,
          'sejourEncourFree' => $sejourEncourFree[0][1], 'pourcentEncourPay' => $pourcentEncourPay,
          'sejourEncourPayent' => $sejourEncourPayent[0][1], 'pourcentEncourFree' => $pourcentEncourFree,
          'sejourEncourFreeLast' => $sejourEncourFreeLast[0][1],
          'sejourEncourPayentLast' => $sejourEncourPayentLast[0][1],
          'sejourEncourHaveAttach' => $sejourEncourHaveAttach, 'tauxSej' => $tauxSej,
          'sejourEncourHaveAttachLast' => $sejourEncourHaveAttachLast, 'tauxCmpt' => $TauxCmpt,
          'sejourEncourHaveAttachPay' => $sejourEncourHaveAttachPay,
          'sejourEncourHaveAttachPayLast' => $sejourEncourHaveAttachPayLast, 'pourcentActifFree' => $pourcentActifFree,
          'sejourEncourHaveAttachFree' => $sejourEncourHaveAttachFree, 'pourcentActifPay' => $pourcentActifPay,
          'sejourEncourHaveAttachFreeLast' => $sejourEncourHaveAttachFreeLast,
          'pourcentActif' => $pourcentActif, 'pourcentEnfant' => $pourcentEnfant,
          'EnfantDeclareLast' => $EnfantDeclareLast,
          'findSejourEncourParent' => $findSejourEncourParent[0][1],
          'findSejourEncourFreeParent' => $findSejourEncourFreeParent[0][1],
          'findSejourEncourPayantParent' => $findSejourEncourPayantParent[0][1],
          'findSejourEncourPayantParentPaye' => $findSejourEncourPayantParentPaye[0][1],
          'ListePart' => $listeEtablissement,
          'EnfantDeclare' => $EnfantDeclare, 'parentCnct' => $parentcnct, 'usercreate' => $FindeUSerParentBetween, 'userActive' => $FindeUSerParentActiveBetween,
          'usercreateLast' => $FindeUSerParentBetweenLast, 'userActiveLast' => $FindeUSerParentActiveBetweenLast, 'sejourCrees' => $getNbrSejourCree, 'sejourCreesLast' => $getNbrSejourCreeLast, 'pourcentuserActive' => $pourcentuserActive, 'pourcentuserCreate' => $pourcentuserCreate,
          'findSejourEncourFreeParentLast' => $findSejourEncourFreeParentLast[0][1], 'findSejourEncourPayantParentPayeLast' => $findSejourEncourPayantParentPayeLast[0][1], 'pourcentuserCnxPay' => $pourcentuserCnxPay,
          'pourcentuserCnxFree' => $pourcentuserCnxFree, 'pourcentuserCnx' => $pourcentuserCnx, 'totalCnxParent' => $totalCnxParent, 'totalCnxParentLast' => $totalCnxParentLast
      ]);
    }

    /**
     * @Route("/DashboardJson", name="AccueilJson")
     */
    public function AccueilJsonAdmin(Request $request)
    {
        //Admin/Accueil
        $dateStrDebut = $request->get("dateDebut");
        $dateTimeDebut = date_create_from_format('Y-m-d', $dateStrDebut);
        $dateStrFin = $request->get("dateFin");
        $dateTimeFin = date_create_from_format('Y-m-d', $dateStrFin);
        // $session = $this->get('session');
        // $session->set('page', 'accueil');
        $SEjourService = $this->homeService;
        $listeSejour = $SEjourService->sejouractive($dateTimeDebut, $dateTimeFin);
        $SEjourServiceB = $this->sejourService;
        $listeSejourB = $SEjourServiceB->index($dateTimeDebut, $dateTimeFin);
        $Nbcnnx = $SEjourService->nombreConnxtion();
        $Nbmnt = $SEjourService->comandehtp($dateTimeDebut, $dateTimeFin);
        $NBmth = $SEjourService->comandetrth($dateTimeDebut, $dateTimeFin);
        $NBmthrevesm = $SEjourService->comandehtreversment($dateTimeDebut, $dateTimeFin);
        //
        $Nbecole = $SEjourService->sejourpierep("ECOLES/AUTRES", $dateTimeDebut, $dateTimeFin);
        $NbPartenaire = $SEjourService->sejourpierep("PARTENAIRES/VOYAGISTES", $dateTimeDebut, $dateTimeFin);
        $CSE = $SEjourService->sejourpierep("CSE", $dateTimeDebut, $dateTimeFin);
        $Nbecolegr1 = $SEjourService->sejourpie("ECOLES/AUTRES", $dateTimeDebut, $dateTimeFin);
        $NbPartenairegr1 = $SEjourService->sejourpie("PARTENAIRES/VOYAGISTES", $dateTimeDebut, $dateTimeFin);
        $CSEgr1 = $SEjourService->sejourpie("CSE", $dateTimeDebut, $dateTimeFin);
        $ecolecart = $SEjourService->cartvisite($dateTimeDebut, $dateTimeFin);
        $csecart = $SEjourService->cartvisitecse($dateTimeDebut, $dateTimeFin);
        $partcart = $SEjourService->cartvisitpart($dateTimeDebut, $dateTimeFin);
        //graphe message
        $partmssg = $SEjourService->messagevisitpart($dateTimeDebut, $dateTimeFin);
        $ecolem = $SEjourService->messgvisitecole($dateTimeDebut, $dateTimeFin);
        $cseem = $SEjourService->messgvisitcse($dateTimeDebut, $dateTimeFin);
        //graphe photo NB  Visisteur
        $cseephoto = $SEjourService->photovisitcse($dateTimeDebut, $dateTimeFin);
        $ecolephoto = $SEjourService->photovisitecole($dateTimeDebut, $dateTimeFin);
        $partphoto = $SEjourService->photovisitepart($dateTimeDebut, $dateTimeFin);
        //graphe connxtion comande par type de sejour transformation client
        //NB connxtion
        $NBcnnxecole = $SEjourServiceB->Nbpecnnxsejourtype('ECOLES/AUTRES', $dateTimeDebut, $dateTimeFin);
        $NBcnnxpart = $SEjourServiceB->Nbpecnnxsejourtype('PARTENAIRES/VOYAGISTES', $dateTimeDebut, $dateTimeFin);
        $NBcnnxcse = $SEjourServiceB->Nbpecnnxsejourtype('CSE', $dateTimeDebut, $dateTimeFin);
        //NB demande
        $NBcomandxecole = $SEjourServiceB->Nbcommandepartypesejour('ECOLES/AUTRES', $dateTimeDebut, $dateTimeFin);
        $NBcomandpart = $SEjourServiceB->Nbcommandepartypesejour('PARTENAIRES/VOYAGISTES', $dateTimeDebut, $dateTimeFin);
        $NBcomandcse = $SEjourServiceB->Nbcommandepartypesejour('CSE', $dateTimeDebut, $dateTimeFin);
        //panier moyen
        $panierecole = $SEjourServiceB->Moyenpainierpartypesejour('ECOLES/AUTRES', $dateTimeDebut, $dateTimeFin);
        $panierpart = $SEjourServiceB->Moyenpainierpartypesejour('PARTENAIRES/VOYAGISTES', $dateTimeDebut, $dateTimeFin);
        $paniercse = $SEjourServiceB->Moyenpainierpartypesejour('CSE', $dateTimeDebut, $dateTimeFin);
        if ($paniercse > $panierpart and $paniercse > $panierecole) {
            $grand = $paniercse;
        }
        $grandce = 1;
        $grandce = $grandce * $paniercse;
        if ($panierpart > $paniercse and $panierpart > $panierecole) {
            $grand = $panierpart;
            $grandpa = $grand * $panierpart;
        }
        $grandpa = 1;
        $grandpa = $grandpa * $panierpart;
        if ($panierecole > $panierpart and $panierecole > $paniercse) {
            $grand = $panierecole;
            $grandecol = $grand * $panierecole;
        }
        $grandecol = 1;
        $grandecol = $grandecol * $panierecole;
        //liste des produit les plu vendu
        $produitpluvendu = $this->typeproduitsevice;
        $typeProduitplusvendu = $produitpluvendu->produitlistType();
        $tab = [];
        $tab2 = [];
        $i = 0;
        foreach ($typeProduitplusvendu as $type) {
            $NbProduitsPLusVente = $produitpluvendu->ProduitPlusVente($type->getId(), $dateTimeDebut, $dateTimeFin);
            $tab[$i]['nbVente'] = count($NbProduitsPLusVente);
            $tab[$i]['nomProduit'] = $type->getLabeletype();
            //            array_push($tab2[$i],$tab[$type->getId()]);
            $i++;
        }
        arsort($tab);
        $sommeProduits = $produitpluvendu->LaSommeDesProduitsVendus($dateTimeDebut, $dateTimeFin);
        // dd(sizeof($listeSejour));
        //nombre parent ayont comande
        $NBpersone_comandecole = $SEjourServiceB->Nbpersone_commandepartypesejour('ECOLES/AUTRES', $dateTimeDebut, $dateTimeFin);
        $NBpersone_comandePart = $SEjourServiceB->Nbpersone_commandepartypesejour('PARTENAIRES/VOYAGISTES', $dateTimeDebut, $dateTimeFin);
        $NBpersone_comandeCse = $SEjourServiceB->Nbpersone_commandepartypesejour('CSE', $dateTimeDebut, $dateTimeFin);
        return new JsonResponse([
            'NBpersone_comandecole' => $NBpersone_comandecole,
            'NBpersone_comandePart' => $NBpersone_comandePart,
            'NBpersone_comandeCse' => $NBpersone_comandeCse,
            'ListeSejour' => sizeof($listeSejour),
            'listeSejourB' => sizeof($listeSejourB),
            'Nbcnnx' => $Nbcnnx,
            'Nbmnt' => ($Nbmnt),
            'NBmth' => ($NBmth),
            'NBmthrevesm' => ($NBmthrevesm),
            'Nbecole' => sizeof($Nbecole),
            'NbPartenaire' => sizeof($NbPartenaire),
            'CSE' => sizeof($CSE),
            'Nbecolegr1' => sizeof($Nbecolegr1),
            'NbPartenairegr1' => sizeof($NbPartenairegr1),
            'CSEgr1' => sizeof($CSEgr1),
            //ne pas oublier le reste
            'ecolecart' => $ecolecart,
            'csecart' => $csecart,
            'partcart' => $partcart,
            //message graphe
            'partmssg' => $partmssg,
            'ecolem' => $ecolem,
            'cseem' => $cseem,
            //photo graphe
            'cseephoto' => $cseephoto,
            'ecolephoto' => $ecolephoto,
            'partphoto' => $partphoto,
            //NB connxtion
            'NBcnnxecole' => $NBcnnxecole,
            'NBcnnxpart' => $NBcnnxpart,
            'NBcnnxcse' => $NBcnnxcse,
            //NB comande
            'NBcomandxecole' => $NBcomandxecole,
            'NBcomandpart' => $NBcomandpart,
            'NBcomandcse' => $NBcomandcse,
            // panier moyen pour cse,partenaire,ecole
            'paniercse' => $paniercse,
            'panierpart' => $panierpart,
            'panierecole' => $panierecole,
            'grandce' => $grandce,
            'grandpa' => $grandpa,
            'grandecol' => $grandecol,
            //liste des produit les plu vendu
            'typeProduitplusvendu' => $typeProduitplusvendu,
            'tableauContientTypeProduitsPlusVente' => $tab,
            'sommeProduits' => $sommeProduits,
            //dd($grand)
        ]);
    }


    /**
     * @Route("/ExportSejourXlsx", name="ExportSejourXlsx")
     */
    public function ExportSejourXlsx()
    {
        $session = $this->get('session');
        $datedebut =  $session->get('datedebut');
        $datefin = $session->get('datefin');
        $part = $session->get('part');
        $typePart = $session->get('typePart');
        $firstdayprevmonth = $session->get('firstdayprevmonth');
        $lastdayprevmonth = $session->get('lastdayprevmonth');
        if ((!(isset($datedebut)) || $datedebut === null || $datedebut === "") && (!(isset($datefin)) || $datefin === null || $datefin === "")) {
            $datedebut = date('01/m/Y');
            $datefin = date('t/m/Y');
            $firstdayprevmonth = date('01/m/Y', strtotime('-1 months'));
            $lastdayprevmonth = date('t/m/Y', strtotime('-1 months'));
            $datedebut = \DateTime::createFromFormat('d/m/Y', $datedebut)->setTime(0, 0);
            $datefin = \DateTime::createFromFormat('d/m/Y', $datefin)->setTime(0, 0);
            $firstdayprevmonth = \DateTime::createFromFormat('d/m/Y', $firstdayprevmonth)->setTime(0, 0);
            $lastdayprevmonth = \DateTime::createFromFormat('d/m/Y', $lastdayprevmonth)->setTime(0, 0);
        }
        $SEjourServiceB = $this->sejourService;
        if ($typePart != null && $typePart != "") {
            $findSejourEncourBetween = $SEjourServiceB->findSejourEncourBetweenParTypePart($datedebut, $datefin, $typePart);
            $findSejourActiveBetween = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachTypePart($datefin, $datedebut, $typePart);
        } elseif ($part != null && $part != "") {
            $findSejourEncourBetween = $SEjourServiceB->findSejourEncourBetweenParPart($datedebut, $datefin, $part);
            $findSejourActiveBetween = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachParPart($datefin, $datedebut, $part);
        } else {
            $findSejourEncourBetween = $this->em->getRepository(Sejour::class)->findSejourMonthEncour($datefin, $datedebut);
            $findSejourActiveBetween = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttach($datefin, $datedebut);
        }
        //        var_dump($findSejourActiveBetweenFormat);var_dump($usercreate);var_dump($userActive);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getActiveSheet()->setTitle("Séjours en cours");
        $sheet->getPageMargins()->setTop(1);
        $sheet->getPageMargins()->setRight(0.75);
        $sheet->getPageMargins()->setLeft(0.75);
        $sheet->getPageMargins()->setBottom(1);
        $sheet->setCellValue('A1', 'Code Sejour');
        $sheet->setCellValue('B1', 'theme du sejour');
        $sheet->setCellValue('C1', 'Date Creation Code');
        $sheet->setCellValue('D1', 'Date Fin Code');
        $sheet->setCellValue('E1', 'Date debut');
        $sheet->setCellValue('F1', 'Date Fin');
        $sheet->setCellValue('G1', 'Adresse de sejour');
        $sheet->setCellValue('H1', 'Statut');
        $sheet->setCellValue('I1', 'Partenaire');
        $sheet->setCellValue('J1', 'nbr enfants déclarer');
        $sheet->setCellValue('K1', 'Prix Connexion Parent');
        $sheet->setCellValue('L1', 'Prix connexion Partenaire ');
        $sheet->setCellValue('M1', 'Reversement Connexion Part');
        $sheet->setCellValue('N1', 'Reversement Produit Part');
        $sheet->setCellValue('O1', 'Nombre photo uploader');
        $sheet->setCellValue('P1', 'Nombre Video Uploader');
        $sheet->setCellValue('Q1', 'Nmbre message Uploader');
        $sheet->setCellValue('R1', 'Nmbre de connexions');
        $row = 2;
        foreach ($findSejourEncourBetween as $sejour) {
            // var_dump($this->em->getRepository(Attachment::class)->CountsearshSejourVideo($sejour->getId())[0]['COUNT(*)']);
            //var_dump($this->em->getRepository(Attachment::class)->CountsearshSejourAtachment($sejour->getId()));
            $sheet->setCellValue('A' . $row, $sejour->getCodeSejour());
            $sheet->setCellValue('B' . $row, $sejour->getThemSejour());
            $sheet->setCellValue('C' . $row, $sejour->getDateCreationCode());
            $sheet->setCellValue('D' . $row, $sejour->getDateFinCode());
            $sheet->setCellValue('E' . $row, $sejour->getDateDebutSejour());
            $sheet->setCellValue('F' . $row, $sejour->getDateFinSejour());
            $sheet->setCellValue('G' . $row, $sejour->getAdresseSejour() . ' ' . $sejour->getCodePostal() . ' ' . $sejour->getVille() . ' ' . $sejour->getPays());
            $sheet->setCellValue('H' . $row, $sejour->getStatut()->getLibiller());
            $sheet->setCellValue('I' . $row, $sejour->getIdEtablisment()->getNomEtab());
            $sheet->setCellValue('J' . $row, $sejour->getNbenfan());
            $sheet->setCellValue('K' . $row, $sejour->getPrixcnxparent());
            $sheet->setCellValue('L' . $row, $sejour->getPrixcnxpartenaire());
            $sheet->setCellValue('M' . $row, $sejour->getReversecnxpart() . '%');
            $sheet->setCellValue('N' . $row, $sejour->getReverseventepart() . '%');
            $sheet->setCellValue('O' . $row, $this->em->getRepository(Attachment::class)->CountsearshSejourAtachment($sejour->getId())[0]['COUNT(*)']);
            $sheet->setCellValue('P' . $row, $this->em->getRepository(Attachment::class)->CountsearshSejourMessage($sejour->getId())[0]['COUNT(*)']);
            $sheet->setCellValue('Q' . $row, $this->em->getRepository(Attachment::class)->CountsearshSejourVideo($sejour->getId())[0]['COUNT(*)']);
            $sheet->setCellValue('R' . $row, $sejour->getCountParentSejour());
            $row = $row + 1;
        }
        $spreadsheet->createSheet();
        // Add some data to the second sheet, resembling some different data types
        $spreadsheet->setActiveSheetIndex(1);
        $spreadsheet->getActiveSheet()->setTitle("Séjours Actifs");
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageMargins()->setTop(1);
        $sheet->getPageMargins()->setRight(0.75);
        $sheet->getPageMargins()->setLeft(0.75);
        $sheet->getPageMargins()->setBottom(1);
        $sheet->setCellValue('A1', 'Code Sejour');
        $sheet->setCellValue('B1', 'theme du sejour');
        $sheet->setCellValue('C1', 'Date Creation Code');
        $sheet->setCellValue('D1', 'Date Fin Code');
        $sheet->setCellValue('E1', 'Date debut');
        $sheet->setCellValue('F1', 'Date Fin');
        $sheet->setCellValue('G1', 'Adresse de sejour');
        $sheet->setCellValue('H1', 'Statut');
        $sheet->setCellValue('I1', 'Partenaire');
        $sheet->setCellValue('J1', 'nbr enfants déclarer');
        $sheet->setCellValue('K1', 'Prix Connexion Parent');
        $sheet->setCellValue('L1', 'Prix connexion Partenaire ');
        $sheet->setCellValue('M1', 'Reversement Connexion Part');
        $sheet->setCellValue('N1', 'Reversement Produit Part');
        $sheet->setCellValue('O1', 'Nombre photo uploader');
        $sheet->setCellValue('P1', 'Nombre Video Uploader');
        $sheet->setCellValue('Q1', 'Nmbre message Uploader');
        $sheet->setCellValue('R1', 'Nmbre de connexions');
        $row = 2;
        foreach ($findSejourActiveBetween as $sejour) {
            // var_dump($this->em->getRepository(Attachment::class)->CountsearshSejourVideo($sejour->getId())[0]['COUNT(*)']);
            //var_dump($this->em->getRepository(Attachment::class)->CountsearshSejourAtachment($sejour->getId()));
            $sheet->setCellValue('A' . $row, $sejour->getCodeSejour());
            $sheet->setCellValue('B' . $row, $sejour->getThemSejour());
            $sheet->setCellValue('C' . $row, $sejour->getDateCreationCode());
            $sheet->setCellValue('D' . $row, $sejour->getDateFinCode());
            $sheet->setCellValue('E' . $row, $sejour->getDateDebutSejour());
            $sheet->setCellValue('F' . $row, $sejour->getDateFinSejour());
            $sheet->setCellValue('G' . $row, $sejour->getAdresseSejour() . ' ' . $sejour->getCodePostal() . ' ' . $sejour->getVille() . ' ' . $sejour->getPays());
            $sheet->setCellValue('H' . $row, $sejour->getStatut()->getLibiller());
            $sheet->setCellValue('I' . $row, $sejour->getIdEtablisment()->getNomEtab());
            $sheet->setCellValue('J' . $row, $sejour->getNbenfan());
            $sheet->setCellValue('K' . $row, $sejour->getPrixcnxparent());
            $sheet->setCellValue('L' . $row, $sejour->getPrixcnxpartenaire());
            $sheet->setCellValue('M' . $row, $sejour->getReversecnxpart() . '%');
            $sheet->setCellValue('N' . $row, $sejour->getReverseventepart() . '%');
            $sheet->setCellValue('O' . $row, $this->em->getRepository(Attachment::class)->CountsearshSejourAtachment($sejour->getId())[0]['COUNT(*)']);
            $sheet->setCellValue('P' . $row, $this->em->getRepository(Attachment::class)->CountsearshSejourMessage($sejour->getId())[0]['COUNT(*)']);
            $sheet->setCellValue('Q' . $row, $this->em->getRepository(Attachment::class)->CountsearshSejourVideo($sejour->getId())[0]['COUNT(*)']);
            $sheet->setCellValue('R' . $row, $sejour->getCountParentSejour());
            $row = $row + 1;
        }
        $writer = new Xlsx($spreadsheet);
        // Create a Temporary file in the system
        $fileName = 'Sejours-' . $datedebut->format('d-m-Y') . '-' . $datefin->format('d-m-Y') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @Route("/ExportUsersxlsx", name="ExportUsersxlsx")
     */
    public function ExportUsersxlsx()
    {
        $session = $this->get('session');
        $datedebut =  $session->get('datedebut');
        $datefin = $session->get('datefin');
        $part = $session->get('part');
        $typePart = $session->get('typePart');
        $firstdayprevmonth = $session->get('firstdayprevmonth');
        $lastdayprevmonth = $session->get('lastdayprevmonth');
        $SEjourServiceB = $this->sejourService;
        if ((!(isset($datedebut)) || $datedebut === null || $datedebut === "") && (!(isset($datefin)) || $datefin === null || $datefin === "")) {
            $datedebut = date('01/m/Y');
            $datefin = date('t/m/Y');
            $datedebut = \DateTime::createFromFormat('d/m/Y', $datedebut)->setTime(0, 0);
            $datefin = \DateTime::createFromFormat('d/m/Y', $datefin)->setTime(0, 0);
        }
        if ($typePart != null && $typePart != "") {
            $FindeUSerParentBetween = $SEjourServiceB->FindeUSerParentBetween($datedebut, $datefin);
            $FindeUSerParentActiveBetween = $SEjourServiceB->FindeUSerParentActiveBetween($datedebut, $datefin);
            $findParentDateBetween = $this->em->getRepository(Sejour::class)->findListeSejourEncourPayantParentParTypePart($datefin, $datedebut, $typePart);
        } elseif ($part != null && $part != "") {
            $FindeUSerParentBetween = $SEjourServiceB->FindeUSerParentBetween($datedebut, $datefin);
            $FindeUSerParentActiveBetween = $SEjourServiceB->FindeUSerParentActiveBetween($datedebut, $datefin);
            $findParentDateBetween = $this->em->getRepository(Sejour::class)->findListeSejourEncourPayantParentParPart($datefin, $datedebut, $part);
        } else {
            $FindeUSerParentBetween = $SEjourServiceB->FindeUSerParentBetween($datedebut, $datefin);
            $FindeUSerParentActiveBetween = $SEjourServiceB->FindeUSerParentActiveBetween($datedebut, $datefin);
            $findParentDateBetween = $this->em->getRepository(Sejour::class)->findListeSejourEncourPayantParentBetween($datefin, $datedebut);
        }
        //        var_dump($findSejourActiveBetweenFormat);var_dump($usercreate);var_dump($userActive);
        //var_dump($FindeUSerParentBetween[0]); user
        // var_dump(  $FindeUSerParentActiveBetween[0]); user
        // var_dump($findParentDateBetween[0]);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getActiveSheet()->setTitle("Comptes créés");
        $sheet->getPageMargins()->setTop(1);
        $sheet->getPageMargins()->setRight(0.75);
        $sheet->getPageMargins()->setLeft(0.75);
        $sheet->getPageMargins()->setBottom(1);
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nom');
        $sheet->setCellValue('C1', 'Prenom');
        $sheet->setCellValue('D1', 'email');
        $sheet->setCellValue('E1', 'Téléphone');
        $row = 2;
        foreach ($FindeUSerParentBetween as $user) {
            $sheet->setCellValue('A' . $row, $user->getId());
            $sheet->setCellValue('B' . $row, $user->getNom());
            $sheet->setCellValue('C' . $row, $user->getPrenom());
            $sheet->setCellValue('D' . $row, $user->getEmail());
            $sheet->setCellValue('E' . $row, $user->getNumMobile());
            $row = $row + 1;
        }
        $spreadsheet->createSheet();
        // Add some data to the second sheet, resembling some different data types
        $spreadsheet->setActiveSheetIndex(1);
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getActiveSheet()->setTitle("Comptes Actifs");
        $row = 2;
        $sheet->getPageMargins()->setTop(1);
        $sheet->getPageMargins()->setRight(0.75);
        $sheet->getPageMargins()->setLeft(0.75);
        $sheet->getPageMargins()->setBottom(1);
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nom');
        $sheet->setCellValue('C1', 'Prenom');
        $sheet->setCellValue('D1', 'email');
        $sheet->setCellValue('E1', 'Téléphone');
        foreach ($FindeUSerParentActiveBetween as $user) {
            $sheet->setCellValue('A' . $row, $user->getId());
            $sheet->setCellValue('B' . $row, $user->getNom());
            $sheet->setCellValue('C' . $row, $user->getPrenom());
            $sheet->setCellValue('D' . $row, $user->getEmail());
            $sheet->setCellValue('E' . $row, $user->getNumMobile());
            $row = $row + 1;
        }
        $spreadsheet->createSheet();
        // Add some data to the second sheet, resembling some different data types
        $spreadsheet->setActiveSheetIndex(2);
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getActiveSheet()->setTitle("Parents Connéctés");
        $row = 2;
        $sheet->getPageMargins()->setTop(1);
        $sheet->getPageMargins()->setRight(0.75);
        $sheet->getPageMargins()->setLeft(0.75);
        $sheet->getPageMargins()->setBottom(1);
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nom');
        $sheet->setCellValue('C1', 'Prenom');
        $sheet->setCellValue('D1', 'email');
        $sheet->setCellValue('E1', 'Téléphone');
        $sheet->setCellValue('F1', 'Code Sejour');
        foreach ($findParentDateBetween as $parent) {
            $user = $parent->getIdParent();
            $sheet->setCellValue('A' . $row, $user->getId());
            $sheet->setCellValue('B' . $row, $user->getNom());
            $sheet->setCellValue('C' . $row, $user->getPrenom());
            $sheet->setCellValue('D' . $row, $user->getEmail());
            $sheet->setCellValue('E' . $row, $user->getNumMobile());
            $sheet->setCellValue('F' . $row, $parent->getIdSejour()->getCodeSejour());
            $row = $row + 1;
        }
        $writer = new Xlsx($spreadsheet);
        // Create a Temporary file in the system
        $fileName = 'Comptes-' . $datedebut->format('d-m-Y') . '-' . $datefin->format('d-m-Y') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }
    /**
     * @Route("/ExportCommandexlsx", name="ExportCommandexlsx")
     */
    public function ExportCommandexlsx()
    {
        $session = $this->get('session');
        $datedebut =  $session->get('datedebut');
        $datefin = $session->get('datefin');
        $part = $session->get('part');
        $typePart = $session->get('typePart');
        $firstdayprevmonth = $session->get('firstdayprevmonth');
        $lastdayprevmonth = $session->get('lastdayprevmonth');
        $SEjourServiceB = $this->sejourService;
        if ((!(isset($datedebut)) || $datedebut === null || $datedebut === "") && (!(isset($datefin)) || $datefin === null || $datefin === "")) {
            $datedebut = date('01/m/Y');
            $datefin = date('t/m/Y');
            $datedebut = \DateTime::createFromFormat('d/m/Y', $datedebut)->setTime(0, 0);
            $datefin = \DateTime::createFromFormat('d/m/Y', $datefin)->setTime(0, 0);
        }
        if ($typePart != null && $typePart != "") {
            $ListDesCommande = $this->em->getRepository(Commande::class)->ListDesCommandeParTypePart($datedebut, $datefin, $typePart);
        } elseif ($part != null && $part != "") {
            $ListDesCommande = $this->em->getRepository(Commande::class)->ListDesCommandeParPart($datedebut, $datefin, $part);
        } else {
            $ListDesCommande = $this->em->getRepository(Commande::class)->ListDesCommande($datedebut, $datefin);
        }
        //        var_dump($findSejourActiveBetweenFormat);var_dump($usercreate);var_dump($userActive);
        //var_dump($FindeUSerParentBetween[0]); user
        // var_dump(  $FindeUSerParentActiveBetween[0]); user
        // var_dump($findParentDateBetween[0]);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getActiveSheet()->setTitle("Liste des Commandes Produit");
        $sheet->getPageMargins()->setTop(1);
        $sheet->getPageMargins()->setRight(0.75);
        $sheet->getPageMargins()->setLeft(0.75);
        $sheet->getPageMargins()->setBottom(1);
        $sheet->setCellValue('A1', 'Client ID');
        $sheet->setCellValue('B1', 'Client');
        $sheet->setCellValue('C1', 'Num Facture');
        $sheet->setCellValue('D1', 'Date de facture');
        $sheet->setCellValue('E1', 'Num Commande');
        $sheet->setCellValue('F1', 'Date de commande');
        $sheet->setCellValue('G1', 'Produits');
        $sheet->setCellValue('H1', 'Quantité');
        $sheet->setCellValue('I1', 'Montant TTC');
        $sheet->setCellValue('J1', 'Frais d\'expédition');
        $sheet->setCellValue('K1', 'Montant HT');
        $sheet->setCellValue('L1', 'Sejour');
        $row = 2;
        foreach ($ListDesCommande as $cmd) {
            $sheet->setCellValue('A' . $row, $cmd->getIdUser()->getId());
            $sheet->setCellValue('B' . $row, $cmd->getIdUser()->getNom() . " " . $cmd->getIdUser()->getPrenom());
            $sheet->setCellValue('C' . $row,  $cmd->getNumfacture());
            $sheet->setCellValue('D' . $row,  $cmd->getDateCreateCommande());
            $sheet->setCellValue('E' . $row,  $cmd->getNumComande());
            $sheet->setCellValue('F' . $row,  $cmd->getDateCreateCommande());
            $prdt = "";
            $qte = 0;
            foreach ($cmd->getCommandesProduits() as $produit) {
                if ($produit->getQuantiter() > 0) {
                    $qte = $qte + $produit->getQuantiter();
                    if ($prdt == '') {
                        $prdt =  $produit->getIdProduit()->getType()->getLabeletype();
                    } else {
                        $prdt = $prdt . ', ' . $produit->getIdProduit()->getType()->getLabeletype();
                    }
                }
            }
            $sheet->setCellValue('G' . $row, $prdt);
            $sheet->setCellValue('H' . $row, $qte);
            $sheet->setCellValue('I' . $row, $cmd->getMontantrth());
            $sheet->setCellValue('J' . $row, $cmd->getMontanenv());
            $montantht = $cmd->getMontantrth() * 100 / (120);
            $sheet->setCellValue('K' . $row,  $montantht);
            $sejour = $cmd->getIdSejour();
            $sheet->setCellValue('L' . $row,  $sejour->getCodeSejour());
            $sheet->getCellByColumnAndRow(12, $row)->getHyperlink()->setUrl('https://5sur5sejour.com/Admin/DetailsSejour/' . $sejour->getId());
            $row = $row + 1;
        }
        $writer = new Xlsx($spreadsheet);
        // Create a Temporary file in the system
        $fileName = 'ListeDesCommandes-' . $datedebut->format('d-m-Y') . '-' . $datefin->format('d-m-Y') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }
    /**
     * @Route("/ExportSejourParentxlsx", name="ExportSejourParentxlsx")
     */
    public function ExportSejourParentxlsx(Request $request)
    {
        $datedebut = $_POST['datedebut'];
        $datefin = $_POST['datefin'];
        $part = $_POST['part'];
        if ($datedebut != null && $datedebut != "" && $datefin != null && $datefin != "") {
            $datedebut = \DateTime::createFromFormat('Y-m-d', $datedebut)->setTime(0, 0);
            $datefin = \DateTime::createFromFormat('Y-m-d', $datefin)->setTime(0, 0);
        } else {
            // First day of the month.
            $datedebut = date('01/m/Y');
            // Last day of the month.
            $datefin = date('t/m/Y');
            $datedebut = \DateTime::createFromFormat('d/m/Y', $datedebut)->setTime(0, 0);
            $datefin = \DateTime::createFromFormat('d/m/Y', $datefin)->setTime(0, 0);
        }
        $SEjourServiceB = $this->sejourService;
        if ($part != null && $part != "") {
            $findSejourActiveBetween = $SEjourServiceB->findSejourActiveBetweenParPart($datedebut, $datefin, $part);
            //        Courbe parent
            $EnfantDeclare = $SEjourServiceB->FormaterarraydateBetweennbrEnfant($findSejourActiveBetween, 'getDateDebutSejour', 'getDateFinSejour', $datedebut, $datefin);
        } else {
            $findSejourActiveBetween = $SEjourServiceB->findSejourActiveBetween($datedebut, $datefin);
            //        Courbe parent
            $EnfantDeclare = $SEjourServiceB->FormaterarraydateBetweennbrEnfant($findSejourActiveBetween, 'getDateDebutSejour', 'getDateFinSejour', $datedebut, $datefin);
        }
        //        var_dump($findSejourActiveBetweenFormat);var_dump($usercreate);var_dump($userActive);
        //var_dump($FindeUSerParentBetween[0]); user
        // var_dump(  $FindeUSerParentActiveBetween[0]); user
        // var_dump($findParentDateBetween[0]);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getActiveSheet()->setTitle("Liste des séjours");
        $sheet->getPageMargins()->setTop(1);
        $sheet->getPageMargins()->setRight(0.75);
        $sheet->getPageMargins()->setLeft(0.75);
        $sheet->getPageMargins()->setBottom(1);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageMargins()->setTop(1);
        $sheet->getPageMargins()->setRight(0.75);
        $sheet->getPageMargins()->setLeft(0.75);
        $sheet->getPageMargins()->setBottom(1);
        $sheet->setCellValue('A1', 'Code Sejour');
        $sheet->setCellValue('B1', 'theme du sejour');
        $sheet->setCellValue('C1', 'Date Creation Code');
        $sheet->setCellValue('D1', 'Date debut');
        $sheet->setCellValue('E1', 'Date Fin');
        $sheet->setCellValue('F1', 'Adresse de sejour');
        $sheet->setCellValue('G1', 'Statut');
        $sheet->setCellValue('H1', 'Partenaire');
        $sheet->setCellValue('I1', 'nbr enfants déclarer');
        $sheet->setCellValue('J1', 'Prix Connexion Parent');
        $sheet->setCellValue('K1', 'Prix connexion Partenaire ');
        $sheet->setCellValue('L1', 'Reversement Connexion Part %');
        $sheet->setCellValue('M1', 'Reversement Produit Part % ');
        $sheet->setCellValue('N1', 'Nbr de Connexion');
        $sheet->setCellValue('O1', 'CA');
        $sheet->setCellValue('P1', 'CA hors taxes');
        $sheet->setCellValue('Q1', 'Reversement total');
        $row = 2;
        foreach ($findSejourActiveBetween as $sejour) {
            // var_dump($this->em->getRepository(Attachment::class)->CountsearshSejourVideo($sejour->getId())[0]['COUNT(*)']);
            //var_dump($this->em->getRepository(Attachment::class)->CountsearshSejourAtachment($sejour->getId()));
            $sheet->setCellValue('A' . $row, $sejour->getCodeSejour());
            $sheet->setCellValue('B' . $row, $sejour->getThemSejour());
            $sheet->setCellValue('C' . $row, $sejour->getDateCreationCode());
            $sheet->setCellValue('D' . $row, $sejour->getDateDebutSejour());
            $sheet->setCellValue('E' . $row, $sejour->getDateFinSejour());
            $sheet->setCellValue('F' . $row, $sejour->getAdresseSejour() . ' ' . $sejour->getCodePostal() . ' ' . $sejour->getVille() . ' ' . $sejour->getPays());
            $sheet->setCellValue('G' . $row, $sejour->getStatut()->getLibiller());
            $sheet->setCellValue('H' . $row, $sejour->getIdEtablisment()->getNomEtab());
            $sheet->setCellValue('I' . $row, $sejour->getNbenfan());
            $sheet->setCellValue('J' . $row, $sejour->getPrixcnxparent());
            $sheet->setCellValue('K' . $row, $sejour->getPrixcnxpartenaire());
            $sheet->setCellValue('L' . $row, $sejour->getReversecnxpart() . '%');
            $sheet->setCellValue('M' . $row, $sejour->getReverseventepart() . '%');
            $sheet->setCellValue('N' . $row, $EnfantDeclare[$sejour->getCodeSejour()]['nbrParentCncte']);
            if (substr($sejour->getCodeSejour(), 1, 1) == 'P') {
                $sheet->setCellValue('O' . $row, $sejour->getPrixcnxparent() * $EnfantDeclare[$sejour->getCodeSejour()]['nbrParentCncte']);
                $sheet->setCellValue('P' . $row, round((($sejour->getPrixcnxparent() * $EnfantDeclare[$sejour->getCodeSejour()]['nbrParentCncte']) * 100) / 120), 2);
                $sheet->setCellValue('Q' . $row, round((((($sejour->getPrixcnxparent() * $EnfantDeclare[$sejour->getCodeSejour()]['nbrParentCncte']) * 100) / 120) * $sejour->getReversecnxpart()) / 100), 2);
            } else {
                $sheet->setCellValue('O' . $row, $sejour->getPrixcnxpartenaire() * $EnfantDeclare[$sejour->getCodeSejour()]['nbrParentCncte']);
                $sheet->setCellValue('P' . $row, round((($sejour->getPrixcnxpartenaire() * $EnfantDeclare[$sejour->getCodeSejour()]['nbrParentCncte']) * 100) / 120), 2);
                $sheet->setCellValue('Q' . $row, round((((($sejour->getPrixcnxpartenaire() * $EnfantDeclare[$sejour->getCodeSejour()]['nbrParentCncte']) * 100) / 120) * $sejour->getReversecnxpart()) / 100), 2);
            }
            $row = $row + 1;
        }
        $writer = new Xlsx($spreadsheet);
        // Create a Temporary file in the system
        $fileName = 'Parent-Sejours-' . $datedebut->format('d-m-Y') . '-' . $datefin->format('d-m-Y') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

     /**
     * @Route("/DashFiltresJson", name="DashFiltresJson")
     */
    public function DashFiltresJson(Request $request)
    {
        $datedebut = $request->get("datedebut");
        $datefin = $request->get("datefin");
        $part = $request->get("idpart");
        $typePart = $request->get("typePart");
        $typeduree = $request->get("typeduree");
        $typecompar = $request->get("typecompar");
        $SEjourServiceB = $this->sejourService;
        $commndeService = $this->statistique;
        if ($typecompar === "" || $typecompar === null || $typecompar === "perPreced") {
            if (($datedebut === null || $datedebut === "") && ($datefin === null || $datefin === "")) {
                if ($typeduree === "" || $typeduree === null || $typeduree === "Ce mois") {
                    $datedebut = date('01/m/Y');
                    $datefin = date('t/m/Y');
                    $firstdayprevmonth = date('01/m/Y', strtotime('-1 months'));
                    // Last day of last month.
                    $lastdayprevmonth = date('t/m/Y', strtotime('-1 months'));
                } elseif ($typeduree === "Le mois dernier") {
                    $datedebut = date('01/m/Y', strtotime('-1 months'));
                    $datefin = date('t/m/Y', strtotime('-1 months'));
                    $firstdayprevmonth = date('01/m/Y', strtotime('-2 months'));
                    // Last day of last month.
                    $lastdayprevmonth = date('t/m/Y', strtotime('-2 months'));
                } elseif ($typeduree === "Cette année") {
                    $datedebut = date('01/01/Y');
                    $datefin = date('t/12/Y');
                    $firstdayprevmonth = date('01/01/Y', strtotime('-1 year'));
                    // Last day of last month.
                    $lastdayprevmonth = date('t/12/Y', strtotime('-1 year'));
                } elseif ($typeduree === "L'année dernière") {
                    $datedebut = date('01/01/Y', strtotime('-1 year'));
                    $datefin = date('t/12/Y', strtotime('-1 year'));
                    $firstdayprevmonth = date('01/01/Y', strtotime('-2 years'));
                    // Last day of last month.
                    $lastdayprevmonth = date('t/12/Y', strtotime('-2 years'));
                }
            } else {
                $datedebut = date('Y-m-d', strtotime($datedebut));
                $datefin = date('Y-m-d', strtotime($datefin));
                $nbrJr = strtotime($datefin) - strtotime($datedebut);
                $nbrJr = round($nbrJr / (60 * 60 * 24));
                //var_dump($nbrJr);
                $firstdayprevmonth = date("d-m-Y", strtotime("- " . $nbrJr . " days", strtotime($datedebut)));
                $lastdayprevmonth = date("d-m-Y", strtotime("- " . $nbrJr . " days", strtotime($datefin)));
                $datedebut = \DateTime::createFromFormat('Y-m-d', $datedebut);
                $datefin = \DateTime::createFromFormat('Y-m-d', $datefin);
                $datedebut =   $datedebut->format('d/m/Y');
                $datefin =   $datefin->format('d/m/Y');
                $firstdayprevmonth = \DateTime::createFromFormat('d-m-Y', $firstdayprevmonth);
                $lastdayprevmonth = \DateTime::createFromFormat('d-m-Y', $lastdayprevmonth);
                $firstdayprevmonth =   $firstdayprevmonth->format('d/m/Y');
                $lastdayprevmonth =   $lastdayprevmonth->format('d/m/Y');
                //dd($nbrJr);
            }
        } elseif ($typecompar === "perAnnePre") {
            if (($datedebut === null || $datedebut === "") && ($datefin === null || $datefin === "")) {
                if ($typeduree === "" || $typeduree === null || $typeduree === "Ce mois") {
                    $datedebut = date('01/m/Y');
                    $datefin = date('t/m/Y');
                    $firstdayprevmonth = date('01/m/Y', strtotime(' -1 year'));
                    // Last day of last month.
                    $lastdayprevmonth = date('t/m/Y', strtotime(' -1 year'));
                } elseif ($typeduree === "Le mois dernier") {
                    $datedebut = date('01/m/Y', strtotime('-1 months'));
                    $datefin = date('t/m/Y', strtotime('-1 months'));
                    $firstdayprevmonth = date('01/m/Y', strtotime('-1 months -1 year'));
                    // Last day of last month.
                    $lastdayprevmonth = date('t/m/Y', strtotime('-1 months -1 year'));
                } elseif ($typeduree === "Cette année") {
                    $datedebut = date('01/01/Y');
                    $datefin = date('t/12/Y');
                    $firstdayprevmonth = date('01/01/Y', strtotime('-1 year'));
                    // Last day of last month.
                    $lastdayprevmonth = date('31/12/Y', strtotime('-1 year'));
                } elseif ($typeduree === "L'année dernière") {
                    $datedebut = date('01/01/Y', strtotime('-1 year'));
                    $datefin = date('t/12/Y', strtotime('-1 year'));
                    $firstdayprevmonth = date('01/01/Y', strtotime('-2 years'));
                    // Last day of last month.
                    $lastdayprevmonth = date('t/12/Y', strtotime('-2 years'));
                }
            } else {
                $nbrJr = strtotime($datefin) - strtotime($datedebut);
                $nbrJr = date('d', $nbrJr);
                $nbrJr = round($nbrJr / (60 * 60 * 24));
                //var_dump($nbrJr);
                $firstdayprevmonth = date("d-m-Y", strtotime("- " . $nbrJr . " days", strtotime($datedebut)));
                $lastdayprevmonth = date("d-m-Y", strtotime("- " . $nbrJr . " days", strtotime($datefin)));
                $datedebut = \DateTime::createFromFormat('Y-m-d', $datedebut);
                $datefin = \DateTime::createFromFormat('Y-m-d', $datefin);
                $datedebut =   $datedebut->format('d/m/Y');
                $datefin =   $datefin->format('d/m/Y');
                $firstdayprevmonth = \DateTime::createFromFormat('d-m-Y', $firstdayprevmonth);
                $lastdayprevmonth = \DateTime::createFromFormat('d-m-Y', $lastdayprevmonth);
                $firstdayprevmonth =   $firstdayprevmonth->format('d/m/Y');
                $lastdayprevmonth =   $lastdayprevmonth->format('d/m/Y');
                //dd($nbrJr);
            }
        }
        /**  Appliquer filtre par partenaire et durée  */
        if ($part !== "" && $part !== null) {
            $datedebut = \DateTime::createFromFormat('d/m/Y', $datedebut)->setTime(0, 0);
            $datefin = \DateTime::createFromFormat('d/m/Y', $datefin)->setTime(0, 0);
            $firstdayprevmonth = \DateTime::createFromFormat('d/m/Y', $firstdayprevmonth)->setTime(0, 0);
            $lastdayprevmonth = \DateTime::createFromFormat('d/m/Y', $lastdayprevmonth)->setTime(0, 0);
            $sejourEncour = $SEjourServiceB->findSejourEncourBetweenParPart($datedebut, $datefin, $part);
            $getNbrSejourCree = $SEjourServiceB->getNbrSejourCreeParPart($datedebut, $datefin, $part);
            $sejourEncourLast = $SEjourServiceB->findSejourEncourBetweenParPart($datedebut, $datefin, $part);
            $getNbrSejourCreeLast = $SEjourServiceB->getNbrSejourCreeParPart($firstdayprevmonth, $lastdayprevmonth, $part);
            $sejourEncourFree = $this->em->getRepository(Sejour::class)->findSejourEncourFreeParPart($datefin, $datedebut, $part);
            //EF & PF
            $sejourEncourFree_EF = $this->em->getRepository(Sejour::class)->findSejourEncourFreeParPart_EF($datefin, $datedebut, $part);
            $sejourEncourFree_PF = $this->em->getRepository(Sejour::class)->findSejourEncourFreeParPart_PF($datefin, $datedebut, $part);
            $sejourEncourPayent = $this->em->getRepository(Sejour::class)->findSejourEncourPayantParPart($datefin, $datedebut, $part);
            $sejourEncourFreeLast = $this->em->getRepository(Sejour::class)->findSejourEncourFreeParPart($lastdayprevmonth, $firstdayprevmonth, $part);
            $sejourEncourPayentLast = $this->em->getRepository(Sejour::class)->findSejourEncourPayantParPart($lastdayprevmonth, $firstdayprevmonth, $part);
            if (sizeOf($getNbrSejourCreeLast) != 0) {
                $pourcentCreer = (((sizeOf($getNbrSejourCree) - sizeOf($getNbrSejourCreeLast)) / sizeOf($getNbrSejourCreeLast)) * 100);
            } else {
                $pourcentCreer = 0;
            }
            //        SEJOURS EN COURS ACTIFS MOIS
            $sejourEncourHaveAttach = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachParPart($datefin, $datedebut, $part);
            $sejourEncourHaveAttachLast = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachParPart($lastdayprevmonth, $firstdayprevmonth, $part);
            $sejourEncourHaveAttachFree = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachFreeParPart($datefin, $datedebut, $part);
            //EF&PF
            $sejourEncourHaveAttachFree_EF = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachFreeParPartEF($datefin, $datedebut, $part);
            $sejourEncourHaveAttachFree_PF = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachFreeParPartPF($datefin, $datedebut, $part);
            $sejourEncourHaveAttachFreeLast = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachFreeParPart($lastdayprevmonth, $firstdayprevmonth, $part);
            $sejourEncourHaveAttachPay = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachPayParPart($datefin, $datedebut, $part);
            $sejourEncourHaveAttachPayLast = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachPayParPart($lastdayprevmonth, $firstdayprevmonth, $part);
            if (intval($sejourEncourPayentLast[0][1]) != 0) {
                $pourcentEncourPay = (((intval($sejourEncourPayent[0][1]) - intval($sejourEncourPayentLast[0][1])) / intval($sejourEncourPayentLast[0][1])) * 100);
            } else {
                $pourcentEncourPay = 0;
            }
            if (intval($sejourEncourFreeLast[0][1]) != 0) {
                $pourcentEncourFree = (((intval($sejourEncourFree[0][1]) - intval($sejourEncourFreeLast[0][1])) / intval($sejourEncourFreeLast[0][1])) * 100);
            } else {
                $pourcentEncourFree = 0;
            }
            if (sizeOf($sejourEncourHaveAttachLast) != 0) {
                $pourcentActif = (((sizeOf($sejourEncourHaveAttach) - sizeOf($sejourEncourHaveAttachLast)) / sizeOf($sejourEncourHaveAttachLast)) * 100);
            } else {
                $pourcentActif = 0;
            }
            if (sizeOf($sejourEncourHaveAttachFreeLast) != 0) {
                $pourcentActifFree = (((sizeOf($sejourEncourHaveAttachFree) - sizeOf($sejourEncourHaveAttachFreeLast)) / sizeOf($sejourEncourHaveAttachFreeLast)) * 100);
            } else {
                $pourcentActifFree = 0;
            }
            if (sizeOf($sejourEncourHaveAttachPayLast) != 0) {
                $pourcentActifPay = (((sizeOf($sejourEncourHaveAttachPay) - sizeOf($sejourEncourHaveAttachPayLast)) / sizeOf($sejourEncourHaveAttachPayLast)) * 100);
            } else {
                $pourcentActifPay = 0;
            }
            $EnfantDeclare = $SEjourServiceB->FormaterarraydateBetweennbrEnfant($sejourEncourHaveAttach, 'getDateDebutSejour', 'getDateFinSejour', $datedebut, $datefin);
            $EnfantDeclareLast = $SEjourServiceB->FormaterarraydateBetweennbrEnfant($sejourEncourHaveAttachLast, 'getDateDebutSejour', 'getDateFinSejour', $firstdayprevmonth, $lastdayprevmonth);
            if (sizeOf($sejourEncourHaveAttach) != 0) {
                $EnfantDeclare = intval($EnfantDeclare['totenf']) / sizeOf($sejourEncourHaveAttach);
            } else {
                $EnfantDeclare = 0;
            }
            if (sizeOf($sejourEncourHaveAttachLast) != 0) {
                $EnfantDeclareLast = intval($EnfantDeclareLast['totenf']) / sizeOf($sejourEncourHaveAttachLast);
            } else {
                $EnfantDeclareLast = 0;
            }
            if ($EnfantDeclareLast != 0) {
                $pourcentEnfant = ((($EnfantDeclare - $EnfantDeclareLast) / $EnfantDeclareLast) * 100);
            } else {
                $pourcentEnfant = 0;
            }
            $ListDesCommande = $this->em->getRepository(Commande::class)->ListDesCommandeParPart($datedebut, $datefin, $part);
            $ListDesCommandeLast = $this->em->getRepository(Commande::class)->ListDesCommandeParPart($firstdayprevmonth, $lastdayprevmonth, $part);
            $nbrCmdParSejour = $commndeService->CommandePArSejourParPart($datedebut, $datefin, $part);
            $nbrCmdParSejourLast = $commndeService->CommandePArSejourParPart($firstdayprevmonth, $lastdayprevmonth, $part);
            $nbrProduit = $this->em->getRepository(Commande::class)->nbrDesProduitParPart($datedebut, $datefin, $part);
            $nbrProduitLast = $this->em->getRepository(Commande::class)->nbrDesProduitParPart($firstdayprevmonth, $lastdayprevmonth, $part);
            if (sizeof($ListDesCommande) == 0) {
                $nbrProduitPartCommande = 0;
            } else {
                $nbrProduitPartCommande = $nbrProduit[0][1] / sizeof($ListDesCommande);
            }
            if (sizeof($ListDesCommandeLast) == 0) {
                $nbrProduitPartCommandeLast = 0;
            } else {
                $nbrProduitPartCommandeLast = $nbrProduitLast[0][1] / sizeof($ListDesCommandeLast);
            }
            if (sizeof($ListDesCommandeLast) != 0) {
                $pourcentNbrCmd = (((sizeOf($ListDesCommande) - sizeOf($ListDesCommandeLast)) / sizeOf($ListDesCommandeLast)) * 100);
            } else {
                $pourcentNbrCmd = 0;
            }
            if ($nbrCmdParSejourLast != 0) {
                $pourcentNbrCmdSej = ((($nbrCmdParSejour - $nbrCmdParSejourLast) / $nbrCmdParSejourLast) * 100);
            } else {
                $pourcentNbrCmdSej = 0;
            }
            if ($nbrProduitPartCommandeLast != 0) {
                $pourcentProdCmd = ((($nbrProduitPartCommande - $nbrProduitPartCommandeLast) / $nbrProduitPartCommandeLast) * 100);
            } else {
                $pourcentProdCmd = 0;
            }
            if ($nbrProduitLast[0][1] != 0) {
                $pourcentNbrProd = ((($nbrProduit[0][1] - $nbrProduitLast[0][1]) / $nbrProduitLast[0][1]) * 100);
            } else {
                $pourcentNbrProd = 0;
            }
            $ConnexionsCa = $this->em->getRepository(ParentSejour::class)->caConnexionTotalParPart($datedebut, $datefin, $part);
            $ConnexionsCaLast = $this->em->getRepository(ParentSejour::class)->caConnexionTotalParPart($firstdayprevmonth, $lastdayprevmonth, $part);
            $caConnexion = $commndeService->CalculeCaConnexionParPart($ConnexionsCa, $part, 'getDateCreation', $datedebut, $datefin);
            $caConnexionLast = $commndeService->CalculeCaConnexionParPart($ConnexionsCaLast, $part, 'getDateCreation', $firstdayprevmonth, $lastdayprevmonth);
            $caConnexion['reverCnx'] = $caConnexion['reverCnx'];
            $caConnexionLast['reverCnx'] = $caConnexionLast['reverCnx'];
            $reversementProduit = $commndeService->getReversementProduit($ListDesCommande);
            $reversementProduitLast = $commndeService->getReversementProduit($ListDesCommandeLast);
            $reversementTotal = $reversementProduit + $caConnexion["reverCnx"];
            $reversementTotalLast = $reversementProduitLast + $caConnexionLast["reverCnx"];
            $caProduits = $commndeService->getSommeProduitParPart($ListDesCommande, $part)['totalCaProduit'];
            $nbrCmds = $commndeService->getSommeProduitParPart($ListDesCommande, $part)['nbrCmd'];
            $nbrCmdsLast = $commndeService->getSommeProduitParPart($ListDesCommandeLast, $part)['nbrCmd'];
            $caProduitsLast = $commndeService->getSommeProduitParPart($ListDesCommandeLast, $part)['totalCaProduit'];
            $totalEnv = $commndeService->getSommeProduit($ListDesCommande)['totalEnv'];
            //dd($caProduit);
            if ($nbrCmds != 0) {
                $PanierMoyen = $caProduits / $nbrCmds;
            } else {
                $PanierMoyen = 0;
            }
            if ($nbrCmdsLast != 0) {
                $PanierMoyenLast = $caProduitsLast / $nbrCmdsLast;
            } else {
                $PanierMoyenLast = 0;
            }
            if ($caConnexionLast['cacnxtotal'] != 0) {
                $pourcentCaCnx = ((($caConnexion['cacnxtotal'] - $caConnexionLast['cacnxtotal']) / $caConnexionLast['cacnxtotal']) * 100);
            } else {
                $pourcentCaCnx = 0;
            }
            if ($caConnexionLast['cacnxPay'] != 0) {
                $pourcentCaCnxPay = ((($caConnexion['cacnxPay'] - $caConnexionLast['cacnxPay']) / $caConnexionLast['cacnxPay']) * 100);
            } else {
                $pourcentCaCnxPay = 0;
            }
            if ($caConnexionLast['cacnxfree'] != 0) {
                $pourcentCaCnxFree = ((($caConnexion['cacnxfree'] - $caConnexionLast['cacnxfree']) / $caConnexionLast['cacnxfree']) * 100);
            } else {
                $pourcentCaCnxFree = 0;
            }
            if ($caConnexionLast['reverCnx'] != 0) {
                $pourcentCaRevCnx = ((($caConnexion['reverCnx'] - $caConnexionLast['reverCnx']) / $caConnexionLast['reverCnx']) * 100);
            } else {
                $pourcentCaRevCnx = 0;
            }
            if ($caProduitsLast != 0) {
                $pourcentCaProd = ((($caProduits - $caProduitsLast) / $caProduitsLast) * 100);
            } else {
                $pourcentCaProd = 0;
            }
            if ($PanierMoyenLast != 0) {
                $pourcentCaPan = ((($PanierMoyen - $PanierMoyenLast) / $PanierMoyenLast) * 100);
            } else {
                $pourcentCaPan = 0;
            }
            if ($reversementTotalLast != 0) {
                $pourcentCaRev = ((($reversementTotal - $reversementTotalLast) / $reversementTotalLast) * 100);
            } else {
                $pourcentCaRev = 0;
            }
            if ($reversementProduitLast != 0) {
                $pourcentCaRevProd = ((($reversementProduit - $reversementProduitLast) / $reversementProduitLast) * 100);
            } else {
                $pourcentCaRevProd = 0;
            }
            if ($reversementProduitLast != 0) {
                $pourcentCaRevProd = ((($reversementProduit - $reversementProduitLast) / $reversementProduitLast) * 100);
            } else {
                $pourcentCaRevProd = 0;
            }
            $findSejourEncourFreeParent = $this->em->getRepository(Sejour::class)->findSejourEncourFreeParentParPart($datefin, $datedebut, $part);
            //EF&PF
            $findSejourEncourFreeParent_EF = $this->em->getRepository(Sejour::class)->findSejourEncourFreeParentParPart_EF($datefin, $datedebut, $part);
            $findSejourEncourFreeParent_PF = $this->em->getRepository(Sejour::class)->findSejourEncourFreeParentParPart_PF($datefin, $datedebut, $part);
            $findSejourEncourPayantParentPaye = $this->em->getRepository(Sejour::class)->findSejourEncourPayantParentPayeParPart($datefin, $datedebut, $part);
            $findSejourEncourFreeParentLast = $this->em->getRepository(Sejour::class)->findSejourEncourFreeParentParPart($lastdayprevmonth, $firstdayprevmonth, $part);
            $findSejourEncourPayantParentPayeLast = $this->em->getRepository(Sejour::class)->findSejourEncourPayantParentPayeParPart($lastdayprevmonth, $firstdayprevmonth, $part);
            //var_dump($findSejourEncourPayantParentPaye);dd($findSejourEncourPayantParentPayeLast);
            if ($findSejourEncourPayantParentPayeLast[0][1] != 0) {
                $pourcentuserCnxPay = ((($findSejourEncourPayantParentPaye[0][1] - $findSejourEncourPayantParentPayeLast[0][1]) / $findSejourEncourPayantParentPayeLast[0][1]) * 100);
            } else {
                $pourcentuserCnxPay = 0;
            }
            if ($findSejourEncourFreeParentLast[0][1] != 0) {
                $pourcentuserCnxFree = ((($findSejourEncourFreeParent[0][1] - $findSejourEncourFreeParentLast[0][1]) / $findSejourEncourFreeParentLast[0][1]) * 100);
            } else {
                $pourcentuserCnxFree = 0;
            }
            $totalCnxParent = $findSejourEncourFreeParent[0][1] + $findSejourEncourPayantParentPaye[0][1];
            $totalCnxParentLast = $findSejourEncourFreeParentLast[0][1] + $findSejourEncourPayantParentPayeLast[0][1];
            if ($totalCnxParentLast != 0) {
                $pourcentuserCnx = ((($totalCnxParent - $totalCnxParentLast) / $totalCnxParentLast) * 100);
            } else {
                $pourcentuserCnx = 0;
            }
            $FindeUSerParentBetween = $SEjourServiceB->FindeUSerParentBetween($datedebut, $datefin);
            $FindeUSerParentActiveBetween = $SEjourServiceB->FindeUSerParentActiveBetween($datedebut, $datefin);
            $FindeUSerParentBetweenLast = $SEjourServiceB->FindeUSerParentBetween($firstdayprevmonth, $lastdayprevmonth);
            $FindeUSerParentActiveBetweenLast = $SEjourServiceB->FindeUSerParentActiveBetween($firstdayprevmonth, $lastdayprevmonth);
            if (sizeOf($FindeUSerParentBetweenLast) != 0) {
                $pourcentuserCreate = (((sizeOf($FindeUSerParentBetween) - sizeOf($FindeUSerParentBetweenLast)) / sizeOf($FindeUSerParentBetweenLast)) * 100);
            } else {
                $pourcentuserCreate = 0;
            }
            if (sizeOf($FindeUSerParentActiveBetweenLast) != 0) {
                $pourcentuserActive = (((sizeOf($FindeUSerParentActiveBetween) - sizeOf($FindeUSerParentActiveBetweenLast)) / sizeOf($FindeUSerParentActiveBetweenLast)) * 100);
            } else {
                $pourcentuserActive = 0;
            }
            if (sizeof($getNbrSejourCree) != 0) {
                $tauxSej = (sizeOf($sejourEncourHaveAttach) / sizeOf($getNbrSejourCree)) * 100;
            } else {
                $tauxSej = 0;
            }
            if (sizeOf($FindeUSerParentBetween) != 0) {
                //  $TauxCmpt = ($totalCnxParent/sizeOf($FindeUSerParentBetween))*100;
                //04-09-2021 => Touhemi cette tâche demandée par Ramzi 
                if (sizeOf($sejourEncourHaveAttach)) {
                    $TauxCmpt = (($totalCnxParent) / (sizeOf($sejourEncourHaveAttach) * $EnfantDeclare)) * 100;
                } else {
                    $TauxCmpt = 0;
                }
            } else {
                $TauxCmpt = 0;
            }
            $tauxCa = (floatval($caConnexion['cacnxtotal']) + floatval($caProduits)) - (floatval($reversementTotal));
            if (($totalCnxParent) != 0) {
                $tauCmd = (sizeof($ListDesCommande) / ($totalCnxParent)) * 100;
            } else {
                $tauCmd = 0;
            }
        }
        /**  Appliquer filtre par type etablissement et durée */
        elseif ($typePart != null && $typePart != null) {
            $datedebut = \DateTime::createFromFormat('d/m/Y', $datedebut)->setTime(0, 0);
            $datefin = \DateTime::createFromFormat('d/m/Y', $datefin)->setTime(0, 0);
            $firstdayprevmonth = \DateTime::createFromFormat('d/m/Y', $firstdayprevmonth)->setTime(0, 0);
            $lastdayprevmonth = \DateTime::createFromFormat('d/m/Y', $lastdayprevmonth)->setTime(0, 0);
            $sejourEncour = $SEjourServiceB->findSejourEncourBetweenParTypePart($datedebut, $datefin, $typePart);
            $getNbrSejourCree = $SEjourServiceB->getNbrSejourCreeParTypePart($datedebut, $datefin, $typePart);
            $sejourEncourLast = $SEjourServiceB->findSejourEncourBetweenParTypePart($datedebut, $datefin, $typePart);
            $getNbrSejourCreeLast = $SEjourServiceB->getNbrSejourCreeParTypePart($firstdayprevmonth, $lastdayprevmonth, $typePart);
            $sejourEncourFree = $this->em->getRepository(Sejour::class)->findSejourEncourFreeParTypePart($datefin, $datedebut, $typePart);
            //EF&PF
            $sejourEncourFree_EF = $this->em->getRepository(Sejour::class)->findSejourEncourFreeParTypePartEF($datefin, $datedebut, $typePart);
            $sejourEncourFree_PF = $this->em->getRepository(Sejour::class)->findSejourEncourFreeParTypePartPF($datefin, $datedebut, $typePart);
            $sejourEncourPayent = $this->em->getRepository(Sejour::class)->findSejourEncourPayantParTypePart($datefin, $datedebut, $typePart);
            $sejourEncourFreeLast = $this->em->getRepository(Sejour::class)->findSejourEncourFreeParTypePart($lastdayprevmonth, $firstdayprevmonth, $typePart);
            $sejourEncourPayentLast = $this->em->getRepository(Sejour::class)->findSejourEncourPayantParTypePart($lastdayprevmonth, $firstdayprevmonth, $typePart);
            if (sizeOf($getNbrSejourCreeLast) != 0) {
                $pourcentCreer = (((sizeOf($getNbrSejourCree) - sizeOf($getNbrSejourCreeLast)) / sizeOf($getNbrSejourCreeLast)) * 100);
            } else {
                $pourcentCreer = 0;
            }
            //        SEJOURS EN COURS ACTIFS MOIS
            $sejourEncourHaveAttach = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachTypePart($datefin, $datedebut, $typePart);
            $sejourEncourHaveAttachLast = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachTypePart($lastdayprevmonth, $firstdayprevmonth, $typePart);
            $sejourEncourHaveAttachFree = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachFreeTypePart($datefin, $datedebut, $typePart);
            //EF&PF
            $sejourEncourHaveAttachFree_EF = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachFreeTypePartEF($datefin, $datedebut, $typePart);
            $sejourEncourHaveAttachFree_PF = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachFreeTypePartPF($datefin, $datedebut, $typePart);
            $sejourEncourHaveAttachFreeLast = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachFreeTypePart($lastdayprevmonth, $firstdayprevmonth, $typePart);
            $sejourEncourHaveAttachPay = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachPayTypePart($datefin, $datedebut, $typePart);
            $sejourEncourHaveAttachPayLast = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachPayTypePart($lastdayprevmonth, $firstdayprevmonth, $typePart);
            if (intval($sejourEncourPayentLast[0][1]) != 0) {
                $pourcentEncourPay = (((intval($sejourEncourPayent[0][1]) - intval($sejourEncourPayentLast[0][1])) / intval($sejourEncourPayentLast[0][1])) * 100);
            } else {
                $pourcentEncourPay = 0;
            }
            if (intval($sejourEncourFreeLast[0][1]) != 0) {
                $pourcentEncourFree = (((intval($sejourEncourFree[0][1]) - intval($sejourEncourFreeLast[0][1])) / intval($sejourEncourFreeLast[0][1])) * 100);
            } else {
                $pourcentEncourFree = 0;
            }
            if (sizeOf($sejourEncourHaveAttachLast) != 0) {
                $pourcentActif = (((sizeOf($sejourEncourHaveAttach) - sizeOf($sejourEncourHaveAttachLast)) / sizeOf($sejourEncourHaveAttachLast)) * 100);
            } else {
                $pourcentActif = 0;
            }
            if (sizeOf($sejourEncourHaveAttachFreeLast) != 0) {
                $pourcentActifFree = (((sizeOf($sejourEncourHaveAttachFree) - sizeOf($sejourEncourHaveAttachFreeLast)) / sizeOf($sejourEncourHaveAttachFreeLast)) * 100);
            } else {
                $pourcentActifFree = 0;
            }
            if (sizeOf($sejourEncourHaveAttachPayLast) != 0) {
                $pourcentActifPay = (((sizeOf($sejourEncourHaveAttachPay) - sizeOf($sejourEncourHaveAttachPayLast)) / sizeOf($sejourEncourHaveAttachPayLast)) * 100);
            } else {
                $pourcentActifPay = 0;
            }
            $EnfantDeclare = $SEjourServiceB->FormaterarraydateBetweennbrEnfant($sejourEncourHaveAttach, 'getDateDebutSejour', 'getDateFinSejour', $datedebut, $datefin);
            $EnfantDeclareLast = $SEjourServiceB->FormaterarraydateBetweennbrEnfant($sejourEncourHaveAttachLast, 'getDateDebutSejour', 'getDateFinSejour', $firstdayprevmonth, $lastdayprevmonth);
            if (sizeOf($sejourEncourHaveAttach) != 0) {
                $EnfantDeclare = intval($EnfantDeclare['totenf']) / sizeOf($sejourEncourHaveAttach);
            } else {
                $EnfantDeclare = 0;
            }
            if (sizeOf($sejourEncourHaveAttachLast) != 0) {
                $EnfantDeclareLast = intval($EnfantDeclareLast['totenf']) / sizeOf($sejourEncourHaveAttachLast);
            } else {
                $EnfantDeclareLast = 0;
            }
            if ($EnfantDeclareLast != 0) {
                $pourcentEnfant = ((($EnfantDeclare - $EnfantDeclareLast) / $EnfantDeclareLast) * 100);
            } else {
                $pourcentEnfant = 0;
            }
            $ListDesCommande = $this->em->getRepository(Commande::class)->ListDesCommandeParTypePart($datedebut, $datefin, $typePart);
            $ListDesCommandeLast = $this->em->getRepository(Commande::class)->ListDesCommandeParTypePart($firstdayprevmonth, $lastdayprevmonth, $typePart);
            $nbrCmdParSejour = $commndeService->CommandePArSejourParType($datedebut, $datefin, $typePart);
            $nbrCmdParSejourLast = $commndeService->CommandePArSejourParType($firstdayprevmonth, $lastdayprevmonth, $typePart);
            $nbrProduit = $this->em->getRepository(Commande::class)->nbrDesProduitParTypePart($datedebut, $datefin, $typePart);
            $nbrProduitLast = $this->em->getRepository(Commande::class)->nbrDesProduitParTypePart($firstdayprevmonth, $lastdayprevmonth, $typePart);
            if (sizeof($ListDesCommande) == 0) {
                $nbrProduitPartCommande = 0;
            } else {
                $nbrProduitPartCommande = $nbrProduit[0][1] / sizeof($ListDesCommande);
            }
            if (sizeof($ListDesCommandeLast) == 0) {
                $nbrProduitPartCommandeLast = 0;
            } else {
                $nbrProduitPartCommandeLast = $nbrProduitLast[0][1] / sizeof($ListDesCommandeLast);
            }
            if (sizeof($ListDesCommandeLast) != 0) {
                $pourcentNbrCmd = (((sizeOf($ListDesCommande) - sizeOf($ListDesCommandeLast)) / sizeOf($ListDesCommandeLast)) * 100);
            } else {
                $pourcentNbrCmd = 0;
            }
            if ($nbrCmdParSejourLast != 0) {
                $pourcentNbrCmdSej = ((($nbrCmdParSejour - $nbrCmdParSejourLast) / $nbrCmdParSejourLast) * 100);
            } else {
                $pourcentNbrCmdSej = 0;
            }
            if ($nbrProduitPartCommandeLast != 0) {
                $pourcentProdCmd = ((($nbrProduitPartCommande - $nbrProduitPartCommandeLast) / $nbrProduitPartCommandeLast) * 100);
            } else {
                $pourcentProdCmd = 0;
            }
            if ($nbrProduitLast[0][1] != 0) {
                $pourcentNbrProd = ((($nbrProduit[0][1] - $nbrProduitLast[0][1]) / $nbrProduitLast[0][1]) * 100);
            } else {
                $pourcentNbrProd = 0;
            }
            $ConnexionsCa = $this->em->getRepository(ParentSejour::class)->caConnexionTotalParTypePart($datedebut, $datefin, $typePart);
            $ConnexionsCaLast = $this->em->getRepository(ParentSejour::class)->caConnexionTotalParTypePart($firstdayprevmonth, $lastdayprevmonth, $typePart);
            $caConnexion = $commndeService->CalculeCaConnexionParType($ConnexionsCa, $typePart, 'getDateCreation', $datedebut, $datefin);
            $caConnexionLast = $commndeService->CalculeCaConnexionParType($ConnexionsCaLast, $typePart, 'getDateCreation', $firstdayprevmonth, $lastdayprevmonth);
            $reversementProduit = $commndeService->getReversementProduit($ListDesCommande);
            $caConnexion['reverCnx'] = $caConnexion['reverCnx'];
            $caConnexionLast['reverCnx'] = $caConnexionLast['reverCnx'];
            $reversementProduitLast = $commndeService->getReversementProduit($ListDesCommandeLast);
            $reversementTotal = $reversementProduit + $caConnexion["reverCnx"];
            $reversementTotalLast = $reversementProduitLast + $caConnexionLast["reverCnx"];
            $caProduits = $commndeService->getSommeProduitParType($ListDesCommande, $typePart)['totalCaProduit'];
            $caProduitsLast = $commndeService->getSommeProduitParType($ListDesCommandeLast, $typePart)['totalCaProduit'];
            $nbrCmds = $commndeService->getSommeProduitParType($ListDesCommande, $typePart)['nbrCmd'];
            $nbrCmdsLast = $commndeService->getSommeProduitParType($ListDesCommandeLast, $typePart)['nbrCmd'];
            $totalEnv = $commndeService->getSommeProduit($ListDesCommande)['totalEnv'];
            //dd($caProduit);
            if ($nbrCmds != 0) {
                $PanierMoyen = $caProduits / $nbrCmds;
            } else {
                $PanierMoyen = 0;
            }
            if ($nbrCmdsLast != 0) {
                $PanierMoyenLast = $caProduitsLast / $nbrCmdsLast;
            } else {
                $PanierMoyenLast = 0;
            }
            if ($caConnexionLast['cacnxtotal'] != 0) {
                $pourcentCaCnx = ((($caConnexion['cacnxtotal'] - $caConnexionLast['cacnxtotal']) / $caConnexionLast['cacnxtotal']) * 100);
            } else {
                $pourcentCaCnx = 0;
            }
            if ($caConnexionLast['cacnxPay'] != 0) {
                $pourcentCaCnxPay = ((($caConnexion['cacnxPay'] - $caConnexionLast['cacnxPay']) / $caConnexionLast['cacnxPay']) * 100);
            } else {
                $pourcentCaCnxPay = 0;
            }
            if ($caConnexionLast['cacnxfree'] != 0) {
                $pourcentCaCnxFree = ((($caConnexion['cacnxfree'] - $caConnexionLast['cacnxfree']) / $caConnexionLast['cacnxfree']) * 100);
            } else {
                $pourcentCaCnxFree = 0;
            }
            if ($caConnexionLast['reverCnx'] != 0) {
                $pourcentCaRevCnx = ((($caConnexion['reverCnx'] - $caConnexionLast['reverCnx']) / $caConnexionLast['reverCnx']) * 100);
            } else {
                $pourcentCaRevCnx = 0;
            }
            if ($caProduitsLast != 0) {
                $pourcentCaProd = ((($caProduits - $caProduitsLast) / $caProduitsLast) * 100);
            } else {
                $pourcentCaProd = 0;
            }
            if ($PanierMoyenLast != 0) {
                $pourcentCaPan = ((($PanierMoyen - $PanierMoyenLast) / $PanierMoyenLast) * 100);
            } else {
                $pourcentCaPan = 0;
            }
            if ($reversementTotalLast != 0) {
                $pourcentCaRev = ((($reversementTotal - $reversementTotalLast) / $reversementTotalLast) * 100);
            } else {
                $pourcentCaRev = 0;
            }
            if ($reversementProduitLast != 0) {
                $pourcentCaRevProd = ((($reversementProduit - $reversementProduitLast) / $reversementProduitLast) * 100);
            } else {
                $pourcentCaRevProd = 0;
            }
            if ($reversementProduitLast != 0) {
                $pourcentCaRevProd = ((($reversementProduit - $reversementProduitLast) / $reversementProduitLast) * 100);
            } else {
                $pourcentCaRevProd = 0;
            }
            $findSejourEncourFreeParent = $this->em->getRepository(Sejour::class)->findSejourEncourFreeParentParTypePart($datefin, $datedebut, $typePart);
            //EF&PF
            $findSejourEncourFreeParent_EF = $this->em->getRepository(Sejour::class)->findSejourEncourFreeParentParTypePartEF($datefin, $datedebut, $typePart);
            $findSejourEncourFreeParent_PF = $this->em->getRepository(Sejour::class)->findSejourEncourFreeParentParTypePartPF($datefin, $datedebut, $typePart);
            $findSejourEncourPayantParentPaye = $this->em->getRepository(Sejour::class)->findSejourEncourPayantParentPayeParTypePart($datefin, $datedebut, $typePart);
            $findSejourEncourFreeParentLast = $this->em->getRepository(Sejour::class)->findSejourEncourFreeParentParTypePart($lastdayprevmonth, $firstdayprevmonth, $typePart);
            $findSejourEncourPayantParentPayeLast = $this->em->getRepository(Sejour::class)->findSejourEncourPayantParentPayeParTypePart($lastdayprevmonth, $firstdayprevmonth, $typePart);
            //var_dump($findSejourEncourPayantParentPaye);dd($findSejourEncourPayantParentPayeLast);
            if ($findSejourEncourPayantParentPayeLast[0][1] != 0) {
                $pourcentuserCnxPay = ((($findSejourEncourPayantParentPaye[0][1] - $findSejourEncourPayantParentPayeLast[0][1]) / $findSejourEncourPayantParentPayeLast[0][1]) * 100);
            } else {
                $pourcentuserCnxPay = 0;
            }
            if ($findSejourEncourFreeParentLast[0][1] != 0) {
                $pourcentuserCnxFree = ((($findSejourEncourFreeParent[0][1] - $findSejourEncourFreeParentLast[0][1]) / $findSejourEncourFreeParentLast[0][1]) * 100);
            } else {
                $pourcentuserCnxFree = 0;
            }
            $totalCnxParent = $findSejourEncourFreeParent[0][1] + $findSejourEncourPayantParentPaye[0][1];
            $totalCnxParentLast = $findSejourEncourFreeParentLast[0][1] + $findSejourEncourPayantParentPayeLast[0][1];
            if ($totalCnxParentLast != 0) {
                $pourcentuserCnx = ((($totalCnxParent - $totalCnxParentLast) / $totalCnxParentLast) * 100);
            } else {
                $pourcentuserCnx = 0;
            }
            $FindeUSerParentBetween = $SEjourServiceB->FindeUSerParentBetween($datedebut, $datefin);
            $FindeUSerParentActiveBetween = $SEjourServiceB->FindeUSerParentActiveBetween($datedebut, $datefin);
            $FindeUSerParentBetweenLast = $SEjourServiceB->FindeUSerParentBetween($firstdayprevmonth, $lastdayprevmonth);
            $FindeUSerParentActiveBetweenLast = $SEjourServiceB->FindeUSerParentActiveBetween($firstdayprevmonth, $lastdayprevmonth);
            if (sizeOf($FindeUSerParentBetweenLast) != 0) {
                $pourcentuserCreate = (((sizeOf($FindeUSerParentBetween) - sizeOf($FindeUSerParentBetweenLast)) / sizeOf($FindeUSerParentBetweenLast)) * 100);
            } else {
                $pourcentuserCreate = 0;
            }
            if (sizeOf($FindeUSerParentActiveBetweenLast) != 0) {
                $pourcentuserActive = (((sizeOf($FindeUSerParentActiveBetween) - sizeOf($FindeUSerParentActiveBetweenLast)) / sizeOf($FindeUSerParentActiveBetweenLast)) * 100);
            } else {
                $pourcentuserActive = 0;
            }
            if (sizeof($getNbrSejourCree) != 0) {
                $tauxSej = (sizeOf($sejourEncourHaveAttach) / sizeOf($getNbrSejourCree)) * 100;
            } else {
                $tauxSej = 0;
            }
            if (sizeOf($FindeUSerParentBetween) != 0) {
                // $TauxCmpt = ($totalCnxParent/sizeOf($FindeUSerParentBetween))*100;
                //04-09-2021 => Touhemi cette tâche demandée par Ramzi 
                if (sizeOf($sejourEncourHaveAttach)) {
                    $TauxCmpt = (($totalCnxParent) / (sizeOf($sejourEncourHaveAttach) * $EnfantDeclare)) * 100;
                } else {
                    $TauxCmpt = 0;
                }
            } else {
                $TauxCmpt = 0;
            }
            $tauxCa = (floatval($caConnexion['cacnxtotal']) + floatval($caProduits)) - (floatval($reversementTotal));
            if (($totalCnxParent) != 0) {
                $tauCmd = (sizeof($ListDesCommande) / ($totalCnxParent)) * 100;
            } else {
                $tauCmd = 0;
            }
        }
        /**  Appliquer filtre par durée seulement*/
        else {
            $datedebut = \DateTime::createFromFormat('d/m/Y', $datedebut)->setTime(0, 0);
            $datefin = \DateTime::createFromFormat('d/m/Y', $datefin)->setTime(0, 0);
            $firstdayprevmonth = \DateTime::createFromFormat('d/m/Y', $firstdayprevmonth)->setTime(0, 0);
            $lastdayprevmonth = \DateTime::createFromFormat('d/m/Y', $lastdayprevmonth)->setTime(0, 0);
            $sejourEncour = $this->em->getRepository(Sejour::class)->findSejourMonthEncour($datefin, $datedebut);
            $sejourEncourFree = $this->em->getRepository(Sejour::class)->findSejourEncourMonthFree($datefin, $datedebut);
            //EF&PF
            $sejourEncourFree_EF = $this->em->getRepository(Sejour::class)->findSejourEncourMonthFreeEcole($datefin, $datedebut);
            $sejourEncourFree_PF = $this->em->getRepository(Sejour::class)->findSejourEncourMonthFreePartenaire($datefin, $datedebut);
            $sejourEncourPayent = $this->em->getRepository(Sejour::class)->findSejourEncourMonthPayant($datefin, $datedebut);
            $sejourEncourFreeLast = $this->em->getRepository(Sejour::class)->findSejourEncourMonthFree($lastdayprevmonth, $firstdayprevmonth);
            $sejourEncourPayentLast = $this->em->getRepository(Sejour::class)->findSejourEncourMonthPayant($lastdayprevmonth, $firstdayprevmonth);
            $getNbrSejourCree = $SEjourServiceB->getNbrSejourCree($datedebut, $datefin);
            $getNbrSejourCreeLast = $SEjourServiceB->getNbrSejourCree($firstdayprevmonth, $lastdayprevmonth);
            $sejourEncourLast = $this->em->getRepository(Sejour::class)->findSejourMonthEncour($lastdayprevmonth, $firstdayprevmonth);
            if (sizeOf($getNbrSejourCreeLast) != 0) {
                $pourcentCreer = (((sizeOf($getNbrSejourCree) - sizeOf($getNbrSejourCreeLast)) / sizeOf($getNbrSejourCreeLast)) * 100);
            } else {
                $pourcentCreer = 0;
            }
            $sejourEncourHaveAttach = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttach($datefin, $datedebut);
            $sejourEncourHaveAttachLast = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttach($lastdayprevmonth, $firstdayprevmonth);
            $sejourEncourHaveAttachFree = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachFree($datefin, $datedebut);
            //EF&PF
            $sejourEncourHaveAttachFree_EF = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachFree_ECOLE($datefin, $datedebut);
            $sejourEncourHaveAttachFree_PF = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachFree_PARTENAIRE($datefin, $datedebut);
            $sejourEncourHaveAttachFreeLast = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachFree($lastdayprevmonth, $firstdayprevmonth);
            $sejourEncourHaveAttachPay = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachPay($datefin, $datedebut);
            $sejourEncourHaveAttachPayLast = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachPay($lastdayprevmonth, $firstdayprevmonth);
            if (intval($sejourEncourPayentLast[0][1]) != 0) {
                $pourcentEncourPay = (((intval($sejourEncourPayent[0][1]) - intval($sejourEncourPayentLast[0][1])) / intval($sejourEncourPayentLast[0][1])) * 100);
            } else {
                $pourcentEncourPay = 0;
            }
            if (intval($sejourEncourFreeLast[0][1]) != 0) {
                $pourcentEncourFree = (((intval($sejourEncourFree[0][1]) - intval($sejourEncourFreeLast[0][1])) / intval($sejourEncourFreeLast[0][1])) * 100);
            } else {
                $pourcentEncourFree = 0;
            }
            if (sizeOf($sejourEncourHaveAttachLast) != 0) {
                $pourcentActif = (((sizeOf($sejourEncourHaveAttach) - sizeOf($sejourEncourHaveAttachLast)) / sizeOf($sejourEncourHaveAttachLast)) * 100);
            } else {
                $pourcentActif = 0;
            }
            if (sizeOf($sejourEncourHaveAttachFreeLast) != 0) {
                $pourcentActifFree = (((sizeOf($sejourEncourHaveAttachFree) - sizeOf($sejourEncourHaveAttachFreeLast)) / sizeOf($sejourEncourHaveAttachFreeLast)) * 100);
            } else {
                $pourcentActifFree = 0;
            }
            if (sizeOf($sejourEncourHaveAttachPayLast) != 0) {
                $pourcentActifPay = (((sizeOf($sejourEncourHaveAttachPay) - sizeOf($sejourEncourHaveAttachPayLast)) / sizeOf($sejourEncourHaveAttachPayLast)) * 100);
            } else {
                $pourcentActifPay = 0;
            }
            $EnfantDeclare = $SEjourServiceB->FormaterarraydateBetweennbrEnfant($sejourEncourHaveAttach, 'getDateDebutSejour', 'getDateFinSejour', $datedebut, $datefin);
            $EnfantDeclareLast = $SEjourServiceB->FormaterarraydateBetweennbrEnfant($sejourEncourHaveAttachLast, 'getDateDebutSejour', 'getDateFinSejour', $firstdayprevmonth, $lastdayprevmonth);
            if (sizeOf($sejourEncourHaveAttach) != 0) {
                $EnfantDeclare = intval($EnfantDeclare['totenf']) / sizeOf($sejourEncourHaveAttach);
            } else {
                $EnfantDeclare = 0;
            }
            if (sizeOf($sejourEncourHaveAttachLast) != 0) {
                $EnfantDeclareLast = intval($EnfantDeclareLast['totenf']) / sizeOf($sejourEncourHaveAttachLast);
            } else {
                $EnfantDeclareLast = 0;
            }
            if ($EnfantDeclareLast != 0) {
                $pourcentEnfant = ((($EnfantDeclare - $EnfantDeclareLast) / $EnfantDeclareLast) * 100);
            } else {
                $pourcentEnfant = 0;
            }
            $ListDesCommande = $this->em->getRepository(Commande::class)->ListDesCommande($datedebut, $datefin);
            $ListDesCommandeLast = $this->em->getRepository(Commande::class)->ListDesCommande($firstdayprevmonth, $lastdayprevmonth);
            $nbrCmdParSejour = $commndeService->CommandePArSejour($datedebut, $datefin);
            $nbrCmdParSejourLast = $commndeService->CommandePArSejour($firstdayprevmonth, $lastdayprevmonth);
            $nbrProduit = $this->em->getRepository(Commande::class)->nbrDesProduit($datedebut, $datefin);
            $nbrProduitLast = $this->em->getRepository(Commande::class)->nbrDesProduit($firstdayprevmonth, $lastdayprevmonth);
            if (sizeof($ListDesCommande) == 0) {
                $nbrProduitPartCommande = 0;
            } else {
                $nbrProduitPartCommande = $nbrProduit[0][1] / sizeof($ListDesCommande);
            }
            if (sizeof($ListDesCommandeLast) == 0) {
                $nbrProduitPartCommandeLast = 0;
            } else {
                $nbrProduitPartCommandeLast = $nbrProduitLast[0][1] / sizeof($ListDesCommandeLast);
            }
            if (sizeof($ListDesCommandeLast) != 0) {
                $pourcentNbrCmd = (((sizeOf($ListDesCommande) - sizeOf($ListDesCommandeLast)) / sizeOf($ListDesCommandeLast)) * 100);
            } else {
                $pourcentNbrCmd = 0;
            }
            if ($nbrCmdParSejourLast != 0) {
                $pourcentNbrCmdSej = ((($nbrCmdParSejour - $nbrCmdParSejourLast) / $nbrCmdParSejourLast) * 100);
            } else {
                $pourcentNbrCmdSej = 0;
            }
            if ($nbrProduitPartCommandeLast != 0) {
                $pourcentProdCmd = ((($nbrProduitPartCommande - $nbrProduitPartCommandeLast) / $nbrProduitPartCommandeLast) * 100);
            } else {
                $pourcentProdCmd = 0;
            }
            if ($nbrProduitLast[0][1] != 0) {
                $pourcentNbrProd = ((($nbrProduit[0][1] - $nbrProduitLast[0][1]) / $nbrProduitLast[0][1]) * 100);
            } else {
                $pourcentNbrProd = 0;
            }
            $ConnexionsCa = $this->em->getRepository(ParentSejour::class)->caConnexionTotal($datedebut, $datefin);
            $ConnexionsCaLast = $this->em->getRepository(ParentSejour::class)->caConnexionTotal($firstdayprevmonth, $lastdayprevmonth);
            $caConnexion = $commndeService->CalculeCaConnexion($ConnexionsCa, 'getDateCreation', $datedebut, $datefin);
            $caConnexionLast = $commndeService->CalculeCaConnexion($ConnexionsCaLast, 'getDateCreation', $firstdayprevmonth, $lastdayprevmonth);
            $caConnexion['reverCnx'] = $caConnexion['reverCnx'];
            $caConnexionLast['reverCnx'] = $caConnexionLast['reverCnx'];
            $reversementProduit = $commndeService->getReversementProduit($ListDesCommande);
            $reversementProduitLast = $commndeService->getReversementProduit($ListDesCommandeLast);
            $reversementTotal = $reversementProduit + $caConnexion["reverCnx"];
            $reversementTotalLast = $reversementProduitLast + $caConnexionLast["reverCnx"];
            $caProduits = $commndeService->getSommeProduit($ListDesCommande)['totalCaProduit'];
            $caProduitsLast = $commndeService->getSommeProduit($ListDesCommandeLast)['totalCaProduit'];
            $nbrCmds = $commndeService->getSommeProduit($ListDesCommande)['nbrCmd'];
            $nbrCmdsLast = $commndeService->getSommeProduit($ListDesCommandeLast)['nbrCmd'];
            $totalEnv = $commndeService->getSommeProduit($ListDesCommande)['totalEnv'];
            //dd($caProduit);
            if ($nbrCmds != 0) {
                $PanierMoyen = $caProduits / $nbrCmds;
            } else {
                $PanierMoyen = 0;
            }
            if ($nbrCmdsLast != 0) {
                $PanierMoyenLast = $caProduitsLast / $nbrCmdsLast;
            } else {
                $PanierMoyenLast = 0;
            }
            if ($caConnexionLast['cacnxtotal'] != 0) {
                $pourcentCaCnx = ((($caConnexion['cacnxtotal'] - $caConnexionLast['cacnxtotal']) / $caConnexionLast['cacnxtotal']) * 100);
            } else {
                $pourcentCaCnx = 0;
            }
            if ($caConnexionLast['cacnxPay'] != 0) {
                $pourcentCaCnxPay = ((($caConnexion['cacnxPay'] - $caConnexionLast['cacnxPay']) / $caConnexionLast['cacnxPay']) * 100);
            } else {
                $pourcentCaCnxPay = 0;
            }
            if ($caConnexionLast['cacnxfree'] != 0) {
                $pourcentCaCnxFree = ((($caConnexion['cacnxfree'] - $caConnexionLast['cacnxfree']) / $caConnexionLast['cacnxfree']) * 100);
            } else {
                $pourcentCaCnxFree = 0;
            }
            if ($caConnexionLast['reverCnx'] != 0) {
                $pourcentCaRevCnx = ((($caConnexion['reverCnx'] - $caConnexionLast['reverCnx']) / $caConnexionLast['reverCnx']) * 100);
            } else {
                $pourcentCaRevCnx = 0;
            }
            if ($caProduitsLast != 0) {
                $pourcentCaProd = ((($caProduits - $caProduitsLast) / $caProduitsLast) * 100);
            } else {
                $pourcentCaProd = 0;
            }
            if ($PanierMoyenLast != 0) {
                $pourcentCaPan = ((($PanierMoyen - $PanierMoyenLast) / $PanierMoyenLast) * 100);
            } else {
                $pourcentCaPan = 0;
            }
            if ($reversementTotalLast != 0) {
                $pourcentCaRev = ((($reversementTotal - $reversementTotalLast) / $reversementTotalLast) * 100);
            } else {
                $pourcentCaRev = 0;
            }
            if ($reversementProduitLast != 0) {
                $pourcentCaRevProd = ((($reversementProduit - $reversementProduitLast) / $reversementProduitLast) * 100);
            } else {
                $pourcentCaRevProd = 0;
            }
            if ($reversementProduitLast != 0) {
                $pourcentCaRevProd = ((($reversementProduit - $reversementProduitLast) / $reversementProduitLast) * 100);
            } else {
                $pourcentCaRevProd = 0;
            }
            $findSejourEncourFreeParent = $this->em->getRepository(Sejour::class)->findSejourEncourFreeParentBetween($datefin, $datedebut);
            //EF&PF
            $findSejourEncourFreeParent_EF = $this->em->getRepository(Sejour::class)->findSejourEncourFreeParentBetween_ECOLE($datefin, $datedebut);
            $findSejourEncourFreeParent_PF = $this->em->getRepository(Sejour::class)->findSejourEncourFreeParentBetween_PARTENAIRE($datefin, $datedebut);
            $findSejourEncourPayantParentPaye = $this->em->getRepository(Sejour::class)->findSejourEncourPayantParentPayeBetween($datefin, $datedebut);
            $findSejourEncourFreeParentLast = $this->em->getRepository(Sejour::class)->findSejourEncourFreeParentBetween($lastdayprevmonth, $firstdayprevmonth);
            $findSejourEncourPayantParentPayeLast = $this->em->getRepository(Sejour::class)->findSejourEncourPayantParentPayeBetween($lastdayprevmonth, $firstdayprevmonth);
            //var_dump($findSejourEncourPayantParentPaye);dd($findSejourEncourPayantParentPayeLast);
            if ($findSejourEncourPayantParentPayeLast[0][1] != 0) {
                $pourcentuserCnxPay = ((($findSejourEncourPayantParentPaye[0][1] - $findSejourEncourPayantParentPayeLast[0][1]) / $findSejourEncourPayantParentPayeLast[0][1]) * 100);
            } else {
                $pourcentuserCnxPay = 0;
            }
            if ($findSejourEncourFreeParentLast[0][1] != 0) {
                $pourcentuserCnxFree = ((($findSejourEncourFreeParent[0][1] - $findSejourEncourFreeParentLast[0][1]) / $findSejourEncourFreeParentLast[0][1]) * 100);
            } else {
                $pourcentuserCnxFree = 0;
            }
            $totalCnxParent = $findSejourEncourFreeParent[0][1] + $findSejourEncourPayantParentPaye[0][1];
            $totalCnxParentLast = $findSejourEncourFreeParentLast[0][1] + $findSejourEncourPayantParentPayeLast[0][1];
            if ($totalCnxParentLast != 0) {
                $pourcentuserCnx = ((($totalCnxParent - $totalCnxParentLast) / $totalCnxParentLast) * 100);
            } else {
                $pourcentuserCnx = 0;
            }
            $FindeUSerParentBetween = $SEjourServiceB->FindeUSerParentBetween($datedebut, $datefin);
            $FindeUSerParentActiveBetween = $SEjourServiceB->FindeUSerParentActiveBetween($datedebut, $datefin);
            $FindeUSerParentBetweenLast = $SEjourServiceB->FindeUSerParentBetween($firstdayprevmonth, $lastdayprevmonth);
            $FindeUSerParentActiveBetweenLast = $SEjourServiceB->FindeUSerParentActiveBetween($firstdayprevmonth, $lastdayprevmonth);
            if (sizeOf($FindeUSerParentBetweenLast) != 0) {
                $pourcentuserCreate = (((sizeOf($FindeUSerParentBetween) - sizeOf($FindeUSerParentBetweenLast)) / sizeOf($FindeUSerParentBetweenLast)) * 100);
            } else {
                $pourcentuserCreate = 0;
            }
            if (sizeOf($FindeUSerParentActiveBetweenLast) != 0) {
                $pourcentuserActive = (((sizeOf($FindeUSerParentActiveBetween) - sizeOf($FindeUSerParentActiveBetweenLast)) / sizeOf($FindeUSerParentActiveBetweenLast)) * 100);
            } else {
                $pourcentuserActive = 0;
            }
            if (sizeof($getNbrSejourCree) != 0) {
                $tauxSej = (sizeOf($sejourEncourHaveAttach) / sizeOf($getNbrSejourCree)) * 100;
            } else {
                $tauxSej = 0;
            }
            if (sizeOf($FindeUSerParentBetween) != 0) {
                //$TauxCmpt = ($totalCnxParent/sizeOf($FindeUSerParentBetween))*100;
                //04-09-2021 => Touhemi cette tâche demandée par Ramzi 
                if (sizeOf($sejourEncourHaveAttach)) {
                    $TauxCmpt = (($totalCnxParent) / (sizeOf($sejourEncourHaveAttach) * $EnfantDeclare)) * 100;
                } else {
                    $TauxCmpt = 0;
                }
            } else {
                $TauxCmpt = 0;
            }
            $tauxCa = (floatval($caConnexion['cacnxtotal']) + floatval($caProduits)) - (floatval($reversementTotal));
            if (($totalCnxParent) != 0) {
                $tauCmd = (sizeof($ListDesCommande) / ($totalCnxParent)) * 100;
            } else {
                $tauCmd = 0;
            }
        }
        $session = $this->session;
        $session->set('datedebut', $datedebut);
        $session->set('datefin', $datefin);
        $session->set('part', $part);
        $session->set('typePart', $typePart);
        $session->set('firstdayprevmonth', $firstdayprevmonth);
        $session->set('lastdayprevmonth', $lastdayprevmonth);
        //var_dump($firstdayprevmonth);dd($lastdayprevmonth);
        return new JsonResponse([
            'sejourEncourFree_EF' => $sejourEncourFree_EF[0][1], 'sejourEncourFree_PF' => $sejourEncourFree_PF[0][1],
            'sejourEncourHaveAttachFree_EF' => $sejourEncourHaveAttachFree_EF, 'sejourEncourHaveAttachFree_PF' => $sejourEncourHaveAttachFree_PF,
            'findSejourEncourFreeParent_EF' => $findSejourEncourFreeParent_EF[0][1], 'findSejourEncourFreeParent_PF' => $findSejourEncourFreeParent_PF[0][1],
            'caConnexions' => $caConnexion, 'caConnexionsLast' => $caConnexionLast, 'pourcentCaCnx' => $pourcentCaCnx,
            'pourcentCaCnxPay' => $pourcentCaCnxPay, 'pourcentCaCnxFree' => $pourcentCaCnxFree,
            'caProduits' => $caProduits, 'caProduitsLast' => $caProduitsLast, 'pourcentCaProd' => $pourcentCaProd,
            'PanierMoyen' => $PanierMoyen, 'PanierMoyenLast' => $PanierMoyenLast, 'pourcentCaPan' => $pourcentCaPan,
            'reversementTotal' => $reversementTotal, 'reversementTotalLast' => $reversementTotalLast, 'pourcentCaRev' => $pourcentCaRev,
            'reversementProduit' => $reversementProduit, 'reversementProduitLast' => $reversementProduitLast, 'pourcentCaRevProd' => $pourcentCaRevProd,
            'nbrDesCommande' => sizeof($ListDesCommande), 'pourcentNbrCmd' => $pourcentNbrCmd, 'pourcentCaRevCnx' => $pourcentCaRevCnx,
            'nbrDesCommandeLast' => sizeof($ListDesCommandeLast), 'pourcentNbrProd' => $pourcentNbrProd,
            'nbrCmdParSejour' => $nbrCmdParSejour, 'nbrDesProduit' => $nbrProduit[0][1], 'nbrDesProduitLast' => $nbrProduitLast[0][1],
            'nbrCmdParSejourLast' => $nbrCmdParSejourLast, 'pourcentNbrCmdSej' => $pourcentNbrCmdSej,
            'nbrProduitPartCommande' => $nbrProduitPartCommande,
            'nbrProduitPartCommandeLast' => $nbrProduitPartCommandeLast, 'pourcentProdCmd' => $pourcentProdCmd,
            'sejourEncourHaveAttachFreeLast' => $sejourEncourHaveAttachFreeLast,
            'sejourEncourFree' => $sejourEncourFree[0][1], 'pourcentEncourPay' => $pourcentEncourPay,
            'sejourEncourPayent' => $sejourEncourPayent[0][1], 'pourcentEncourFree' => $pourcentEncourFree,
            'sejourEncourFreeLast' => $sejourEncourFreeLast[0][1],
            'sejourEncourPayentLast' => $sejourEncourPayentLast[0][1], 'tauxSej' => $tauxSej,
            'tauxCmpt' => $TauxCmpt, 'sejourEncourHaveAttachPay' => $sejourEncourHaveAttachPay,
            'sejourEncourHaveAttachPayLast' => $sejourEncourHaveAttachPayLast, 'pourcentActifFree' => $pourcentActifFree,
            'sejourEncourHaveAttachFree' => $sejourEncourHaveAttachFree, 'pourcentActifPay' => $pourcentActifPay,
            'sejourEncour' => $sejourEncour, 'tauxCa' => $tauxCa, 'tauCmd' => $tauCmd,
            'sejourEncourLast' => $sejourEncourLast, 'TauxCmpt' => $TauxCmpt,
            'pourcentCreer' => $pourcentCreer,
            'sejourEncourHaveAttach' => $sejourEncourHaveAttach,
            'sejourEncourHaveAttachLast' => $sejourEncourHaveAttachLast,
            'pourcentActif' => $pourcentActif, 'pourcentEnfant' => $pourcentEnfant,
            'EnfantDeclareLast' => $EnfantDeclareLast,
            'findSejourEncourFreeParent' => $findSejourEncourFreeParent[0][1],
            'findSejourEncourPayantParentPaye' => $findSejourEncourPayantParentPaye[0][1],
            'EnfantDeclare' => $EnfantDeclare, 'usercreate' => $FindeUSerParentBetween, 'userActive' => $FindeUSerParentActiveBetween,
            'usercreateLast' => $FindeUSerParentBetweenLast, 'userActiveLast' => $FindeUSerParentActiveBetweenLast, 'sejourCrees' => $getNbrSejourCree, 'sejourCreesLast' => $getNbrSejourCreeLast, 'pourcentuserActive' => $pourcentuserActive, 'pourcentuserCreate' => $pourcentuserCreate,
            'findSejourEncourFreeParentLast' => $findSejourEncourFreeParentLast[0][1], 'findSejourEncourPayantParentPayeLast' => $findSejourEncourPayantParentPayeLast[0][1], 'pourcentuserCnxPay' => $pourcentuserCnxPay,
            'pourcentuserCnxFree' => $pourcentuserCnxFree, 'pourcentuserCnx' => $pourcentuserCnx, 'totalCnxParent' => $totalCnxParent, 'totalCnxParentLast' => $totalCnxParentLast,
            'ListDesCommande' => $ListDesCommande, 'ListDesCommandeLast' => $ListDesCommandeLast, 'pourcentuserActive' => $pourcentuserActive,
        ]);
    }
    /**
     * @Route("/Graphe/{type}", name="PageGraphe")
     */
    public function PageGraphe($type, ChartBuilderInterface $chartBuilder): Response
    {
        $session = $this->session;
        $datedebut =  $session->get('datedebut');
        $datefin = $session->get('datefin');
        $part = $session->get('part');
        $typePart = $session->get('typePart');
        $firstdayprevmonth = $session->get('firstdayprevmonth');
        $lastdayprevmonth = $session->get('lastdayprevmonth');
        $SEjourServiceB = $this->sejourService;
        if ((!(isset($datedebut)) || $datedebut === null || $datedebut === "") && (!(isset($datefin)) || $datefin === null || $datefin === "")) {
            $datedebut = date('01/m/Y');
            $datefin = date('t/m/Y');
            $firstdayprevmonth = date('01/m/Y', strtotime('-1 months'));
            $lastdayprevmonth = date('t/m/Y', strtotime('-1 months'));
            $datedebut = \DateTime::createFromFormat('d/m/Y', $datedebut)->setTime(0, 0);
            $datefin = \DateTime::createFromFormat('d/m/Y', $datefin)->setTime(0, 0);
            $firstdayprevmonth = \DateTime::createFromFormat('d/m/Y', $firstdayprevmonth)->setTime(0, 0);
            $lastdayprevmonth = \DateTime::createFromFormat('d/m/Y', $lastdayprevmonth)->setTime(0, 0);
        }
        if ($type === "Sejours") {
            if ($part != null && $part != "") {
                $getNbrSejourCree = $SEjourServiceB->getNbrSejourCreeParPart($datedebut, $datefin, $part);
                $getNbrSejourCreeFormated = $SEjourServiceB->FormaterarraydateBetween($getNbrSejourCree, 'getDateDebutSejour', 'getDateFinSejour', $datedebut, $datefin);
                $getNbrSejourCreeLast = $SEjourServiceB->getNbrSejourCreeParPart($firstdayprevmonth, $lastdayprevmonth, $part);
                $getNbrSejourCreeFormatedLast = $SEjourServiceB->FormaterarraydateBetween($getNbrSejourCreeLast, 'getDateDebutSejour', 'getDateFinSejour', $firstdayprevmonth, $lastdayprevmonth);
                $findSejourActiveBetween = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachParPart($datedebut, $datefin, $part);
                $findSejourActiveBetweenFormat = $SEjourServiceB->FormaterarraydateBetween($findSejourActiveBetween, 'getDateDebutSejour', 'getDateFinSejour', $datedebut, $datefin);
                $findSejourActiveBetweenLast = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachParPart($firstdayprevmonth, $lastdayprevmonth, $part);
                $findSejourActiveBetweenFormatLast = $SEjourServiceB->FormaterarraydateBetween($findSejourActiveBetweenLast, 'getDateDebutSejour', 'getDateFinSejour', $firstdayprevmonth, $lastdayprevmonth);
            } elseif ($typePart != null && $typePart != "") {
                $getNbrSejourCree = $SEjourServiceB->getNbrSejourCreeParTypePart($datedebut, $datefin, $typePart);
                $getNbrSejourCreeFormated = $SEjourServiceB->FormaterarraydateBetween($getNbrSejourCree, 'getDateDebutSejour', 'getDateFinSejour', $datedebut, $datefin);
                $getNbrSejourCreeLast = $SEjourServiceB->getNbrSejourCreeParTypePart($firstdayprevmonth, $lastdayprevmonth, $typePart);
                $getNbrSejourCreeFormatedLast = $SEjourServiceB->FormaterarraydateBetween($getNbrSejourCreeLast, 'getDateDebutSejour', 'getDateFinSejour', $firstdayprevmonth, $lastdayprevmonth);
                $findSejourActiveBetween = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachTypePart($datefin, $datedebut, $typePart);
                $findSejourActiveBetweenFormat = $SEjourServiceB->FormaterarraydateBetween($findSejourActiveBetween, 'getDateDebutSejour', 'getDateFinSejour', $datedebut, $datefin);
                $findSejourActiveBetweenLast = $this->em->getRepository(Sejour::class)->findSejourEncourMonthHaveAttachTypePart($lastdayprevmonth, $firstdayprevmonth, $typePart);
                $findSejourActiveBetweenFormatLast = $SEjourServiceB->FormaterarraydateBetween($findSejourActiveBetweenLast, 'getDateDebutSejour', 'getDateFinSejour', $firstdayprevmonth, $lastdayprevmonth);
            } else {
                $getNbrSejourCree = $SEjourServiceB->getNbrSejourCree($datedebut, $datefin);
                $getNbrSejourCreeFormated = $SEjourServiceB->FormaterarraydateBetween($getNbrSejourCree, 'getDateDebutSejour', 'getDateFinSejour', $datedebut, $datefin);
                $getNbrSejourCreeLast = $SEjourServiceB->getNbrSejourCree($firstdayprevmonth, $lastdayprevmonth);
                $getNbrSejourCreeFormatedLast = $SEjourServiceB->FormaterarraydateBetween($getNbrSejourCreeLast, 'getDateDebutSejour', 'getDateFinSejour', $firstdayprevmonth, $lastdayprevmonth);
                $findSejourActiveBetween = $SEjourServiceB->findSejourActiveBetween($datedebut, $datefin);
                $findSejourActiveBetweenFormat = $SEjourServiceB->FormaterarraydateBetween($findSejourActiveBetween, 'getDateDebutSejour', 'getDateFinSejour', $datedebut, $datefin);
                $findSejourActiveBetweenLast = $SEjourServiceB->findSejourActiveBetween($firstdayprevmonth, $lastdayprevmonth);
                $findSejourActiveBetweenFormatLast = $SEjourServiceB->FormaterarraydateBetween($findSejourActiveBetweenLast, 'getDateDebutSejour', 'getDateFinSejour', $firstdayprevmonth, $lastdayprevmonth);
            }
            // dd($getNbrSejourCreeFormated);
            return $this->render('PageGraphe.html.twig', [
                'type' => $type, 'datedebut' => $datedebut, 'datefin' => $datefin,
                'firstdayprevmonth' => $firstdayprevmonth, 'lastdayprevmonth' => $lastdayprevmonth,
                'SejEnCour' => $getNbrSejourCreeFormated, 'SejActf' => $findSejourActiveBetweenFormat,
                'SejEnCourLast' => $getNbrSejourCreeFormatedLast, 'SejActfLast' => $findSejourActiveBetweenFormatLast
            ]);
        } elseif ($type === "Comptes_Parents") {
            if ($part != null && $part != "") {
                $FindeUSerParentBetween = $SEjourServiceB->FindeUSerParentBetween($datedebut, $datefin);
                $FindeUSerParentActiveBetween = $SEjourServiceB->FindeUSerParentActiveBetween($datedebut, $datefin);
                $usercreate = $SEjourServiceB->FormaterarraydateBetweennbrPArentCncte($FindeUSerParentBetween, 'getDateCreation', $datedebut, $datefin);
                $userActive = $SEjourServiceB->FormaterarraydateBetweennbrPArentCncte($FindeUSerParentActiveBetween, 'getDateCreation', $datedebut, $datefin);
                $FindeUSerParentBetweenLast = $SEjourServiceB->FindeUSerParentBetween($firstdayprevmonth, $lastdayprevmonth);
                $FindeUSerParentActiveBetweenLast = $SEjourServiceB->FindeUSerParentActiveBetween($firstdayprevmonth, $lastdayprevmonth);
                $usercreateLast = $SEjourServiceB->FormaterarraydateBetweennbrPArentCncte($FindeUSerParentBetweenLast, 'getDateCreation', $firstdayprevmonth, $lastdayprevmonth);
                $userActiveLast = $SEjourServiceB->FormaterarraydateBetweennbrPArentCncte($FindeUSerParentActiveBetweenLast, 'getDateCreation', $firstdayprevmonth, $lastdayprevmonth);
                $findParentDateBetween = $SEjourServiceB->findParentDateBetweenParPart($datedebut, $datefin, $part);
                $parentcnct = $SEjourServiceB->FormaterarraydateBetweennbrPArentCncte($findParentDateBetween, 'getDateCreation', $datedebut, $datefin);
                $findParentDateBetweenLast = $SEjourServiceB->findParentDateBetweenParPart($firstdayprevmonth, $lastdayprevmonth, $part);
                $parentcnctLast = $SEjourServiceB->FormaterarraydateBetweennbrPArentCncte($findParentDateBetweenLast, 'getDateCreation', $firstdayprevmonth, $lastdayprevmonth);
            } elseif ($typePart != null && $typePart != "") {
                $FindeUSerParentBetween = $SEjourServiceB->findParentDateBetween($datedebut, $datefin);
                $FindeUSerParentActiveBetween = $SEjourServiceB->FindeUSerParentActiveBetweenParTypePart($datedebut, $datefin, $typePart);
                $FindeUSerParentBetweenLast = $SEjourServiceB->findParentDateBetween($firstdayprevmonth, $lastdayprevmonth);
                $FindeUSerParentActiveBetweenLast = $SEjourServiceB->FindeUSerParentActiveBetweenParTypePart($firstdayprevmonth, $lastdayprevmonth, $typePart);
                $usercreate = $SEjourServiceB->FormaterarraydateBetweennbrPArentCncte($FindeUSerParentBetween, 'getDateCreation', $datedebut, $datefin);
                $userActive = $SEjourServiceB->FormaterarraydateBetweennbrPArentCncte($FindeUSerParentActiveBetween, 'getDateCreation', $datedebut, $datefin);
                $usercreateLast = $SEjourServiceB->FormaterarraydateBetweennbrPArentCncte($FindeUSerParentBetweenLast, 'getDateCreation', $firstdayprevmonth, $lastdayprevmonth);
                $userActiveLast = $SEjourServiceB->FormaterarraydateBetweennbrPArentCncte($FindeUSerParentActiveBetweenLast, 'getDateCreation', $firstdayprevmonth, $lastdayprevmonth);
                $findParentDateBetween = $SEjourServiceB->findParentDateBetweenParTypePart($datedebut, $datefin, $typePart);
                $parentcnct = $SEjourServiceB->FormaterarraydateBetweennbrPArentCncte($FindeUSerParentBetween, 'getDateCreation', $datedebut, $datefin);
                $findParentDateBetweenLast = $SEjourServiceB->findParentDateBetween($firstdayprevmonth, $lastdayprevmonth);
                $parentcnctLast = $SEjourServiceB->FormaterarraydateBetweennbrPArentCncte($FindeUSerParentBetween, 'getDateCreation', $firstdayprevmonth, $lastdayprevmonth);
            } else {
                $FindeUSerParentBetween = $SEjourServiceB->FindeUSerParentBetween($datedebut, $datefin);
                $FindeUSerParentActiveBetween = $SEjourServiceB->FindeUSerParentActiveBetween($datedebut, $datefin);
                $usercreate = $SEjourServiceB->FormaterarraydateBetweennbrPArentCncte($FindeUSerParentBetween, 'getDateCreation', $datedebut, $datefin);
                $userActive = $SEjourServiceB->FormaterarraydateBetweennbrPArentCncte($FindeUSerParentActiveBetween, 'getDateCreation', $datedebut, $datefin);
                $FindeUSerParentBetweenLast = $SEjourServiceB->FindeUSerParentBetween($firstdayprevmonth, $lastdayprevmonth);
                $FindeUSerParentActiveBetweenLast = $SEjourServiceB->FindeUSerParentActiveBetween($firstdayprevmonth, $lastdayprevmonth);
                $usercreateLast = $SEjourServiceB->FormaterarraydateBetweennbrPArentCncte($FindeUSerParentBetweenLast, 'getDateCreation', $firstdayprevmonth, $lastdayprevmonth);
                $userActiveLast = $SEjourServiceB->FormaterarraydateBetweennbrPArentCncte($FindeUSerParentActiveBetweenLast, 'getDateCreation', $firstdayprevmonth, $lastdayprevmonth);
                $findParentDateBetween = $SEjourServiceB->findParentDateBetween($datedebut, $datefin);
                $parentcnct = $SEjourServiceB->FormaterarraydateBetweennbrPArentCncte($findParentDateBetween, 'getDateCreation', $datedebut, $datefin);
                $findParentDateBetweenLast = $SEjourServiceB->findParentDateBetween($firstdayprevmonth, $lastdayprevmonth);
                $parentcnctLast = $SEjourServiceB->FormaterarraydateBetweennbrPArentCncte($findParentDateBetweenLast, 'getDateCreation', $firstdayprevmonth, $lastdayprevmonth);
            }
            return $this->render('PageGraphe.html.twig', [
                'type' => $type, 'parentCnct' => $parentcnct, 'datedebut' => $datedebut, 'datefin' => $datefin,
                'firstdayprevmonth' => $firstdayprevmonth, 'lastdayprevmonth' => $lastdayprevmonth,
                'usercreate' => $usercreate, 'userActive' => $userActive,
                'parentCnctLast' => $parentcnctLast, 'usercreateLast' => $usercreateLast, 'userActiveLast' => $userActiveLast
            ]);
        } elseif ($type === "Commandes") {
            if ($part != null && $part != "") {
                $ListDesCommande = $this->em->getRepository(Commande::class)->ListDesCommandeParPart($datedebut, $datefin, $part);
                $ListDesCommandeLast = $this->em->getRepository(Commande::class)->ListDesCommandeParPart($firstdayprevmonth, $lastdayprevmonth, $part);
                $ListDesCommandeFormated = $SEjourServiceB->FormaterarraydateBetweennbrPArentCncte($ListDesCommande, 'getDateCreateCommande', $datedebut, $datefin);
                $ListDesCommandeFormatedLast = $SEjourServiceB->FormaterarraydateBetweennbrPArentCncte($ListDesCommandeLast, 'getDateCreateCommande', $firstdayprevmonth, $lastdayprevmonth);
            } elseif ($typePart != null && $typePart != "") {
                $ListDesCommande = $this->em->getRepository(Commande::class)->ListDesCommandeParTypePart($datedebut, $datefin, $typePart);
                $ListDesCommandeLast = $this->em->getRepository(Commande::class)->ListDesCommandeParTypePart($firstdayprevmonth, $lastdayprevmonth, $typePart);
                $ListDesCommandeFormated = $SEjourServiceB->FormaterarraydateBetweennbrPArentCncte($ListDesCommande, 'getDateCreateCommande', $datedebut, $datefin);
                $ListDesCommandeFormatedLast = $SEjourServiceB->FormaterarraydateBetweennbrPArentCncte($ListDesCommandeLast, 'getDateCreateCommande', $firstdayprevmonth, $lastdayprevmonth);
            } else {
                $ListDesCommande = $this->em->getRepository(Commande::class)->ListDesCommande($datedebut, $datefin);
                $ListDesCommandeLast = $this->em->getRepository(Commande::class)->ListDesCommande($firstdayprevmonth, $lastdayprevmonth);
                //var_dump(sizeof($ListDesCommande));var_dump(sizeof($ListDesCommandeLast));
                $ListDesCommandeFormated = $SEjourServiceB->FormaterarraydateBetweennbrPArentCncte($ListDesCommande, 'getDateCreateCommande', $datedebut, $datefin);
                $ListDesCommandeFormatedLast = $SEjourServiceB->FormaterarraydateBetweennbrPArentCncte($ListDesCommandeLast, 'getDateCreateCommande', $firstdayprevmonth, $lastdayprevmonth);
            }
                // dd($ListDesCommandeFormated);
                $chartLabels = array_keys($ListDesCommandeFormated);
                $chartValues = array_values($ListDesCommandeFormated);
            return $this->render('PageGraphe.html.twig', [
                'chartLabels' => $chartLabels,
                'chartValues' => $chartValues,
                'type' => $type, 'datedebut' => $datedebut, 'datefin' => $datefin,
                'firstdayprevmonth' => $firstdayprevmonth, 'lastdayprevmonth' => $lastdayprevmonth,
                'ListCmd' => $ListDesCommandeFormated, 'ListCmdLast' => $ListDesCommandeFormatedLast
            ]);
        } elseif ($type === "Chiffre_D_Affaires") {
            $commndeService = $this->statistique;
            if ($part != null && $part != "") {
                $ConnexionsCa = $this->em->getRepository(ParentSejour::class)->caConnexionTotalParPart($datedebut, $datefin, $part);
                $ConnexionsCaLast = $this->em->getRepository(ParentSejour::class)->caConnexionTotalParPart($firstdayprevmonth, $lastdayprevmonth, $part);
                $ConnexionsCaFormated = $commndeService->FormaterarrayCABetween($ConnexionsCa, 'getDateCreation', $datedebut, $datefin);
                $ConnexionsCaFormatedLast = $commndeService->FormaterarrayCABetween($ConnexionsCaLast, 'getDateCreation', $firstdayprevmonth, $lastdayprevmonth);
                $ListDesCommande = $this->em->getRepository(Commande::class)->ListDesCommandeParPart($datedebut, $datefin, $part);
                $ListDesCommandeLast = $this->em->getRepository(Commande::class)->ListDesCommandeParPart($firstdayprevmonth, $lastdayprevmonth, $part);
                $ListDesCommandeFormated = $commndeService->FormaterarrayCmdBetween($ListDesCommande, 'getDateCreateCommande', $datedebut, $datefin);
                $ListDesCommandeFormatedLast = $commndeService->FormaterarrayCmdBetween($ListDesCommandeLast, 'getDateCreateCommande', $firstdayprevmonth, $lastdayprevmonth);
                $reverCnx = $commndeService->FormaterarrayRevCnxBetween($ConnexionsCa, 'getDateCreation', $datedebut, $datefin);
                $reverCnxLast = $commndeService->FormaterarrayRevCnxBetween($ConnexionsCaLast, 'getDateCreation', $firstdayprevmonth, $lastdayprevmonth);
                $revProd = $commndeService->FormaterarrayRevProdBetween($ListDesCommande, 'getDateCreateCommande', $datedebut, $datefin);
                $revProdLast = $commndeService->FormaterarrayRevProdBetween($ListDesCommandeLast, 'getDateCreateCommande', $firstdayprevmonth, $lastdayprevmonth);
            } elseif ($typePart != null && $typePart != "") {
                $ConnexionsCa = $this->em->getRepository(ParentSejour::class)->caConnexionTotalParTypePart($datedebut, $datefin, $typePart);
                $ConnexionsCaLast = $this->em->getRepository(ParentSejour::class)->caConnexionTotalParTypePart($firstdayprevmonth, $lastdayprevmonth, $typePart);
                $ConnexionsCaFormated = $commndeService->FormaterarrayCABetween($ConnexionsCa, 'getDateCreation', $datedebut, $datefin);
                $ConnexionsCaFormatedLast = $commndeService->FormaterarrayCABetween($ConnexionsCaLast, 'getDateCreation', $firstdayprevmonth, $lastdayprevmonth);
                $ListDesCommande = $this->em->getRepository(Commande::class)->ListDesCommandeParTypePart($datedebut, $datefin, $typePart);
                $ListDesCommandeLast = $this->em->getRepository(Commande::class)->ListDesCommandeParTypePart($firstdayprevmonth, $lastdayprevmonth, $typePart);
                $ListDesCommandeFormated = $commndeService->FormaterarrayCmdBetween($ListDesCommande, 'getDateCreateCommande', $datedebut, $datefin);
                $ListDesCommandeFormatedLast = $commndeService->FormaterarrayCmdBetween($ListDesCommandeLast, 'getDateCreateCommande', $firstdayprevmonth, $lastdayprevmonth);
                $reverCnx = $commndeService->FormaterarrayRevCnxBetween($ConnexionsCa, 'getDateCreation', $datedebut, $datefin);
                $reverCnxLast = $commndeService->FormaterarrayRevCnxBetween($ConnexionsCaLast, 'getDateCreation', $firstdayprevmonth, $lastdayprevmonth);
                $revProd = $commndeService->FormaterarrayRevProdBetween($ListDesCommande, 'getDateCreateCommande', $datedebut, $datefin);
                $revProdLast = $commndeService->FormaterarrayRevProdBetween($ListDesCommandeLast, 'getDateCreateCommande', $firstdayprevmonth, $lastdayprevmonth);
            } else {
                $ConnexionsCa = $this->em->getRepository(ParentSejour::class)->caConnexionTotal($datedebut, $datefin);
                $ConnexionsCaLast = $this->em->getRepository(ParentSejour::class)->caConnexionTotal($firstdayprevmonth, $lastdayprevmonth);
                $ConnexionsCaFormated = $commndeService->FormaterarrayCABetween($ConnexionsCa, 'getDateCreation', $datedebut, $datefin);
                $ConnexionsCaFormatedLast = $commndeService->FormaterarrayCABetween($ConnexionsCaLast, 'getDateCreation', $firstdayprevmonth, $lastdayprevmonth);
                $ListDesCommande = $this->em->getRepository(Commande::class)->ListDesCommande($datedebut, $datefin);
                $ListDesCommandeLast = $this->em->getRepository(Commande::class)->ListDesCommande($firstdayprevmonth, $lastdayprevmonth);
                $ListDesCommandeFormated = $commndeService->FormaterarrayCmdBetween($ListDesCommande, 'getDateCreateCommande', $datedebut, $datefin);
                $ListDesCommandeFormatedLast = $commndeService->FormaterarrayCmdBetween($ListDesCommandeLast, 'getDateCreateCommande', $firstdayprevmonth, $lastdayprevmonth);
                $reverCnx = $commndeService->FormaterarrayRevCnxBetween($ConnexionsCa, 'getDateCreation', $datedebut, $datefin);
                $reverCnxLast = $commndeService->FormaterarrayRevCnxBetween($ConnexionsCaLast, 'getDateCreation', $firstdayprevmonth, $lastdayprevmonth);
                $revProd = $commndeService->FormaterarrayRevProdBetween($ListDesCommande, 'getDateCreateCommande', $datedebut, $datefin);
                $revProdLast = $commndeService->FormaterarrayRevProdBetween($ListDesCommandeLast, 'getDateCreateCommande', $firstdayprevmonth, $lastdayprevmonth);
            }
            //  dd($ConnexionsCaFormated);
                $chartLabels = array_keys($revProd);
                $chartValues = array_values($revProd);
                

            return $this->render('PageGraphe.html.twig', [
                'type' => $type, 'datedebut' => $datedebut, 'datefin' => $datefin,
                'chartLabels' => $chartLabels,
                'chartValues' => $chartValues,
                'firstdayprevmonth' => $firstdayprevmonth, 'lastdayprevmonth' => $lastdayprevmonth,
                'CaCnx' => $ConnexionsCaFormated, 'CaCnxLast' => $ConnexionsCaFormatedLast, 'listCmd' => $ListDesCommandeFormated,
                'listCmdLast' => $ListDesCommandeFormatedLast, 'reverCnxLast' => $reverCnxLast,
                'reverCnx' => $reverCnx, 'revProd' => $revProd, 'revProdLast' => $revProdLast,
            ]); 
        }
    }

    /**
     * @Route("/ExportCAxlsx", name="ExportCAxlsx")
     */
    public function ExportCAxlsx()
    {
        $session = $this->session;
        $datedebut =  $session->get('datedebut');
        $datefin = $session->get('datefin');
        $part = $session->get('part');
        $typePart = $session->get('typePart');
        $firstdayprevmonth = $session->get('firstdayprevmonth');
        $lastdayprevmonth = $session->get('lastdayprevmonth');
        $SEjourServiceB = $this->sejourService;
        if ((!(isset($datedebut)) || $datedebut === null || $datedebut === "") && (!(isset($datefin)) || $datefin === null || $datefin === "")) {
            $datedebut = date('01/m/Y');
            $datefin = date('t/m/Y');
            $datedebut = \DateTime::createFromFormat('d/m/Y', $datedebut)->setTime(0, 0);
            $datefin = \DateTime::createFromFormat('d/m/Y', $datefin)->setTime(0, 0);
        }
        if ($typePart != null && $typePart != "") {
            $ConnexionsCa = $this->em->getRepository(ParentSejour::class)->caConnexionTotalParTypePart($datedebut, $datefin, $typePart);
            $ListDesCommande = $this->em->getRepository(Commande::class)->ListDesCommandeParTypePart($datedebut, $datefin, $typePart);
        } elseif ($part != null && $part != "") {
            $ConnexionsCa = $this->em->getRepository(ParentSejour::class)->caConnexionTotalParPart($datedebut, $datefin, $part);
            $ListDesCommande = $this->em->getRepository(Commande::class)->ListDesCommandeParPart($datedebut, $datefin, $part);
        } else {
            $ConnexionsCa = $this->em->getRepository(ParentSejour::class)->caConnexionTotal($datedebut, $datefin);
            $ListDesCommande = $this->em->getRepository(Commande::class)->ListDesCommande($datedebut, $datefin);
        }
        //        var_dump($findSejourActiveBetweenFormat);var_dump($usercreate);var_dump($userActive);
        //var_dump($FindeUSerParentBetween[0]); user
        // var_dump(  $FindeUSerParentActiveBetween[0]); user
        // var_dump($findParentDateBetween[0]);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getActiveSheet()->setTitle("Connexions Gratuites");
        $sheet->getPageMargins()->setTop(1);
        $sheet->getPageMargins()->setRight(0.75);
        $sheet->getPageMargins()->setLeft(0.75);
        $sheet->getPageMargins()->setBottom(1);
        $sheet->setCellValue('A1', 'Sejour');
        $sheet->setCellValue('B1', 'Date Debut Séjour');
        $sheet->setCellValue('C1', 'Date Fin Séjour');
        $sheet->setCellValue('D1', 'Date Code Séjour');
        $sheet->setCellValue('E1', 'Date Fin Code Séjour');
        $sheet->setCellValue('F1', 'Parteniare');
        $sheet->setCellValue('G1', 'Client');
        $sheet->setCellValue('H1', 'Prix TTC');
        $sheet->setCellValue('I1', 'Prix HT');
        $sheet->setCellValue('J1', 'Date de connexion');
        $row = 2;
        foreach ($ConnexionsCa as $ParentSejour) {
            if (substr($ParentSejour->getIdSejour()->getCodeSejour(), 1, 1) == 'F') {
                $sheet->setCellValue('A' . $row, $ParentSejour->getIdSejour()->getCodeSejour());
                $sheet->setCellValue('B' . $row, $ParentSejour->getIdSejour()->getDateDebutSejour());
                $sheet->setCellValue('C' . $row, $ParentSejour->getIdSejour()->getDateFinSejour());
                $sheet->setCellValue('D' . $row, $ParentSejour->getIdSejour()->getDateCreationCode());
                $sheet->setCellValue('E' . $row, $ParentSejour->getIdSejour()->getDateFinCode());
                $sheet->setCellValue('F' . $row, $ParentSejour->getIdSejour()->getIdEtablisment()->getNomEtab());
                $sheet->setCellValue('G' . $row, $ParentSejour->getIdParent()->getNom() . " " . $ParentSejour->getIdParent()->getPrenom());
                $sheet->setCellValue('H' . $row, number_format((($ParentSejour->getIdSejour()->getPrixcnxpartenaire()) + ($ParentSejour->getIdSejour()->getPrixcnxpartenaire() * 20 / 100)) * $ParentSejour->getFlagPrix(), 2, '.', ''));
                $sheet->setCellValue('I' . $row, $ParentSejour->getIdSejour()->getPrixcnxpartenaire() * $ParentSejour->getFlagPrix());
                $sheet->setCellValue('J' . $row, $ParentSejour->getDateCreation());
                $row = $row + 1;
            }
        }
        $spreadsheet->createSheet();
        // Add some data to the second sheet, resembling some different data types
        $spreadsheet->setActiveSheetIndex(1);
        $spreadsheet->getActiveSheet()->setTitle("Connexions payantes");
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageMargins()->setTop(1);
        $sheet->getPageMargins()->setRight(0.75);
        $sheet->getPageMargins()->setLeft(0.75);
        $sheet->getPageMargins()->setBottom(1);
        $sheet->setCellValue('A1', 'Sejour');
        $sheet->setCellValue('B1', 'Date Debut Séjour');
        $sheet->setCellValue('C1', 'Date Fin Séjour');
        $sheet->setCellValue('D1', 'Date Code Séjour');
        $sheet->setCellValue('E1', 'Date Fin Code Séjour');
        $sheet->setCellValue('F1', 'Parteniare');
        $sheet->setCellValue('G1', 'Client');
        $sheet->setCellValue('H1', 'Date de connexion');
        $sheet->setCellValue('I1', 'Prix TTC');
        $sheet->setCellValue('J1', 'Prix HT');
        $sheet->setCellValue('K1', 'taux du Reversement');
        $sheet->setCellValue('L1', 'Reversement');
        $row = 2;
        foreach ($ConnexionsCa as $ParentSejour) {
            if ($ParentSejour->getPayment() == 1) {
                $sheet->setCellValue('A' . $row, $ParentSejour->getIdSejour()->getCodeSejour());
                $sheet->setCellValue('B' . $row, $ParentSejour->getIdSejour()->getDateDebutSejour());
                $sheet->setCellValue('C' . $row, $ParentSejour->getIdSejour()->getDateFinSejour());
                $sheet->setCellValue('D' . $row, $ParentSejour->getIdSejour()->getDateCreationCode());
                $sheet->setCellValue('E' . $row, $ParentSejour->getIdSejour()->getDateFinCode());
                $sheet->setCellValue('F' . $row, $ParentSejour->getIdSejour()->getIdEtablisment()->getNomEtab());
                $sheet->setCellValue('G' . $row, $ParentSejour->getIdParent()->getNom() . " " . $ParentSejour->getIdParent()->getPrenom());
                $sheet->setCellValue('H' . $row, $ParentSejour->getDateCreation());
                $sheet->setCellValue('I' . $row, $ParentSejour->getIdSejour()->getPrixcnxparent());
                $sheet->setCellValue('J' . $row,  number_format(($ParentSejour->getIdSejour()->getPrixcnxparent() * 100 / 120), 2, '.', ''));
                $sheet->setCellValue('K' . $row, $ParentSejour->getIdSejour()->getReversecnxpart());
                $sheet->setCellValue('L' . $row, number_format(((($ParentSejour->getIdSejour()->getPrixcnxparent() * 100 / 120) * $ParentSejour->getIdSejour()->getReversecnxpart()) / 100) * -1, 2, '.', ''));
                $row = $row + 1;
            }
        }
        $spreadsheet->createSheet();
        // Add some data to the second sheet, resembling some different data types
        $spreadsheet->setActiveSheetIndex(2);
        $spreadsheet->getActiveSheet()->setTitle("Liste des Commandes Produit");
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageMargins()->setTop(1);
        $sheet->getPageMargins()->setRight(0.75);
        $sheet->getPageMargins()->setLeft(0.75);
        $sheet->getPageMargins()->setBottom(1);
        $sheet->setCellValue('A1', 'Client ID');
        $sheet->setCellValue('B1', 'Client');
        $sheet->setCellValue('C1', 'Num Facture');
        $sheet->setCellValue('D1', 'Date de facture');
        $sheet->setCellValue('E1', 'Num Commande');
        $sheet->setCellValue('F1', 'Date de commande');
        $sheet->setCellValue('G1', 'Produits');
        $sheet->setCellValue('H1', 'Quantité');
        $sheet->setCellValue('I1', 'Montant TTC');
        $sheet->setCellValue('J1', 'Frais d\'expédition');
        $sheet->setCellValue('K1', 'Montant HT');
        $sheet->setCellValue('L1', 'Sejour');
        $sheet->setCellValue('M1', 'Partenaire');
        $sheet->setCellValue('N1', 'taux de Reversement');
        $sheet->setCellValue('O1', 'Reversement');
        $row = 2;
        foreach ($ListDesCommande as $cmd) {
            $sheet->setCellValue('A' . $row, $cmd->getIdUser()->getId());
            $sheet->setCellValue('B' . $row, $cmd->getIdUser()->getNom() . " " . $cmd->getIdUser()->getPrenom());
            $sheet->setCellValue('C' . $row,  $cmd->getNumfacture());
            $sheet->setCellValue('D' . $row,  $cmd->getDateCreateCommande());
            $sheet->setCellValue('E' . $row,  $cmd->getNumComande());
            $sheet->setCellValue('F' . $row,  $cmd->getDateCreateCommande());
            $prdt = "";
            $qte = 0;
            foreach ($cmd->getCommandesProduits() as $produit) {
                if ($produit->getQuantiter() > 0) {
                    $qte = $qte + $produit->getQuantiter();
                    if ($prdt == '') {
                        $prdt =  $produit->getIdProduit()->getType()->getLabeletype();
                    } else {
                        $prdt = $prdt . ', ' . $produit->getIdProduit()->getType()->getLabeletype();
                    }
                }
            }
            $sheet->setCellValue('G' . $row, $prdt);
            $sheet->setCellValue('H' . $row, $qte);
            $sheet->setCellValue('I' . $row, $cmd->getMontantrth() - $cmd->getMontanenv());
            $sheet->setCellValue('J' . $row, $cmd->getMontanenv());
            $montantht = number_format(($cmd->getMontantrth()) * 100 / (120), 2, '.', '');
            $sheet->setCellValue('K' . $row,  $montantht);
            $sheet->setCellValue('L' . $row,  $cmd->getIdSejour()->getCodeSejour());
            $sheet->setCellValue('M' . $row, $cmd->getIdSejour()->getIdEtablisment()->getNomEtab());
            $sheet->setCellValue('N' . $row, $cmd->getIdSejour()->getReverseventepart());
            $sheet->setCellValue('O' . $row, number_format((((((($cmd->getMontantrth() - $cmd->getMontanenv()) * 100) / 120) * $cmd->getIdSejour()->getReverseventepart()) / 100) * -1), 2, '.', ''));
            $row = $row + 1;
        }
        $writer = new Xlsx($spreadsheet);
        // Create a Temporary file in the system
        $fileName = 'CA-' . $datedebut->format('d-m-Y') . '-' . $datefin->format('d-m-Y') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
