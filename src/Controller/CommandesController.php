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
 * @Route("/commandes")
 */
class CommandesController extends AbstractController
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
     * @Route("/changerStatutCommande", name="changerStatutCommande")
     */
    public function changerStatutCommande(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        // $idCmd = $data['idCmd'];
        // $mailClient = $data['mailClient'];
        // $numClient =    $data['numClient'];
        // $statut = $data['statut'];
        $statut = $request->request->get('statut');
        $idCmd = $request->request->get('idCmd');
        $mailClient = $request->request->get('mailClient');
        $numClient = $request->request->get('numClient');
        $logs = [];

        // $em = $this->container->get('doctrine')->getManager();
        $commande =   $this->em->getRepository(Commande::class)->find($idCmd);
        // return new JsonResponse(['id' => $idCmd]);
        if (!$commande) {
            return new JsonResponse(['message' => 'Commande not found', 'logs' => $logs], 404);
        }
        if ($statut == "payé") {
            $commande->setDupligetStatut('vérifié');
        } elseif ($statut == "vérifié") {
            $commande->setDupligetStatut('imprimé');
        } elseif ($statut == "imprimé") {
            $commande->setDupligetStatut('expédié');
        } else {
            $commande->setDupligetStatut('payé');
        }


        $this->em->getManager()->persist($commande);
        $this->em->getManager()->flush();

        //Envoyer mail à l'utilisateur 
        if ($statut == "imprimé") {
            $logs = array_merge($logs, $this->sendEmail($commande, 'Votre commande a été imprimée', 'Votre commande est maintenant imprimée et prête à être expédiée.'));
        }

        if ($statut == "expédié") {
            $logs = array_merge($logs, $this->sendEmail($commande, 'Votre commande a été expédiée', 'Votre commande a été expédiée et est en route.'));
        }

        return new JsonResponse(['id' => $idCmd, 'logs' => $logs]);
        // return $this->redirectToRoute('commandes');
    }

    //     private function sendEmail(MailerInterface $mailer,  $subject, $body)
    // {
    //     $email = (new Email())
    //         ->from('onboarding@resend.dev')
    //         ->to('mohamedyaakoubiweb@gmail.com')
    //         ->subject($subject)
    //         ->text(strip_tags($body)) // For plain text version
    //         ->html('<p>' . nl2br($body) . '</p>'); // Convert newlines to <br> in HTML

    //     $mailer->send($email);

    //     return "Email sent successfully to mohamedyaakoubiweb@gmail.com with subject {$subject}";
    // }

    /**
     * @Route("/panier_rappel", name="panier_rappel")
     */
    public function panier_rappel(Request $request)
    {
        $idPanier = $request->request->get('id');
        
        $panier = $this->em->getRepository(Panier::class)->find($idPanier);

        $from = 'onboarding@resend.dev';
        $to = "mohamedyaakoubiweb@gmail.com";
        $subject = 'confirmation du panier';
        $html = "<p>veuillez confirmer votre panier ou le supprimer si vous n'êtes plus intéressé</p>";
        
        try {
            $this->resendEmailService->sendEmail($to, $subject, $html, ['verify' => false]);
            $responseContent = ['status' => 'Email sent successfully'];
        } catch (\Exception $e) {
            $this->logger->error('Failed to send email: ' . $e->getMessage());
            $responseContent = ['error' => 'Failed to send email: ' . $e->getMessage()];
        }

        $responseJson = json_encode($responseContent);
        // return new Response($responseJson, Response::HTTP_OK, ['Content-Type' => 'application/json']);
        return $this->redirectToRoute('commandes');
    }

    private function sendEmail($commande, $subject, $body)
    {
        $from = 'onboarding@resend.dev';
        $to = 'mohamedyaakoubiweb@gmail.com';
        $subject = 'Hello World';
        $html = '<p>Congrats on sending your <strong>first email</strong>!</p>';
        try {
            $this->resendEmailService->sendEmail($to, $subject, $html);
            // Return a status message or array to be merged with $logs
            return ['status' => 'Email sent successfully'];
        } catch (\Exception $e) {
            $this->logger->error('Failed to send email: ' . $e->getMessage());
            // Return an error message or array to be merged with $logs
            return ['error' => 'Failed to send email'. $e->getMessage()];
        }

        // $email = (new Email())
        //     ->from('yaakoubiprojects@gmail.com')
        //     ->to('mohamedyaakoubiweb@gmail.com') // Assuming $commande has a method to get the user's email
        //     ->subject($subject)
        //     ->text($body)
        //     ->html('<p>' . $body . '</p>');

        //     $logs = ['Attempting to send email'];
        // // Log the email message
        // $logMessage = [
        //     'to' => 'mohamedyaakoubiweb@gmail.com',
        //     'subject' => $subject,
        //     'body' => $body
        // ];

        // $this->logger->debug('Sending email', $logMessage);

        // $logs = [];

        // try {
        //     $this->mailer->send($email);
        //     $logs[] = 'Email sent successfully';
        //     $this->logger->debug('Email sent successfully');
        // } catch (\Exception $e) {
        //     $logs[] = 'Failed to send email: ' . $e->getMessage();
        //     $this->logger->error('Failed to send email', ['exception' => $e->getMessage()]);
        // }

        // return $logs;
    }

    /**
     * @Route("/{id}/confirmerCommande", name="commandes_confirmer")
     */
    public function confirmerCommande(Request $request)
    {
        $idCmd = $request->get("idCmd");
        $em = $this->container->get('doctrine')->getManager();
        $panier = $this->em->getRepository(Panier::class)->findOneBy(array("id" => $idCmd));

        $commande = new Commande();
        $commande->setIdPanier($panier);
        $commande->setDateCreateCommande(new \DateTime());
    }



    /**
     * @Route("Commandes_Produits_du_mois", name="Commandes_Produits_du_mois")
     */
    public function Commandes_Produits_du_mois()
    {
        $datePrev = date('Y-m', strtotime('first day of last month'));
        $monthPrev = date('m', strtotime('first day of last month'));
        $yearPrev = date('Y', strtotime('first day of last month'));
        if ($monthPrev == "01" || $monthPrev == "03" || $monthPrev == "05" || $monthPrev == "07" || $monthPrev == "08" || $monthPrev == "10" || $monthPrev == "12") {
            $fdayPrev = "01";
            $ldayPrev = "31";
        }
        if ($monthPrev == "04" || $monthPrev == "06" || $monthPrev == "09" || $monthPrev == "11") {
            $fdayPrev = "01";
            $ldayPrev = "30";
        }
        if ($monthPrev == "02") {
            $intyear = intval($yearPrev);
            $fdayPrev = "01";
            if ($intyear % 4 === 0) {
                $ldayPrev = "29";
            } else {
                $ldayPrev = "28";
            }
        }
        $date = date("Y-m");
        $year = date("Y");
        $month = date("m");
        if ($month == "01" || $month == "03" || $month == "05" || $month == "07" || $month == "08" || $month == "10" || $month == "12") {
            $fday = "01";
            $lday = "31";
        }
        if ($month == "04" || $month == "06" || $month == "09" || $month == "11") {
            $fday = "01";
            $lday = "30";
        }
        if ($month == "02") {
            $intyear = intval($year);
            $fday = "01";
            if ($intyear % 4 === 0) {
                $lday = "29";
            } else {
                $lday = "28";
            }
        }
        $datedebutPrev = $datePrev . "-" . $fdayPrev;
        $datefinPrev = $datePrev . "-" . $ldayPrev;
        $datedebutNow = $date . "-" . $fday;
        $datefinNow = $date . "-" . $lday;
        // $ComandeFind = $this->em->getRepository(ComandeProduit::class)->ProduitCommandeAvecCmdPayExip();
        $datedebutPrev = \DateTime::createFromFormat('Y-m-d', $datedebutPrev)->setTime(0, 0);
        $datefinPrev = \DateTime::createFromFormat('Y-m-d', $datefinPrev)->setTime(0, 0);
        $ComandeFindPrev = $this->em->getRepository(ComandeProduit::class)->ProduitCommandeAvecCmdPayExipBetween($datedebutPrev, $datefinPrev);
        $datedebutNow = \DateTime::createFromFormat('Y-m-d', $datedebutNow)->setTime(0, 0);
        $datefinNow = \DateTime::createFromFormat('Y-m-d', $datefinNow)->setTime(0, 0);
        $ComandeFindNow = $this->em->getRepository(ComandeProduit::class)->ProduitCommandeAvecCmdPayExipBetween($datedebutNow, $datefinNow);
        $ComandeFind = array_merge($ComandeFindPrev, $ComandeFindNow);
        $alletablissement = $this->em->getRepository(Etablisment::class)->findAll();
        $listeSejour = $this->em->getRepository(Sejour::class)->findSejourListForSearch();
        $listeCommande = $this->em->getRepository(Commande::class)->findCommandeListForSearchEspaceComptable();
        //        dd($listeCommande);
        $listeTypeProduit = $this->em->getRepository(TypeProduitConditionnement::class)->findAll();
        $tab = $this->em->getRepository(Commande::class)->findAppelFacture();
        array_unique($tab, SORT_REGULAR);
        $array = [];
        foreach ($tab as $appelFact) {
            $array[$appelFact['id']][$appelFact['periode']]['numAppelFacture'] = $appelFact['numfacture'];
        }
        $dateNow = date('Y-m');
        return $this->render('Admin/ListeCommandeProduits.html.twig', ['tabNumFacture' => $array, 'dateNow' => $dateNow, 'listeTypeProduit' => $listeTypeProduit, 'listeSejour' => $listeSejour, 'listeCommande' => $listeCommande, 'alletablissement' => $alletablissement, 'ComandeFind' =>   $ComandeFind]);
        //        dd($TablisteProduits);
    }

    /**
     * @Route("/", name="commandes", name="commandes")
     */
    public function Commandes_du_mois()
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
        return $this->render('commandes/index.html.twig', [
            'tabNumFacture' => $array,
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




    /**
     * @Route("SuiviCommandes", name="SuiviCommandesPayee")
     */
    public function SuiviCommandes()
    {
        $ListeCmd = $this->em->getRepository(Commande::class)->findBy(['statut' => 33], ['id' => 'ASC']);
        //        foreach($ListeCmd as $cmd){
        //
        //        dd($cmd->getIdUser()->getEmail());
        //        }
        return $this->render('Admin/ListedesCommandes.html.twig', ['ListeCmd' => $ListeCmd]);
    }






    /**
     * @Route("/{id}", name="commandes_delete", methods={"POST"})
     */
    public function delete(Request $request, Panier $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $commande->getId(), $request->request->get('_token'))) {
            try {
                $entityManager->remove($commande);
                $entityManager->flush();
                $this->addFlash('success', 'Commande deleted successfully');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error while deleting commande: ' . $e->getMessage());
            }
        } else {
            $this->addFlash('error', 'Invalid CSRF token');
        }

        return $this->redirectToRoute('commandes');
    }


    /**
     * @Route("/confirm/{id}", name="panier_confirm")
     */
    public function confirm($id, EntityManagerInterface $em): Response
    {
        // Fetch the Panier entity
        $panier = $em->getRepository(Panier::class)->find($id);

        if (!$panier) {
            throw $this->createNotFoundException('No panier found for id ' . $id);
        }

        // Create a new Commande entity
        $commande = new Commande();

        // Copy relevant data from Panier to Commande
        $commande->setNumComande($panier->getNumPanier());
        $commande->setMontantht($panier->getPrixTotal());
        $commande->setIdUser($panier->getCreerPar());
        $commande->setIdSejour($panier->getIdSejour());
        $commande->setStatut($panier->getStatut());
        $commande->setDateCreateCommande(new \DateTime());

        // Copy PanierProduits to CommandeProduits
        foreach ($panier->getPanierProduits() as $panierProduit) {
            $commandeProduit = new CommandeProduit();
            $commandeProduit->setIdComande($commande);
            $commandeProduit->setIdProduit($panierProduit->getIdProduit());
            $commandeProduit->setQuantiter($panierProduit->getQuantiter());
            $commandeProduit->setReversement($panierProduit->getReversement());
            $commandeProduit->setDate($panierProduit->getDate());
            $commandeProduit->setPourcentage($panierProduit->getPourcentage());

            $commande->addCommandesProduit($commandeProduit);
        }

        // Persist the Commande entity
        $em->persist($commande);

        // Remove the Panier entity
        $em->remove($panier);

        // Flush the changes to the database
        $em->flush();

        return new Response('Panier confirmed and converted to Commande with ID ' . $commande->getId());
    }
}
