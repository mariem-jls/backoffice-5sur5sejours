<?php

namespace App\Service;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Entity\Commande;
use App\Entity\Sejour;
use App\Entity\User;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \DatePeriod;
use \DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class StatistiqueService
{
    private $em;
  private $container;
  private $params;
  public function __construct(ManagerRegistry $em, ContainerInterface $container, ParameterBagInterface $params)
  {
    $this->em = $em;
    $this->container = $container;
    $this->params = $params;
  }
  function genererPhpExcelSuivi()
  {
    $Commande =  $this->em->getRepository(Commande::class)->GetCommandeExcel();
    $spreadsheet = new Spreadsheet();
    /* @var $sheet \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet */
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'num_Comande');
    $sheet->setCellValue('B1', 'Date création');
    $sheet->setCellValue('C1', 'montant');
    $sheet->setCellValue('D1', 'Statut');
    $sheet->setCellValue('E1', 'Produit');
    $sheet->setCellValue('F1', 'Suivi');
    $sheet->setCellValue('G1', 'email');
    $sheet->setCellValue('H1', 'num_Tel');
    $sheet->setCellValue('I1', 'nom');
    $sheet->setCellValue('J1', 'prenom');
    $sheet->setTitle("Commande");
    foreach ($Commande as $key => $cmd) {
      $row = $key + 2;
      $sheet = $spreadsheet->getActiveSheet();
      $sheet->setCellValue('A' . $row, $cmd->getNumComande());
      $sheet->setCellValue('B' . $row, $cmd->getDateCreateCommande());
      $sheet->setCellValue('C' . $row, $cmd->getMontantrth());
      $sheet->setCellValue('D' . $row, $cmd->getStatut()->getLibiller());
      $listProduit = "";
      foreach ($cmd->getCommandesProduits() as $prduit) {
        $listProduit = $listProduit . '//' . $prduit->getIdProduit()->getLabele();
      }
      $sheet->setCellValue('E' . $row, $listProduit);
      $sheet->setCellValue('F' . $row, $cmd->getMontanenv());
      $sheet->setCellValue('G' . $row, $cmd->getIdUser()->getEmail());
      $sheet->setCellValue('H' . $row, $cmd->getIdUser()->getNummobile());
      $sheet->setCellValue('I' . $row, $cmd->getIdUser()->getNom());
      $sheet->setCellValue('J' . $row, $cmd->getIdUser()->getPrenom());
    }
    // Create your Office 2007 Excel (XLSX Format)
    $writer = new Xlsx($spreadsheet);
    // In this case, we want to write the file in the public directory
    $publicDirectory = $this->params->get('kernel.project_dir') . '/public';
    // e.g /var/www/project/public/my_first_excel_symfony4.xlsx
    $excelFilepath =  $publicDirectory . '/commandeliste.xlsx';
    // Create the file
    $writer->save($excelFilepath);
    // Return a text response to the browser saying that the excel was succesfully created
    return "Excel generated succesfully";
  }
  public function getCommandePArSejour($listeDesCommandes)
  {
    $listeSejour = array();
    foreach ($listeDesCommandes as $commande) {
      $code = $commande->getIdSejour()->getCodeSejour();
      array_push($listeSejour, $code);
    }
    $uniqueArray = array_unique($listeSejour);
    return sizeof($uniqueArray);
  }
  public function CalculeCaConnexion($listeParentSejour, $chanttest, $dateDEbut, $dateFinoriginal)
  {
    $caduhourfree = 0;
    $caduhourPay = 0;
    $reversementCaDujour = 0;
    $tabSej = array();
    $tabSejr = array();
    foreach ($listeParentSejour as $parentSejour) {
      if (substr($parentSejour->getIdSejour()->getCodeSejour(), 1, 1) == 'F') {
        if (!(isset($tabSejr[$parentSejour->$chanttest()->format('d/m/Y')]))) {
          $tabSejr[$parentSejour->$chanttest()->format('d/m/Y')] = 1;
        } else {
          $tabSejr[$parentSejour->$chanttest()->format('d/m/Y')] = $tabSejr[$parentSejour->$chanttest()->format('d/m/Y')] + 1;
        }
        $caduhourfree = $caduhourfree + ($parentSejour->getIdSejour()->getPrixcnxpartenaire() * $parentSejour->getFlagPrix());
      } else {
        if ($parentSejour->getPayment() == 1) {
          $caduhourPay = $caduhourPay + ($parentSejour->getIdSejour()->getPrixcnxparent()) * 100 / 120;
          $reversementCaDujour = $reversementCaDujour + (((($parentSejour->getIdSejour()->getPrixcnxparent()) * 100 / 120) * $parentSejour->getIdSejour()->getReversecnxpart()) / 100);
        }
      }
    }
    $dateFin = clone $dateFinoriginal;
    $dateFin->modify('+1 day');
    $period = new DatePeriod(
      $dateDEbut,
      new DateInterval('P1D'),
      $dateFin
    );
    $arrayFInalPay = array();
    $arrayFInalFree = array();
    $arrayFInal = array();
    $arrayFInal['cnxFree'] = array();
    $arrayFInal['cnxFree'] = array();
    foreach ($period as $key => $value) {
      $arrayFInalPay[$value->format('d/m/Y')] = 0;
      $arrayFInalFree[$value->format('d/m/Y')] = 0;
      foreach ($listeParentSejour as $entit) {
        if ($value->format('d/m/Y') == $entit->$chanttest()->format('d/m/Y')) {
          //if (substr($entit->getIdSejour()->getCodeSejour(), 1, 1) == 'F'){
          //   if (array_key_exists($value->format('d/m/Y'), $arrayFInalFree)) {
          //           if($entit->getIdSejour()->getNbenfan() != null && $entit->getIdSejour()->getNbenfan() != 0){
          //             if(!(in_array($entit->$chanttest()->format('d/m/Y'),$tabSej ))){
          //            array_push($tabSej,$entit->$chanttest()->format('d/m/Y'));
          //          if($tabSejr[$entit->$chanttest()->format('d/m/Y')] > $entit->getIdSejour()->getNbenfan() ){
          //       $arrayFInalFree[$value->format('d/m/Y')] = $arrayFInalFree[$value->format('d/m/Y')] + (($entit->getIdSejour()->getNbenfan() )* 1.58);
          //     }
          //   elseif($tabSejr[$entit->$chanttest()->format('d/m/Y')] <= $entit->getIdSejour()->getNbenfan() ){
          // $arrayFInalFree[$value->format('d/m/Y')] = $arrayFInalFree[$value->format('d/m/Y')] + (($tabSejr[$entit->$chanttest()->format('d/m/Y')] )* 1.58);
          // }
          // }
          // }
          // else{
          // $arrayFInalFree[$value->format('d/m/Y')] = $arrayFInalFree[$value->format('d/m/Y')] + (($tabSejr[$entit->$chanttest()->format('d/m/Y')] )* 1.58);
          // }
          //}
          //else{
          //                  if($entit->getIdSejour()->getNbenfan() != null && $entit->getIdSejour()->getNbenfan() != 0){
          //               if(!(in_array($entit->$chanttest()->format('d/m/Y'),$tabSej ))){
          //              array_push($tabSej,$entit->$chanttest()->format('d/m/Y'));
          //            if($tabSejr[$entit->$chanttest()->format('d/m/Y')] > $entit->getIdSejour()->getNbenfan() ){
          //         $arrayFInalFree[$value->format('d/m/Y')] = (($entit->getIdSejour()->getNbenfan() )* 1.58);
          //       }
          //     elseif($tabSejr[$entit->$chanttest()->format('d/m/Y')] <= $entit->getIdSejour()->getNbenfan() ){
          //   $arrayFInalFree[$value->format('d/m/Y')] = (($tabSejr[$entit->$chanttest()->format('d/m/Y')] )* 1.58);
          //  }
          //}
          // }
          //else{
          //   $arrayFInalFree[$value->format('d/m/Y')] = (($tabSejr[$entit->$chanttest()->format('d/m/Y')] )* 1.58);
          //}
          //}
          //         }
          //stoopCom
        }
      }
    }
    // dd($arrayFInalFree);
    //    foreach($arrayFInalFree as $se){
    //      $caduhourfree = $caduhourfree + $se;
    // }
    $caConnexionTotal = $caduhourPay + $caduhourfree;
    return array('cacnxtotal' => $caConnexionTotal, 'cacnxfree' => $caduhourfree, 'cacnxPay' => $caduhourPay, 'reverCnx' => $reversementCaDujour);
  }
  public function CalculeCaConnexionParPart($listeParentSejour, $part, $chanttest, $dateDEbut, $dateFinoriginal)
  {
    $caduhourfree = 0;
    $caduhourPay = 0;
    $reversementCaDujour = 0;
    $tabSej = array();
    $tabSejr = array();
    foreach ($listeParentSejour as $parentSejour) {
      if (substr($parentSejour->getIdSejour()->getCodeSejour(), 1, 1) == 'F') {
        if (!(isset($tabSejr[$parentSejour->$chanttest()->format('d/m/Y')]))) {
          $tabSejr[$parentSejour->$chanttest()->format('d/m/Y')] = 1;
        } else {
          $tabSejr[$parentSejour->$chanttest()->format('d/m/Y')] = $tabSejr[$parentSejour->$chanttest()->format('d/m/Y')] + 1;
        }
        $caduhourfree = $caduhourfree + ($parentSejour->getIdSejour()->getPrixcnxpartenaire() * $parentSejour->getFlagPrix());
      } else {
        if ($parentSejour->getPayment() == 1) {
          $caduhourPay = $caduhourPay + ($parentSejour->getIdSejour()->getPrixcnxparent()) * 100 / 120;
          $reversementCaDujour = $reversementCaDujour + (((($parentSejour->getIdSejour()->getPrixcnxparent()) * 100 / 120) * $parentSejour->getIdSejour()->getReversecnxpart()) / 100);
        }
      }
    }
    $dateFin = clone $dateFinoriginal;
    $dateFin->modify('+1 day');
    $period = new DatePeriod(
      $dateDEbut,
      new DateInterval('P1D'),
      $dateFin
    );
    $arrayFInalPay = array();
    $arrayFInalFree = array();
    $arrayFInal = array();
    $arrayFInal['cnxFree'] = array();
    $arrayFInal['cnxFree'] = array();
    foreach ($period as $key => $value) {
      $arrayFInalPay[$value->format('d/m/Y')] = 0;
      $arrayFInalFree[$value->format('d/m/Y')] = 0;
      foreach ($listeParentSejour as $entit) {
        if ($value->format('d/m/Y') == $entit->$chanttest()->format('d/m/Y')) {
          //if (substr($entit->getIdSejour()->getCodeSejour(), 1, 1) == 'F'){
          //   if (array_key_exists($value->format('d/m/Y'), $arrayFInalFree)) {
          //           if($entit->getIdSejour()->getNbenfan() != null && $entit->getIdSejour()->getNbenfan() != 0){
          //             if(!(in_array($entit->$chanttest()->format('d/m/Y'),$tabSej ))){
          //            array_push($tabSej,$entit->$chanttest()->format('d/m/Y'));
          //          if($tabSejr[$entit->$chanttest()->format('d/m/Y')] > $entit->getIdSejour()->getNbenfan() ){
          //       $arrayFInalFree[$value->format('d/m/Y')] = $arrayFInalFree[$value->format('d/m/Y')] + (($entit->getIdSejour()->getNbenfan() )* 1.58);
          //     }
          //   elseif($tabSejr[$entit->$chanttest()->format('d/m/Y')] <= $entit->getIdSejour()->getNbenfan() ){
          // $arrayFInalFree[$value->format('d/m/Y')] = $arrayFInalFree[$value->format('d/m/Y')] + (($tabSejr[$entit->$chanttest()->format('d/m/Y')] )* 1.58);
          //}
          // }
          //}
          //else{
          //$arrayFInalFree[$value->format('d/m/Y')] = $arrayFInalFree[$value->format('d/m/Y')] + (($tabSejr[$entit->$chanttest()->format('d/m/Y')] )* 1.58);
          // }
          // }
          //else{
          //                  if($entit->getIdSejour()->getNbenfan() != null && $entit->getIdSejour()->getNbenfan() != 0){
          //               if(!(in_array($entit->$chanttest()->format('d/m/Y'),$tabSej ))){
          //              array_push($tabSej,$entit->$chanttest()->format('d/m/Y'));
          //            if($tabSejr[$entit->$chanttest()->format('d/m/Y')] > $entit->getIdSejour()->getNbenfan() ){
          //         $arrayFInalFree[$value->format('d/m/Y')] = (($entit->getIdSejour()->getNbenfan() )* 1.58);
          //       }
          //     elseif($tabSejr[$entit->$chanttest()->format('d/m/Y')] <= $entit->getIdSejour()->getNbenfan() ){
          //    $arrayFInalFree[$value->format('d/m/Y')] = (($tabSejr[$entit->$chanttest()->format('d/m/Y')] )* 1.58);
          //   }
          // }
          //}
          //else{
          //$arrayFInalFree[$value->format('d/m/Y')] = (($tabSejr[$entit->$chanttest()->format('d/m/Y')] )* 1.58);
          // }
          //}
          //         }
          //StopComm
        }
      }
    }
    // dd($arrayFInalFree);
    //    foreach($arrayFInalFree as $se){
    //      $caduhourfree = $caduhourfree + $se;
    //}
    $caConnexionTotal = $caduhourPay + $caduhourfree;
    return array('cacnxtotal' => $caConnexionTotal, 'cacnxfree' => $caduhourfree, 'cacnxPay' => $caduhourPay, 'reverCnx' => $reversementCaDujour);
  }
  public function CalculeCaConnexionParType($listeParentSejour, $Type, $chanttest, $dateDEbut, $dateFinoriginal)
  {
    $caduhourfree = 0;
    $caduhourPay = 0;
    $reversementCaDujour = 0;
    $tabSej = array();
    $tabSejr = array();
    foreach ($listeParentSejour as $parentSejour) {
      if (substr($parentSejour->getIdSejour()->getCodeSejour(), 1, 1) == 'F') {
        if (!(isset($tabSejr[$parentSejour->$chanttest()->format('d/m/Y')]))) {
          $tabSejr[$parentSejour->$chanttest()->format('d/m/Y')] = 1;
        } else {
          $tabSejr[$parentSejour->$chanttest()->format('d/m/Y')] = $tabSejr[$parentSejour->$chanttest()->format('d/m/Y')] + 1;
        }
        $caduhourfree = $caduhourfree + ($parentSejour->getIdSejour()->getPrixcnxpartenaire() * $parentSejour->getFlagPrix());
      } else {
        if ($parentSejour->getPayment() == 1) {
          $caduhourPay = $caduhourPay + ($parentSejour->getIdSejour()->getPrixcnxparent()) * 100 / 120;
          $reversementCaDujour = $reversementCaDujour + (((($parentSejour->getIdSejour()->getPrixcnxparent()) * 100 / 120) * $parentSejour->getIdSejour()->getReversecnxpart()) / 100);
        }
      }
    }
    $dateFin = clone $dateFinoriginal;
    $dateFin->modify('+1 day');
    $period = new DatePeriod(
      $dateDEbut,
      new DateInterval('P1D'),
      $dateFin
    );
    $arrayFInalPay = array();
    $arrayFInalFree = array();
    $arrayFInal = array();
    $arrayFInal['cnxFree'] = array();
    $arrayFInal['cnxFree'] = array();
    foreach ($period as $key => $value) {
      $arrayFInalPay[$value->format('d/m/Y')] = 0;
      $arrayFInalFree[$value->format('d/m/Y')] = 0;
      foreach ($listeParentSejour as $entit) {
        if ($value->format('d/m/Y') == $entit->$chanttest()->format('d/m/Y')) {
          //         if (substr($entit->getIdSejour()->getCodeSejour(), 1, 1) == 'F'){
          //if (array_key_exists($value->format('d/m/Y'), $arrayFInalFree)) {
          //        if($entit->getIdSejour()->getNbenfan() != null && $entit->getIdSejour()->getNbenfan() != 0){
          //          if(!(in_array($entit->$chanttest()->format('d/m/Y'),$tabSej ))){
          //         array_push($tabSej,$entit->$chanttest()->format('d/m/Y'));
          //       if($tabSejr[$entit->$chanttest()->format('d/m/Y')] > $entit->getIdSejour()->getNbenfan() ){
          //    $arrayFInalFree[$value->format('d/m/Y')] = $arrayFInalFree[$value->format('d/m/Y')] + (($entit->getIdSejour()->getNbenfan() )* 1.58);
          //  }
          // elseif($tabSejr[$entit->$chanttest()->format('d/m/Y')] <= $entit->getIdSejour()->getNbenfan() ){
          //$arrayFInalFree[$value->format('d/m/Y')] = $arrayFInalFree[$value->format('d/m/Y')] + (($tabSejr[$entit->$chanttest()->format('d/m/Y')] )* 1.58);
          // }
          //}
          //}
          //else{
          //$arrayFInalFree[$value->format('d/m/Y')] = $arrayFInalFree[$value->format('d/m/Y')] + (($tabSejr[$entit->$chanttest()->format('d/m/Y')] )* 1.58);
          //}
          //}
          //else{
          //                  if($entit->getIdSejour()->getNbenfan() != null && $entit->getIdSejour()->getNbenfan() != 0){
          //
          //             if(!(in_array($entit->$chanttest()->format('d/m/Y'),$tabSej ))){
          //            array_push($tabSej,$entit->$chanttest()->format('d/m/Y'));
          //          if($tabSejr[$entit->$chanttest()->format('d/m/Y')] > $entit->getIdSejour()->getNbenfan() ){
          //       $arrayFInalFree[$value->format('d/m/Y')] = (($entit->getIdSejour()->getNbenfan() )* 1.58);
          //     }
          //   elseif($tabSejr[$entit->$chanttest()->format('d/m/Y')] <= $entit->getIdSejour()->getNbenfan() ){
          // $arrayFInalFree[$value->format('d/m/Y')] = (($tabSejr[$entit->$chanttest()->format('d/m/Y')] )* 1.58);
          //}
          //}
          //}
          //else{
          //$arrayFInalFree[$value->format('d/m/Y')] = (($tabSejr[$entit->$chanttest()->format('d/m/Y')] )* 1.58);
          // }
          //}
          //         }
          //StopComm
        }
      }
    }
    // dd($arrayFInalFree);
    //foreach($arrayFInalFree as $se){
    //  $caduhourfree = $caduhourfree + $se;
    //}
    $caConnexionTotal = $caduhourPay + $caduhourfree;
    return array('cacnxtotal' => $caConnexionTotal, 'cacnxfree' => $caduhourfree, 'cacnxPay' => $caduhourPay, 'reverCnx' => $reversementCaDujour);
  }
  public function getReversementProduit($listeDesCommandes)
  {
    $reversementTotal = 0;
    foreach ($listeDesCommandes as $cmd) {
      $reversementTotal = $reversementTotal + (((($cmd->getMontantrth() - $cmd->getMontanenv()) * 100 / 120) * $cmd->getIdSejour()->getReverseventepart()) / 100);
    }
    return $reversementTotal;
  }
  public function getReversementProduitParPart($listeDesCommandes, $part)
  {
    $reversementTotal = 0;
    foreach ($listeDesCommandes as $cmd) {
      if ($cmd->getIdSejour()->getIdEtablisment()->getId() == $part) {
        $reversementTotal = $reversementTotal + (((($cmd->getMontantrth() - $cmd->getMontanenv()) * 100 / 120) * $cmd->getIdSejour()->getReverseventepart()) / 100);
      }
    }
    return $reversementTotal;
  }
  public function getReversementProduitParType($listeDesCommandes, $Type)
  {
    $reversementTotal = 0;
    foreach ($listeDesCommandes as $cmd) {
      if ($cmd->getIdSejour()->getIdEtablisment()->getTypeetablisment() == $Type) {
        $reversementTotal = $reversementTotal + (((($cmd->getMontantrth() - $cmd->getMontanenv()) * 100 / 120) * $cmd->getIdSejour()->getReverseventepart()) / 100);
      }
    }
    return $reversementTotal;
  }
  public function getSommeProduit($listeDesCommandes)
  {
    $result = array();
    $totalCaProduit = 0;
    $totalEnv = 0;
    $nbrCommande = 0;
    foreach ($listeDesCommandes as $cmd) {
      $totalCaProduit = $totalCaProduit + ($cmd->getMontantrth()) * 100 / 120;
      $totalEnv = $totalEnv + $cmd->getMontanenv();
      $nbrCommande++;
    }
    $result['totalCaProduit'] = $totalCaProduit;
    $result['totalEnv'] = $totalEnv;
    $result['nbrCmd'] = $nbrCommande;
    return $result;
  }
  public function getSommeProduitParPart($listeDesCommandes, $part)
  {
    $result = array();
    $totalCaProduit = 0;
    $totalEnv = 0;
    $nbrCommande = 0;
    foreach ($listeDesCommandes as $cmd) {
      if ($cmd->getIdSejour()->getIdEtablisment()->getId() == $part) {
        $totalCaProduit = $totalCaProduit + ($cmd->getMontantrth()) * 100 / 120;
        $totalEnv = $totalEnv + $cmd->getMontanenv();
        $nbrCommande++;
      }
    }
    $result['totalCaProduit'] = $totalCaProduit;
    $result['totalEnv'] = $totalEnv;
    $result['nbrCmd'] = $nbrCommande;
    return $result;
  }
  public function getSommeProduitParType($listeDesCommandes, $Type)
  {
    $result = array();
    $totalCaProduit = 0;
    $totalEnv = 0;
    $nbrCommande = 0;
    foreach ($listeDesCommandes as $cmd) {
      if ($cmd->getIdSejour()->getIdEtablisment()->getTypeetablisment() == $Type) {
        $totalCaProduit = $totalCaProduit + ($cmd->getMontantrth()) * 100 / 120;
        $totalEnv = $totalEnv + $cmd->getMontanenv();
        $nbrCommande++;
      }
    }
    $result['totalCaProduit'] = $totalCaProduit;
    $result['totalEnv'] = $totalEnv;
    $result['nbrCmd'] = $nbrCommande;
    return $result;
  }
  public function FormaterarrayCABetween($arraytoFormat, $chanttest, $dateDebut, $dateFinoriginal)
{
    $dateFin = clone $dateFinoriginal;
    $dateFin->modify('+1 day');
    
    // Calculate the difference in days between dateDebut and dateFin
    $diffDays = $dateDebut->diff($dateFin)->days;

    // Define an array to map month numbers to French month names
    $months = [
        '01' => 'Janvier',
        '02' => 'Février',
        '03' => 'Mars',
        '04' => 'Avril',
        '05' => 'Mai',
        '06' => 'Juin',
        '07' => 'Juillet',
        '08' => 'Août',
        '09' => 'Septembre',
        '10' => 'Octobre',
        '11' => 'Novembre',
        '12' => 'Décembre',
    ];

    $arrayFinalPay = [];
    $arrayFinalFree = [];
    $arrayFinal = [];
    $arrayFinal['cnxFree'] = [];
    $arrayFinal['cnxPay'] = [];
    $tabSej = [];
    $tabSejr = [];

    foreach ($arraytoFormat as $entit) {
        if (substr($entit->getIdSejour()->getCodeSejour(), 1, 1) == 'F') {
            $dateKey = $entit->$chanttest()->format('d/m/Y');
            if (!isset($tabSejr[$dateKey])) {
                $tabSejr[$dateKey] = 1;
            } else {
                $tabSejr[$dateKey]++;
            }
        }
    }

    if ($diffDays <= 31) {
        // Use days if the difference is less than 31 days
        $period = new DatePeriod(
            $dateDebut,
            new DateInterval('P1D'),
            $dateFin
        );

        foreach ($period as $value) {
            $dateKey = $value->format('d/m/Y');
            $day = (int)$value->format('j');
            $arrayFinalPay[$day] = 0;
            $arrayFinalFree[$day] = 0;
            
            foreach ($arraytoFormat as $entit) {
                if ($dateKey == $entit->$chanttest()->format('d/m/Y')) {
                    if (substr($entit->getIdSejour()->getCodeSejour(), 1, 1) == 'F') {
                        if (!in_array($dateKey, $tabSej)) {
                            array_push($tabSej, $dateKey);
                            if ($tabSejr[$dateKey] > $entit->getIdSejour()->getNbenfan()) {
                                $arrayFinalFree[$day] += $entit->getIdSejour()->getNbenfan() * 1.58;
                            } else {
                                $arrayFinalFree[$day] += $tabSejr[$dateKey] * 1.58;
                            }
                        } else {
                            $arrayFinalFree[$day] += $tabSejr[$dateKey] * 1.58;
                        }
                    } elseif (substr($entit->getIdSejour()->getCodeSejour(), 1, 1) == 'P') {
                        if ($entit->getPayment() == 1) {
                            $arrayFinalPay[$day] += $entit->getIdSejour()->getPrixcnxparent() * 100 / 120;
                        }
                    }
                }
            }
        }
    } else {
        // Use months if the difference is 31 days or more
        $period = new DatePeriod(
            $dateDebut,
            new DateInterval('P1M'),
            $dateFin
        );

        foreach ($period as $value) {
            $monthKey = $value->format('m');
            $monthName = $months[$monthKey];
            $arrayFinalPay[$monthName] = 0;
            $arrayFinalFree[$monthName] = 0;
        }

        foreach ($arraytoFormat as $entit) {
            $monthKey = $entit->$chanttest()->format('m');
            $monthName = $months[$monthKey];
            if (substr($entit->getIdSejour()->getCodeSejour(), 1, 1) == 'F') {
                if (!isset($arrayFinalFree[$monthName])) {
                    $arrayFinalFree[$monthName] = 0;
                }
                if (!in_array($entit->$chanttest()->format('d/m/Y'), $tabSej)) {
                    array_push($tabSej, $entit->$chanttest()->format('d/m/Y'));
                    if ($tabSejr[$entit->$chanttest()->format('d/m/Y')] > $entit->getIdSejour()->getNbenfan()) {
                        $arrayFinalFree[$monthName] += $entit->getIdSejour()->getNbenfan() * 1.58;
                    } else {
                        $arrayFinalFree[$monthName] += $tabSejr[$entit->$chanttest()->format('d/m/Y')] * 1.58;
                    }
                } else {
                    $arrayFinalFree[$monthName] += $tabSejr[$entit->$chanttest()->format('d/m/Y')] * 1.58;
                }
            } elseif (substr($entit->getIdSejour()->getCodeSejour(), 1, 1) == 'P') {
                if (!isset($arrayFinalPay[$monthName])) {
                    $arrayFinalPay[$monthName] = 0;
                }
                if ($entit->getPayment() == 1) {
                    $arrayFinalPay[$monthName] += $entit->getIdSejour()->getPrixcnxparent() * 100 / 120;
                }
            }
        }
    }

    $arrayFinal['cnxFree'] = $arrayFinalFree;
    $arrayFinal['cnxPay'] = $arrayFinalPay;
    return $arrayFinal;
}



  public function FormaterarrayCmdBetween($arraytoFormat, $chanttest, $dateDEbut, $dateFinoriginal)
{
    $dateFin = clone $dateFinoriginal;
    $dateFin->modify('+1 day');
    $interval = $dateDEbut->diff($dateFin);

    if ($interval->y >= 1) {
        // If the difference is a year or more, return the month names
        $months = [];
        $currentDate = clone $dateDEbut;
        while ($currentDate <= $dateFinoriginal) {
            $months[] = $currentDate->format('F');
            $currentDate->modify('+1 month');
        }
        return array_unique($months);
    } elseif ($interval->days <= 31) {
        // If the difference is 31 days, return the list of day numbers
        $days = range(1, 31);
        return $days;
    } else {
        // If the difference is less than a month, return the formatted array
        $period = new DatePeriod(
            $dateDEbut,
            new DateInterval('P1D'),
            $dateFin
        );
        $arrayFInal = array();
        foreach ($period as $key => $value) {
            $arrayFInal[$value->format('d/m/Y')] = 0;
            foreach ($arraytoFormat as $entit) {
                if ($value->format('d/m/Y') == $entit->$chanttest()->format('d/m/Y')) {
                    if (array_key_exists($value->format('d/m/Y'), $arrayFInal)) {
                        $arrayFInal[$value->format('d/m/Y')] = $arrayFInal[$value->format('d/m/Y')] + $entit->getMontantrth();
                    } else {
                        $arrayFInal[$value->format('d/m/Y')] = $entit->getMontantrth();
                    }
                }
            }
        }
        return $arrayFInal;
    }
}



