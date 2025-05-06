<?php

namespace App\Controller;

use ZipArchive;
use App\Entity\User;
use App\Entity\Sejour;
use App\Entity\Commande;
use App\Entity\Etablisment;
use App\Entity\ParentSejour;
use App\Entity\ComandeProduit;
use App\Entity\CommandeComptable;
use Doctrine\Persistence\ManagerRegistry;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Entity\TypeProduitConditionnement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class FacturesController extends AbstractController
{

    private $em;
    private $params;

    public function __construct(
        ManagerRegistry $em,
        ParameterBagInterface $params,
    ) {
        $this->em = $em;
        $this->params = $params;
    }
    /**
     * @Route("/factures", name="factures_index")
     */
    public function index(): Response
    {
        
        //        $ListeCmd = $this->em->getRepository(ComandeProduit::class)->ProduitCommandeAvecCmdPayExip();
        $cmds = $this->em->getRepository(Commande::class)->CmdPayExip();
        
        //        $ComandeFind = $this->em->getRepository(Commande::class)->rechercherCmdFcatureCnx();
        //        $date = date("2020-09");
        //        $year=date("2020");
        //        $month = date("09");
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
        $datedebut = $date . "-" . $fday;
        $datefin = $date . "-" . $lday;
        $idPartenaire = "";
        $idSejour = "";
        $idCmdF = "";
        $idCmd = "";
        $produit = "";
        if ($datedebut != "" && $datefin != "") {
            $datedebut = \DateTime::createFromFormat('Y-m-d', $datedebut)->setTime(0, 0);
            $datefin = \DateTime::createFromFormat('Y-m-d', $datefin)->setTime(0, 0);
        }
        
        if ($datedebutPrev != "" && $datefinPrev != "") {
            $datedebutPrev = \DateTime::createFromFormat('Y-m-d', $datedebutPrev)->setTime(0, 0);
            $datefinPrev = \DateTime::createFromFormat('Y-m-d', $datefinPrev)->setTime(0, 0);
        }

        // //ListeDesFiltes
        $liteCnxParPeriode = $this->em->getRepository(ParentSejour::class)->RechercheAvancerliteCnxParPeriode($datedebut, $datefin, $idSejour, $idPartenaire);
        
        if ($liteCnxParPeriode) {
            $ComandeFind = $this->em->getRepository(Commande::class)->listeCommandeCnx($liteCnxParPeriode);
        } else {
            $ComandeFind = "";
        }
        $allCmd = $this->em->getRepository(ComandeProduit::class)->rechercherAvanceCmdProduitEspaceComptable($datedebut, $datefin, $idCmdF, $idCmd, $idSejour, $idPartenaire, $produit);
        
        //        $ListeCmdPrev = $this->em->getRepository(ComandeProduit::class)->rechercherAvanceCmdProduitEspaceComptable($datedebutPrev,$datefinPrev,$idCmdF,$idCmd,$idSejour,$idPartenaire,$produit);
        //$allCmd=array_merge($ListeCmd,$ListeCmdPrev);
        //        $numCmd= $this->em->getRepository(Commande::class);
        //        $ListeCmd =[];
        //        $cmds=[];
        //        $ComandeFind = [];
        // dd($allCmd);
        $publicDirectory = $this->params->get('kernel.project_dir') . '/public/Facture/';
        $arrayFiles = [];
        $tabTestFile = [];
        foreach ($cmds as $cmd) {
            //            var_dump($cmd->getId());
            // e.g /var/www/project/public/mypdf.pdf
            $filename = $publicDirectory . "Facture" . $cmd->getId() . '-' . $cmd->getNumComande() . ".pdf";
            if (file_exists($filename)) {
                $tabTestFile[$cmd->getId()]["fileExiste"] = "oui";
            } else {
                $tabTestFile[$cmd->getId()]["fileExiste"] = "non";
            }
        }
        array_push($arrayFiles, $tabTestFile);

        //        $alletablissement=$this->em->getRepository(Etablisment::class)->findAll();
        $alletablissement = $this->em->getRepository(Etablisment::class)->listeEtablisementsOntSejour();
        $listeSejour = $this->em->getRepository(Sejour::class)->findSejourListForSearch();
        $listeCommande = $this->em->getRepository(Commande::class)->findCommandeListForSearchEspaceComptable();
        // //        dd($listeCommande);
        $listeTypeProduit = $this->em->getRepository(TypeProduitConditionnement::class)->findAll();
        // dd($listeTypeProduit);
        return $this->render('factures/index.html.twig', ['listeTypeProduit' => $listeTypeProduit, 'listeSejour' => $listeSejour, 'listeCommande' => $listeCommande, 'alletablissement' => $alletablissement, 'arrayFiles' => $arrayFiles, 'ListeCmd' => $allCmd, 'ComandeFind' => $ComandeFind]);
        
    }

    /**
     * @Route("/factures/{id}", name="factures_show")
     */
    public function show($id): Response
    {
        // Your code here

        return $this->render('factures/show.html.twig', [
            'controller_name' => 'FacturesController',
            'id' => $id,
        ]);
    }

    /**
     * @Route("/generatePdfFacturePartenaireParPeriode/{idPartenaire}/{periode}", name="generatePdfFacturePartenaireParPeriode")
     */
    public function generatePdfFacturePartenaireParPeriode($idPartenaire, $periode)
    {
        $part = $this->em->getRepository(User::class)->find($idPartenaire);
        $etab = $this->em->getRepository(Etablisment::class)->findOneBy(array('user' => $part));
        $ComandeFind = $this->em->getRepository(Commande::class)->findBy(array('statut' => 31, "idUser" => $part, 'periode' => $periode));
        $serviceuser = $this->etablissementService;
        $Nbconnxtionss = $serviceuser->getNombreconnxtionPartenaireV2($etab->getId());
        $datePeriode = explode("-", $periode);
        if ($datePeriode[0]) {
            $year = $datePeriode[0];
        }
        if ($datePeriode[1]) {
            $month = $datePeriode[1];
            if ($month == "01" || $month == "03" || $month == "05" || $month == "07" || $month == "08" || $month == "10" || $month == "12") {
                $fday = "01";
                $lday = "31";
            }
            if ($month == "04" || $month == "06" || $month == "09" || $month == "11") {
                $fday = "01";
                $lday = "30";
            }
            if ($month == "02") {
                $intyear = intval($datePeriode[0]);
                $fday = "01";
                if ($intyear % 4 === 0) {
                    $lday = "29";
                } else {
                    $lday = "28";
                }
            }
        }
        $dateDebutPeriode = $fday . '-' . $month . '-' . $year;
        $dateFinPeriode = $lday . '-' . $month . '-' . $year;
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('isRemoteEnabled', TRUE);
        $pdfOptions->set('defaultFont', 'Arial');
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $dompdf->set_option('isRemoteEnabled', TRUE);
        $nbRelleCnx = [];
        foreach ($ComandeFind as $cmd) {
            $parentsejourP = $this->em->getRepository(ParentSejour::class)->searchNbCmdPayParPeriode($cmd->getIdSejour(), $periode);
            $parentsejour = $this->em->getRepository(ParentSejour::class)->searchNbCmdTotalParPeriode($cmd->getIdSejour(), $periode);
            $nbRelleCnx[$cmd->getIdSejour()->getId()] = count($parentsejour);
            $nbCnxFactures[$cmd->getIdSejour()->getId()] = count($parentsejourP);
        }
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('Admin/Pdffacture_partenaire.html.twig', [
            "Commande" => $ComandeFind,
            "Nbconnxtionss" => $Nbconnxtionss,
            "nbRelleCnx" => $nbRelleCnx,
            'nbRelleCnxFactures' => $nbCnxFactures,
            'dateDebutPeriode' => $dateDebutPeriode,
            'dateFinPeriode' => $dateFinPeriode
        ]);
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');
        // Render the HTML as PDF
        $dompdf->render();
        $nompart = "";
        if ($etab->getNometab() != "") {
            $nompart = $etab->getNometab();
        }
        // Store PDF Binary Data
        $output = $dompdf->output();
        // In this case, we want to write the file in the public directory
        $publicDirectory = $this->params->get('kernel.project_dir') . '/public/Facture/';
        // e.g /var/www/project/public/mypdf.pdf
        $pdfFilepath = $publicDirectory . "Facture_Connexion_" . $nompart . $part->getId() . ".pdf";
        // Write file to the desired path
        file_put_contents($pdfFilepath, $output);
        // Output the generated PDF to Browser (force download)
        $dompdf->stream("Facture_Connexion_" . $nompart . $part->getId() . "_" . $periode . ".pdf", [
            "Attachment" => true
        ]);
    }

    /**
     * @Route("Admin/comptablexlsx", name="comptablexlsx")
     */
    function ComptableFacturexlsx(Request $request)
    {
        $datedebut = $_POST['datedebut'];
        $datefin = $_POST['datefin'];
        if ($datedebut != null && $datedebut != "" && $datefin != null && $datefin != "") {
            $datedebut = \DateTime::createFromFormat('Y-m-d', $datedebut)->setTime(0, 0);
            $datefin = \DateTime::createFromFormat('Y-m-d', $datefin)->setTime(0, 0);
            $ListeCmd = $this->em->getRepository(CommandeComptable::class)->CommandeComptableBetween($datedebut, $datefin);
        } else {
            $ListeCmd = $this->em->getRepository(CommandeComptable::class)->findAll();
        }
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
        $sheet->setCellValue('L1', 'Montant TVA');
        $sheet->setCellValue('M1', 'Moyen de paiement');
        $arrayCmd = [];
        $row = 2;
        foreach ($ListeCmd as $cmd) {
            $sheet->setCellValue('A' . $row, $cmd->getIdclient());
            $sheet->setCellValue('B' . $row, $cmd->getClient());
            $sheet->setCellValue('C' . $row, $cmd->getNumfacture());
            $sheet->setCellValue('D' . $row, $cmd->getDatefacture());
            $sheet->setCellValue('E' . $row, $cmd->getNumcmd());
            $sheet->setCellValue('F' . $row, $cmd->getDatecmd());
            $sheet->setCellValue('G' . $row, $cmd->getProduits());
            $sheet->setCellValue('H' . $row, $cmd->getQuantite());
            $sheet->setCellValue('I' . $row, round($cmd->getMontant_ttc(), 2));
            $sheet->setCellValue('J' . $row, $cmd->getFrais_expedition());
            $sheet->setCellValue('K' . $row, round($cmd->getMontant_ht(), 2));
            $sheet->setCellValue('L' . $row, $cmd->getTva());
            $sheet->setCellValue('M' . $row, $cmd->getMoyen_paiement());
            $row = $row + 1;
        }
        $writer = new Xlsx($spreadsheet);
        // Create a Temporary file in the system
        $fileName = 'Comptable.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @Route("/ZipfactureAdminVersion2", name="ZipfactureAdminVersion2")
     */
    public function ZipfactureAdminVersion2(Request $request)
    {
        $data = json_decode($request->get('factureParent'));
        $partenaires = json_decode($request->get('partenaire'));
        $array_ofpathe = array();
        $milliseconds = round(microtime(true) * 1000);
        $dateJour = date('dmy');
        $codeZip = $dateJour . '-' . $milliseconds;
        // dd($data);
        if ($data != "") {
            foreach ($data as $cmdId) {
                $cmd = $this->em->getRepository(Commande::class)->find($cmdId);
                $publicDirectory = $this->params->get('kernel.project_dir') . '/public/backupFacture/';
                $filePath = "Facture" . $cmd->getId() . '-' . $cmd->getNumComande() . ".pdf";
                $pdfFilepath = $publicDirectory . $filePath;
                array_push($array_ofpathe, ['filePath' => $pdfFilepath, 'fileName' => $filePath]);
            }
        }

        if ($partenaires != null) {
            foreach ($partenaires as $partenaire) {
                $part = $this->em->getRepository(User::class)->find(intval($partenaire->idPartenaire));
                $etab = $this->em->getRepository(Etablisment::class)->findOneBy(array('user' => $part));
                $nompart = $etab->getNometab() ? str_replace("/", "", str_replace('"', '', $etab->getNometab())) : "";
                $publicDirectory = $this->params->get('kernel.project_dir') . '/public/backupFacture/';
                $filePath = "Facture_Connexion_" . $nompart . $part->getId() . '_' . $partenaire->Periode . ".pdf";
                $pdfFilepath = $publicDirectory . $filePath;
                array_push($array_ofpathe, ['filePath' => $pdfFilepath, 'fileName' => $filePath]);
            }
        }

        $zip = new ZipArchive();
        $projectRoot = $this->params->get('kernel.project_dir');
        $zipFilePath = $projectRoot . '/public/Facture/ZipFactures' . $codeZip . '.zip';
        
        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            foreach ($array_ofpathe as $fileNames) {
                if (file_exists($fileNames["filePath"])) {
                    $zip->addFile($fileNames["filePath"], $fileNames['fileName']);
                }
            }
            $zip->close();
        } else {
            return new Response('Failed to create zip file.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response('/Facture/ZipFactures' . $codeZip . '.zip');
    }


     /**
     * @Route("FiltreEspaceComptable", name="FiltreEspaceComptable")
     */
    function FiltreEspaceComptable(Request $request)
    {
        $datedebut = $request->get("datedebut");
        $datefin = $request->get("datefin");
        $partenaire = $request->get("partenaire");
        $sejour = $request->get("sejour");
        $commande = $request->get("numCmd");
        $facture = $request->get("numFacture");
        $produit = $request->get("produit");
        if ($datedebut != "" && $datefin != "") {
            $datedebut = \DateTime::createFromFormat('Y-m-d', $datedebut)->setTime(0, 0);
            $datefin = \DateTime::createFromFormat('Y-m-d', $datefin)->setTime(0, 0);
        }
        //ListeDesFiltes
        $dataPart = explode('-', $partenaire);
        if ($partenaire != "") {
            $idPartenaire = $dataPart[0];
        } else {
            $idPartenaire = ""; 
        }
        $dataSejour = explode('-', $sejour);
        if ($sejour != "") {
            $idSejour = $dataSejour[0];
        } else {
            $idSejour = "";
        }
        $dataCmd = explode('-', $commande);
        if ($commande != "") {
            $idCmd = $dataCmd[0];
            $TypeCmd = $dataCmd[3];
        } else {
            $idCmd = "";
            $TypeCmd = "";
        }
        $dataCmdF = explode('-', $facture);
        if ($facture != "") {
            $idCmdF = $dataCmdF[0];
            $TypeCmdF = $dataCmdF[3];
        } else {
            $idCmdF = "";
            $TypeCmdF = "";
        }
        if (($TypeCmdF == "Facture" || $TypeCmd == "Facture" || $TypeCmdF == "" || $TypeCmd == "")) {
            $liteCnxParPeriode = $this->em->getRepository(ParentSejour::class)->RechercheAvancerliteCnxParPeriode($datedebut, $datefin, $idSejour, $idPartenaire);
            //   dd($liteCnxParPeriode);
            if ($idCmdF == "" && $idCmd == "") {
                if ($liteCnxParPeriode) {
                    $ComandeFind = $this->em->getRepository(Commande::class)->listeCommandeCnx($liteCnxParPeriode);
                } else {
                    $ComandeFind = "";
                }
            } else {
                $ComandeFind = $this->em->getRepository(Commande::class)->listeCommandeCnxAvancer($idCmdF, $idCmd);
            }
        } else {
            $ComandeFind = "";
        }
        if (($TypeCmdF == "payer" || $TypeCmd == "Expédié" || $TypeCmdF == "Expédié" || $TypeCmd == "payer" || $TypeCmdF == "" || $TypeCmd == "")) {
            //Rechercher commande produit
            $ListeCmd = $this->em->getRepository(ComandeProduit::class)->rechercherAvanceCmdProduitEspaceComptable($datedebut, $datefin, $idCmdF, $idCmd, $idSejour, $idPartenaire, $produit);
        } else {
            $ListeCmd = "";
        }
       
        $arrayCmd = [];
        if ($ListeCmd != "" || $ListeCmd != null) {
            foreach ($ListeCmd as $cmd) {
                //            if($cmd->getDatefacture() != null ){
                //                $Datefacture =  $cmd->getDatefacture()->format('d/m/Y');
                //            }
                //            else{
                //                $Datefacture = "";
                //            }
                if ($cmd->getIdComande()->getDateCreateCommande() != null) {
                    $Datecmd =  $cmd->getIdComande()->getDateCreateCommande()->format('d/m/Y');
                } else {
                    $Datecmd = "";
                }
                $nomProduit = $cmd->getIdProduit()->getType()->getLabeletype() . ' ' . $cmd->getIdProduit()->getIdConditionnement()->getSousTitre();
                $montantTTC = $cmd->getIdProduit()->getIdConditionnement()->getMontantTTC() * $cmd->getQuantiter();
                $montantHT = ($cmd->getIdProduit()->getIdConditionnement()->getMontantTTC() * 100 / 120) * $cmd->getQuantiter();
                $montantTVA = $montantTTC - $montantHT;
                $nomClient = $cmd->getIdComande()->getIdUser()->getNom() . ' ' . $cmd->getIdComande()->getIdUser()->getPrenom();
                $arrayCmd[] = [
                    'id' => $cmd->getId(),
                    'Idcmd' => $cmd->getIdComande()->getId(),
                    'Idclient' => $cmd->getIdComande()->getIdUser()->getId(),
                    'Client' => $nomClient,
                    'idPartenaire' => "",
                    'Numfacture' => $cmd->getIdComande()->getNumfacture(),
                    'Datefacture' => "",
                    'Datecmd' => $Datecmd,
                    'Numcmd' => $cmd->getIdComande()->getNumComande(),
                    'Quantite' => $cmd->getQuantiter(),
                    'Produits' => $nomProduit,
                    'Montant_ttc' => $montantTTC,
                    'Frais_expedition' => $cmd->getIdComande()->getMontanenv(),
                    'Montant_ht' => $montantHT,
                    'Tva' => $montantTVA,
                    'periode' => $cmd->getIdComande()->getPeriode(),
                    'Moyen_paiement' => $cmd->getIdComande()->getPaymentType()
                ];
            }
        }
        // if ($ComandeFind != "" && $ComandeFind != null) {
        //     foreach ($ComandeFind as $cmd) {
        //         //            var_dump($cmd->getNbconnx());
        //         for ($i = 0; $i < $cmd->getNbconnx(); $i++) {
        //             if ($cmd->getDateCreateCommande() != null) {
        //                 $Datecmd = $cmd->getDateCreateCommande()->format('d/m/Y');
        //             } else {
        //                 $Datecmd = "";
        //             }
        //             if ($cmd->getDateFacture() != null) {
        //                 $Datecmd = $cmd->getDateFacture()->format('d/m/Y');
        //             } else {
        //                 $Datecmd = "";
        //             }
        //             //            $montantTTC=$cmd-> getIdProduit()->getIdConditionnement()->getMontantTTC() *$cmd->getQuantiter();
        //             //            $montantHT=($cmd-> getIdProduit()->getIdConditionnement()->getMontantTTC()*100/120) *$cmd->getQuantiter();
        //             //            $montantTVA=$montantTTC-$montantHT;
        //             $MontantTTC = $cmd->getIdSejour()->getPrixcnxpartenaire() + ($cmd->getIdSejour()->getPrixcnxpartenaire() * 0.2);
        //             $MontantHT = $cmd->getIdSejour()->getPrixcnxpartenaire();
        //             $montantTva = $MontantTTC - $MontantHT;
        //             $nompart = "";
        //             if ($cmd->getIdSejour()->getIdEtablisment()->getNometab()) {
        //                 $nompart1 = str_replace('"', '', $cmd->getIdSejour()->getIdEtablisment()->getNometab());
        //                 $nompart = str_replace("/", "", $nompart1);
        //             }
        //             $arrayCmd[] = [
        //                 'id' => $cmd->getId(),
        //                 'Idcmd' => $cmd->getId(),
        //                 'Idclient' => $cmd->getIdSejour()->getIdEtablisment()->getId(),
        //                 'Client' => $cmd->getIdSejour()->getIdEtablisment()->getNometab(),
        //                 'Numfacture' => $cmd->getNumfacture(),
        //                 'idPartenaire' => $cmd->getIdSejour()->getIdPartenaire()->getId(),
        //                 'Datefacture' => "",
        //                 'Datecmd' => $Datecmd,
        //                 'Numcmd' => $cmd->getNumComande(),
        //                 'Quantite' => 1,
        //                 'Produits' => "Connexion",
        //                 'Montant_ttc' => $MontantTTC,
        //                 'Frais_expedition' => "",
        //                 'Montant_ht' => $MontantHT,
        //                 'Tva' => $montantTva,
        //                 'nompart' => $nompart,
        //                 'periode' => $cmd->getPeriode(),
        //                 'Moyen_paiement' => $cmd->getPaymentType(),
        //             ];
        //         }
        //     }
        // }
            //    dd($arrayCmd);
        // json_encode($arrayCmd);
        return new JsonResponse($arrayCmd);
        // return new JsonResponse([
        //     'arrayCmd' => $arrayCmd,
        //     'ListeCmd' => $ListeCmd,
        //     'liteCnxParPeriode' => $liteCnxParPeriode,
        //     'ComandeFind' => $ComandeFind,
        // ]);
        
    }

    /**
     * @Route("/pdffactureAdmin/{id}", name="pdffactureAdmin")
     */
    public function pdffactureAccomp($id)
    {
        $cmd = $this->em->getRepository(Commande::class)->find($id);
        //dd($cmdProd);
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('isRemoteEnabled', TRUE);
        $pdfOptions->set('defaultFont', 'Arial');
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $dompdf->set_option('isRemoteEnabled', TRUE);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('Admin/pdfFactureparent.html.twig', [
            "Commande" => $cmd,
        ]);
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');
        // Render the HTML as PDF
        $dompdf->render();
        // Store PDF Binary Data
        $output = $dompdf->output();
        // In this case, we want to write the file in the public directory
        $publicDirectory = $this->params->get('kernel.project_dir') . '/public/Facture/';
        // e.g /var/www/project/public/mypdf.pdf
        $pdfFilepath = $publicDirectory . "Facture" . $cmd->getId() . '-' . $cmd->getNumComande() . ".pdf";
        // Write file to the desired path
        file_put_contents($pdfFilepath, $output);
        // Output the generated PDF to Browser (force download)
        $dompdf->stream("Facture" . $cmd->getId() . '-' . $cmd->getNumComande() . ".pdf", [
            "Attachment" => true
        ]);
    }


    // public function AdminComptable()
    // {
    //     $session = $this->get('session');
    //     $session->set('page', 'AdminComptable');
    //     //        $ListeCmd = $this->em->getRepository(ComandeProduit::class)->ProduitCommandeAvecCmdPayExip();
    //     $cmds = $this->em->getRepository(Commande::class)->CmdPayExip();
    //     //        $ComandeFind = $this->em->getRepository(Commande::class)->rechercherCmdFcatureCnx();
    //     //        $date = date("2020-09");
    //     //        $year=date("2020");
    //     //        $month = date("09");
    //     $datePrev = date('Y-m', strtotime('first day of last month'));
    //     $monthPrev = date('m', strtotime('first day of last month'));
    //     $yearPrev = date('Y', strtotime('first day of last month'));
    //     if ($monthPrev == "01" || $monthPrev == "03" || $monthPrev == "05" || $monthPrev == "07" || $monthPrev == "08" || $monthPrev == "10" || $monthPrev == "12") {
    //         $fdayPrev = "01";
    //         $ldayPrev = "31";
    //     }
    //     if ($monthPrev == "04" || $monthPrev == "06" || $monthPrev == "09" || $monthPrev == "11") {
    //         $fdayPrev = "01";
    //         $ldayPrev = "30";
    //     }
    //     if ($monthPrev == "02") {
    //         $intyear = intval($yearPrev);
    //         $fdayPrev = "01";
    //         if ($intyear % 4 === 0) {
    //             $ldayPrev = "29";
    //         } else {
    //             $ldayPrev = "28";
    //         }
    //     }
    //     $date = date("Y-m");
    //     $year = date("Y");
    //     $month = date("m");
    //     if ($month == "01" || $month == "03" || $month == "05" || $month == "07" || $month == "08" || $month == "10" || $month == "12") {
    //         $fday = "01";
    //         $lday = "31";
    //     }
    //     if ($month == "04" || $month == "06" || $month == "09" || $month == "11") {
    //         $fday = "01";
    //         $lday = "30";
    //     }
    //     if ($month == "02") {
    //         $intyear = intval($year);
    //         $fday = "01";
    //         if ($intyear % 4 === 0) {
    //             $lday = "29";
    //         } else {
    //             $lday = "28";
    //         }
    //     }
    //     $datedebutPrev = $datePrev . "-" . $fdayPrev;
    //     $datefinPrev = $datePrev . "-" . $ldayPrev;
    //     $datedebut = $date . "-" . $fday;
    //     $datefin = $date . "-" . $lday;
    //     $idPartenaire = "";
    //     $idSejour = "";
    //     $idCmdF = "";
    //     $idCmd = "";
    //     $produit = "";
    //     if ($datedebut != "" && $datefin != "") {
    //         $datedebut = \DateTime::createFromFormat('Y-m-d', $datedebut)->setTime(0, 0);
    //         $datefin = \DateTime::createFromFormat('Y-m-d', $datefin)->setTime(0, 0);
    //     }
    //     if ($datedebutPrev != "" && $datefinPrev != "") {
    //         $datedebutPrev = \DateTime::createFromFormat('Y-m-d', $datedebutPrev)->setTime(0, 0);
    //         $datefinPrev = \DateTime::createFromFormat('Y-m-d', $datefinPrev)->setTime(0, 0);
    //     }
    //     //ListeDesFiltes
    //     $liteCnxParPeriode = $this->em->getRepository(ParentSejour::class)->RechercheAvancerliteCnxParPeriode($datedebut, $datefin, $idSejour, $idPartenaire);
    //     if ($liteCnxParPeriode) {
    //         $ComandeFind = $this->em->getRepository(Commande::class)->listeCommandeCnx($liteCnxParPeriode);
    //     } else {
    //         $ComandeFind = "";
    //     }
    //     $allCmd = $this->em->getRepository(ComandeProduit::class)->rechercherAvanceCmdProduitEspaceComptable($datedebut, $datefin, $idCmdF, $idCmd, $idSejour, $idPartenaire, $produit);
    //     //        $ListeCmdPrev = $this->em->getRepository(ComandeProduit::class)->rechercherAvanceCmdProduitEspaceComptable($datedebutPrev,$datefinPrev,$idCmdF,$idCmd,$idSejour,$idPartenaire,$produit);
    //     //$allCmd=array_merge($ListeCmd,$ListeCmdPrev);
    //     //        $numCmd= $this->em->getRepository(Commande::class);
    //     //        $ListeCmd =[];
    //     //        $cmds=[];
    //     //        $ComandeFind = [];
    //     $publicDirectory = $this->params->get('kernel.project_dir') . '/public/Facture/';
    //     $arrayFiles = [];
    //     $tabTestFile = [];
    //     foreach ($cmds as $cmd) {
    //         //            var_dump($cmd->getId());
    //         // e.g /var/www/project/public/mypdf.pdf
    //         $filename = $publicDirectory . "Facture" . $cmd->getId() . '-' . $cmd->getNumComande() . ".pdf";
    //         if (file_exists($filename)) {
    //             $tabTestFile[$cmd->getId()]["fileExiste"] = "oui";
    //         } else {
    //             $tabTestFile[$cmd->getId()]["fileExiste"] = "non";
    //         }
    //     }
    //     array_push($arrayFiles, $tabTestFile);
    //     //        $alletablissement=$this->em->getRepository(Etablisment::class)->findAll();
    //     $alletablissement = $this->em->getRepository(Etablisment::class)->listeEtablisementsOntSejour();
    //     $listeSejour = $this->em->getRepository(Sejour::class)->findSejourListForSearch();
    //     $listeCommande = $this->em->getRepository(Commande::class)->findCommandeListForSearchEspaceComptable();
    //     //        dd($listeCommande);
    //     $listeTypeProduit = $this->em->getRepository(TypeProduitConditionnement::class)->findAll();
    //     return $this->render('Admin/EspaceAdminComptable.html.twig', ['listeTypeProduit' => $listeTypeProduit, 'listeSejour' => $listeSejour, 'listeCommande' => $listeCommande, 'alletablissement' => $alletablissement, 'arrayFiles' => $arrayFiles, 'ListeCmd' => $allCmd, 'ComandeFind' => $ComandeFind]);
    // }

     /**
     * @Route("/ListeCommandes", name="AdminComptable")
     */
    public function AdminComptable()
    {
        $session = $this->get('session');
        $session->set('page', 'AdminComptable');
        //        $ListeCmd = $this->em->getRepository(ComandeProduit::class)->ProduitCommandeAvecCmdPayExip();
        $cmds = $this->em->getRepository(Commande::class)->CmdPayExip();
        //        $ComandeFind = $this->em->getRepository(Commande::class)->rechercherCmdFcatureCnx();
        //        $date = date("2020-09");
        //        $year=date("2020");
        //        $month = date("09");
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
        $datedebut = $date . "-" . $fday;
        $datefin = $date . "-" . $lday;
        $idPartenaire = "";
        $idSejour = "";
        $idCmdF = "";
        $idCmd = "";
        $produit = "";
        if ($datedebut != "" && $datefin != "") {
            $datedebut = \DateTime::createFromFormat('Y-m-d', $datedebut)->setTime(0, 0);
            $datefin = \DateTime::createFromFormat('Y-m-d', $datefin)->setTime(0, 0);
        }
        if ($datedebutPrev != "" && $datefinPrev != "") {
            $datedebutPrev = \DateTime::createFromFormat('Y-m-d', $datedebutPrev)->setTime(0, 0);
            $datefinPrev = \DateTime::createFromFormat('Y-m-d', $datefinPrev)->setTime(0, 0);
        }
        //ListeDesFiltes
        $liteCnxParPeriode = $this->em->getRepository(ParentSejour::class)->RechercheAvancerliteCnxParPeriode($datedebut, $datefin, $idSejour, $idPartenaire);
        if ($liteCnxParPeriode) {
            $ComandeFind = $this->em->getRepository(Commande::class)->listeCommandeCnx($liteCnxParPeriode);
        } else {
            $ComandeFind = "";
        }
        $allCmd = $this->em->getRepository(ComandeProduit::class)->rechercherAvanceCmdProduitEspaceComptable($datedebut, $datefin, $idCmdF, $idCmd, $idSejour, $idPartenaire, $produit);
        //        $ListeCmdPrev = $this->em->getRepository(ComandeProduit::class)->rechercherAvanceCmdProduitEspaceComptable($datedebutPrev,$datefinPrev,$idCmdF,$idCmd,$idSejour,$idPartenaire,$produit);
        //$allCmd=array_merge($ListeCmd,$ListeCmdPrev);
        //        $numCmd= $this->em->getRepository(Commande::class);
        //        $ListeCmd =[];
        //        $cmds=[];
        //        $ComandeFind = [];
        $publicDirectory = $this->params->get('kernel.project_dir') . '/public/Facture/';
        $arrayFiles = [];
        $tabTestFile = [];
        foreach ($cmds as $cmd) {
            //            var_dump($cmd->getId());
            // e.g /var/www/project/public/mypdf.pdf
            $filename = $publicDirectory . "Facture" . $cmd->getId() . '-' . $cmd->getNumComande() . ".pdf";
            if (file_exists($filename)) {
                $tabTestFile[$cmd->getId()]["fileExiste"] = "oui";
            } else {
                $tabTestFile[$cmd->getId()]["fileExiste"] = "non";
            }
        }
        array_push($arrayFiles, $tabTestFile);
        //        $alletablissement=$this->em->getRepository(Etablisment::class)->findAll();
        $alletablissement = $this->em->getRepository(Etablisment::class)->listeEtablisementsOntSejour();
        $listeSejour = $this->em->getRepository(Sejour::class)->findSejourListForSearch();
        $listeCommande = $this->em->getRepository(Commande::class)->findCommandeListForSearchEspaceComptable();
        //        dd($listeCommande);
        $listeTypeProduit = $this->em->getRepository(TypeProduitConditionnement::class)->findAll();
        return $this->render('Admin/EspaceAdminComptable.html.twig', ['listeTypeProduit' => $listeTypeProduit, 'listeSejour' => $listeSejour, 'listeCommande' => $listeCommande, 'alletablissement' => $alletablissement, 'arrayFiles' => $arrayFiles, 'ListeCmd' => $allCmd, 'ComandeFind' => $ComandeFind]);
    }

    /**
     * @Route("/comptablexlsxV3", name="comptablexlsxV3")
     */
    function ComptableFacturexlsxV3(Request $request)
    {
        $datedebut = $_POST['datedebut'];
        $datefin = $_POST['datefin'];
        $partenaire = $_POST['idPartenaire'];
        $sejour = $_POST['idSejour'];
        $commande = $_POST['idCommande'];
        $facture = $_POST['idFacture'];
        $produit = $_POST['idProduit'];
        $test = false;
        if ($produit == "") {
            $testProduit = false;
        } else {
            $testProduit = true;
        }
        if ($facture == "") {
            $testFacture = false;
        } else {
            $testFacture = true;
        }
        if ($commande == "") {
            $testCmd = false;
        } else {
            $testCmd = true;
        }
        if ($sejour == "") {
            $testSej = false;
        } else {
            $testSej = true;
        }
        if ($partenaire == "") {
            $testPart = false;
        } else {
            $testPart = true;
        }
        if ($datefin == "" || $datedebut == "") {
            $testPeriode = false;
        } else {
            $testPeriode = true;
        }
        if ($testPeriode || $testProduit || $testCmd || $testFacture || $testPart || $testSej) {
            $test = true;
        } else {
            $test = false;
        }
        if ($datedebut != "" && $datefin != "") {
            $datedebut = \DateTime::createFromFormat('Y-m-d', $datedebut)->setTime(0, 0);
            $datefin = \DateTime::createFromFormat('Y-m-d', $datefin)->setTime(0, 0);
        }
        //ListeDesFiltes
        $dataPart = explode('-', $partenaire);
        if ($partenaire != "") {
            $idPartenaire = $dataPart[0];
        } else {
            $idPartenaire = "";
        }
        $dataSejour = explode('-', $sejour);
        if ($sejour != "") {
            $idSejour = $dataSejour[0];
        } else {
            $idSejour = "";
        }
        $dataCmd = explode('-', $commande);
        if ($commande != "") {
            $idCmd = $dataCmd[0];
            $TypeCmd = $dataCmd[3];
        } else {
            $idCmd = "";
            $TypeCmd = "";
        }
        $dataCmdF = explode('-', $facture);
        if ($facture != "") {
            $idCmdF = $dataCmdF[0];
            $TypeCmdF = $dataCmdF[3];
        } else {
            $idCmdF = "";
            $TypeCmdF = "";
        }
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
        if ($test == true) {
            if (($TypeCmdF == "Facture" || $TypeCmd == "Facture" || $TypeCmdF == "" || $TypeCmd == "")) {
                $liteCnxParPeriode = $this->em->getRepository(ParentSejour::class)->RechercheAvancerliteCnxParPeriode($datedebut, $datefin, $idSejour, $idPartenaire);
                //   dd($liteCnxParPeriode);
                if ($idCmdF == "" && $idCmd == "") {
                    if ($liteCnxParPeriode) {
                        $ComandeFind = $this->em->getRepository(Commande::class)->listeCommandeCnx($liteCnxParPeriode);
                    } else {
                        $ComandeFind = "";
                    }
                } else {
                    $ComandeFind = $this->em->getRepository(Commande::class)->listeCommandeCnxAvancer($idCmdF, $idCmd);
                }
            } else {
                $ComandeFind = "";
            }
            if (($TypeCmdF == "payer" || $TypeCmd == "Expédié" || $TypeCmdF == "Expédié" || $TypeCmd == "payer" || $TypeCmdF == "" || $TypeCmd == "")) {
                //Rechercher commande produit
                $ListeCmd = $this->em->getRepository(ComandeProduit::class)->rechercherAvanceCmdProduitEspaceComptable($datedebut, $datefin, $idCmdF, $idCmd, $idSejour, $idPartenaire, $produit);
            } else {
                $ListeCmd = "";
            }
        } else {
            if ($datedebutNow != "" && $datefinNow != "") {
                $datedebutNow = \DateTime::createFromFormat('Y-m-d', $datedebutNow)->setTime(0, 0);
                $datefinNow = \DateTime::createFromFormat('Y-m-d', $datefinNow)->setTime(0, 0);
            }
            if ($datedebutPrev != "" && $datefinPrev != "") {
                $datedebutPrev = \DateTime::createFromFormat('Y-m-d', $datedebutPrev)->setTime(0, 0);
                $datefinPrev = \DateTime::createFromFormat('Y-m-d', $datefinPrev)->setTime(0, 0);
            }
            $liteCnxParPeriode = $this->em->getRepository(ParentSejour::class)->RechercheAvancerliteCnxParPeriode($datedebutNow, $datefinNow, $idSejour, $idPartenaire);
            $liteCnxParPeriodePrev = $this->em->getRepository(ParentSejour::class)->RechercheAvancerliteCnxParPeriode($datedebutPrev, $datefinPrev, $idSejour, $idPartenaire);
            $result = array_merge($liteCnxParPeriode, $liteCnxParPeriodePrev);
            if ($result) {
                $ComandeFind = $this->em->getRepository(Commande::class)->listeCommandeCnx($result);
            } else {
                $ComandeFind = "";
            }
            //dd($ComandeFind);
            //Rechercher commande produit
            $ListeCmdNow = $this->em->getRepository(ComandeProduit::class)->rechercherAvanceCmdProduitEspaceComptable($datedebut, $datefin, $idCmdF, $idCmd, $idSejour, $idPartenaire, $produit);
            $ListeCmdPrev = $this->em->getRepository(ComandeProduit::class)->rechercherAvanceCmdProduitEspaceComptable($datedebutPrev, $datefinPrev, $idCmdF, $idCmd, $idSejour, $idPartenaire, $produit);
            $ListeCmd = array_merge($ListeCmdNow, $ListeCmdPrev);
        }
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
        $sheet->setCellValue('A1', 'Client ID');
        $sheet->setCellValue('B1', 'Client');
        $sheet->setCellValue('C1', 'Num Facture');
        $sheet->setCellValue('D1', 'Date de facture');
        $sheet->setCellValue('E1', 'Num Commande');
        $sheet->setCellValue('F1', 'Date de commande');
        $sheet->setCellValue('G1', 'Produits');
        $sheet->setCellValue('H1', 'Quantité');
        $sheet->setCellValue('I1', 'Montant HT');
        $sheet->setCellValue('J1', 'Montant TVA');
        $sheet->setCellValue('K1', 'Montant TTC');
        $sheet->setCellValue('L1', 'Frais d\'expédition');
        $sheet->setCellValue('M1', 'Moyen de paiement');
        $arrayCmd = [];
        $row = 2;
        $arrayFrais = [];
        if ($ListeCmd) {
            foreach ($ListeCmd as $cmd) {
                //            if($cmd->getIdComande()->getDateCreateCommande() != null ){
                //                $Datecmd =  $cmd->getIdComande()->getDateCreateCommande()->format('d/m/Y');
                //            }
                //            else{
                //                $Datecmd = "";
                //            }
                $nomProduit = $cmd->getIdProduit()->getType()->getLabeletype() . ' ' . $cmd->getIdProduit()->getIdConditionnement()->getSousTitre();
                $montantTTC = $cmd->getIdProduit()->getIdConditionnement()->getMontantTTC() * $cmd->getQuantiter();
                $montantHT = ($cmd->getIdProduit()->getIdConditionnement()->getMontantTTC() * 100 / 120) * $cmd->getQuantiter();
                $montantTVA = $montantTTC - $montantHT;
                $nomClient = $cmd->getIdComande()->getIdUser()->getNom() . ' ' . $cmd->getIdComande()->getIdUser()->getPrenom();
                $sheet->setCellValue('A' . $row, $cmd->getIdComande()->getIdUser()->getId());
                $sheet->setCellValue('B' . $row, $nomClient);
                $sheet->setCellValue('C' . $row, $cmd->getIdComande()->getNumfacture());
                $sheet->setCellValue('D' . $row, "");
                $sheet->setCellValue('E' . $row, $cmd->getIdComande()->getNumComande());
                $sheet->setCellValue('F' . $row, $cmd->getIdComande()->getDateCreateCommande());
                $sheet->setCellValue('G' . $row, $nomProduit);
                $sheet->setCellValue('H' . $row, $cmd->getQuantiter());
                //            $sheet->setCellValue('J'.$row,$cmd->getFrais_expedition());
                $sheet->setCellValue('I' . $row, round($montantHT, 2));
                $sheet->setCellValue('J' . $row, round($montantTVA, 2));
                $sheet->setCellValue('K' . $row, round($montantTTC, 2));
                if (isset($arrayFrais[$cmd->getIdComande()->getId()])) {
                    $sheet->setCellValue('L' . $row, 0);
                } else {
                    $arrayFrais[$cmd->getIdComande()->getId()] = $cmd->getIdComande()->getMontanenv();
                    $sheet->setCellValue('L' . $row, round($cmd->getIdComande()->getMontanenv(), 2));
                }
                $sheet->setCellValue('M' . $row, $cmd->getIdComande()->getPaymentType());
                $row = $row + 1;
            }
        }
        $row2 = $row;
        if ($ComandeFind != "") {
            foreach ($ComandeFind as $cmdv) {
                for ($i = 0; $i < $cmdv->getNbconnx(); $i++) {
                    $MontantTTC = $cmdv->getIdSejour()->getPrixcnxpartenaire() + ($cmdv->getIdSejour()->getPrixcnxpartenaire() * 0.2);
                    $MontantHT = $cmdv->getIdSejour()->getPrixcnxpartenaire();
                    $montantTva = $MontantTTC - $MontantHT;
                    $sheet->setCellValue('A' . $row2, $cmdv->getIdSejour()->getIdEtablisment()->getId());
                    $sheet->setCellValue('B' . $row2, $cmdv->getIdSejour()->getIdEtablisment()->getNometab());
                    $sheet->setCellValue('C' . $row2, $cmdv->getNumfacture());
                    $sheet->setCellValue('D' . $row2, "");
                    $sheet->setCellValue('E' . $row2, $cmdv->getNumComande());
                    $sheet->setCellValue('F' . $row2, $cmdv->getDateCreateCommande());
                    $sheet->setCellValue('G' . $row2, "Connexion");
                    $sheet->setCellValue('H' . $row2, 1);
                    //            $sheet->setCellValue('J'.$row,$cmd->getFrais_expedition());
                    $sheet->setCellValue('I' . $row2, round($MontantHT, 2));
                    $sheet->setCellValue('J' . $row2, $montantTva);
                    $sheet->setCellValue('K' . $row2, round($MontantTTC, 2));
                    $sheet->setCellValue('L' . $row2, $cmdv->getPaymentType());
                    $row2 = $row2 + 1;
                }
            }
        }
        $writer = new Xlsx($spreadsheet);
        $milliseconds = round(microtime(true) * 1000);
        $dateJour = date('dmy');
        $codeExcel = $dateJour . '-' . $milliseconds;
        // Create a Temporary file in the system
        $fileName = 'ListeFactures-' . $codeExcel . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

     /**
     * @Route("/ListeAppelAFacutre", name="listeappelafacutre")
     */
    public function ListeAppelAFacutre()
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
        //        $ComandeFind = $this->em->getRepository(ComandeProduit::class)->ProduitCommandeAvecCmdPayExip();
        $ComandeFind = [];
        $datedebutPrev = \DateTime::createFromFormat('Y-m-d', $datedebutPrev)->setTime(0, 0);
        $datefinPrev = \DateTime::createFromFormat('Y-m-d', $datefinPrev)->setTime(0, 0);
        //        $ComandeFindPrev = $this->em->getRepository(ComandeProduit::class)->ProduitCommandeAvecCmdPayExipBetween($datedebutPrev,$datefinPrev);
        $datedebutNow = \DateTime::createFromFormat('Y-m-d', $datedebutNow)->setTime(0, 0);
        $datefinNow = \DateTime::createFromFormat('Y-m-d', $datefinNow)->setTime(0, 0);
        $ComandeFind = $this->em->getRepository(ComandeProduit::class)->ProduitCommandeAvecCmdPayExipBetween($datedebutNow, $datefinNow);
        //        $ComandeFind=array_merge($ComandeFindPrev,$ComandeFindNow);
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
        //        $tab2=$this->em->getRepository(Commande::class)->rechercheSumMontantTTC();
        //        array_unique($tab,SORT_REGULAR);
        //
        //        foreach($tab2 as $appelFact)
        //        {
        //            $array[$appelFact['periode']]['numAppelFacture']=$appelFact['moantantTtcregl'];
        //
        //        }
        $dateNow = date('Y-m');
        //     dd($appelfact);
        return $this->render('factures/appelFacture.html.twig', ['tabNumFacture' => $array, 'dateNow' => $dateNow, 'listeTypeProduit' => $listeTypeProduit, 'listeSejour' => $listeSejour, 'listeCommande' => $listeCommande, 'alletablissement' => $alletablissement, 'ComandeFind' => $ComandeFind]);
        //        dd($TablisteProduits);
    }

    /**
     * @Route("Admin/comptableJSonV2", name="comptableJSonV2")
     */
    function ComptableFactureJsonV2(Request $request)
    {
        $datedebut = $request->get("datedebut");
        $datefin = $request->get("datefin");
        if ($datedebut != null && $datedebut != "" && $datefin != null && $datefin != "") {
            $datedebut = \DateTime::createFromFormat('Y-m-d', $datedebut)->setTime(0, 0);
            $datefin = \DateTime::createFromFormat('Y-m-d', $datefin)->setTime(0, 0);
            $ListeCmd = $this->em->getRepository(ComandeProduit::class)->CommandeProduitsComptableBetween($datedebut, $datefin);
            $liteCnxParPeriode = $this->em->getRepository(ParentSejour::class)->liteCnxParPeriode($datedebut, $datefin);
            //           dd($liteCnxParPeriode);
            if (count($liteCnxParPeriode)) {
                $ComandeFind = $this->em->getRepository(Commande::class)->listeCommandeCnx($liteCnxParPeriode);
            } else {
                $ComandeFind = "";
            }
        } else {
            $ListeCmd = $this->em->getRepository(ComandeProduit::class)->ProduitCommandeAvecCmdPayExip();
            $ComandeFind = $this->em->getRepository(Commande::class)->rechercherCmdFcatureCnx();
        }
        $arrayCmd = [];
        foreach ($ListeCmd as $cmd) {
            //            if($cmd->getDatefacture() != null ){
            //                $Datefacture =  $cmd->getDatefacture()->format('d/m/Y');
            //            }
            //            else{
            //                $Datefacture = "";
            //            }
            if ($cmd->getIdComande()->getDateCreateCommande() != null) {
                $Datecmd =  $cmd->getIdComande()->getDateCreateCommande()->format('d/m/Y');
            } else {
                $Datecmd = "";
            }
            $nomProduit = $cmd->getIdProduit()->getType()->getLabeletype() . ' ' . $cmd->getIdProduit()->getIdConditionnement()->getSousTitre();
            $montantTTC = $cmd->getIdProduit()->getIdConditionnement()->getMontantTTC() * $cmd->getQuantiter();
            $montantHT = ($cmd->getIdProduit()->getIdConditionnement()->getMontantTTC() * 100 / 120) * $cmd->getQuantiter();
            $montantTVA = $montantTTC - $montantHT;
            $nomClient = $cmd->getIdComande()->getIdUser()->getNom() . ' ' . $cmd->getIdComande()->getIdUser()->getPrenom();
            $arrayCmd[] = [
                'id' => $cmd->getId(),
                'Idcmd' => $cmd->getIdComande()->getId(),
                'Idclient' => $cmd->getIdComande()->getIdUser()->getId(),
                'Client' => $nomClient,
                'idPartenaire' => "",
                'Numfacture' => $cmd->getIdComande()->getNumfacture(),
                'Datefacture' => "",
                'Datecmd' => $Datecmd,
                'Numcmd' => $cmd->getIdComande()->getNumComande(),
                'Quantite' => $cmd->getQuantiter(),
                'Produits' => $nomProduit,
                'Montant_ttc' => $montantTTC,
                'Frais_expedition' => $cmd->getIdComande()->getMontanenv(),
                'Montant_ht' => $montantHT,
                'Tva' => $montantTVA,
                'periode' => $cmd->getIdComande()->getPeriode(),
                'Moyen_paiement' => $cmd->getIdComande()->getPaymentType()
            ];
        }
        if ($ComandeFind != "") {
            foreach ($ComandeFind as $cmd) {
                //            var_dump($cmd->getNbconnx());
                for ($i = 0; $i < $cmd->getNbconnx(); $i++) {
                    if ($cmd->getDateCreateCommande() != null) {
                        $Datecmd = $cmd->getDateCreateCommande()->format('d/m/Y');
                    } else {
                        $Datecmd = "";
                    }
                    if ($cmd->getDateFacture() != null) {
                        $Datecmd = $cmd->getDateFacture()->format('d/m/Y');
                    } else {
                        $Datecmd = "";
                    }
                    //            $montantTTC=$cmd-> getIdProduit()->getIdConditionnement()->getMontantTTC() *$cmd->getQuantiter();
                    //            $montantHT=($cmd-> getIdProduit()->getIdConditionnement()->getMontantTTC()*100/120) *$cmd->getQuantiter();
                    //            $montantTVA=$montantTTC-$montantHT;
                    $MontantTTC = $cmd->getIdSejour()->getPrixcnxpartenaire() + ($cmd->getIdSejour()->getPrixcnxpartenaire() * 0.2);
                    $MontantHT = $cmd->getIdSejour()->getPrixcnxpartenaire();
                    $montantTva = $MontantTTC - $MontantHT;
                    $arrayCmd[] = [
                        'id' => $cmd->getId(),
                        'Idcmd' => $cmd->getId(),
                        'Idclient' => $cmd->getIdSejour()->getIdEtablisment()->getId(),
                        'Client' => $cmd->getIdSejour()->getIdEtablisment()->getNometab(),
                        'Numfacture' => $cmd->getNumfacture(),
                        'idPartenaire' => $cmd->getIdSejour()->getIdPartenaire()->getId(),
                        'Datefacture' => "",
                        'Datecmd' => $Datecmd,
                        'Numcmd' => $cmd->getNumComande(),
                        'Quantite' => 1,
                        'Produits' => "Connexion",
                        'Montant_ttc' => $MontantTTC,
                        'Frais_expedition' => "",
                        'Montant_ht' => $MontantHT,
                        'Tva' => $montantTva,
                        'periode' => $cmd->getPeriode(),
                        'Moyen_paiement' => $cmd->getPaymentType(),
                    ];
                }
            }
        }
        //        dd($arrayCmd);
        // json_encode($arrayCmd);
        return new JsonResponse($arrayCmd);
    }

    /**
     * @Route("Comptable/EspaceComptableListeAppelAFacutre", name="EspaceComptableListeAppelAFacutre")
     */
    public function EspaceComptableListeAppelAFacutre()
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
        //        $ComandeFind = $this->em->getRepository(ComandeProduit::class)->ProduitCommandeAvecCmdPayExip();
        $ComandeFind = [];
        $datedebutPrev = \DateTime::createFromFormat('Y-m-d', $datedebutPrev)->setTime(0, 0);
        $datefinPrev = \DateTime::createFromFormat('Y-m-d', $datefinPrev)->setTime(0, 0);
        //        $ComandeFindPrev = $this->em->getRepository(ComandeProduit::class)->ProduitCommandeAvecCmdPayExipBetween($datedebutPrev,$datefinPrev);
        $datedebutNow = \DateTime::createFromFormat('Y-m-d', $datedebutNow)->setTime(0, 0);
        $datefinNow = \DateTime::createFromFormat('Y-m-d', $datefinNow)->setTime(0, 0);
        $ComandeFind = $this->em->getRepository(ComandeProduit::class)->ProduitCommandeAvecCmdPayExipBetween($datedebutNow, $datefinNow);
        //        $ComandeFind=array_merge($ComandeFindPrev,$ComandeFindNow);
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
        //     dd($appelfact);
        return $this->render('Admin/ComptableListeAppelAFacture.html.twig', ['tabNumFacture' => $array, 'dateNow' => $dateNow, 'listeTypeProduit' => $listeTypeProduit, 'listeSejour' => $listeSejour, 'listeCommande' => $listeCommande, 'alletablissement' => $alletablissement, 'ComandeFind' => $ComandeFind]);
        //        dd($TablisteProduits);
    }

    /**
     * @Route("Admin/ListeFacturesConnexion", name="ListeFacutresConnexion")
     */
    public function ListeFacutresConnexion()
    {
        $role = "ROLE_PARTENAIRE";
        //rechercher liste des partenaires:
        $Partenaires = $this->em->getRepository(User::class)->findByRole($role);
        $appelfact = [];
        foreach ($Partenaires as $part) {
            $etab = $this->em->getRepository(Etablisment::class)->findOneBy(array('user' => $part));
            $ComandeFind = $this->em->getRepository(Commande::class)->findBy(array('statut' => 31, "idUser" => $part));
            $serviceuser = $this->etablissementService;
            //
            //            $Nbconnxtionss = $serviceuser->getNombreconnxtionPartenaireV2($etab->getId());
            $i = 0;
            foreach ($ComandeFind as $cmd) {
                $factureConnexion[$part->getId()][$i]['codeSejour'] = $cmd->getIdSejour()->getCodeSejour();
                $factureConnexion[$part->getId()][$i]['nbEnfants'] = $cmd->getIdSejour()->getNbenfan();
                $factureConnexion[$part->getId()][$i]['prixcnxpartenaire'] = $cmd->getIdSejour()->getPrixcnxpartenaire();
                $factureConnexion[$part->getId()][$i]['nbConnexion'] = $cmd->getNbconnx();
                $factureConnexion[$part->getId()][$i]['MontantTt'] = $cmd->getMoantantTtcregl();
                $factureConnexion[$part->getId()][$i]['dateFacture'] = $cmd->getDateFacture();
                $factureConnexion[$part->getId()][$i]['numFacture'] = $cmd->getNumfacture();
                $i++;
            }
            $factureConnexion[$part->getId()]['nbCmds'] = count($ComandeFind);
        }
        //     dd($appelfact);
        return $this->render('Admin/ListeFactureConnexion.html.twig', ['factureConnexion' => $factureConnexion, 'Partenaires' => $Partenaires]);
        //        dd($TablisteProduits);
    }

    /**
     * @Route("Admin/ExportAppelAFacture", name="ExportAppelAFacture")
     */
    function ExportAppelAFacture(Request $request)
    {
        $datedebut = $_POST['datedebut'];
        $datefin = $_POST['datefin'];
        $partenaire = $_POST['idPartenaire'];
        $sejour = $_POST['idSejour'];
        $commande = $_POST['idCommande'];
        $facture = $_POST['idFacture'];
        $produit = $_POST['idProduit'];
        $test = false;
        if ($produit == "") {
            $testProduit = false;
        } else {
            $testProduit = true;
        }
        if ($facture == "") {
            $testFacture = false;
        } else {
            $testFacture = true;
        }
        if ($commande == "") {
            $testCmd = false;
        } else {
            $testCmd = true;
        }
        if ($sejour == "") {
            $testSej = false;
        } else {
            $testSej = true;
        }
        if ($partenaire == "") {
            $testPart = false;
        } else {
            $testPart = true;
        }
        if ($datefin == "" || $datedebut == "") {
            $testPeriode = false;
        } else {
            $testPeriode = true;
        }
        if ($testPeriode || $testProduit || $testCmd || $testFacture || $testPart || $testSej) {
            $test = true;
        } else {
            $test = false;
        }
        if ($datedebut != "" && $datefin != "") {
            $datedebut = \DateTime::createFromFormat('Y-m-d', $datedebut)->setTime(0, 0);
            $datefin = \DateTime::createFromFormat('Y-m-d', $datefin)->setTime(0, 0);
        }
        //ListeDesFiltes
        $dataPart = explode('-', $partenaire);
        if ($partenaire != "") {
            $idPartenaire = $dataPart[0];
        } else {
            $idPartenaire = "";
        }
        $dataSejour = explode('-', $sejour);
        if ($sejour != "") {
            $idSejour = $dataSejour[0];
        } else {
            $idSejour = "";
        }
        $dataCmd = explode('-', $commande);
        if ($commande != "") {
            $idCmd = $dataCmd[0];
            $TypeCmd = $dataCmd[3];
        } else {
            $idCmd = "";
            $TypeCmd = "";
        }
        $dataCmdF = explode('-', $facture);
        if ($facture != "") {
            $idCmdF = $dataCmdF[0];
            $TypeCmdF = $dataCmdF[3];
        } else {
            $idCmdF = "";
            $TypeCmdF = "";
        }
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
        if ($test == true) {
            $ComandeFind = $this->em->getRepository(ComandeProduit::class)->CommandeProduitsComptableRechercheAvancerReve($datedebut, $datefin, $idCmdF, $idCmd, $idSejour, $idPartenaire, $produit);
        } else {
            $datedebutPrev = \DateTime::createFromFormat('Y-m-d', $datedebutPrev)->setTime(0, 0);
            $datefinPrev = \DateTime::createFromFormat('Y-m-d', $datefinPrev)->setTime(0, 0);
            $ComandeFindPrev = $this->em->getRepository(ComandeProduit::class)->ProduitCommandeAvecCmdPayExipBetween($datedebutPrev, $datefinPrev);
            $datedebutNow = \DateTime::createFromFormat('Y-m-d', $datedebutNow)->setTime(0, 0);
            $datefinNow = \DateTime::createFromFormat('Y-m-d', $datefinNow)->setTime(0, 0);
            $ComandeFindNow = $this->em->getRepository(ComandeProduit::class)->ProduitCommandeAvecCmdPayExipBetween($datedebutNow, $datefinNow);
            $ComandeFind = array_merge($ComandeFindPrev, $ComandeFindNow);
        }
        $tab = $this->em->getRepository(Commande::class)->findAppelFacture();
        array_unique($tab, SORT_REGULAR);
        $array = [];
        foreach ($tab as $appelFact) {
            $array[$appelFact['id']][$appelFact['periode']]['numAppelFacture'] = $appelFact['numfacture'];
        }
        $dateNow = date('Y-m');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getActiveSheet()->setTitle("Liste appel à facture");
        $sheet->getPageMargins()->setTop(1);
        $sheet->getPageMargins()->setRight(0.75);
        $sheet->getPageMargins()->setLeft(0.75);
        $sheet->getPageMargins()->setBottom(1);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageMargins()->setTop(1);
        $sheet->getPageMargins()->setRight(0.75);
        $sheet->getPageMargins()->setLeft(0.75);
        $sheet->getPageMargins()->setBottom(1);
        $sheet->setCellValue('A1', 'Client ID');
        $sheet->setCellValue('B1', 'Client');
        $sheet->setCellValue('C1', 'Num appel à Facture');
        $sheet->setCellValue('D1', 'Date de l\'appel à facture');
        $sheet->setCellValue('E1', 'Produits');
        $sheet->setCellValue('F1', 'Montant HT');
        $sheet->setCellValue('G1', 'Quantité');
        $sheet->setCellValue('H1', '% reversement');
        $sheet->setCellValue('I1', 'Montant de reversement');
        $arrayCmd = [];
        $row = 2;
        $HT = 0;
        $TVA = 0;
        foreach ($ComandeFind as $cmd) {
            $prixHTUNITAIRE = ($cmd->getIdProduit()->getIdConditionnement()->getMontantTTC() * 100) / 120;
            if (is_null(($cmd->getPourcentage()))) {
                $pourcentage_remise = 0;
            } else {
                $pourcentage_remise = $cmd->getPourcentage();
            }
            $Montant =  $prixHTUNITAIRE - (($prixHTUNITAIRE / 100) * $pourcentage_remise);
            $REV = $Montant * 0.1;
            if ($cmd->getQuantiter() != 0 and $cmd->getIdComande()->getIdSejour()->getReverseventepart() != 0) {
                if ($cmd->getIdProduit()->getIdConditionnement()->getIdTypeProduit()->getLabeletype() == "Connexion") {
                    $pourncentageR = $cmd->getIdComande()->getIdSejour()->getReversecnxpart();
                } else {
                    $pourncentageR = $cmd->getIdComande()->getIdSejour()->getReverseventepart();
                }
                if ($dateNow != $cmd->getIdComande()->getDateCreateCommande()->format('Y-m')) {
                    $numFacture = $array[$cmd->getIdComande()->getIdSejour()->getIdEtablisment()->getUser()->getId()][$cmd->getIdComande()->getDateCreateCommande()->format('Y-m')]["numAppelFacture"];
                } else {
                    $numFacture = "";
                }
                $sheet->setCellValue('A' . $row, $cmd->getIdComande()->getIdSejour()->getIdEtablisment()->getId());
                $sheet->setCellValue('B' . $row,  $cmd->getIdComande()->getIdSejour()->getIdEtablisment()->getNometab());
                $sheet->setCellValue('C' . $row, $numFacture);
                $sheet->setCellValue('D' . $row, $cmd->getIdComande()->getDateCreateCommande());
                $sheet->setCellValue('E' . $row, $cmd->getIdProduit()->getIdConditionnement()->getIdTypeProduit()->getLabeletype() . ' ' . $cmd->getIdProduit()->getIdConditionnement()->getSousTitre());
                $sheet->setCellValue('F' . $row, round($Montant, 2));
                $sheet->setCellValue('G' . $row, $cmd->getQuantiter());
                $sheet->setCellValue('H' . $row, $pourncentageR);
                $sheet->setCellValue('I' . $row, '- ' . round($REV, 2));
                $row = $row + 1;
            }
        }
        $writer = new Xlsx($spreadsheet);
        // Create a Temporary file in the system
        $milliseconds = round(microtime(true) * 1000);
        $dateJour = date('dmy');
        $codeExcel = $dateJour . '-' . $milliseconds;
        $fileName = 'listeAppelAFacture_' . $codeExcel . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @Route("Admin/comptableAppelFactXlx", name="comptableAppelFactXlx")
     */
    function comptableAppelFactXlx(Request $request)
    {
        $datedebut = $_POST['datedebut'];
        $datefin = $_POST['datefin'];
        if ($datedebut != null && $datedebut != "" && $datefin != null && $datefin != "") {
            $datedebut = \DateTime::createFromFormat('Y-m-d', $datedebut)->setTime(0, 0);
            $datefin = \DateTime::createFromFormat('Y-m-d', $datefin)->setTime(0, 0);
            $ComandeFind = $this->em->getRepository(ComandeProduit::class)->ProduitCommandeAvecCmdPayExipBetween($datedebut, $datefin);
            $dateFichier = ':du' . $_POST['datedebut'] . 'à' . $_POST['datefin'];
        } else {
            $ComandeFind = $this->em->getRepository(ComandeProduit::class)->ProduitCommandeAvecCmdPayExip();
            $dateFichier = "";
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getActiveSheet()->setTitle("Liste appel à facture");
        $sheet->getPageMargins()->setTop(1);
        $sheet->getPageMargins()->setRight(0.75);
        $sheet->getPageMargins()->setLeft(0.75);
        $sheet->getPageMargins()->setBottom(1);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageMargins()->setTop(1);
        $sheet->getPageMargins()->setRight(0.75);
        $sheet->getPageMargins()->setLeft(0.75);
        $sheet->getPageMargins()->setBottom(1);
        $sheet->setCellValue('A1', 'Client ID');
        $sheet->setCellValue('B1', 'Client');
        $sheet->setCellValue('C1', 'Num appel à Facture');
        $sheet->setCellValue('D1', 'Date de l\'appel à facture');
        $sheet->setCellValue('E1', 'Produits');
        $sheet->setCellValue('F1', 'Montant HT');
        $sheet->setCellValue('G1', 'Quantité');
        $sheet->setCellValue('H1', '% reversement');
        $sheet->setCellValue('I1', 'Montant de reversement');
        $arrayCmd = [];
        $row = 2;
        $HT = 0;
        $TVA = 0;
        foreach ($ComandeFind as $cmd) {
            $Montant = ($cmd->getIdProduit()->getIdConditionnement()->getMontantTTC() * 100) / 120;
            $REV = $Montant * 0.1;
            if ($cmd->getQuantiter() != 0 and $cmd->getIdComande()->getIdSejour()->getReverseventepart() != 0) {
                if ($cmd->getIdProduit()->getIdConditionnement()->getIdTypeProduit()->getLabeletype() == "Connexion") {
                    $pourncentageR = $cmd->getIdComande()->getIdSejour()->getReversecnxpart();
                } else {
                    $pourncentageR = $cmd->getIdComande()->getIdSejour()->getReverseventepart();
                }
                $sheet->setCellValue('A' . $row, $cmd->getIdComande()->getIdSejour()->getIdEtablisment()->getId());
                $sheet->setCellValue('B' . $row,  $cmd->getIdComande()->getIdSejour()->getIdEtablisment()->getNometab());
                $sheet->setCellValue('C' . $row, $cmd->getIdComande()->getNumfacture());
                $sheet->setCellValue('D' . $row, $cmd->getIdComande()->getDateCreateCommande());
                $sheet->setCellValue('E' . $row, $cmd->getIdProduit()->getIdConditionnement()->getIdTypeProduit()->getLabeletype() . ' ' . $cmd->getIdProduit()->getIdConditionnement()->getSousTitre());
                $sheet->setCellValue('F' . $row, round($Montant, 2));
                $sheet->setCellValue('G' . $row, $cmd->getQuantiter());
                $sheet->setCellValue('H' . $row, $pourncentageR);
                $sheet->setCellValue('I' . $row, '- ' . round($REV, 2));
                $row = $row + 1;
            }
        }
        $writer = new Xlsx($spreadsheet);
        // Create a Temporary file in the system
        $fileName = 'listeAppelAFacture_' . $dateFichier . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @Route("Admin/ZipAppelFActVersion2", name="ZipAppelFActVersion2")
     */
    public function  ZipAppelFActVersion2(Request $request)
    {
        $partenaires = json_decode($request->get('partenaire'));
        $array_ofpathe = [];
        foreach ($partenaires as $idPartenaire) {
            $part = $this->em->getRepository(User::class)->find(intval($idPartenaire->idPartenaire));
            $etab = $this->em->getRepository(Etablisment::class)->findOneBy(array('user' => $part));
            $nompart = "";
            if ($etab->getNometab()) {
                $nompart1 = str_replace('"', '', $etab->getNometab());
                $nompart = str_replace("/", "", $nompart1);
            }
            // In this case, we want to write the file in the public directory
            $publicDirectory = $this->params->get('kernel.project_dir') . '/public/backupFacture/';
            // e.g /var/www/project/public/mypdf.pdf
            $pdfFilepath = "Appel_Facture_" . $nompart . $part->getId() . '_' . $idPartenaire->Periode . ".pdf";
            $rootAbsoluPdf = $publicDirectory . $pdfFilepath;
            // W
            array_push($array_ofpathe, ['filePath' => $rootAbsoluPdf, 'fileName' => $pdfFilepath]);
        }
        $milliseconds = round(microtime(true) * 1000);
        $dateJour = date('dmy');
        $codeZip = $dateJour . '-' . $milliseconds;
        $zip = new ZipArchive();
        $projectRoot = $this->params->get('kernel.project_dir');
        //var_dump($projectRoot . '/public/Facture/testZip.zip');
        if ($zip->open($projectRoot . '/public/Facture/AppelFactureZip' . $codeZip . '.zip', ZipArchive::CREATE) === TRUE) {
            // Add files to the zip file
            foreach ($array_ofpathe as $fileNames) {
                $zip->addFile($fileNames["filePath"], $fileNames['fileName']);
            }
            $zip->close();
        }
        return new Response('/Facture/AppelFactureZip' . $codeZip . '.zip');
    }

    /**
     * @Route("Admin/comptableReversementParPeriodeRecherchercheAvancerVersion2", name="comptableReversementParPeriodeRecherchercheAvancerVersion2")
     */
    function comptableReversementParPeriodeRecherchercheAvancerVersion2(Request $request)
    {
        $datedebut = $request->get("datedebut");
        $datefin = $request->get("datefin");
        $partenaire = $request->get("partenaire");
        $sejour = $request->get("sejour");
        $commande = $request->get("numCmd");
        $facture = $request->get("numFacture");
        $produit = $request->get("produit");
        if ($datedebut != "" && $datefin != "") {
            $datedebut = \DateTime::createFromFormat('Y-m-d', $datedebut)->setTime(0, 0);
            $datefin = \DateTime::createFromFormat('Y-m-d', $datefin)->setTime(0, 0);
        }
        //ListeDesFiltes
        $dataPart = explode('-', $partenaire);
        if ($partenaire != "") {
            $idPartenaire = $dataPart[0];
        } else {
            $idPartenaire = "";
        }
        $dataSejour = explode('-', $sejour);
        if ($sejour != "") {
            $idSejour = $dataSejour[0];
        } else {
            $idSejour = "";
        }
        $dataCmd = explode('-', $commande);
        if ($commande != "") {
            $idCmd = $dataCmd[0];
        } else {
            $idCmd = "";
        }
        $dataCmdF = explode('-', $facture);
        if ($facture != "") {
            $idCmdF = $dataCmdF[0];
        } else {
            $idCmdF = "";
        }
        $ComandeFind = $this->em->getRepository(ComandeProduit::class)->CommandeProduitsComptableRechercheAvancerReve($datedebut, $datefin, $idCmdF, $idCmd, $idSejour, $idPartenaire, $produit);
        $tab = $this->em->getRepository(Commande::class)->findAppelFacture();
        //        dd($tab);
        array_unique($tab, SORT_REGULAR);
        $array = [];
        foreach ($tab as $appelFact) {
            $array[$appelFact['id']][$appelFact['periode']]['numAppelFacture'] = $appelFact['numfacture'];
        }
        $dateNow = date('Y-m');
        $arrayCmd = [];
        foreach ($ComandeFind as $cmd) {
            //            if($cmd->getDatefacture() != null ){
            //                $Datefacture =  $cmd->getDatefacture()->format('d/m/Y');
            //            }
            //            else{
            //                $Datefacture = "";
            //            }
            if ($cmd->getIdComande()->getDateCreateCommande() != null) {
                $Datecmd =  $cmd->getIdComande()->getDateCreateCommande()->format('d/m/Y');
            } else {
                $Datecmd = "";
            }
            if ($cmd->getIdComande()->getDateCreateCommande() != null) {
                $periode =  $cmd->getIdComande()->getDateCreateCommande()->format('Y-m');
            } else {
                $periode = "";
            }
            $nomProduit = $cmd->getIdProduit()->getType()->getLabeletype() . ' ' . $cmd->getIdProduit()->getIdConditionnement()->getSousTitre();
            $montantTTC = $cmd->getIdProduit()->getIdConditionnement()->getMontantTTC() * $cmd->getQuantiter();
            if (is_null($cmd->getPourcentage())) {
                $pourcentage_remise = 0;
            } else {
                $pourcentage_remise = $cmd->getPourcentage();
            }
            $prixHTUNITAIRE = $cmd->getIdProduit()->getIdConditionnement()->getMontantTTC() * 100 / 120;
            $montantHT = ($prixHTUNITAIRE - (($prixHTUNITAIRE / 100) * $pourcentage_remise)) * $cmd->getQuantiter();
            $pourCent = 0;
            if ($cmd->getIdProduit()->getType()->getLabeletype() == "Connexion") {
                $pourCent = $cmd->getIdComande()->getIdSejour()->getReversecnxpart();
            } else {
                $pourCent = $cmd->getIdComande()->getIdSejour()->getReverseventepart();
            }
            if ($cmd->getIdComande()->getDateCreateCommande()) {
                if ($dateNow != $cmd->getIdComande()->getDateCreateCommande()->format('Y-m')) {
                    $numFacture = $array[$cmd->getIdComande()->getIdSejour()->getIdEtablisment()->getUser()->getId()][$cmd->getIdComande()->getDateCreateCommande()->format('Y-m')]["numAppelFacture"];
                } else {
                    $numFacture = "";
                }
            } else {
                $numFacture = "";
            }
            $REV = $montantHT * ($pourCent / 100);
            $montantTVA = $montantTTC - $montantHT;
            $arrayCmd[] = [
                'id' => $cmd->getId(),
                'Idcmd' => $cmd->getIdComande()->getId(),
                'Idclient' => $cmd->getIdComande()->getIdSejour()->getIdEtablisment()->getId(),
                'Client' => $cmd->getIdComande()->getIdSejour()->getIdEtablisment()->getNometab(),
                'Numfacture' => $numFacture,
                'Datefacture' => $Datecmd,
                'Quantite' => $cmd->getQuantiter(),
                'Produits' => $nomProduit,
                'Montant_ttc' => $montantTTC,
                'Rev' => $REV,
                'Montant_ht' => $montantHT,
                'pourCent' => $pourCent,
                'periode' => $periode,
                'idPart' => $cmd->getIdComande()->getIdSejour()->getIdPartenaire()->getId()
                //                'Moyen_paiement'=>$cmd->getIdComande()->getPaymentType()
            ];
        }
        // json_encode($arrayCmd);
        return new JsonResponse($arrayCmd);
    }

    /**
     * @Route("/Admin/generatePdfReversementPartenaireParPeriode/{idPartenaire}/{periode}", name="generatePdfReversementPartenaireParPeriode")
     */
    public function generatePdfReversementPartenaireParPeriode($idPartenaire, $periode)
    {
        $part = $this->em->getRepository(User::class)->find($idPartenaire);
        $etab = $this->em->getRepository(Etablisment::class)->findOneBy(array('user' => $part));
        $Sejours = $this->em->getRepository(Sejour::class)->findBy(array("idPartenaire" => $part));
        //        dd($Commandes);
        $datePeriode = explode("-", $periode);
        if ($datePeriode[0]) {
            $year = $datePeriode[0];
        }
        if ($datePeriode[1]) {
            $month = $datePeriode[1];
            if ($month == "01" || $month == "03" || $month == "05" || $month == "07" || $month == "08" || $month == "10" || $month == "12") {
                $fday = "01";
                $lday = "31";
            }
            if ($month == "04" || $month == "06" || $month == "09" || $month == "11") {
                $fday = "01";
                $lday = "30";
            }
            if ($month == "02") {
                $intyear = intval($datePeriode[0]);
                $fday = "01";
                if ($intyear % 4 === 0) {
                    $lday = "29";
                } else {
                    $lday = "28";
                }
            }
        }
        $dateDebutPeriode = $fday . '-' . $month . '-' . $year;
        $dateFinPeriode = $lday . '-' . $month . '-' . $year;
        $tabPourcentage = [];
        foreach ($Sejours as $sejour) {
            $Commandes = $this->em->getRepository(Commande::class)->rechercheMyCommandesYeayMonth($sejour->getId(), $periode);
            foreach ($Commandes as $cmd) {
                $listeProduits = $this->em->getRepository(ComandeProduit::class)->findBy(array("idComande" => $cmd->getId()));
                foreach ($listeProduits as $produit) {
                    if (is_null($produit->getPourcentage())) {
                        $pourcentage = "0";
                    } else {
                        $pourcentage = strval($produit->getPourcentage());
                    }
                    array_push($tabPourcentage, $pourcentage);
                }
            }
        }
        $tabPourcentageUnique =  array_values(array_unique($tabPourcentage));
        if (sizeof($tabPourcentageUnique) == 0) {
            array_push($tabPourcentage, "0");
        }
        $TablisteProduits = [];
        foreach ($tabPourcentageUnique as $pourcentage) {
            $Condition1 = $this->em->getRepository(TypeProduitConditionnement::class)->find(4);
            $prixHT = round(($Condition1->getMontantTTC() * 100) / 120, 2);
            ${"montantTTCAlbum_" . strval($pourcentage)} = $prixHT - (($prixHT / 100) * intval($pourcentage));
            $Condition2 = $this->em->getRepository(TypeProduitConditionnement::class)->find(5);
            $prixHT = round(($Condition2->getMontantTTC() * 100) / 120, 2);
            ${"montantTTCLivre_" . strval($pourcentage)} = $prixHT - (($prixHT / 100) * intval($pourcentage));
            //suit
            $Condition4 = $this->em->getRepository(TypeProduitConditionnement::class)->find(1);
            $prixHT = round(($Condition4->getMontantTTC() * 100) / 120, 2);
            ${"montantTTCPhotos12_" . strval($pourcentage)} = $prixHT - (($prixHT / 100) * intval($pourcentage));
            //   $montantTTCPhotos12=round(($Condition4->getMontantTTC()*100)/120,2);
            $Condition5 = $this->em->getRepository(TypeProduitConditionnement::class)->find(2);
            $prixHT = round(($Condition5->getMontantTTC() * 100) / 120, 2);
            ${"montantTTCPhotos24_" . strval($pourcentage)} = $prixHT - (($prixHT / 100) * intval($pourcentage));
            //$montantTTCPhotos24=round(($Condition5->getMontantTTC()*100)/120,2);
            $Condition6 = $this->em->getRepository(TypeProduitConditionnement::class)->find(3);
            $prixHT = round(($Condition6->getMontantTTC() * 100) / 120, 2);
            ${"montantTTCPhotos36_" . strval($pourcentage)} = $prixHT - (($prixHT / 100) * intval($pourcentage));
            //  $montantTTCPhotos36=round(($Condition6->getMontantTTC()*100)/120,2);
            $Condition7 = $this->em->getRepository(TypeProduitConditionnement::class)->find(6);
            $prixHT = round(($Condition7->getMontantTTC() * 100) / 120, 2);
            ${"montantTTCCoffert_" . strval($pourcentage)} = $prixHT - (($prixHT / 100) * intval($pourcentage));
            //$montantTTCCoffert=round(($Condition7->getMontantTTC()*100)/120,2);
            $Condition8 = $this->em->getRepository(TypeProduitConditionnement::class)->find(7);
            $prixHT = round(($Condition8->getMontantTTC() * 100) / 120, 2);
            ${"montantTTCCalendrier_" . strval($pourcentage)} = $prixHT - (($prixHT / 100) * intval($pourcentage));
            //    $montantTTCCalendrier=round(($Condition8->getMontantTTC()*100)/120,2);
            $Condition9 = $this->em->getRepository(TypeProduitConditionnement::class)->find(8);
            $prixHT = round(($Condition9->getMontantTTC() * 100) / 120, 2);
            ${"montantTTCBoxRetros_" . strval($pourcentage)} = $prixHT - (($prixHT / 100) * intval($pourcentage));
            //    $montantTTCBoxRetros=round(($Condition9->getMontantTTC()*100)/120,2);
            $Condition10 = $this->em->getRepository(TypeProduitConditionnement::class)->find(9);
            $prixHT = round(($Condition10->getMontantTTC() * 100) / 120, 2);
            ${"montantTTCPhotosRetros12_" . strval($pourcentage)} = $prixHT - (($prixHT / 100) * intval($pourcentage));
            //   $montantTTCPhotosRetros12=round(($Condition10->getMontantTTC()*100)/120,2);
            $Condition11 = $this->em->getRepository(TypeProduitConditionnement::class)->find(10);
            $prixHT = round(($Condition11->getMontantTTC() * 100) / 120, 2);
            ${"montantTTCPhotosRetros24_" . strval($pourcentage)} = $prixHT - (($prixHT / 100) * intval($pourcentage));
            // $montantTTCPhotosRetros24=round(($Condition11->getMontantTTC()*100)/120,2);
            ${"montantRevPhotos12_" . strval($pourcentage)} = 0;
            ${"montantRevPhotos24_" . strval($pourcentage)} = 0;
            ${"montantRevPhotos36_" . strval($pourcentage)} = 0;
            ${"montantRevAlbumPhotos_" . strval($pourcentage)} = 0;
            ${"montantRevLivreSouvenir_" . strval($pourcentage)} = 0;
            ${"montantRevBoxRetros_" . strval($pourcentage)} = 0;
            ${"montantRevPhotosRetros12_" . strval($pourcentage)} = 0;
            ${"montantRevPhotosRetros24_" . strval($pourcentage)} = 0;
            ${"montantRevCalendrier_" . strval($pourcentage)} = 0;
            ${"montantRevCoffertCadeau_" . strval($pourcentage)} = 0;
            ${"nbPhotos12_" . strval($pourcentage)} = 0;
            ${"nbPhotos24_" . strval($pourcentage)} = 0;
            ${"nbPhotos36_" . strval($pourcentage)} = 0;
            ${"nbAlbum_" . strval($pourcentage)} = 0;
            ${"nbLivre_" . strval($pourcentage)} = 0;
            ${"boxRetros_" . strval($pourcentage)} = 0;
            ${"nbPhotosRetro12_" . strval($pourcentage)} = 0;
            ${"nbPhotosRetro24_" . strval($pourcentage)} = 0;
            ${"calendrier_" . strval($pourcentage)} = 0;
            ${"coffertCadeau_" . strval($pourcentage)} = 0;
        }
        $connexion = 0;
        $montantRevConnexion = 0;
        $Condition12 = $this->em->getRepository(TypeProduitConditionnement::class)->find(11);
        $montantTTCConnexion = round(($Condition12->getMontantTTC() * 100) / 120, 2);
        $nbcc = 0;
        $montatnatRev = 0;
        $nbSejour = 0;
        foreach ($Sejours as $sejour) {
            if (substr($sejour->getCodeSejour(), 1, 1) == "P") {
                $nbSejour++;
                $montatnatRev = $montatnatRev + $sejour->getReversecnxpart();
                $NbC = $this->em->getRepository(ParentSejour::class)->rechercheNbCnxwxYeayMonth($sejour->getId(), $periode);
                //le nbre  nbre de connexion total
                $nbr_cnxx = sizeof($NbC);
                $nbcc += $nbr_cnxx;
                //Prix reversement de connexion selon prix reversement partenaire  par séjour
                $montantRevConnexion += $nbr_cnxx * $montantTTCConnexion * ($sejour->getReversecnxpart() / 100);
            }
            $Commandes = $this->em->getRepository(Commande::class)->rechercheMyCommandesYeayMonth($sejour->getId(), $periode);
            $nbSejour = 0;
            foreach ($Commandes as $cmd) {
                //  $nbSejour++;
                // $montatnatRev = $montatnatRev + $sejour->getReversecnxpart();
                $listeProduits = $this->em->getRepository(ComandeProduit::class)->findBy(array("idComande" => $cmd->getId()));
                foreach ($listeProduits as $produit) {
                    if (is_null($produit->getPourcentage())) {
                        $p = 0;
                    } else {
                        $p = $produit->getPourcentage();
                    }
                    if ($produit->getIdproduit()->getIdConditionnement()->getIdTypeProduit()->getLabeletype() == "Photos") {
                        if ($produit->getIdproduit()->getIdConditionnement()->getSousTitre() == "x 12" && $produit->getIdproduit()->getIdConditionnement()->getId() == 1) {
                            //                            $montantRevPhotos12 = $montantRevPhotos12 + (($produit->getIdproduit()->getIdConditionnement()->getMontantTTC()*100)/120) * $produit->getQuantiter() * ($cmd->getIdSejour()->getReverseventepart() / 100);
                            ${"montantRevPhotos12_" . strval($p)} += ${"montantTTCPhotos12_" . strval($p)} * $produit->getQuantiter() * ($cmd->getIdSejour()->getReverseventepart() / 100);
                            ${"nbPhotos12_" . strval($p)} =  ${"nbPhotos12_" . strval($p)} + $produit->getQuantiter();
                        }
                        if ($produit->getIdproduit()->getIdConditionnement()->getSousTitre() == "x 24" && $produit->getIdproduit()->getIdConditionnement()->getId() == 2) {
                            //                            $montantRevPhotos24 = $montantRevPhotos24 + (($produit->getIdproduit()->getIdConditionnement()->getMontantTTC()*100)/120)* $produit->getQuantiter() * ($cmd->getIdSejour()->getReverseventepart() / 100);
                            ${"montantRevPhotos24_" . strval($p)} += ${"montantTTCPhotos24_" . strval($p)} * $produit->getQuantiter() * ($cmd->getIdSejour()->getReverseventepart() / 100);
                            ${"nbPhotos24_" . strval($p)} = ${"nbPhotos24_" . strval($p)} + $produit->getQuantiter();
                        }
                        if ($produit->getIdproduit()->getIdConditionnement()->getSousTitre() == "x 36" && $produit->getIdproduit()->getIdConditionnement()->getId() == 3) {
                            //                            $montantRevPhotos36 = $montantRevPhotos36 + (($produit->getIdproduit()->getIdConditionnement()->getMontantTTC()*100)/120) * $produit->getQuantiter() * ($cmd->getIdSejour()->getReverseventepart() / 100);
                            ${"montantRevPhotos36_" . strval($p)} += ${"montantTTCPhotos36_" . strval($p)} * $produit->getQuantiter() * ($cmd->getIdSejour()->getReverseventepart() / 100);
                            ${"nbPhotos36_" . strval($p)} = ${"nbPhotos36_" . strval($p)} + $produit->getQuantiter();
                        }
                    }
                    if ($produit->getIdproduit()->getIdConditionnement()->getIdTypeProduit()->getLabeletype() == "Album photos") {
                        //                        $montantRevAlbumPhotos = $montantRevAlbumPhotos + (($produit->getIdproduit()->getIdConditionnement()->getMontantTTC()*100)/120) * $produit->getQuantiter() * ($cmd->getIdSejour()->getReverseventepart() / 100);
                        ${"montantRevAlbumPhotos_" . strval($p)} += ${"montantTTCAlbum_" . strval($p)} * $produit->getQuantiter() * ($cmd->getIdSejour()->getReverseventepart() / 100);
                        ${"nbAlbum_" . strval($p)} = ${"nbAlbum_" . strval($p)} + $produit->getQuantiter();
                    }
                    if ($produit->getIdproduit()->getIdConditionnement()->getIdTypeProduit()->getLabeletype() == "Livre souvenirs") {
                        //                        $montantRevLivreSouvenir = $montantRevLivreSouvenir + (($produit->getIdproduit()->getIdConditionnement()->getMontantTTC()*100)/120) * $produit->getQuantiter() * ($cmd->getIdSejour()->getReverseventepart() / 100);
                        ${"montantRevLivreSouvenir_" . strval($p)} +=   ${"montantTTCLivre_" . strval($p)} * $produit->getQuantiter() * ($cmd->getIdSejour()->getReverseventepart() / 100);
                        ${"nbLivre_" . strval($p)} = ${"nbLivre_" . strval($p)} + $produit->getQuantiter();
                    }
                    if ($produit->getIdproduit()->getIdConditionnement()->getIdTypeProduit()->getLabeletype() == "Box retros ") {
                        //                        $montantRevBoxRetros = $montantRevBoxRetros + (($produit->getIdproduit()->getIdConditionnement()->getMontantTTC()*100)/120) * $produit->getQuantiter() * ($cmd->getIdSejour()->getReverseventepart() / 100);
                        ${"montantRevBoxRetros_" . strval($p)} += ${"montantTTCBoxRetros_" . strval($p)} * $produit->getQuantiter() * ($cmd->getIdSejour()->getReverseventepart() / 100);
                        ${"boxRetros_" . strval($p)} = ${"boxRetros_" . strval($p)} + $produit->getQuantiter();
                    }
                    if ($produit->getIdproduit()->getIdConditionnement()->getIdTypeProduit()->getLabeletype() == "Photos retros") {
                        if ($produit->getIdproduit()->getIdConditionnement()->getSousTitre() == "x 12" && $produit->getIdproduit()->getIdConditionnement()->getId() == 9) {
                            //                            $montantRevPhotosRetros12 = $montantRevPhotosRetros12 + (($produit->getIdproduit()->getIdConditionnement()->getMontantTTC()*100)/120) * $produit->getQuantiter() * ($cmd->getIdSejour()->getReverseventepart() / 100);
                            ${"montantRevPhotosRetros12_" . strval($p)} +=   ${"montantTTCPhotosRetros12_" . strval($p)} * $produit->getQuantiter() * ($cmd->getIdSejour()->getReverseventepart() / 100);
                            ${"nbPhotosRetro12_" . strval($p)} = ${"nbPhotosRetro12_" . strval($p)} + $produit->getQuantiter();
                        }
                        if ($produit->getIdproduit()->getIdConditionnement()->getSousTitre() == "x 24" && $produit->getIdproduit()->getIdConditionnement()->getId() == 10) {
                            //                            $montantRevPhotosRetros24 = $montantRevPhotosRetros24 + (($produit->getIdproduit()->getIdConditionnement()->getMontantTTC()*100)/120) * $produit->getQuantiter() * ($cmd->getIdSejour()->getReverseventepart() / 100);
                            ${"montantRevPhotosRetros24_" . strval($p)} += ${"montantTTCPhotosRetros24_" . strval($p)} * $produit->getQuantiter() * ($cmd->getIdSejour()->getReverseventepart() / 100);
                            ${"nbPhotosRetro24_" . strval($p)} = ${"nbPhotosRetro24_" . strval($p)} + $produit->getQuantiter();
                        }
                    }
                    if ($produit->getIdproduit()->getIdConditionnement()->getIdTypeProduit()->getLabeletype() == "Calendrier chevalet") {
                        //                        $montantRevCalendrier = $montantRevCalendrier + (($produit->getIdproduit()->getIdConditionnement()->getMontantTTC()*100)/120) * $produit->getQuantiter() * ($cmd->getIdSejour()->getReverseventepart() / 100);
                        ${"montantRevCalendrier_" . strval($p)} += ${"montantTTCCalendrier_" . strval($p)} * $produit->getQuantiter() * ($cmd->getIdSejour()->getReverseventepart() / 100);
                        ${"calendrier_" . strval($p)} = ${"calendrier_" . strval($p)} + $produit->getQuantiter();
                    }
                    if ($produit->getIdproduit()->getIdConditionnement()->getIdTypeProduit()->getLabeletype() == "Coffret cadeau") {
                        //                        $montantRevCoffertCadeau = $montantRevCoffertCadeau + (($produit->getIdproduit()->getIdConditionnement()->getMontantTTC()*100)/120) * $produit->getQuantiter() * ($cmd->getIdSejour()->getReverseventepart() / 100);
                        //                       $revUnitaireCoffert=round($montantTTCCoffert* ($cmd->getIdSejour()->getReverseventepart() / 100),2);
                        ${"montantRevCoffertCadeau_" . strval($p)} += ${"montantTTCCoffert_" . strval($p)} * $produit->getQuantiter() * ($cmd->getIdSejour()->getReverseventepart() / 100);
                        ${"coffertCadeau_" . strval($p)} = ${"coffertCadeau_" . strval($p)} + $produit->getQuantiter();
                    }
                }
            }
        }
        //
        //        }
        $montantRevPhotos12 = 0;
        $montantRevPhotos24 = 0;
        $montantRevPhotos36 = 0;
        $montantRevAlbumPhotos = 0;
        $montantRevLivreSouvenir = 0;
        $montantRevBoxRetros = 0;
        $montantRevPhotosRetros12 = 0;
        $montantRevPhotosRetros24 = 0;
        $montantRevCalendrier = 0;
        $montantRevCoffertCadeau = 0;
        foreach ($tabPourcentageUnique as $pourcentage) {
            $montantRevPhotos12 = $montantRevPhotos12 + ${"montantRevPhotos12_" . strval($pourcentage)};
            $montantRevPhotos24 = $montantRevPhotos24 + ${"montantRevPhotos24_" . strval($pourcentage)};
            $montantRevPhotos36 = $montantRevPhotos36 + ${"montantRevPhotos36_" . strval($pourcentage)};
            $montantRevAlbumPhotos = $montantRevAlbumPhotos + ${"montantRevAlbumPhotos_" . strval($pourcentage)};
            $montantRevLivreSouvenir = $montantRevLivreSouvenir + ${"montantRevLivreSouvenir_" . strval($pourcentage)};
            $montantRevBoxRetros = $montantRevBoxRetros + ${"montantRevBoxRetros_" . strval($pourcentage)};
            $montantRevPhotosRetros12 = $montantRevPhotosRetros12 + ${"montantRevPhotosRetros12_" . strval($pourcentage)};
            $montantRevPhotosRetros24 = $montantRevPhotosRetros24 + ${"montantRevPhotosRetros24_" . strval($pourcentage)};
            $montantRevCalendrier = $montantRevCalendrier + ${"montantRevCalendrier_" . strval($pourcentage)};
            $montantRevCoffertCadeau = $montantRevCoffertCadeau + ${"montantRevCoffertCadeau_" . strval($pourcentage)};
        }
        $somme = $montantRevPhotos12 + $montantRevPhotos24 + $montantRevPhotos36 + $montantRevAlbumPhotos + $montantRevLivreSouvenir + $montantRevBoxRetros + $montantRevPhotosRetros12 + $montantRevPhotosRetros24 + $montantRevCalendrier + $montantRevCoffertCadeau + $montantRevConnexion;
        $totalF = round($somme, 2);
        foreach ($tabPourcentageUnique as $pourcentage) {
            $tabProduit[0]["Photos"]["P12"][strval($pourcentage)]["rev"] = round(${"montantRevPhotos12_" . strval($pourcentage)}, 2);
            $tabProduit[0]["Photos"]["P24"][strval($pourcentage)]["rev"] = round(${"montantRevPhotos24_" . strval($pourcentage)}, 2);
            $tabProduit[0]["Photos"]["P36"][strval($pourcentage)]["rev"] = round(${"montantRevPhotos36_" . strval($pourcentage)}, 2);
            $tabProduit[1]["AlbumPhotos"][strval($pourcentage)]["rev"] = round(${"montantRevAlbumPhotos_" . strval($pourcentage)}, 2);
            $tabProduit[2]["LivreSouvenirs"][strval($pourcentage)]["rev"] = round(${"montantRevLivreSouvenir_" . strval($pourcentage)}, 2);
            $tabProduit[3]["BoxRetros"][strval($pourcentage)]["rev"] = round(${"montantRevBoxRetros_" . strval($pourcentage)}, 2);
            $tabProduit[4]["PhotosRetros"]["P12"][strval($pourcentage)]["rev"] = round(${"montantRevPhotosRetros12_" . strval($pourcentage)}, 2);
            $tabProduit[4]["PhotosRetros"]["P24"][strval($pourcentage)]["rev"] = round(${"montantRevPhotosRetros24_" . strval($pourcentage)}, 2);
            $tabProduit[5]["CalendrierChevalet"][strval($pourcentage)]["rev"] = round(${"montantRevCalendrier_" . strval($pourcentage)}, 2);
            $tabProduit[6]["CoffretCadeau"][strval($pourcentage)]["rev"] = round(${"montantRevCoffertCadeau_" . strval($pourcentage)}, 2);
        }
        $tabProduit[7]["connexion"]["rev"] = round($montantRevConnexion, 2);
        if ($nbSejour) {
            $revF = $montatnatRev / $nbSejour;
        } else {
            $revF = 0;
        }
        $tabProduit[7]["connexion"]["pourcentage"] = round($revF, 2);
        foreach ($tabPourcentageUnique as $pourcentage) {
            $tabProduit[0]["Photos"]["P12"][strval($pourcentage)]["montant"] = ${"montantTTCPhotos12_" . strval($pourcentage)};
            $tabProduit[0]["Photos"]["P24"][strval($pourcentage)]["montant"] = ${"montantTTCPhotos24_" . strval($pourcentage)};
            $tabProduit[0]["Photos"]["P36"][strval($pourcentage)]["montant"] = ${"montantTTCPhotos36_" . strval($pourcentage)};
            $tabProduit[1]["AlbumPhotos"][strval($pourcentage)]["montant"] = ${"montantTTCAlbum_" . strval($pourcentage)};
            $tabProduit[2]["LivreSouvenirs"][strval($pourcentage)]["montant"] = ${"montantTTCLivre_" . strval($pourcentage)};
            $tabProduit[3]["BoxRetros"][strval($pourcentage)]["montant"] = ${"montantTTCBoxRetros_" . strval($pourcentage)};
            $tabProduit[4]["PhotosRetros"]["P12"][strval($pourcentage)]["montant"] = ${"montantTTCPhotosRetros12_" . strval($pourcentage)};
            $tabProduit[4]["PhotosRetros"]["P24"][strval($pourcentage)]["montant"] = ${"montantTTCPhotosRetros24_" . strval($pourcentage)};
            $tabProduit[5]["CalendrierChevalet"][strval($pourcentage)]["montant"] = ${"montantTTCCalendrier_" . strval($pourcentage)};
            $tabProduit[6]["CoffretCadeau"][strval($pourcentage)]["montant"] = ${"montantTTCCoffert_" . strval($pourcentage)};
        }
        $tabProduit[7]["connexion"]["montant"] = $montantTTCConnexion;
        foreach ($tabPourcentageUnique as $pourcentage) {
            $tabProduit[0]["Photos"]["P12"][strval($pourcentage)]["qte"] = ${"nbPhotos12_" . strval($pourcentage)};
            $tabProduit[0]["Photos"]["P24"][strval($pourcentage)]["qte"] = ${"nbPhotos24_" . strval($pourcentage)};
            $tabProduit[0]["Photos"]["P36"][strval($pourcentage)]["qte"] = ${"nbPhotos36_" . strval($pourcentage)};
            $tabProduit[1]["AlbumPhotos"][strval($pourcentage)]["qte"] = ${"nbAlbum_" . strval($pourcentage)};
            $tabProduit[2]["LivreSouvenirs"][strval($pourcentage)]["qte"] = ${"nbLivre_" . strval($pourcentage)};
            $tabProduit[3]["BoxRetros"][strval($pourcentage)]["qte"] = ${"boxRetros_" . strval($pourcentage)};
            $tabProduit[4]["PhotosRetros"]["P12"][strval($pourcentage)]["qte"] = ${"nbPhotosRetro12_" . strval($pourcentage)};
            $tabProduit[4]["PhotosRetros"]["P24"][strval($pourcentage)]["qte"] = ${"nbPhotosRetro24_" . strval($pourcentage)};
            $tabProduit[5]["CalendrierChevalet"][strval($pourcentage)]["qte"] = ${"calendrier_" . strval($pourcentage)};
            $tabProduit[6]["CoffretCadeau"][strval($pourcentage)]["qte"] = ${"coffertCadeau_" . strval($pourcentage)};
        }
        $tabProduit[7]["connexion"]["qte"] = $nbcc;
        array_push($TablisteProduits, $tabProduit);
        //        dd($TablisteProduits);
        $ComandeFind = $this->em->getRepository(Commande::class)->rechercherCmdReverPourFacture($part->getId(), $periode);
        $pdfOptions = new Options();
        $pdfOptions->set('isRemoteEnabled', TRUE);
        $pdfOptions->set('defaultFont', 'Arial');
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $dompdf->set_option('isRemoteEnabled', TRUE);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('Admin/PdffactureReversementPartenaire.html.twig', [
            "Commande" => $ComandeFind,
            "listeProduits" => $TablisteProduits,
            "dateFinPeriode" => $dateFinPeriode,
            "dateDebutPeriode" => $dateDebutPeriode,
            'totalF' => $totalF,
            'tabPourcentage' => $tabPourcentageUnique
        ]);
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');
        // Render the HTML as PDF
        $dompdf->render();
        $nompart = "";
        if ($etab->getNometab() != "") {
            $nompart = $etab->getNometab();
        }
        // Store PDF Binary Data
        $output = $dompdf->output();
        // In this case, we want to write the file in the public directory
        $publicDirectory = $this->params->get('kernel.project_dir') . '/public/Facture/';
        // e.g /var/www/project/public/mypdf.pdf
        $pdfFilepath = $publicDirectory . "Appel_Facture_" . $nompart . $part->getId() . '_' . $periode . ".pdf";
        // Write file to the desired path
        file_put_contents($pdfFilepath, $output);
        // Output the generated PDF to Browser (force download)
        $dompdf->stream("Appel_Facture" . $nompart . $part->getId() . '_' . $periode . ".pdf", [
            "Attachment" => true
        ]);
    }
}