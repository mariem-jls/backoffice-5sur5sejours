<?php

namespace App\Controller;

use App\Entity\Ref;
use App\Entity\Page;
use App\Entity\User;
use Twig\Environment;
use App\Entity\Panier;
use App\Entity\Sejour;
use App\Entity\Produit;
use App\Entity\Commande;
use App\Entity\Etablisment;
use App\Entity\Typeproduit;
use App\Entity\ParentSejour;
use App\Service\Statistique;
use App\Service\UserService;
use App\Entity\ComandeProduit;
use App\Service\SejourService;
use App\Service\ComandeService;
use App\Service\StatistiqueService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\TypeProduitConditionnement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;
use App\Service\ResendEmailService;


/**
 * @Route("/Support")
 */
class SupportController extends AbstractController
{

    private $em;
    private $templating;
    private $params;
    private $sejourService;
    private $statistique;
    private $userService;
    private $comandeService;
    private $session;
    private $messageBus;
    private $mailer;
    private $logger;
    private ResendEmailService $resendEmailService;

    public function __construct(
        ManagerRegistry $em,
        ParameterBagInterface $params,
        SejourService $sejourService,
        StatistiqueService $statistique,
        UserService $userService,
        ComandeService $comandeService,
        Environment $templating,
        MessageBusInterface $messageBus,
        MailerInterface $mailer,
        LoggerInterface $logger,
        ResendEmailService $resendEmailService
    ) {
        $this->em = $em;
        $this->templating = $templating;
        $this->sejourService = $sejourService;
        $this->userService = $userService;
        $this->comandeService = $comandeService;
        $this->params = $params;
        $this->messageBus = $messageBus;
        $this->statistique = $statistique;
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->resendEmailService = $resendEmailService;
    }

    



    /**
     * @Route("/", name="Support")
     */
    public function supportHome()
    {
        $dates = $this->calculateDateRanges();

        $datedebutPrev = \DateTime::createFromFormat('Y-m-d', $dates['datedebutPrev'])->setTime(0, 0);
        $datefinPrev = \DateTime::createFromFormat('Y-m-d', $dates['datefinPrev'])->setTime(0, 0);
        $datedebutNow = \DateTime::createFromFormat('Y-m-d', $dates['datedebutNow'])->setTime(0, 0);
        $datefinNow = \DateTime::createFromFormat('Y-m-d', $dates['datefinNow'])->setTime(0, 0);

        // $ComandeFindPrev = $this->em->getRepository(ComandeProduit::class)->ProduitCommandeAvecCmdPayExipBetween($datedebutPrev, $datefinPrev);
        // $ComandeFindNow = $this->em->getRepository(ComandeProduit::class)->ProduitCommandeAvecCmdPayExipBetween($datedebutNow, $datefinNow);

        $alletablissement = $this->em->getRepository(Etablisment::class)->findAll();
        $listeSejour = $this->em->getRepository(Sejour::class)->findSejourListForSearch();
        $listeCommande = $this->em->getRepository(Commande::class)->findCommandeListForSearchEspaceComptable();
        $listeTypeProduit = $this->em->getRepository(TypeProduitConditionnement::class)->findAll();
        $tab = $this->em->getRepository(Commande::class)->findAppelFacture();

        $array = $this->formatAppelFacture($tab);
        $dateNow = date('Y/m');
        $currentDate = date('d/m/Y');
        $dateToday = \DateTime::createFromFormat('d/m/Y', $currentDate)->setTime(0, 0);
        // $session = $this->get('session');
        // $session->set('page', 'listeproduit');
        $listeCMDAbondonnees = $this->em->getRepository(Commande::class)->listeCommandesNonPayeeBetween($datedebutPrev, $datedebutNow);

        $datedebut = \DateTime::createFromFormat('d/m/Y', date('01/m/Y'))->setTime(0, 0);
        $datefin = \DateTime::createFromFormat('d/m/Y', date('t/m/Y'))->setTime(0, 0);
        $ListDesCommande = $this->em->getRepository(Commande::class)->ListDesCommande($datedebut, $datefin);
        $ListDesCommandeToday = $this->em->getRepository(Commande::class)->ListDesCommandeToday($dateToday);

        $day = date('w');
        $week_start = \DateTime::createFromFormat('d/m/Y', date('d/m/Y', strtotime('-' . $day . ' days')));
        $week_end = \DateTime::createFromFormat('d/m/Y', date('d/m/Y', strtotime('+' . (6 - $day) . ' days')));
        $ListDesCommandeWeek = $this->em->getRepository(Commande::class)->ListDesCommande($week_start, $week_end);

        $commndeService = $this->statistique;
        // dd($ListDesCommandeWeek, $ListDesCommande);
        $totalMontanthtMonth = $this->statistique->getSommeProduit($ListDesCommande)['totalCaProduit'];
        $totalMontanthtWeek = $this->statistique->getSommeProduit($ListDesCommandeWeek)['totalCaProduit'];
        $listePanier = $this->em->getRepository(Panier::class)->findListePaniersBetween($datedebut, $datefin);
        $current_user = $this->getUser();
        //dd($current_user);
        return $this->render('commandes/index.html.twig', [
            'tabNumFacture' => $array,
            'current_user' => $current_user,
            'dateNow' => $dateNow,
            'listeTypeProduit' => $listeTypeProduit,
            'listeSejour' => $listeSejour,
            'listeCommande' => $ListDesCommande,
            'alletablissement' => $alletablissement,
            // 'ComandeFind' => $ComandeFindNow,
            'listeCMDAbondonnees' => $listeCMDAbondonnees,
            'ListDesCommandeToday' => $ListDesCommandeToday,
            'ListDesCommandesWeek' => $ListDesCommandeWeek,
            'totalMontanthtMonth' => $totalMontanthtMonth,
            'nbrDesCommande' => sizeof($ListDesCommande),
            'totalMontanthtWeek' => $totalMontanthtWeek,
            'nbrDesCmdWeek' => sizeof($ListDesCommandeWeek),
            'listePanier' => $listePanier
        ]);
    }

    private function calculateDateRanges()
    {
        $datePrev = date('Y-m', strtotime('first day of last month'));
        $date = date("Y-m");
        $fdayPrev = "01";
        $ldayPrev = date("t", strtotime($datePrev));
        $fday = "01";
        $lday = date("t", strtotime($date));

        return [
            'datedebutPrev' => "$datePrev-$fdayPrev",
            'datefinPrev' => "$datePrev-$ldayPrev",
            'datedebutNow' => "$date-$fday",
            'datefinNow' => "$date-$lday"
        ];
    }

    private function formatAppelFacture($tab)
    {
        $array = [];
        foreach ($tab as $appelFact) {
            $array[$appelFact['id']][$appelFact['periode']]['numAppelFacture'] = $appelFact['numfacture'];
        }
        return $array;
    }




    






    
}