public function FormaterarrayRevCnxBetween($arraytoFormat, $chanttest, $dateDebut, $dateFinoriginal)
{
    $dateFin = clone $dateFinoriginal;
    $dateFin->modify('+1 day');
    
    // Calculate the difference in days between dateDebut and dateFin
    $diffDays = $dateDebut->diff($dateFin)->days;

    // Define an array to map month numbers to French month names
    $months = [
        '01' => 'Janvier',
        '02' => 'Février',
        '03' => 'Mars',
        '04' => 'Avril',
        '05' => 'Mai',
        '06' => 'Juin',
        '07' => 'Juillet',
        '08' => 'Août',
        '09' => 'Septembre',
        '10' => 'Octobre',
        '11' => 'Novembre',
        '12' => 'Décembre',
    ];

    $arrayFinal = [];

    if ($diffDays <= 31) {
        // Use days if the difference is less than 31 days
        $period = new DatePeriod(
            $dateDebut,
            new DateInterval('P1D'),
            $dateFin
        );

        foreach ($period as $value) {
            $day = (int)$value->format('j');
            $arrayFinal[$day] = 0;

            foreach ($arraytoFormat as $entit) {
                if (substr($entit->getIdSejour()->getCodeSejour(), 1, 1) == 'P' && $entit->getPayment() == 1) {
                    if ($value->format('d/m/Y') == $entit->$chanttest()->format('d/m/Y')) {
                        $arrayFinal[$day] += (((($entit->getIdSejour()->getPrixcnxparent()) * 100 / 120) * $entit->getIdSejour()->getReversecnxpart()) / 100);
                    }
                }
            }
        }
    } else {
        // Use months if the difference is 31 days or more
        $period = new DatePeriod(
            $dateDebut,
            new DateInterval('P1M'),
            $dateFin
        );

        foreach ($period as $value) {
            $monthKey = $value->format('m');
            $monthName = $months[$monthKey];
            $arrayFinal[$monthName] = 0;
        }

        foreach ($arraytoFormat as $entit) {
            if (substr($entit->getIdSejour()->getCodeSejour(), 1, 1) == 'P' && $entit->getPayment() == 1) {
                $monthKey = $entit->$chanttest()->format('m');
                $monthName = $months[$monthKey];
                if (!isset($arrayFinal[$monthName])) {
                    $arrayFinal[$monthName] = 0;
                }
                $arrayFinal[$monthName] += (((($entit->getIdSejour()->getPrixcnxparent()) * 100 / 120) * $entit->getIdSejour()->getReversecnxpart()) / 100);
            }
        }
    }

    return $arrayFinal;
}


  public function FormaterarrayRevProdBetween($arraytoFormat, $chanttest, $dateDebut, $dateFinoriginal)
{
    $dateFin = clone $dateFinoriginal;
    $dateFin->modify('+1 day');
    $arrayFinal = array();
    
    // Calculate the difference in days between the start and end dates
    $diffDays = $dateDebut->diff($dateFin)->days;

    // Define an array to map month numbers to French month names
    $months = [
        '01' => 'Janvier',
        '02' => 'Février',
        '03' => 'Mars',
        '04' => 'Avril',
        '05' => 'Mai',
        '06' => 'Juin',
        '07' => 'Juillet',
        '08' => 'Août',
        '09' => 'Septembre',
        '10' => 'Octobre',
        '11' => 'Novembre',
        '12' => 'Décembre',
    ];

    if ($diffDays <= 31) {
        // Use days if the difference is less than 31 days
        $period = new DatePeriod(
            $dateDebut,
            new DateInterval('P1D'),
            $dateFin
        );

        foreach ($period as $key => $value) {
            $dayKey = $value->format('d/m/Y');
            $dayName = $value->format('j');
            $arrayFinal[$dayName] = 0;
            foreach ($arraytoFormat as $entit) {
                if ($dayKey == $entit->$chanttest()->format('d/m/Y')) {
                    if (array_key_exists($dayName, $arrayFinal)) {
                        $arrayFinal[$dayName] += (((($entit->getMontantrth()) * 100 / 120) * $entit->getIdSejour()->getReverseventepart()) / 100);
                    } else {
                        $arrayFinal[$dayName] = (((($entit->getMontantrth()) * 100 / 120) * $entit->getIdSejour()->getReverseventepart()) / 100);
                    }
                }
            }
        }
    } else {
        // Use months if the difference is 31 days or more
        $period = new DatePeriod(
            $dateDebut,
            new DateInterval('P1M'),
            $dateFin
        );

        // Initialize the array with all months in the selected period set to 0
        foreach ($period as $key => $value) {
            $monthName = $months[$value->format('m')];
            if (!array_key_exists($monthName, $arrayFinal)) {
                $arrayFinal[$monthName] = 0;
            }
        }

        foreach ($arraytoFormat as $entit) {
            $monthKey = $entit->$chanttest()->format('m/Y');
            $monthName = $months[$entit->$chanttest()->format('m')];
            if (array_key_exists($monthName, $arrayFinal)) {
                $arrayFinal[$monthName] += (((($entit->getMontantrth()) * 100 / 120) * $entit->getIdSejour()->getReverseventepart()) / 100);
            } else {
                $arrayFinal[$monthName] = (((($entit->getMontantrth()) * 100 / 120) * $entit->getIdSejour()->getReverseventepart()) / 100);
            }
        }
    }

    return $arrayFinal;
}



  
  public function CommandePArSejour($dateDebut, $dateFin5)
  {
    $totSejour   = $this->em->getRepository(Sejour::class)->findSejourListWithAttache($dateFin5);
    $totCommande =    $this->em->getRepository(Commande::class)->ProduitCommandeTotal($dateFin5);
    if (!(isset($totSejour[0][1]))) {
      return 0;
    }
    return $totCommande[0][1] / $totSejour[0][1];
  }
  public function CommandePArSejourParPart($dateDebut, $dateFin6, $part)
  {
    $totSejour  =  $this->em->getRepository(Sejour::class)->findSejourListWithAttacheParPart($dateFin6, $part);
    $totCommande  = $this->em->getRepository(Commande::class)->ProduitCommandeTotalParPArt($dateFin6, $part);
    if (!(isset($totSejour[0][1]))) {
      return 0;
    }
    return $totCommande[0][1] / $totSejour[0][1];
  }
  public function CommandePArSejourParType($dateDebut, $dateFin7, $TypePart)
  {
    $totSejour   = $this->em->getRepository(Sejour::class)->findSejourListWithAttacheParType($dateFin7, $TypePart);
    $totCommande =  $this->em->getRepository(Commande::class)->ProduitCommandeTotalPArTypePart($dateFin7, $TypePart);
    if (!(isset($totSejour[0][1]))) {
      return 0;
    }
    return $totCommande[0][1] / $totSejour[0][1];
  }
}
