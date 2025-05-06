<?php

namespace App\Service;

use Unirest;
use ZipArchive;
use App\Entity\Page;
use App\Entity\Sejour;
use App\Entity\Produit;
use App\Entity\Commande;
use App\Entity\CommandeFile;
use App\Entity\Photonsumeriques;
use Exception;
use Qipsius\TCPDFBundle\Controller\TCPDFController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class PrinterService
{
    private $em;
    private $params;
    private $tcpdf;
    private $httpClient;
    private $logger;
    public function __construct(\Doctrine\ORM\EntityManagerInterface $em, ParameterBagInterface $params, TCPDFController $tcpdf, HttpClientInterface $httpClient, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->params = $params;
        $this->tcpdf = $tcpdf;
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }
    function ftpSender()
    {
        $ftp_server = "51.38.179.156";
        $ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
        $src_dir = $this->params->get('projectDir') . '/public/pdfDocs';
        $login = ftp_login($ftp_conn, 'root', 'SCZ6l1k8');
        // open file for reading
        $file = 'nameFile';
        // var_dump($newpath);
        // upload file
        $dst_dir = "";
        if (!(ftp_is_dir($ftp_conn, $dst_dir))) {
            ftp_mkdir($ftp_conn, $dst_dir);
        }
        if (ftp_fput($ftp_conn, $dst_dir . "/" . $file, $src_dir . '/' . $file, FTP_BINARY)) {
            //  echo "Successfully uploaded $file.";
        } else {
            //  echo "Error uploading $file.";
        }
        // close this connection and file handler
        ftp_close($ftp_conn);
        fclose($file);
    }
    function ApiDupliget()
    {
    }
    function csvFormat()
    {
        $projectRoot = $this->params->get('projectDir');
        $filename = $projectRoot . '/public/pdfDocs/example_youssef.csv';
        $fp = fopen($filename, 'w');
        //header
        $header = array();
        $header[] = "ID";
        $header[] = 'noFacture';
        $header[] = 'noCommande';
        $header[] = 'civilite';
        $header[] = 'nom';
        $header[] = 'prenom';
        $header[] = 'mail';
        $header[] = 'adresse';
        $header[] = 'complementAdresse';
        $header[] = 'codePostal';
        $header[] = 'ville';
        $header[] = 'codePays';
        $header[] = 'pays';
        $header[] = 'sejour';
        $header[] = 'typeEnvoi';
        $header[] = 'fichierImpression';
        $header[] = 'albumPhotos';
        $header[] = 'livreSouvenirs';
        $header[] = 'calendrier';
        $header[] = 'polaroid';
        $header[] = 'formatPolaroid';
        $header[] = 'packPhoto';
        $header[] = 'formatPackPhoto';
        $header[] = 'coffretRigide';
        fputcsv($fp, $header, ";");
        $body = array();
        $body[] = "fileCommande->getId()";
        $body[] = "fileCommande->getNoFacture()";
        $body[] = "fileCommande->getNoCommande()";
        $body[] = "fileCommande->getCivilite()";
        $body[] = "fileCommande->getNom()";
        $body[] = "fileCommande->getPrenom()";
        $body[] = "fileCommande->getMail()";
        $body[] = "fileCommande->getAdresse()";
        $body[] = "fileCommande->getComplementAdresse()";
        $body[] = "fileCommande->getCodePostal()";
        $body[] = "fileCommande->getVille()";
        $body[] = "fileCommande->getCodePays()";
        $body[] = "fileCommande->getPays()";
        $body[] = "fileCommande->getSejour()";
        $body[] = "fileCommande->getTypeEnvoi()";
        $body[] = "fileCommande->getFichierImpression()";
        $body[] = "fileCommande-> getAlbumPhotos()";
        $body[] = "fileCommande-> getLivreSouvenirs()";
        $body[] = "fileCommande->getCalendrier()";
        $body[] = "fileCommande->getPolaroid()";
        $body[] = "fileCommande->getFormatPolaroid()";
        $body[] = "fileCommande->getPackPhoto()";
        $body[] = "fileCommande->getFormatPackPhoto()";
        fputcsv($fp, $body, ";");
        fclose($fp);
    }
    function code($id)
    {
        ini_set("max_execution_time", -1);
        $pageLayout = array(21, 15);
        $pdf = $this->tcpdf->create('L', 'CM', $pageLayout, true, 'UTF-8', false);
        //$pdf = $this->container->get("white_october.tcpdf")->create('L', 'CM', $pageLayout, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Nicola Asuni');
        $pdf->SetTitle('TCPDF Example 009');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        //        $pdf->AddPage();
        $pdf->setJPEGQuality(200);
        $horizontal_alignments = array('L', 'C', 'R');
        $vertical_alignments = array('T', 'M', 'B');
        //var_dump($pdf->getPageWidth());
        //var_dump($pdf->getPageHeight());die();
        $em = $this->em;
        $Album = $em->getRepository(Produit::class)->findOneBy(['id' => $id]);
        $AllPages = $em->getRepository(Page::class)->findBy(['idproduit' => $Album]);
        //dd($AllPages);
        //        $fP=[];
        //        array_push($fP,$AllPages[0]);
        //dd($AllPages);
        foreach ($AllPages as $p) {
            if ($AllPages[sizeof($AllPages) - 1] == $p) {
                $pdf->AddPage();
                $bMargin = $pdf->getBreakMargin();
                $auto_page_break = $pdf->getAutoPageBreak();
                $pdf->SetAutoPageBreak(false, 0);
                $pdf->setJPEGQuality(200);
                $style = array(
                    'position' => '',
                    'align' => 'C',
                    'stretch' => false,
                    'fitwidth' => true,
                    'cellfitalign' => '',
                    'border' => false,
                    'hpadding' => 'auto',
                    'vpadding' => 'auto',
                    'fgcolor' => array(0, 0, 0),
                    'bgcolor' => false, //array(255,255,255),
                    'text' => true,
                    'font' => 'helvetica',
                    'fontsize' => 6,
                    'stretchtext' => 1
                );
                var_dump("hello");
                $exist = false;
                $path = 'https://res.cloudinary.com/af5sur5sejour/image/upload/v1589679918/newprod/logoHeader_yirn8n.png';
                if ($Album->getIdsjour()->getIdPartenaire() != null) {
                    if (($Album->getIdsjour()->getIdPartenaire()->getLogourl() != null) &&  (!preg_match('/(?:GIF|gif)$/i', $Album->getIdsjour()->getIdPartenaire()->getLogourl())) && (trim(($Album->getIdsjour()->getIdPartenaire()->getLogourl() != null)) != "")) {
                        $pdf->SetFont('helvetica', '', 15);
                        $pdf->SetXY(5, 4);
                        $pdf->write(1.5, 'Gardez un souvenir de votre voyage avec');
                        $pdf->SetXY(8, 7);
                        $path = $Album->getIdsjour()->getIdPartenaire()->getLogourl();
                        $path = preg_replace('/(?:png|gif)$/i', 'jpg', $path);
                        $pdf->Image($path, 7.5, 6, 6, 5, 'JPG', '', '', false, 200, '', false, false, 1, false, false, false);
                        $exist = true;
                    }
                }
                if (!($exist)) {
                    $widhtL = 294 * 0.0264583333;
                    $hightL = 110 * 0.0264583333;
                    $xL = (21 - $widhtL) / 2;
                    $yL = (15 - $hightL) / 2;
                    $path = 'https://res.cloudinary.com/af5sur5sejour/image/upload/v1589679918/newprod/logoHeader_yirn8n.png';
                    $pdf->Image($path, $xL, $yL, $widhtL, $hightL, 'PNG', '', '', false, 200, '', false, false, 1, false, false, false);
                }
                // PRINT VARIOUS 1D BARCODES
                $pdf->SetFont('helvetica', '', 4);
                $pdf->StartTransform();
                $pdf->Rotate(270, 19, 12.5);
                $pdf->write1DBarcode($numCommande, 'C39', 19, 12.5, 3.5, 1.5, 0.4, $style, 'N');
                $pdf->StopTransform();
            }
        }
        $projectRoot = $this->params->get('projectDir');
        $pdf->Output($projectRoot . '/public/pdfDocs/example_' . $id . '.pdf', 'F');
    }
    function createCSVCommande($id)
    {
        $this->logger->notice('Looking up {commandId} in Database...', [
            'commandId' => $id,
        ]);
        $commande = $this->em->getRepository(Commande::class)->find($id);
        if ($commande) {
            $this->logger->notice('Found!');
        }
        $projectRoot = $this->params->get('projectDir');
        $filename = $projectRoot . '/public/pdfDocs/' . $commande->getNumComande() . '.csv';
        $this->logger->notice('Generating {filename}', [
            'filename' => $filename,
        ]);
        $fp = fopen($filename, 'w');
        //header
        $header = array();
        $header[] = 'ID';
        $header[] = 'noFacture';
        $header[] = 'noCommande';
        $header[] = 'civilite';
        $header[] = 'nom';
        $header[] = 'prenom';
        $header[] = 'mail';
        $header[] = 'adresse';
        $header[] = 'complementAdresse';
        $header[] = 'codePostal';
        $header[] = 'ville';
        $header[] = 'codePays';
        $header[] = 'pays';
        $header[] = 'sejour';
        $header[] = 'typeEnvoi';
        $header[] = 'fichierImpression';
        $header[] = 'albumPhotos';
        $header[] = 'livreSouvenirs';
        $header[] = 'calendrier';
        $header[] = 'polaroid';
        $header[] = 'formatPolaroid';
        $header[] = 'packPhoto';
        $header[] = 'formatPackPhoto';
        $header[] = 'coffretRigide';
        fputcsv($fp, $header, ";");
        $produits = $commande->getCommandesProduits();
        foreach ($produits as $produitcmd) {
            $prdt = $produitcmd->getIdProduit();
            $fileCommande = new CommandeFile();
            $fileCommande->setNoFacture($commande->getNumfacture());
            $fileCommande->setNoCommande($commande->getNumComande());
            $fileCommande->setCivilite("Mr"); // idont have
            $nomadrres = '';
            $prenomadress = '';
            $Codepostal = '';
            $ville = '';
            $pays = '';
            $rueVoie = '';
            $numadress = '';
            if ($commande->getAdresslivraison()) {
                $nomadrres = $commande->getAdresslivraison()->getNomadrres();
                $prenomadress = $commande->getAdresslivraison()->getPrenomadress();
                $Codepostal = $commande->getAdresslivraison()->getCodepostal();
                $ville = $commande->getAdresslivraison()->getVille();
                $pays = $commande->getAdresslivraison()->getPays();
                $rueVoie = $commande->getAdresslivraison()->getRuevoi();
                $numadress = $commande->getAdresslivraison()->getNumadress();
            }
            $fileCommande->setNom($nomadrres);
            $fileCommande->setPrenom($prenomadress);
            $fileCommande->setMail($commande->getIdUser()->getEmail());
            $fileCommande->setAdresse(str_replace(',', ' ', $numadress) . ' ' . str_replace(',', ' ', $rueVoie)); //null in data base
            //$fileCommande->setAdresse('14, rue Maurice BOYAU');
            $fileCommande->setComplementAdresse(''); //null in data base
            $fileCommande->setCodePostal($Codepostal); // null in data base
            //$fileCommande->setCodePostal(91220);
            $fileCommande->setVille($ville); //null in data base
            //$fileCommande->setVille('BRETIGNY SUR ORGE');
            $fileCommande->setCodePays('FR'); //idont HAve
            $fileCommande->setPays(strtoupper($pays)); //null in data base
            //$fileCommande->setPays("FRANCE");
            $fileCommande->setSejour($commande->getIdSejour()->getCodeSejour());
            if ($commande->getMontanenv() == 6 || $commande->getMontanenv() == 1) {
                $fileCommande->setTypeEnvoi("express"); // i dont have
                //$fileCommande->setFichierImpression();
            } else {
                $fileCommande->setTypeEnvoi("normal"); // i dont have
            }
            #  $fileCommande->setProduit();
            $fileCommande->setAlbumPhotos('0');
            $fileCommande->setLivreSouvenirs('0');
            $fileCommande->setCalendrier('0');
            $fileCommande->setPolaroid('0');
            $fileCommande->setFormatPolaroid('0');
            $fileCommande->setPackPhoto('0');
            $fileCommande->setFormatPackPhoto('0');
            $fileCommande->setCoffretRigide('0');
            if ($prdt->getType()->getId() == 2) {
                $fileCommande->setAlbumPhotos($produitcmd->getQuantiter());
                if ($prdt->getVersion() == "new") {
                    $name = $this->TcPdf($prdt->getId(), $commande->getNumComande(), 'Album');
                } else {
                    $name = $this->TcPdfOldVersion($prdt->getId(), $commande->getNumComande(), 'Album');
                }
                //  Album photos
            }
            if ($prdt->getType()->getId() == 4) {
                $fileCommande->setLivreSouvenirs($produitcmd->getQuantiter());
                // Livre souvenirs
                if ($prdt->getVersion() == "new") {
                    $name = $this->TcPdf($prdt->getId(), $commande->getNumComande(), 'Livre');
                } else {
                    $name = $this->TcPdfOldVersion($prdt->getId(), $commande->getNumComande(), 'Livre');
                }
            }
            if ($prdt->getType()->getId() == 10) {
                $fileCommande->setPackPhoto($produitcmd->getQuantiter());
                $pages = $this->em->getRepository(Page::class)->findBy(array("idproduit" => $prdt));
                $fileCommande->setFormatPackPhoto(sizeOf($pages));
                // Photos
                $this->logger->notice('Generating PDF for Pack photos');
                $name = $this->TcPdfPhoto($prdt->getId(), $commande->getNumComande(), 'Photo');
                dump($name);
                dump($prdt->getId());
            }
            if ($prdt->getType()->getId() == 15) {
                // Coffret cadeau
                $fileCommande->setCoffretRigide($produitcmd->getQuantiter());
                $name = "";
            }
            if ($prdt->getType()->getId() == 16) {
                // Calendrier chevalet
                $fileCommande->setCalendrier($produitcmd->getQuantiter());
                $name = $this->TcPdfCalendrier($prdt->getId(), $commande->getNumComande(), 'Calendrier');
            }
            if ($prdt->getType()->getId() == 17) {
                // Box retro
                $fileCommande->setPolaroid($produitcmd->getQuantiter());
                $pages = $this->em->getRepository(Page::class)->findBy(array("idproduit" => $prdt));
                $fileCommande->setFormatPolaroid(sizeOf($pages));
                $name = $this->TcPdfPhotoR($prdt->getId(), $commande->getNumComande(), 'PhotoRetro');
            }
            if ($prdt->getType()->getId() == 18) {
                // Photos Retros
                $fileCommande->setPolaroid($produitcmd->getQuantiter());
                $pages = $this->em->getRepository(Page::class)->findBy(array("idproduit" => $prdt));
                $fileCommande->setFormatPolaroid(sizeOf($pages));
                $name = $this->TcPdfPhotoR($prdt->getId(), $commande->getNumComande(), 'PhotoRetro');
            }
            $fileCommande->setFichierImpression($name);
            $this->em->persist($fileCommande);
            $this->em->flush();
            $body = array();
            $body[] = $fileCommande->getId();
            $body[] = $fileCommande->getNoFacture();
            $body[] = $fileCommande->getNoCommande();
            $body[] = $fileCommande->getCivilite();
            $body[] = $fileCommande->getNom();
            $body[] = $fileCommande->getPrenom();
            $body[] = $fileCommande->getMail();
            $body[] = $fileCommande->getAdresse();
            $body[] = $fileCommande->getComplementAdresse();
            $body[] = $fileCommande->getCodePostal();
            $body[] = $fileCommande->getVille();
            $body[] = $fileCommande->getCodePays();
            $body[] = $fileCommande->getPays();
            $body[] = $fileCommande->getSejour();
            $body[] = $fileCommande->getTypeEnvoi();
            $body[] = $fileCommande->getFichierImpression();
            $body[] = $fileCommande->getAlbumPhotos();
            $body[] = $fileCommande->getLivreSouvenirs();
            $body[] = $fileCommande->getCalendrier();
            $body[] = $fileCommande->getPolaroid();
            $body[] = $fileCommande->getFormatPolaroid();
            $body[] = $fileCommande->getPackPhoto();
            $body[] = $fileCommande->getFormatPackPhoto();
            $body[] = $fileCommande->getCoffretRigide();
            //header('Content-type: application/csv');
            //header('Content-Disposition: attachment; filename=' . $filename);
            fputcsv($fp, $body, ";");
        }
        fclose($fp);
        return $filename;
    }
    public function TcPdf($idPrdt, $numCommande, $type)
    {
        ini_set("max_execution_time", -1);
        $em = $this->em;
        $Album = $em->getRepository(Produit::class)->findOneBy(['id' => $idPrdt]);
        $AllPages = $em->getRepository(Page::class)->findBy(['idproduit' => $Album]);
        if (sizeof($AllPages) > 24) {
            $pageLayout = array(22, 15.85);
        } else {
            $pageLayout = array(22, 16);
        }
        $pdf = $this->tcpdf->create('L', 'CM', $pageLayout, true, 'UTF-8', false);
        //$pdf = $this->container->get("white_october.tcpdf")->create('L', 'CM', $pageLayout, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Nicola Asuni');
        $pdf->SetTitle('TCPDF Example 009');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        //        $pdf->AddPage();
        $pdf->setJPEGQuality(200);
        $horizontal_alignments = array('L', 'C', 'R');
        $vertical_alignments = array('T', 'M', 'B');
        //var_dump($pdf->getPageWidth());
        //var_dump($pdf->getPageHeight());die();
        //dd($AllPages);
        //        $fP=[];
        //        array_push($fP,$AllPages[0]);
        //dd($AllPages);
        foreach ($AllPages as $p) {
            $contenu = json_decode(json_decode($p->getCouleurbordure())[0]);
            $nbatach = $contenu->nbrAttc;
            //var_dump("nbrattcha________________" . $nbatach);
            if (($AllPages[sizeof($AllPages) - 1] == $p) && (($nbatach == "\"last\"") || ($nbatach == '0'))) {
                $pdf->AddPage();
                $bMargin = $pdf->getBreakMargin();
                $auto_page_break = $pdf->getAutoPageBreak();
                $pdf->SetAutoPageBreak(false, 0);
                $pdf->setJPEGQuality(200);
                $color = $contenu->color;
                //var_dump('color txt'.$color);
                $colorTxt = array();
                // $color="rgb(255,255,255)";
                $color = str_replace('"rgb(', '', $color);
                $color = str_replace(')"', '', $color);
                $color = explode(",", $color);
                $colorp = array(intval($color[0]), intval($color[1]), intval($color[2]));
                if (intval($color[0] !== 0)) {
                    $pdf->Rect(0, 0, 22, 16, 'F', array(), $colorp);
                }
                $style = array(
                    'position' => '',
                    'align' => 'C',
                    'stretch' => false,
                    'fitwidth' => true,
                    'cellfitalign' => '',
                    'border' => false,
                    'hpadding' => 'auto',
                    'vpadding' => 'auto',
                    'fgcolor' => array(0, 0, 0),
                    'bgcolor' => false, //array(255,255,255),
                    'text' => true,
                    'font' => 'helvetica',
                    'fontsize' => 6,
                    'stretchtext' => 1
                );
                var_dump("hello");
                $exist = false;
                $path = 'https://res.cloudinary.com/af5sur5sejour/image/upload/v1589679918/newprod/logoHeader_yirn8n.png';
                $path = "https://5sur5sejour.com/Accueil/imagesAccueil/logoHeader.png";
                $path = "http://54.36.104.133/Accueil/imagesAccueil/logoHeader.png";
                if ($Album->getIdsjour()->getIdPartenaire() != null) {
                    if (($Album->getIdsjour()->getIdPartenaire()->getLogourl() != null) && (!preg_match('/(?:GIF|gif)$/i', $Album->getIdsjour()->getIdPartenaire()->getLogourl())) && (trim(($Album->getIdsjour()->getIdPartenaire()->getLogourl() != null)) != "")) {
                        $tabtxt = json_decode($contenu->txt);
                        $tabphoto = json_decode($contenu->attache);
                        for ($i = 0; $i < sizeof($tabtxt); $i++) {
                            $txt = json_decode($tabtxt[$i]);
                            var_dump($txt);
                            $colorTxt = $txt->color;
                            // $color="rgb(255,255,255)";
                            $colorTxt = str_replace('"rgb(', '', $colorTxt);
                            $colorTxt = str_replace(')"', '', $colorTxt);
                            $colorTxt = explode(",", $colorTxt);
                        }
                        $pdf->SetTextColor(intval($colorTxt[0]), intval($colorTxt[1]), intval($colorTxt[2]));
                        $pdf->SetFont('helvetica', '', 15);
                        $pdf->SetXY(6, 4);
                        $pdf->write(1.5, 'Gardez un souvenir de votre voyage avec');
                        $pdf->SetXY(8, 7);
                        $path = $Album->getIdsjour()->getIdPartenaire()->getLogourl();
                        // $path = preg_replace('/(?:png|gif)$/i', 'jpg', $path);
                        if ($path == "https://media.5sur5sejour.com/api/upload/original/logo._627012ab1e988.bmp") {
                            $path = "http://54.36.104.133/upload/original/logo._627012ab1e988.bmp";
                        }
                        var_dump("im here");
                        var_dump($path);
                        if (preg_match('/(?:webp|WEBP)$/i', $path)) {
                            $im = imagecreatefromwebp($path);
                            $basejpeg = str_replace(".webp", ".jpg", basename($path));
                            $path = $this->params->get('projectDir') . "/public/images/" . $basejpeg;
                            if ($im) {
                                imagejpeg($im, $path, 100);
                            }
                        }
                        if (preg_match('/(?:bmp|BMP)$/i', $path)) {
                            $im = imagecreatefrombmp($path);
                            $basejpeg = str_replace(".bmp", ".jpg", basename($path));
                            $path = $this->params->get('projectDir') . "/public/images/" . $basejpeg;
                            if ($im) {
                                imagejpeg($im, $path, 100);
                            }
                        }
                        if (preg_match('/(?:png|PNG)$/i', $path)) {
                            //var_dump("png");
                            $pdf->Image($path, 7.5, 6, 6, 5, 'PNG', '', '', false, 200, '', false, false, 0, false, false, false);
                        } else {
                            //var_dump("jpg");
                            $pdf->Image($path, 7.5, 6, 6, 5, 'JPG', '', '', false, 200, '', false, false, 0, false, false, false);
                        }
                        $exist = true;
                    }
                }
                if (!($exist)) {
                    $widhtL = 294 * 0.0264583333;
                    $hightL = 110 * 0.0264583333;
                    $xL = (22 - $widhtL) / 2;
                    $yL = (16 - $hightL) / 2;
                    $path = 'https://res.cloudinary.com/af5sur5sejour/image/upload/v1589679918/newprod/logoHeader_yirn8n.png';
                    $path = "https://5sur5sejour.com/Accueil/imagesAccueil/logoHeader.png";
                    $path = "http://54.36.104.133/Accueil/imagesAccueil/logoHeader.png";
                    //var_dump("ssl");
                    $pdf->Image($path, $xL, $yL, $widhtL, $hightL, 'PNG', '', '', false, 200, '', false, false, 0, false, false, false);
                }
            } else {
                $pdf->AddPage();
                $pdf->setJPEGQuality(200);
                $contenu = json_decode(json_decode($p->getCouleurbordure())[0]);
                $checkBAck = str_replace('"', '', $nbatach);
                $checkbackground = false;
                if (substr($checkBAck, 1, 1) == 'F') {
                    $checkbackground = true;
                }
                $nbatach = $contenu->nbrAttc;
                $nbatach = intval(str_replace('"', '', $nbatach));
                $color = $contenu->color;
                // $color="rgb(255,255,255)";
                $color = str_replace('"rgb(', '', $color);
                $color = str_replace(')"', '', $color);
                $color = explode(",", $color);
                $colorp = array(intval($color[0]), intval($color[1]), intval($color[2]));
                $tabtxt = json_decode($contenu->txt);
                $tabphoto = json_decode($contenu->attache);
                //
                //dd($tabtxt);
                //         dd($tabtxt);
                //dd($tabphoto);
                $tabClips = json_decode($contenu->clips);
                // dd($tabClips);
                $x = 1.1;
                $y = 1.3;
                $w = 19.8;
                $h = 13.4;
                // get the current page break margin
                $bMargin = $pdf->getBreakMargin();
                // get current auto-page-break mode
                $auto_page_break = $pdf->getAutoPageBreak();
                // disable auto-page-break
                $pdf->SetAutoPageBreak(false, 0);
                // test all combinations of alignments
                $fitbox = $horizontal_alignments[1] . ' ';
                $fitbox[1] = $vertical_alignments[1];
                $pdf->Rect(0, 0, 22, 16, 'F', array(), $colorp);
                //    sizeof($tabphoto)
                for ($i = 0; $i < sizeof($tabphoto); $i++) {
                    $photo = json_decode($tabphoto[$i]);
                    //les coordonnées de l'image réel:
                    $hght = $photo->height;
                    $top = $photo->top;
                    $left = $photo->left;
                    $width = $photo->width;
                    $ordre = $photo->ordre;
                    $zoom = 1;
                    $path = $photo->path;
                    $hght = floatval(str_replace('cm', '', $hght));
                    $top = floatval(str_replace('cm', '', $top));
                    $left = floatval(str_replace('cm', '', $left));
                    $width = floatval(str_replace('cm', '', $width));
                    //les coordonnées dropzone:
                    $heightOriginal = $photo->height;
                    $widthOriginal = $photo->width;
                    $top = $photo->top;
                    $left = $photo->left;
                    $heightOriginal = floatval(str_replace('cm', '', $heightOriginal));
                    $widthOriginal = floatval(str_replace('cm', '', $widthOriginal));
                    $top = floatval(str_replace('cm', '', $top));
                    $left = floatval(str_replace('cm', '', $left));
                    $heightOriginal = $heightOriginal * 37.7952755906;
                    $widthOriginal = $widthOriginal * 37.7952755906;
                    $topOriginal = $top * 37.7952755906;
                    $leftOriginal = $left * 37.7952755906;
                    $heightCrop = $photo->heightCrop;
                    $topCrop = $photo->topCrop;
                    $leftCrop = $photo->leftCrop;
                    $widthCrop = $photo->widthCrop;
                    $path = $photo->path;
                    $heightCrop = floatval(str_replace('cm', '', $heightCrop));
                    $topCrop = floatval(str_replace('cm', '', $topCrop));
                    $leftCrop = floatval(str_replace('cm', '', $leftCrop));
                    $widthCrop = floatval(str_replace('cm', '', $widthCrop));
                    $widthCropPX = $widthCrop * 37.7952755906;
                    $heightCropPX = $heightCrop * 37.7952755906;
                    $topCropPX = $topCrop * 37.7952755906;
                    $leftCropPX = $leftCrop * 37.7952755906;
                    // var_dump("original widh: ".$widthCrop." "."original height : ".$heightCrop);
                    // var_dump("original left: ".$topCrop." "."original top : ".$leftCrop);
                    // var_dump("//00");
                    // var_dump("multip 37 widh: ".$widthCropPX." "."multip 37 height : ".$heightCropPX);
                    // var_dump("multip 37 top: ".$topCropPX." "."multip 37 left : ".$leftCropPX);
                    //Calculer position des images selon nombres images par page:
                    $positionX = 0;
                    $positionY = 0;
                    $widthImg = 0;
                    $heightImg = 0;
                    if ($checkbackground == true) {
                        $positionX = 0.5;
                        $positionY = 0.5;
                        $widthImg = 21;
                        $heightImg = 15;
                    } elseif ($nbatach == 1) {
                        $positionX = 2;
                        $positionY = 2;
                        $widthImg = 18;
                        $heightImg = 12;
                    } elseif ($nbatach == 2) {
                        if ($ordre == 1) {
                            $positionX = 2;
                            $positionY = 2;
                            $widthImg = 8.7;
                            $heightImg = 12;
                        }
                        if ($ordre == 2) {
                            $positionX = 11.3;
                            $positionY = 2;
                            $widthImg = 8.7;
                            $heightImg = 12;
                        }
                    } elseif ($nbatach == 3) {
                        if ($ordre == 1) {
                            $positionX = 2;
                            $positionY = 2;
                            $widthImg = 8.7;
                            $heightImg = 12;
                        }
                        if ($ordre == 2) {
                            $positionX = 11.3;
                            $positionY = 2;
                            $widthImg = 8.7;
                            $heightImg = 5.7;
                        }
                        if ($ordre == 3) {
                            $positionX = 11.3;
                            $positionY = 8.3;
                            $widthImg = 8.7;
                            $heightImg = 5.7;
                        }
                    } elseif ($nbatach == 4) {
                        if ($ordre == 1) {
                            $positionX = 2;
                            $positionY = 2;
                            $widthImg = 8.7;
                            $heightImg = 5.7;
                        }
                        if ($ordre == 2) {
                            $positionX = 2;
                            $positionY = 8.3;
                            $widthImg = 8.7;
                            $heightImg = 5.7;
                        }
                        if ($ordre == 3) {
                            $positionX = 11.3;
                            $positionY = 2;
                            $widthImg = 8.7;
                            $heightImg = 5.7;
                        }
                        if ($ordre == 4) {
                            $positionX = 11.3;
                            $positionY = 8.3;
                            $widthImg = 8.7;
                            $heightImg = 5.7;
                        }
                    } elseif ($nbatach == 5) {
                        if ($ordre == 1) {
                            $positionX = 2;
                            $positionY = 2;
                            $widthImg = 18;
                            $heightImg = 8;
                        }
                        if ($ordre == 2) {
                            $positionX = 2;
                            $positionY = 10.6;
                            $widthImg = 4.1;
                            $heightImg = 3;
                        }
                        if ($ordre == 3) {
                            $positionX = 6.65;
                            $positionY = 10.6;
                            $widthImg = 4.1;
                            $heightImg = 3;
                        }
                        if ($ordre == 4) {
                            $positionX = 11.3;
                            $positionY = 10.6;
                            $widthImg = 4.1;
                            $heightImg = 3;
                        }
                        if ($ordre == 5) {
                            $positionX = 15.95;
                            $positionY = 10.6;
                            $widthImg = 4.1;
                            $heightImg = 3;
                        }
                    } elseif ($nbatach == 6) {
                        if ($ordre == 1) {
                            $positionX = 2;
                            $positionY = 2;
                            $widthImg = 5.53;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 2) {
                            $positionX = 2;
                            $positionY = 6.2;
                            $widthImg = 5.53;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 3) {
                            $positionX = 2;
                            $positionY = 10.4;
                            $widthImg = 5.53;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 4) {
                            $positionX = 8.13;
                            $positionY = 2;
                            $widthImg = 5.53;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 5) {
                            $positionX = 8.13;
                            $positionY = 6.2;
                            $widthImg = 5.53;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 6) {
                            $positionX = 8.13;
                            $positionY = 10.4;
                            $widthImg = 5.53;
                            $heightImg = 3.6;
                        }
                    } elseif ($nbatach == 12) {
                        if ($ordre == 1) {
                            $positionX = 2;
                            $positionY = 2;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 2) {
                            $positionX = 2;
                            $positionY = 6.2;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 3) {
                            $positionX = 2;
                            $positionY = 10.4;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 4) {
                            $positionX = 6.65;
                            $positionY = 2;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 5) {
                            $positionX = 6.65;
                            $positionY = 6.2;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 6) {
                            $positionX = 6.65;
                            $positionY = 10.4;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 7) {
                            $positionX = 11.3;
                            $positionY = 2;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 8) {
                            $positionX = 11.3;
                            $positionY = 6.2;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 9) {
                            $positionX = 11.3;
                            $positionY = 10.4;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 10) {
                            $positionX = 15.95;
                            $positionY = 2;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 11) {
                            $positionX = 15.95;
                            $positionY = 6.2;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 12) {
                            $positionX = 15.95;
                            $positionY = 10.4;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                    }
                    //Recarder l'image :
                    // $path=str_replace( 'upload/', 'upload/ar_1.1'.',c_crop/q_auto:good/',$path);<
                    //                $path=str_replace( 'upload/', 'upload/ar_1,c_crop,x_'.round($left*37.7952755906).',y_'.round($top*37.7952755906).',w_'.round($widthCropPX).',h_'.round($heightCropPX).',g_north_east/',$path);
                    if (strpos($path, 'res.cloudinary.com') !== false) {
                        $pathArray = explode("/", $path);
                        $idsArray = explode(".", $pathArray[sizeof($pathArray) - 1]);
                        $idImage = "";
                        foreach ($idsArray as $key => $elem) {
                            if ($key != (sizeof($idsArray) - 1)) {
                                $idImage = $idImage . $elem;
                            }
                        }
                        //   $cloudinaryWidht=$widthOriginal;
                        // $cloudinaryHeight=$widthOriginal;
                        $idImage = 'newprod/' . $idImage;
                        //var_dump($idImage);
                        if ((strstr($path, "af5sur5sejour"))) {
                            Unirest\Request::auth('263346742199243', 'jYw-jg0FOJGv89-o5Wo0Fa3rQWU');
                            $url = 'https://api.cloudinary.com/v1_1/af5sur5sejour/resources/image/upload/' . $idImage;
                        } else {
                            Unirest\Request::auth('319835665915435', 'xmlIYw147bjaGTtgk4D4UtiGBlg');
                            $url = 'https://api.cloudinary.com/v1_1/dknprksho/resources/image/upload/' . $idImage;
                        }
                        $headers = array('Accept' => 'application/json');
                        $data = array("public_ids" => array($idImage));
                        $body = Unirest\Request\Body::form($data);
                        Unirest\Request::verifyPeer(false);
                        // $resultMetadata=  \Cloudinary::Api.resources_by_ids([$idImage]);
                        //  var_dump($url);
                        $resultMetadata = Unirest\Request::post($url, $headers, $body);
                        if (isset(json_decode($resultMetadata->raw_body)->width)) {
                            $cloudinaryWidht = json_decode($resultMetadata->raw_body)->width;
                            $cloudinaryHeight = json_decode($resultMetadata->raw_body)->height;
                        } else {
                            $cloudinaryWidht = $widthOriginal;
                            $cloudinaryHeight = $heightOriginal;
                        }
                        $ratiohight = $cloudinaryWidht / $widthOriginal;
                        $ratioHight = $cloudinaryHeight / $heightOriginal;
                        //var_dump('width rat rat ' .$ratiohight .' hight ratio  '. $ratioHight);
                        //$cloudinaryHeight=$cloudinaryHeight*$zoom;
                        //$cloudinaryWidht=$cloudinaryWidht*$zoom;
                        //var_dump($cloudinaryWidht);
                        //var_dump($cloudinaryHeight);
                        //var_dump($ratioHight);
                        //var_dump($zoom);
                        //var_dump($leftOriginal);
                        //var_dump('x_' . round(abs($leftOriginal / $zoom) * $ratioHight));
                        //var_dump('y_' . round(abs($topOriginal / $zoom) * $ratiohight));
                        //var_dump('w_' . round(($widthCropPX / $zoom) * $ratioHight));
                        //var_dump('h_' . round(($heightCropPX / $zoom) * $ratiohight));
                        //var_dump($path);i
                        //        $xFormule=($leftOriginal+())
                        $path = str_replace('upload/', 'upload/w_' . round($cloudinaryWidht) . ',h_' . round($cloudinaryHeight) . ',c_scale/x_' . round(abs($leftOriginal / $zoom) * $ratioHight) . ',y_' . round(abs($topOriginal / $zoom) * $ratiohight) . ',w_' . round(($widthCropPX / $zoom) * $ratioHight) . ',h_' . round(($heightCropPX / $zoom) * $ratiohight) . ',c_crop/', $path);
                        //var_dump($path);i
                        //                $path=str_replace( 'upload/', 'upload/w_'.round($widthOriginal).',h_'.round($heightOriginal).'/x_'.round(abs($leftOriginal)).',y_'.round(abs($topOriginal)).',w_'.round($widthCropPX).',h_'.round($heightCropPX).',c_crop/',$path);
                        //var_dump($path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_10,fl_relative.tiled,", '', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_35,fl_relative.tiled,", '', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_10,", '', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_35,", '', $path);
                        //  var_dump("//00");
                        //  var_dump("avant 1.4 widh: ".round($widthCropPX)." "."avant 1.4 height : ".round($heightCropPX));
                        //  var_dump("avant 1.4 top: ".round(abs($top*37.7952755906))." avant 1.4 final left : ".round(abs($left*37.7952755906)));
                        //    var_dump("//00");
                        //   var_dump("final widh: ".Imageround($widthCropPX*1.4)." "."final height : ".round($heightCropPX*1.4));
                        //  var_dump("final top: ".round(abs($top*37.7952755906*1.4))." "."final left : ".round(abs($left*37.7952755906*1.4)));
                        //var_dump($zoom);
                        //var_dump($path);
                        // $path="https://res.cloudinary.com/af5sur5sejour/image/upload/w_691,h_356,c_crop/a_exif/v1587482806/newprod/crepes-au-chocolat_re9wvk.jpg";
                        $path = preg_replace('/(?:png|gif)$/i', 'jpg', $path);
                        $pdf->Image($path, $positionX, $positionY, $widthImg, $heightImg, 'JPG', '', '', false, 200, '', false, false, 0, false, false, false);
                        //                $pdf->Rect($positionX,$positionY ,$widthImg, $heightImg, 'F', array(), array(264,200,67));
                        //
                    } else {
                        //IMAGINARY
                        $pathArray = explode("/", $path);
                        $idsArray =  $pathArray[sizeof($pathArray) - 1];
                        $url = $this->params->get('nodeImaginaryHost') . '/info?url=' . $this->params->get('imaginaryHost') . '/upload/autorate/' . $idsArray;
                        //   $idImage = 'newprod/' . $idImage;
/*                         $headers = array('Accept' => 'application/json');
                        Unirest\Request::verifyPeer(false);
                        $resultMetadata = Unirest\Request::get($url, $headers); */
                        $resultMetadata = $this->httpClient->request('GET', $url);
                        //var_dump($resultMetadata);
                        $statusCodeMeta = $resultMetadata->getStatusCode();
                        if ($statusCodeMeta == 200 && isset(json_decode($resultMetadata->getContent())->width)) {
                            //var_dump("imaginary_metadata");
                            $cloudinaryWidht = json_decode($resultMetadata->getContent())->width;
                            $cloudinaryHeight = json_decode($resultMetadata->getContent())->height;
                        } else {
                            $cloudinaryWidht = $widthOriginal;
                            $cloudinaryHeight = $heightOriginal;
                        }
                        $ratiohight = 0.0;
                        $ratioHight = 0.0;
                        if ($widthOriginal != 0.0) {
                            $ratiohight = $cloudinaryWidht / $widthOriginal;
                        }
                        if ($heightOriginal != 0.0) {
                            $ratioHight = $cloudinaryHeight / $heightOriginal;
                        }
                        if ($widthOriginal == 0.0 && $heightOriginal == 0.0 && $ratiohight == 0.0 && $ratioHight == 0.0) {
                            continue;
                        }
                        //$ratiohight = $cloudinaryWidht / $widthOriginal;
                        //$ratioHight = $cloudinaryHeight / $heightOriginal;
                        //var_dump('width rat rat ' .$ratiohight .' hight ratio  '. $ratioHight); 
                        //var_dump($cloudinaryWidht);
                        //var_dump($cloudinaryHeight);
                        //var_dump($ratioHight);
                        //var_dump($zoom);
                        //var_dump($leftOriginal);
                        //var_dump('x_' . round(abs($leftOriginal / $zoom) * $ratioHight));
                        //var_dump('y_' . round(abs($topOriginal / $zoom) * $ratiohight));
                        //var_dump('w_' . round(($widthCropPX / $zoom) * $ratiohight));
                        //var_dump('h_' . round(($heightCropPX / $zoom) * $ratioHight));
                        $topYImaginary = floor(abs($topOriginal / $zoom) * $ratioHight);
                        $leftXimaginary = floor(abs($leftOriginal / $zoom) * $ratiohight);
                        $widthimaginary = floor(($widthCropPX / $zoom) * $ratiohight);
                        $heigthimaginary = floor(($heightCropPX / $zoom) * $ratioHight);
                        //var_dump('widht all '.$cloudinaryWidht .'crop widhth ' . $widthimaginary .' left point' . $leftXimaginary);                                                                 var_dump('Height all '.$cloudinaryHeight .'crop Height ' . $heigthimaginary .' top point' . $topYImaginary);    
                        if ($cloudinaryWidht < $widthimaginary + $leftXimaginary) {
                            $widthimaginary = $cloudinaryWidht - $leftXimaginary;
                        }
                        if ($cloudinaryHeight < $heigthimaginary + $topYImaginary) {
                            $heigthimaginary = $cloudinaryHeight - $topYImaginary;
                        }
                        $newpath = $this->params->get('nodeImaginaryHost') . '/extract?areawidth=' . $widthimaginary . '&areaheight=' . $heigthimaginary . '&top=' . $topYImaginary . '&left=' . $leftXimaginary . '&url=' . $this->params->get('imaginaryHost') . '/upload/original/' . rawurlencode($idsArray);
                        $path = str_replace('upload/', 'upload/w_' . round($cloudinaryWidht) . ',h_' . round($cloudinaryHeight) . ',c_scale/x_' . round(abs($leftOriginal / $zoom) * $ratioHight) . ',y_' . round(abs($topOriginal / $zoom) * $ratiohight) . ',w_' . round(($widthCropPX / $zoom) * $ratioHight) . ',h_' . round(($heightCropPX / $zoom) * $ratiohight) . ',c_crop/', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_10,fl_relative.tiled,", '', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_35,fl_relative.tiled,", '', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_10,", '', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_35,", '', $path);
                        var_dump($path);
                        var_dump($newpath);
                        //http://51.83.99.222:81/api/upload/w_1600,h_1200,c_scale/x_0,y_80,w_1600,h_1041,c_crop/a_exif/IMG_20201024_152305761%20(Copier)._5fcf3575800e7.jpg
                        //  $path = preg_replace('/(?:png|gif)$/i', 'jpg', $path);
                        $imgdata = file_get_contents($newpath);
                        if (preg_match('/\.(Png|png|PNG)$/', $newpath)) {
                            $pdf->Image('@' . $imgdata, $positionX, $positionY, $widthImg, $heightImg, 'PNG', '', '', false, 200, '', false, false, 0, false, false, false);
                        } else {
                            if (preg_match('/(?:webp|WEBP)$/i', $newpath)) {
                                $im = imagecreatefromwebp($newpath);
                                $basejpeg = str_replace(".webp", ".jpg", basename($newpath));
                                $newpath = $this->params->get('projectDir') . "/public/images/" . $basejpeg;
                                imagejpeg($im, $newpath, 100);
                                $imgdata = file_get_contents($newpath);
                            }
                            if (preg_match('/(?:bmp|BMP)$/i', $newpath)) {
                                $im = imagecreatefrombmp($newpath);
                                $basejpeg = str_replace(".bmp", ".jpg", basename($newpath));
                                $newpath = $this->params->get('projectDir') . "/public/images/" . $basejpeg;
                                imagejpeg($im, $newpath, 100);
                                $imgdata = file_get_contents($newpath);
                            }
                            //tmpYoussef
                            $EXT = "JPG";
                            if (($newpath == "http://54.36.104.133:9090/extract?areawidth=720&areaheight=994&top=285&left=0&url=https://media.5sur5sejour.com/upload/original/Screenshot_20210823-184401_VideoPlayer._6123d1fb26584.jpg") || ($newpath == "http://54.36.104.133:9090/extract?areawidth=701&areaheight=969&top=181&left=0&url=https://media.5sur5sejour.com/upload/original/Screenshot_20210823-185737_VideoPlayer._6123d46d5151b.jpg") || ($newpath == "http://54.36.104.133:9090/extract?areawidth=720&areaheight=994&top=133&left=0&url=https://media.5sur5sejour.com/upload/original/Screenshot_20210823-185846_VideoPlayer._6123d4823a380.jpg")) {
                                $EXT = "PNG";
                            }
                            $pdf->Image('@' . $imgdata, $positionX, $positionY, $widthImg, $heightImg, $EXT, '', '', false, 200, '', false, false, 0, false, false, false);
                        }
                    }
                }
                //    $pdf->Image('https://demo.appsfactor.fr/images/ClipArt_SVG/Etoilerose.svg',3,  2, 19, 13, 'SVG', '', '', false, 300, '', false, false, 0, $fitbox, false, false);
                for ($i = 0; $i < sizeof($tabtxt); $i++) {
                    $txt = json_decode($tabtxt[$i]);
                    var_dump($txt);
                    if (isset($txt->fontSize)) {
                        $fontSize = floatval(str_replace('px', '', $txt->fontSize)) * 0.75;
                    } else {
                        $fontSize = floatval(str_replace('px', '', $txt->{'font-size'})) * 0.75;
                    }
                    //$txt->rotation
                    //       $check = explode(',', $txt->fontFamily);
                    //     $finalFont = "times";
                    $weight = '';
                    //    if (sizeof($check) > 1) {
                    //default
                    //      if (($check[0] == "-apple-system") && ($txt->fontWeight == 400)) {
                    //        $finalFont = "helvetica";
                    //       $weight = '';
                    //  }
                    //classique
                    //  if (($check[0] == "Georgia") && ($txt->fontWeight == 400)) {
                    //     $finalFont = "times";
                    //    $weight = '';
                    //                        }
                    // //creative
                    // if (($check[0] == "-apple-system") && ($txt->fontWeight == 700)) {
                    //    $finalFont = "helveticaB";
                    //   $weight = '';
                    // }
                    // manuscrite
                    // if (($check[0] == "Comic Sans") && ($txt->fontWeight == 400)) {
                    //    $finalFont = "Courier";
                    //    $weight = '';
                    //}
                    //c.s-microsoft.com/static/fonts/segoe-ui/west-european/light/latest.woff2
                    //} else {
                    //baton
                    //   if (($txt->fontFamily == 'Impact') && ($txt->fontWeight == 400)) {
                    //     $finalFont = "helveticaB";
                    //    $weight = 'B';
                    // }
                    //}
                    $finalFont = 'helvetica';
                    $weight = '';
                    $pdf->SetFont($finalFont, $weight, $fontSize);
                    //$leftTxt = $txt->left;
                    //$topTxt = $txt->top;
                    //$heightClips = $txt->height;
                    //$widthClips = $txt->width;
                    //$heightTxt = floatval(str_replace('cm', '', $heightClips));
                    if ($i == 0) {
                        $topTxt = floatval(0.5) + 0.5;
                    } else {
                        $topTxt = 14.2;
                    }
                    $leftTxt = floatval(2);
                    $widthTxt = 18;
                    $pdf->SetXY($leftTxt, $topTxt, true);
                    //                    var_dump($leftTxt);
                    //                  var_dump($topTxt);
                    // var_dump(floatval(str_replace('rad','',$txt->rotation))*57,2958);
                    //                var_dump("text");
                    //$pdf->StartTransform();
                    //   $pdf->Rotate((floatval(str_replace('rad', '', $txt->rotation)) * 57.2958) * -1, $leftTxt + ($widthTxt / 2), $topTxt + ($heightTxt / 2));
                    // $pdf->Rotate(45);
                    //   $pdf->Text($leftTxt, $topTxt, $txt->contenu);
                    if ($colorp == [0, 0, 0]) {
                        $pdf->SetTextColor(255, 255, 255);
                    }
                    $colorTxt = $txt->color;
                    // $color="rgb(255,255,255)";
                    $colorTxt = str_replace('"rgb(', '', $colorTxt);
                    $colorTxt = str_replace(')"', '', $colorTxt);
                    $colorTxt = explode(",", $colorTxt);
                    if (isset($txt->fontSize)) {
                        $fontsize = str_replace("px", "", $txt->fontSize);
                    } else {
                        $fontsize = str_replace("px", "", $txt->{"font-size"});
                    }
                    $txtContent = $this->decodeEmoticons($txt->contenu, $fontsize);
                    //$txtContent=iconv('UTF-8','ISO-8859',$txtContent);
                    $pdf->SetTextColor(intval($colorTxt[0]), intval($colorTxt[1]), intval($colorTxt[2]));
                    //$pdf->Text($leftTxt, $topTxt, $txt->contenu);
                    // $pdf->MultiCell($leftTxt, $topTxt,  $txt->contenu, 0, $ln=0, 'C', 0, '', 0, false, 'C', 'C');
                    //                    $pdf->Write(str_replace('cm', '', $txt->height), trim($txt->contenu));
                    #$txtContent=str_replace('< 3','&lt;img src=\"https://5sur5sejour.com/redheartPDF.png\"  height=\"5px\" /&gt;',$txtContent);
                    # $txtContent=str_replace('< 3','<3',$txtContent);
                    //var_dump($txtContent);
                    if (isset($txt->fontSize)) {
                        $pdf->writeHTML('<p style="color:' . $txt->color . '; text-align:center; font-size:' . $txt->fontSize . '">' . $txtContent . '</p>', true, false, false, false, '');
                    } else {
                        $pdf->writeHTML('<p style="color:' . $txt->color . '; text-align:center; font-size:' . $txt->{"font-size"} . '">' . $txtContent . '</p>', true, false, false, false, '');
                        #$pdf->write($txt->{"font-size"},$txtContent);
                        //                    $fontsize = str_replace("px", "", $txt->{"font-size"});
                    }
                    //$pdf->StopTransform();
                }
                //Positionner text
                //            $pdf->ImageSVG("C:\\Users\\AppsFactor12\\Desktop\\5sur5\\5sur5Sejour\\public\\images\\ClipArt_SVG\\Etoilerose.svg",100,200,500, 500, '', '', '', 0, false);
                //Positionner clipart
                //            dd($tabClips);
                for ($i = 0; $i < sizeof($tabClips); $i++) {
                    $Clips = json_decode($tabClips[$i]);
                    $heightClips = $Clips->height;
                    $topClips = $Clips->top;
                    $leftClips = $Clips->left;
                    $widthClips = $Clips->width;
                    $path = $Clips->path;
                    $pathClips = str_replace('"', '', $path);
                    $heightClips = floatval(str_replace('cm', '', $heightClips));
                    $topClips = floatval(str_replace('cm', '', $topClips)) + 0.5;
                    $leftClips = floatval(str_replace('cm', '', $leftClips)) + 0.5;
                    $widthClips = floatval(str_replace('cm', '', $widthClips));
                    $heightClipsPX = round($heightClips * 37.7952755906);
                    $widthClipsPX = round($widthClips * 37.7952755906);
                    ////                $positionXclips = $positionX + $leftClips;
                    ////                $positionYclips = $positionY + $topClips;
                    //
                    //$pdf->ImageSVG("images/ClipArt_SVG/Ete4.svg",$leftClips,$topClips,$widthClips, $heightClips);
                    //https://res.cloudinary.com/af5sur5sejour/image/private/s--EdExAzx8--/v1588758453/GlobeFooter_c4duua.svg
                    // https://res.cloudinary.com/af5sur5sejour/image/upload/v1588764528/Groupe_113_pcjyj4.png
                    //var_dump($pathClips);
                    $pdf->StartTransform();
                    $pdf->Rotate((floatval(str_replace('rad', '', $Clips->rotation)) * 57.2958) * -1, $leftClips + ($widthClips / 2), $topClips + ($heightClips / 2));
                    $pdf->Image($this->newPAthCLipart($pathClips, $heightClipsPX, $widthClipsPX), $leftClips, $topClips, $widthClips, $heightClips, '', '', '', false, 200);
                    $pdf->StopTransform();
                }
                //Touhemi 08-07:position cut
                ////Touhemi:fin position cut
            }
            if ($AllPages[sizeof($AllPages) - 1] == $p) {
                // PRINT VARIOUS 1D BARCODES
                $style = array(
                    'position' => '',
                    'align' => 'C',
                    'stretch' => false,
                    'fitwidth' => true,
                    'cellfitalign' => '',
                    'border' => false,
                    'hpadding' => 'auto',
                    'vpadding' => 'auto',
                    'fgcolor' => array(0, 0, 0),
                    'bgcolor' => array(255, 255, 255), //false
                    'text' => true,
                    'font' => 'helvetica',
                    'fontsize' => 6,
                    'stretchtext' => 1
                );
                $pdf->SetFont('helvetica', '', 4);
                $pdf->StartTransform();
                $pdf->Rotate(270, 21.6, 10.5);
                $pdf->write1DBarcode($numCommande, 'C39',  21.6, 10.5, 3.5, 1.5, 0.4, $style, 'N');
                $pdf->StopTransform();
            }
            //if ($checkbackground==false){
            if (sizeof($AllPages) > 24) {
                $pdf->cropMark(0.5, 0.5, 0.5, 0.5, 'TL', array(124, 252, 0));
                $pdf->cropMark(21.5, 0.5, 0.5, 0.5, 'TR', array(124, 252, 0));
                $pdf->cropMark(0.5, 15.35, 0.5, 0.5, 'BL', array(124, 252, 0));
                $pdf->cropMark(21.5, 15.35, 0.5, 0.5, 'BR', array(124, 252, 0));
            } else {
                $pdf->cropMark(0.5, 0.5, 0.5, 0.5, 'TL', array(124, 252, 0));
                $pdf->cropMark(21.5, 0.5, 0.5, 0.5, 'TR', array(124, 252, 0));
                $pdf->cropMark(0.5, 15.5, 0.5, 0.5, 'BL', array(124, 252, 0));
                $pdf->cropMark(21.5, 15.5, 0.5, 0.5, 'BR', array(124, 252, 0));
            }
        }
        // echo '</pre>';
        //return new response("yezi");
        $projectRoot = $this->params->get('projectDir');
        $pdf->Output($projectRoot . '/public/pdfDocs/' . $numCommande . '_' . $type . '_' . $idPrdt . '.pdf', 'F');
        return $numCommande . '_' . $type . '_' . $idPrdt . '.pdf';
        //return $pdf->Output('example_009.pdf', 'I');
    }
    function newPAthCLipart($url, $heigh, $width)
    {
        $allImage = [
            'Eclaire.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143379/newprod/clipart/Eclaire_pr0ecu.png',
            'ARcenciel.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143391/newprod/clipart/ARcenciel_grkyap.png',
            'Drapeaux.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143377/newprod/clipart/Drapeaux_zqo8v3.png',
            'Annif.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143391/newprod/clipart/Annif_ihvyoo.png',
            'Basquettes.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143377/newprod/clipart/Basquettes_znjd41.png',
            'Foot.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143382/newprod/clipart/Foot_az6bct.png',
            'Hello.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143383/newprod/clipart/Hello_njqifl.png',
            'Love.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143386/newprod/clipart/Love_q39usk.png',
            'LICORNE.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143385/newprod/clipart/LICORNE_hpomru.png',
            'Noeud.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143388/newprod/clipart/Noeud_mw5a4j.png',
            'Etoilerose.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143380/newprod/clipart/Etoilerose_ewrzff.png',
            'Ete4.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143379/newprod/clipart/Ete4_pdxadk.png',
            'Ete6.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143380/newprod/clipart/Ete6_a3xgpd.png',
            'Ete9.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143380/newprod/clipart/Ete9_s4vllm.png',
            'Hiver1.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143384/newprod/clipart/Hiver1_fekrjz.png',
            'Hiver2.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143384/newprod/clipart/Hiver2_e9iygg.png',
            'Noel2.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143387/newprod/clipart/Noel2_eqvdzr.png',
            'Hiver.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143383/newprod/clipart/Hiver_et04e1.png',
            'Sapin.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143390/newprod/clipart/Sapin_qjkxz6.png',
            'Chaussetet.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143377/newprod/clipart/Chaussetet_xfpt6t.png',
            'Cloches.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143377/newprod/clipart/Cloches_x2v3eo.png',
            'Like.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143387/newprod/clipart/Like_ynxb2u.png',
            'coeurlike.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143379/newprod/clipart/coeurlike_ferv9y.png',
            'Fetedesmeres.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/w_' . $width . '/upload/v1589143383/newprod/clipart/Fetedesmeres_ud9vac.png',
            'Fetedesperes.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/w_' . $width . '/upload/v1589143384/newprod/clipart/Fetedesperes_mltjmo.png',
            'OOPS.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143389/newprod/clipart/OOPS_gj9p1c.png',
            'OOPS_1.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143389/newprod/clipart/OOPS_1_r9a7l6.png',
            'WoW.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143393/newprod/clipart/WoW_ljlqjm.png',
            'Nuage.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143387/newprod/clipart/Nuage_fgomd0.png',
            'Pingouin.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143389/newprod/clipart/Pingouin_ogxb6e.png',
            'Renard.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143378/newprod/clipart/Chouette_mc1ik2.png',
            'Chouette.svg' => 'https://res.cloudinary.com/af5sur5sejour/image/upload/w_' . $width . '/v1589143390/newprod/clipart/Renard_mlhrxt.png'
        ];
        //http://127.0.0.1:8000/images/ClipArt_SVG/LICORNE.svg
        $arryUrls = explode('/', $url);
        return ($allImage[$arryUrls[sizeof($arryUrls) - 1]]);
    }
    public function TcPdfPhoto($idPrdt, $numCommande, $type)
    {
        ini_set("max_execution_time", -1);
        $arrayFiles = [];
        $projectRoot = $this->params->get('projectDir');
        $em = $this->em;
        $pageLayout = array(15, 10);
        $pdf = $this->tcpdf->create('L', 'CM', $pageLayout, true, 'UTF-8', false);
        // set document information
        $this->logger->notice('setting document information');
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Nicola Asuni');
        $pdf->SetTitle('TCPDF Example 009');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->setJPEGQuality(200);
        $horizontal_alignments = array('L', 'C', 'R');
        $vertical_alignments = array('T', 'M', 'B');
        $Album = $em->getRepository(Produit::class)->findOneBy(['id' => $idPrdt]);
        if ($Album) {
            $this->logger->notice('Found Album : {album}', ['album' => $idPrdt]);
        } else {
            $this->logger->warning('Album Not found : {album}', ['album' => $idPrdt]);
        }
        $AllPages = $em->getRepository(Page::class)->findBy(['idproduit' => $Album]);
        if ($AllPages) {
            $this->logger->notice('Found Album pages');
        } else {
            $this->logger->warning('Album pages Not found');
        }
        $this->logger->notice('Generating pages');
        foreach ($AllPages as $keyPage => $p) {
            $pdf->AddPage();
            $pdf->setJPEGQuality(200);
            $contenu = json_decode(json_decode($p->getCouleurbordure())[0]);
            $nbatach = $contenu->nbrAttc;
            $nbatach = intval(str_replace('"', '', $nbatach));
            $tabphoto = json_decode($contenu->attache);
            // disable auto-page-break
            $pdf->SetAutoPageBreak(false, 0);
            // test all combinations of alignments
            $fitbox = $horizontal_alignments[1] . ' ';
            $fitbox[1] = $vertical_alignments[1];
            for ($i = 0; $i < sizeof($tabphoto); $i++) {
                $photo = json_decode($tabphoto[$i]);
                //les coordonnées de l'image réel:
                $hght = $photo->height;
                $top = $photo->top;
                $left = $photo->left;
                $width = $photo->width;
                $zoom = 1;
                $path = $photo->path;
                $hght = floatval(str_replace('cm', '', $hght));
                $top = floatval(str_replace('cm', '', $top));
                $left = floatval(str_replace('cm', '', $left));
                $width = floatval(str_replace('cm', '', $width));
                //les coordonnées dropzone:
                $heightOriginal = $photo->height;
                $widthOriginal = $photo->width;
                $top = $photo->top;
                $left = $photo->left;
                $heightOriginal = floatval(str_replace('cm', '', $heightOriginal));
                $widthOriginal = floatval(str_replace('cm', '', $widthOriginal));
                $top = floatval(str_replace('cm', '', $top));
                $left = floatval(str_replace('cm', '', $left));
                $heightOriginal = $heightOriginal * 37.7952755906;
                $widthOriginal = $widthOriginal * 37.7952755906;
                $topOriginal = $top * 37.7952755906;
                $leftOriginal = $left * 37.7952755906;
                $heightCrop = $photo->heightCrop;
                $topCrop = $photo->topCrop;
                $leftCrop = $photo->leftCrop;
                $widthCrop = $photo->widthCrop;
                $path = $photo->path;
                $heightCrop = floatval(str_replace('cm', '', $heightCrop));
                $topCrop = floatval(str_replace('cm', '', $topCrop));
                $leftCrop = floatval(str_replace('cm', '', $leftCrop));
                $widthCrop = floatval(str_replace('cm', '', $widthCrop));
                $widthCropPX = $widthCrop * 37.7952755906;
                $heightCropPX = $heightCrop * 37.7952755906;
                //Calculer position des images selon nombres images par page:
                $positionX = 0;
                $positionY = 0;
                $widthImg = 0;
                $heightImg = 0;
                if ($nbatach == 1) {
                    $positionX = 0;
                    $positionY = 0;
                    $widthImg = 15;
                    $heightImg = 10;
                }
                //Recarder l'image :
                if (strpos($path, 'res.cloudinary.com') !== false) {
                    $pathArray = explode("/", $path);
                    $idsArray = explode(".", $pathArray[sizeof($pathArray) - 1]);
                    $idImage = "";
                    foreach ($idsArray as $key => $elem) {
                        if ($key != (sizeof($idsArray) - 1)) {
                            $idImage = $idImage . $elem;
                        }
                    }
                    $idImage = 'newprod/' . $idImage;
                    if ((strstr($path, "af5sur5sejour"))) {
                        Unirest\Request::auth('263346742199243', 'jYw-jg0FOJGv89-o5Wo0Fa3rQWU');
                        $url = 'https://api.cloudinary.com/v1_1/af5sur5sejour/resources/image/upload/' . $idImage;
                    } else {
                        Unirest\Request::auth('319835665915435', 'xmlIYw147bjaGTtgk4D4UtiGBlg');
                        $url = 'https://api.cloudinary.com/v1_1/dknprksho/resources/image/upload/' . $idImage;
                    }
                    $headers = array('Accept' => 'application/json');
                    $data = array("public_ids" => array($idImage));
                    $body = Unirest\Request\Body::form($data);
                    Unirest\Request::verifyPeer(false);
                    $resultMetadata = Unirest\Request::post($url, $headers, $body);
                    if (isset(json_decode($resultMetadata->raw_body)->width)) {
                        $cloudinaryWidht = json_decode($resultMetadata->raw_body)->width;
                        $cloudinaryHeight = json_decode($resultMetadata->raw_body)->height;
                    } else {
                        $cloudinaryWidht = $widthOriginal;
                        $cloudinaryHeight = $heightOriginal;
                    }
                    $ratioWidth = $cloudinaryWidht / $widthOriginal;
                    $ratioHight = $cloudinaryHeight / $heightOriginal;
                    //$cloudinaryHeight=$cloudinaryHeight*$zoom;
                    //$cloudinaryWidht=$cloudinaryWidht*$zoom;
                    $zoom = 1;
                    var_dump($cloudinaryWidht);
                    var_dump($cloudinaryHeight);
                    var_dump($ratioHight);
                    var_dump($zoom);
                    var_dump($leftOriginal);
                    var_dump('x_' . round(abs($leftOriginal / $zoom) * $ratioHight));
                    var_dump('y_' . round(abs($topOriginal / $zoom) * $ratioWidth));
                    var_dump('w_' . round(($widthCropPX / $zoom) * $ratioHight));
                    var_dump('h_' . round(($heightCropPX / $zoom) * $ratioWidth));
                    //var_dump($path);i
                    if (stristr($path, '/a_90/')) {
                        $path = str_replace('/a_90/', '/', $path);
                        $path = str_replace('upload/', 'upload/a_90/w_' . round($cloudinaryWidht) . ',h_' . round($cloudinaryHeight) . ',c_scale/x_' . round(abs($leftOriginal / $zoom) * $ratioHight) . ',y_' . round(abs($topOriginal / $zoom) * $ratioWidth) . ',w_' . round(($widthCropPX / $zoom) * $ratioHight) . ',h_' . round(($heightCropPX / $zoom) * $ratioWidth) . ',c_crop/', $path);
                    } else {
                        $path = str_replace('upload/', 'upload/w_' . round($cloudinaryWidht) . ',h_' . round($cloudinaryHeight) . ',c_scale/x_' . round(abs($leftOriginal / $zoom) * $ratioHight) . ',y_' . round(abs($topOriginal / $zoom) * $ratioWidth) . ',w_' . round(($widthCropPX / $zoom) * $ratioHight) . ',h_' . round(($heightCropPX / $zoom) * $ratioWidth) . ',c_crop/', $path);
                    }
                    $path = str_replace("l_Logo5Sur5White_nh6tyk,o_10,fl_relative.tiled,", '', $path);
                    $path = str_replace("l_Logo5Sur5White_nh6tyk,o_35,fl_relative.tiled,", '', $path);
                    $path = str_replace("l_Logo5Sur5White_nh6tyk,o_10,", '', $path);
                    $path = str_replace("l_Logo5Sur5White_nh6tyk,o_35,", '', $path);
                    var_dump($zoom);
                    var_dump($path);
                    $path = preg_replace('/(?:png|gif)$/i', 'jpg', $path);
                    $pdf->Image($path, $positionX, $positionY, $widthImg, $heightImg, 'JPG', '', '', false, 200, '', false, false, 0, false, false, false);
                } else {
                    $this->logger->notice('Generating page {keyPage}...', ['keyPage' => $keyPage]);
                    $pathArray = explode("/", $path);
                    $idsArray = $pathArray[sizeof($pathArray) - 1];
                    $headers = array('Accept' => 'application/json');
                    $url = $this->params->get('nodeImaginaryHost') . '/info?url=' . $this->params->get('imaginaryHost') . '/upload/original/' . $idsArray;
                    $this->logger->notice('Looking up photo using : {url}' . $url, ['url' => $url]);
                    $resultMetadata = $this->httpClient->request('GET', $url);
                    $statusCodeMeta = $resultMetadata->getStatusCode();
                    if ($statusCodeMeta == 200 && isset(json_decode($resultMetadata->getContent())->width)) {
                        $this->logger->notice('Photo metadata from imaginary : {metadata}', ['metadata' => json_decode($resultMetadata->getContent())]);
                        if (stristr($path, '/a_90/')) {
                            if (json_decode($resultMetadata->getContent())->height > json_decode($resultMetadata->getContent())->width) {
                                $cloudinaryWidht = json_decode($resultMetadata->getContent())->height;
                                $cloudinaryHeight = json_decode($resultMetadata->getContent())->width;
                            } else {
                                $cloudinaryWidht = json_decode($resultMetadata->getContent())->width;
                                $cloudinaryHeight = json_decode($resultMetadata->getContent())->height;
                            }
                        } else {
                            $cloudinaryWidht = json_decode($resultMetadata->getContent())->width;
                            $cloudinaryHeight = json_decode($resultMetadata->getContent())->height;
                        }
                    } else {
                        $cloudinaryWidht = $widthOriginal;
                        $cloudinaryHeight = $heightOriginal;
                    }
                    $ratioWidth = 0.0;
                    $ratioHight = 0.0;
                    if ($widthOriginal != 0.0) {
                        $ratioWidth = $cloudinaryWidht / $widthOriginal;
                    }
                    if ($heightOriginal != 0.0) {
                        $ratioHight = $cloudinaryHeight / $heightOriginal;
                    }
                    $zoom = 1;
                    if ($widthOriginal == 0.0 && $heightOriginal == 0.0 && $ratioWidth == 0.0 && $ratioHight == 0.0) {
                        continue;
                    }
                    if (floor(($widthCropPX / $zoom) * $ratioHight) < floor(($heightCropPX / $zoom) * $ratioWidth)) {
                        $ratioWidth = $cloudinaryWidht / $heightOriginal;
                        $ratioHight = $cloudinaryHeight / $widthOriginal;
                        $topYImaginary = floor(abs($topOriginal / $zoom) * $ratioWidth);
                        $leftXimaginary = floor(abs($leftOriginal / $zoom) * $ratioHight);
                        $widthimaginary = floor(($widthCropPX / $zoom) * $ratioHight);
                        $heigthimaginary = floor(($heightCropPX / $zoom) * $ratioWidth);
                    } else {
                        $topYImaginary = floor(abs($topOriginal / $zoom) * $ratioWidth);
                        $leftXimaginary = floor(abs($leftOriginal / $zoom) * $ratioHight);
                        $widthimaginary = floor(($widthCropPX / $zoom) * $ratioHight);
                        $heigthimaginary = floor(($heightCropPX / $zoom) * $ratioWidth);
                    }
                    if ($cloudinaryWidht < $widthimaginary) {
                        $widthimaginary = $cloudinaryWidht - $leftXimaginary;
                    }
                    if ($cloudinaryHeight < $heigthimaginary) {
                        $heigthimaginary = $cloudinaryHeight - $topYImaginary;
                    }
                    if (stristr($path, '/a_90/')) {
                        $newpath = $this->params->get('nodeImaginaryHost') . '/extract?areawidth=' . $widthimaginary . '&areaheight=' . $heigthimaginary . '&top=' . $topYImaginary . '&left=' . $leftXimaginary . '&url=https://media.5sur5sejour.com/upload/originalRotate/' . rawurlencode($idsArray);
                    } else {
                        if ($ratioHight < $ratioWidth) {
                            $newpath = $this->params->get('nodeImaginaryHost') . '/extract?areawidth=' . $heigthimaginary . '&areaheight=' . $widthimaginary . '&top=' . $leftXimaginary . '&left=' . $topYImaginary . '&url=https://media.5sur5sejour.com/upload/originalRotate/' . rawurlencode($idsArray);
                        } else {
                            $newpath = $this->params->get('nodeImaginaryHost') . '/extract?areawidth=' . $widthimaginary . '&areaheight=' . $heigthimaginary . '&top=' . $topYImaginary . '&left=' . $leftXimaginary . '&url=' . $this->params->get('imaginaryHost') . '/upload/original/' . rawurlencode($idsArray);
                        }
                    }
                    if ($ratioHight < $ratioWidth) {
                        $path = str_replace('upload/', 'upload/w_' . round($cloudinaryHeight) . ',h_' . round($cloudinaryWidht) . ',c_scale/x_' . round(abs($topOriginal / $zoom) * $ratioWidth) . ',y_' . round(abs($leftOriginal / $zoom) * $ratioHight) . ',w_' . round(($heightCropPX / $zoom) * $ratioWidth) . ',h_' .  round(($widthCropPX / $zoom) * $ratioHight) . ',c_crop/', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_10,fl_relative.tiled,", '', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_35,fl_relative.tiled,", '', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_10,", '', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_35,", '', $path);
                        $path = $newpath;
                    } else {
                        $path = str_replace('upload/', 'upload/w_' . round($cloudinaryWidht) . ',h_' . round($cloudinaryHeight) . ',c_scale/x_' . round(abs($leftOriginal / $zoom) * $ratioHight) . ',y_' . round(abs($topOriginal / $zoom) * $ratioWidth) . ',w_' . round(($widthCropPX / $zoom) * $ratioHight) . ',h_' . round(($heightCropPX / $zoom) * $ratioWidth) . ',c_crop/', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_10,fl_relative.tiled,", '', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_35,fl_relative.tiled,", '', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_10,", '', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_35,", '', $path);
                        $path = $newpath;
                    }
                }
                $filename = $numCommande . '_' . $idPrdt . '_' . $keyPage . '.jpg';
                array_push($arrayFiles, $filename);
                $this->logger->notice('Saving photo locally...');
                try {
                    file_put_contents($projectRoot . '/public/pdfDocs/' . $numCommande . '_' . $idPrdt . '_' . $keyPage . '.jpg', file_get_contents($path));
                } catch (Exception $e) {
                    $this->logger->notice($e->getMessage());
                }
            }
        }
        $this->logger->notice('Generating Zip file...');
        $zip = new ZipArchive;
        if ($zip->open($projectRoot . '/public/pdfDocs/' . $numCommande . '_' . $type . '_' . $idPrdt . '.zip', ZipArchive::CREATE) === TRUE) {
            // Add files to the zip file
            foreach ($arrayFiles as $fileNames)
                $zip->addFile($projectRoot . '/public/pdfDocs/' . $fileNames, $fileNames);
            $zip->close();
        }
        $this->logger->notice('Done');
        return $numCommande . '_' . $type . '_' . $idPrdt . '.zip';
    }
    public function TcPdfCalendrier($idPrdt, $numCommande, $type)
    {
        ini_set("max_execution_time", -1);
        $pageLayout = array(21, 15);
        $pdf = $this->tcpdf->create('L', 'CM', $pageLayout, true, 'UTF-8', false);
        //$pdf = $this->container->get("white_october.tcpdf")->create('L', 'CM', $pageLayout, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Nicola Asuni');
        $pdf->SetTitle('TCPDF Example 009');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        //        $pdf->AddPage();
        $pdf->setJPEGQuality(200);
        $horizontal_alignments = array('L', 'C', 'R');
        $vertical_alignments = array('T', 'M', 'B');
        //var_dump($pdf->getPageWidth());
        //var_dump($pdf->getPageHeight());die();
        $em = $this->em;
        $Album = $em->getRepository(Produit::class)->findOneBy(['id' => $idPrdt]);
        $AllPages = $em->getRepository(Page::class)->findBy(['idproduit' => $Album]);
        //dd($AllPages);
        //        $fP=[];
        //        array_push($fP,$AllPages[0]);
        //dd($AllPages);
        foreach ($AllPages as $key => $p) {
            if ($key == 1) {
                //                $pdf->AddPage();
                //                $bMargin = $pdf->getBreakMargin();
                //                $auto_page_break = $pdf->getAutoPageBreak();
                //                $pdf->SetAutoPageBreak(false, 0);
                //                $pdf->setJPEGQuality(200);
                //                $style = array(
                //                    'position' => '',
                //                    'align' => 'C',
                //                    'stretch' => false,
                //                    'fitwidth' => true,
                //                    'cellfitalign' => '',
                //                    'border' => false,
                //                    'hpadding' => 'auto',
                //                    'vpadding' => 'auto',
                //                    'fgcolor' => array(0, 0, 0),
                //                    'bgcolor' => false, //array(255,255,255),
                //                    'text' => true,
                //                    'font' => 'helvetica',
                //                    'fontsize' => 6,
                //                    'stretchtext' => 1
                //                );
                //                var_dump("hello logo");
                //
                //
                //                $exist = false;
                //                $path = 'https://res.cloudinary.com/af5sur5sejour/image/upload/v1589679918/newprod/logoHeader_yirn8n.png';
                //                if ($Album->getIdsjour()->getIdPartenaire() != null) {
                //                    if (($Album->getIdsjour()->getIdPartenaire()->getLogourl() != null) && (trim(($Album->getIdsjour()->getIdPartenaire()->getLogourl() != null)) != "")) {
                //                        $pdf->SetFont('helvetica', '', 15);
                //                        $pdf->SetXY(5, 4);
                //                        $pdf->write(1.5, 'Gardez un souvenir de votre voyage avec');
                //                        $pdf->SetXY(8, 7);
                //
                //
                //                        $path = $Album->getIdsjour()->getIdPartenaire()->getLogourl();
                //                        var_dump($path);
                //                    $arrps=explode(".",$path);
                //                        $ext=$arrps[sizeof($arrps)-1];
                //
                //                        $pdf->Image($path, 7.5, 6, 6, 5,strtoupper($ext), '', '', false, 200, '', false, false, 1, false, false, false);
                //
                //                        $exist = true;
                //                    }
                //                }
                //                if (!($exist)) {
                //                    $widhtL = 294 * 0.0264583333;
                //                    $hightL = 110 * 0.0264583333;
                //                    $xL = (21 - $widhtL) / 2;
                //                    $yL = (15 - $hightL) / 2;
                //                    $path = 'https://res.cloudinary.com/af5sur5sejour/image/upload/v1589679918/newprod/logoHeader_yirn8n.png';
                //                    $pdf->Image($path, $xL, $yL, $widhtL, $hightL, 'PNG', '', '', false, 200, '', false, false, 1, false, false, false);
                //                }
            } else {
                $pdf->AddPage();
                $pdf->setJPEGQuality(200);
                $contenu = json_decode(json_decode($p->getCouleurbordure())[0]);
                $nbatachS = $contenu->nbrAttc;
                $nbatach = intval(str_replace('"', '', $nbatachS));
                if (strpos($nbatachS, "H")) {
                    $nbatach = $nbatach . 'H';
                }
                $tabtxt = json_decode($contenu->txt);
                $tabphoto = json_decode($contenu->attache);
                //
                //dd($tabtxt);
                //         dd($tabtxt);
                //dd($tabphoto);
                $tabClips = json_decode($contenu->clips);
                // dd($tabClips);
                $x = 0.6;
                $y = 0.8;
                $w = 19.8;
                $h = 13.4;
                // get the current page break margin
                $bMargin = $pdf->getBreakMargin();
                // get current auto-page-break mode
                $auto_page_break = $pdf->getAutoPageBreak();
                // disable auto-page-break
                $pdf->SetAutoPageBreak(false, 0);
                // test all combinations of alignments
                $fitbox = $horizontal_alignments[1] . ' ';
                $fitbox[1] = $vertical_alignments[1];
                //    sizeof($tabphoto)
                for ($i = 0; $i < sizeof($tabphoto); $i++) {
                    $photo = json_decode($tabphoto[$i]);
                    //les coordonnées de l'image réel:
                    $hght = $photo->height;
                    $top = $photo->top;
                    $left = $photo->left;
                    $width = $photo->width;
                    $ordre = $photo->ordre;
                    $zoom = 1;
                    $path = $photo->path;
                    $hght = floatval(str_replace('cm', '', $hght));
                    $top = floatval(str_replace('cm', '', $top));
                    $left = floatval(str_replace('cm', '', $left));
                    $width = floatval(str_replace('cm', '', $width));
                    //les coordonnées dropzone:
                    $heightOriginal = $photo->height;
                    $widthOriginal = $photo->width;
                    $top = $photo->top;
                    $left = $photo->left;
                    $heightOriginal = floatval(str_replace('cm', '', $heightOriginal));
                    $widthOriginal = floatval(str_replace('cm', '', $widthOriginal));
                    $top = floatval(str_replace('cm', '', $top));
                    $left = floatval(str_replace('cm', '', $left));
                    $heightOriginal = $heightOriginal * 37.7952755906;
                    $widthOriginal = $widthOriginal * 37.7952755906;
                    $topOriginal = $top * 37.7952755906;
                    $leftOriginal = $left * 37.7952755906;
                    $heightCrop = $photo->heightCrop;
                    $topCrop = $photo->topCrop;
                    $leftCrop = $photo->leftCrop;
                    $widthCrop = $photo->widthCrop;
                    $path = $photo->path;
                    $heightCrop = floatval(str_replace('cm', '', $heightCrop));
                    $topCrop = floatval(str_replace('cm', '', $topCrop));
                    $leftCrop = floatval(str_replace('cm', '', $leftCrop));
                    $widthCrop = floatval(str_replace('cm', '', $widthCrop));
                    $widthCropPX = $widthCrop * 37.7952755906;
                    $heightCropPX = $heightCrop * 37.7952755906;
                    $topCropPX = $topCrop * 37.7952755906;
                    $leftCropPX = $leftCrop * 37.7952755906;
                    // var_dump("original widh: ".$widthCrop." "."original height : ".$heightCrop);
                    // var_dump("original left: ".$topCrop." "."original top : ".$leftCrop);
                    // var_dump("//00");
                    // var_dump("multip 37 widh: ".$widthCropPX." "."multip 37 height : ".$heightCropPX);
                    // var_dump("multip 37 top: ".$topCropPX." "."multip 37 left : ".$leftCropPX);
                    //Calculer position des images selon nombres images par page:
                    $positionX = 0;
                    $positionY = 0;
                    $widthImg = 0;
                    $heightImg = 0;
                    var_dump('nbr_attach ' . $nbatach);
                    if ($nbatach == 1) {
                        if ($key == 0) {
                            $positionX = 1.5;
                            $positionY = 1.5;
                            $widthImg = 18;
                            $heightImg = 10;
                        } else {
                            $positionX = 1.5;
                            $positionY = 1.5;
                            $widthImg = 8.7;
                            $heightImg = 12;
                        }
                    } elseif (($nbatach == 2) && ($nbatach == '2V')) {
                        if ($ordre == 1) {
                            $positionX = 1.5;
                            $positionY = 1.5;
                            $widthImg = 4.05;
                            $heightImg = 12;
                        }
                        if ($ordre == 2) {
                            $positionX = 6.15;
                            $positionY = 1.5;
                            $widthImg = 4.05;
                            $heightImg = 12;
                        }
                    } elseif ($nbatach == '2H') {
                        if ($ordre == 1) {
                            $positionX = 1.5;
                            $positionY = 1.5;
                            $widthImg = 8.7;
                            $heightImg = 5.7;
                        }
                        if ($ordre == 2) {
                            $positionX = 1.5;
                            $positionY = 7.8;
                            $widthImg = 8.7;
                            $heightImg = 5.7;
                        }
                    } elseif ($nbatach == '3H') {
                        if ($ordre == 1) {
                            $positionX = 1.5;
                            $positionY = 1.5;
                            $widthImg = 8.7;
                            $heightImg = 5.7;
                        }
                        if ($ordre == 2) {
                            $positionX = 1.5;
                            $positionY = 7.8;
                            $widthImg = 4.05;
                            $heightImg = 5.7;
                        }
                        if ($ordre == 3) {
                            $positionX = 6.15;
                            $positionY = 7.8;
                            $widthImg = 4.05;
                            $heightImg = 5.7;
                        }
                    } elseif (($nbatach == 3) && ($nbatach == "3V")) {
                        if ($ordre == 1) {
                            $positionX = 1.5;
                            $positionY = 1.5;
                            $widthImg = 4.05;
                            $heightImg = 12;
                        }
                        if ($ordre == 2) {
                            $positionX = 6.15;
                            $positionY = 1.5;
                            $widthImg = 4.05;
                            $heightImg = 5.7;
                        }
                        if ($ordre == 3) {
                            $positionX = 6.15;
                            $positionY = 7.8;
                            $widthImg = 4.05;
                            $heightImg = 5.7;
                        }
                    } elseif ($nbatach == 4) {
                        if ($ordre == 1) {
                            $positionX = 1.5;
                            $positionY = 1.5;
                            $widthImg = 4.05;
                            $heightImg = 5.7;
                        }
                        if ($ordre == 2) {
                            $positionX = 1.5;
                            $positionY = 7.8;
                            $widthImg = 4.05;
                            $heightImg = 5.7;
                        }
                        if ($ordre == 3) {
                            $positionX = 6.15;
                            $positionY = 1.5;
                            $widthImg = 4.05;
                            $heightImg = 5.7;
                        }
                        if ($ordre == 4) {
                            $positionX = 6.15;
                            $positionY = 7.8;
                            $widthImg = 4.05;
                            $heightImg = 5.7;
                        }
                    } //nop
                    elseif ($nbatach == 5) {
                        if ($ordre == 1) {
                            $positionX = 1.5;
                            $positionY = 1.5;
                            $widthImg = 18;
                            $heightImg = 8;
                        }
                        if ($ordre == 2) {
                            $positionX = 1.5;
                            $positionY = 10.5;
                            $widthImg = 3.75;
                            $heightImg = 3;
                        }
                        if ($ordre == 3) {
                            $positionX = 6.25;
                            $positionY = 10.5;
                            $widthImg = 3.75;
                            $heightImg = 3;
                        }
                        if ($ordre == 4) {
                            $positionX = 11;
                            $positionY = 10.5;
                            $widthImg = 3.75;
                            $heightImg = 3;
                        }
                        if ($ordre == 5) {
                            $positionX = 15.75;
                            $positionY = 10.5;
                            $widthImg = 3.75;
                            $heightImg = 3;
                        }
                    } elseif ($nbatach == 6) {
                        if ($ordre == 1) {
                            $positionX = 1.5;
                            $positionY = 1.5;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 2) {
                            $positionX = 1.5;
                            $positionY = 5.7;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 3) {
                            $positionX = 1.5;
                            $positionY = 9.9;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 4) {
                            $positionX = 6.15;
                            $positionY = 1.5;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 5) {
                            $positionX = 6.15;
                            $positionY = 5.7;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 6) {
                            $positionX = 6.15;
                            $positionY = 9.9;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                    } //nop
                    elseif ($nbatach == 12) {
                        if ($ordre == 1) {
                            $positionX = 1.5;
                            $positionY = 1.5;
                            $widthImg = 3.75;
                            $heightImg = 3.33;
                        }
                        if ($ordre == 2) {
                            $positionX = 1.5;
                            $positionY = 5.83;
                            $widthImg = 3.75;
                            $heightImg = 3.33;
                        }
                        if ($ordre == 3) {
                            $positionX = 1.5;
                            $positionY = 10.16;
                            $widthImg = 3.75;
                            $heightImg = 3.33;
                        }
                        if ($ordre == 4) {
                            $positionX = 6.25;
                            $positionY = 1.5;
                            $widthImg = 3.75;
                            $heightImg = 3.33;
                        }
                        if ($ordre == 5) {
                            $positionX = 6.25;
                            $positionY = 5.83;
                            $widthImg = 3.75;
                            $heightImg = 3.33;
                        }
                        if ($ordre == 6) {
                            $positionX = 6.25;
                            $positionY = 10.16;
                            $widthImg = 3.75;
                            $heightImg = 3.33;
                        }
                        if ($ordre == 7) {
                            $positionX = 11;
                            $positionY = 1.5;
                            $widthImg = 3.75;
                            $heightImg = 3.33;
                        }
                        if ($ordre == 8) {
                            $positionX = 11;
                            $positionY = 5.83;
                            $widthImg = 3.75;
                            $heightImg = 3.33;
                        }
                        if ($ordre == 9) {
                            $positionX = 11;
                            $positionY = 10.16;
                            $widthImg = 3.75;
                            $heightImg = 3.33;
                        }
                        if ($ordre == 10) {
                            $positionX = 15.75;
                            $positionY = 1.5;
                            $widthImg = 3.75;
                            $heightImg = 3.33;
                        }
                        if ($ordre == 11) {
                            $positionX = 15.75;
                            $positionY = 5.83;
                            $widthImg = 3.75;
                            $heightImg = 3.33;
                        }
                        if ($ordre == 12) {
                            $positionX = 15.75;
                            $positionY = 10.16;
                            $widthImg = 3.75;
                            $heightImg = 3.33;
                        }
                    } elseif ($nbatach == 15) {
                        if ($ordre == 1) {
                            $positionX = 1.5;
                            $positionY = 1.5;
                            $widthImg = 2.5;
                            $heightImg = 1.92;
                        }
                        if ($ordre == 2) {
                            $positionX = 1.5;
                            $positionY = 4.02;
                            $widthImg = 2.5;
                            $heightImg = 1.92;
                        }
                        if ($ordre == 3) {
                            $positionX = 1.5;
                            $positionY = 6.54;
                            $widthImg = 2.5;
                            $heightImg = 1.92;
                        }
                        if ($ordre == 4) {
                            $positionX = 1.5;
                            $positionY = 9.06;
                            $widthImg = 2.5;
                            $heightImg = 1.92;
                        }
                        if ($ordre == 5) {
                            $positionX = 1.5;
                            $positionY = 11.58;
                            $widthImg = 2.5;
                            $heightImg = 1.92;
                        }
                        if ($ordre == 6) {
                            $positionX = 4.6;
                            $positionY = 1.5;
                            $widthImg = 2.5;
                            $heightImg = 1.92;
                        }
                        if ($ordre == 7) {
                            $positionX = 4.6;
                            $positionY = 4.02;
                            $widthImg = 2.5;
                            $heightImg = 1.92;
                        }
                        if ($ordre == 8) {
                            $positionX = 4.6;
                            $positionY = 6.54;
                            $widthImg = 2.5;
                            $heightImg = 1.92;
                        }
                        if ($ordre == 9) {
                            $positionX = 4.6;
                            $positionY = 9.06;
                            $widthImg = 2.5;
                            $heightImg = 1.92;
                        }
                        if ($ordre == 10) {
                            $positionX = 4.6;
                            $positionY = 11.58;
                            $widthImg = 2.5;
                            $heightImg = 1.92;
                        }
                        if ($ordre == 11) {
                            $positionX = 7.7;
                            $positionY = 1.5;
                            $widthImg = 2.5;
                            $heightImg = 1.92;
                        }
                        if ($ordre == 12) {
                            $positionX = 7.7;
                            $positionY = 4.02;
                            $widthImg = 2.5;
                            $heightImg = 1.92;
                        }
                        if ($ordre == 13) {
                            $positionX = 7.7;
                            $positionY = 6.54;
                            $widthImg = 2.5;
                            $heightImg = 1.92;
                        }
                        if ($ordre == 14) {
                            $positionX = 7.7;
                            $positionY = 9.06;
                            $widthImg = 2.5;
                            $heightImg = 1.92;
                        }
                        if ($ordre == 15) {
                            $positionX = 7.7;
                            $positionY = 11.58;
                            $widthImg = 2.5;
                            $heightImg = 1.92;
                        }
                    }
                    if (($i == sizeof($tabphoto) - 1) && ($key != 0)) {
                        $path = 'https://5sur5sejour.com' . $path;
                        $positionX = 10.8;
                        $positionY = 1.5;
                        $widthImg = 8.7;
                        $heightImg = 12;
                        $linkFile = parse_url($path, PHP_URL_PATH);       // get path from url
                        var_dump($linkFile);
                        $extension = pathinfo($linkFile, PATHINFO_EXTENSION);
                        var_dump($extension);
                        $path = $path;
                        var_dump($path);
                        $pdf->Image($path, $positionX, $positionY, $widthImg, $heightImg, 'JPG', '', '', false, 200, '', false, false, 0, false, false, false);
                        var_dump('test dump 1');
                    } else {
                        if (strpos($path, 'res.cloudinary.com') !== false) {
                            //Recarder l'image :
                            // $path=str_replace( 'upload/', 'upload/ar_1.1'.',c_crop/q_auto:good/',$path);<
                            //                $path=str_replace( 'upload/', 'upload/ar_1,c_crop,x_'.round($left*37.7952755906).',y_'.round($top*37.7952755906).',w_'.round($widthCropPX).',h_'.round($heightCropPX).',g_north_east/',$path);
                            $pathArray = explode("/", $path);
                            $idsArray = explode(".", $pathArray[sizeof($pathArray) - 1]);
                            $idImage = "";
                            foreach ($idsArray as $key2 => $elem) {
                                if ($key2 != (sizeof($idsArray) - 1)) {
                                    $idImage = $idImage . $elem;
                                }
                            }
                            //   $cloudinaryWidht=$widthOriginal;
                            // $cloudinaryHeight=$widthOriginal;
                            $idImage = 'newprod/' . $idImage;
                            //var_dump($idImage);
                            if ((strstr($path, "af5sur5sejour"))) {
                                Unirest\Request::auth('263346742199243', 'jYw-jg0FOJGv89-o5Wo0Fa3rQWU');
                                $url = 'https://api.cloudinary.com/v1_1/af5sur5sejour/resources/image/upload/' . $idImage;
                            } else {
                                Unirest\Request::auth('319835665915435', 'xmlIYw147bjaGTtgk4D4UtiGBlg');
                                $url = 'https://api.cloudinary.com/v1_1/dknprksho/resources/image/upload/' . $idImage;
                            }
                            $headers = array('Accept' => 'application/json');
                            $data = array("public_ids" => array($idImage));
                            $body = Unirest\Request\Body::form($data);
                            Unirest\Request::verifyPeer(false);
                            // $resultMetadata=  \Cloudinary::Api.resources_by_ids([$idImage]);
                            //  var_dump($url);
                            $resultMetadata = Unirest\Request::post($url, $headers, $body);
                            var_dump($resultMetadata);
                            if (isset(json_decode($resultMetadata->raw_body)->width)) {
                                $cloudinaryWidht = json_decode($resultMetadata->raw_body)->width;
                                $cloudinaryHeight = json_decode($resultMetadata->raw_body)->height;
                            } else {
                                $cloudinaryWidht = $widthOriginal;
                                $cloudinaryHeight = $heightOriginal;
                            }
                            $ratiohight = $cloudinaryWidht / $widthOriginal;
                            $ratioHight = $cloudinaryHeight / $heightOriginal;
                            //$cloudinaryHeight=$cloudinaryHeight*$zoom;
                            //$cloudinaryWidht=$cloudinaryWidht*$zoom;
                            var_dump($cloudinaryWidht);
                            var_dump($cloudinaryHeight);
                            var_dump($ratioHight);
                            var_dump($zoom);
                            $zoom = 1;
                            var_dump($leftOriginal);
                            var_dump('x_' . round(abs($leftOriginal / $zoom) * $ratioHight));
                            var_dump('y_' . round(abs($topOriginal / $zoom) * $ratiohight));
                            var_dump('w_' . round(($widthCropPX / $zoom) * $ratioHight));
                            var_dump('h_' . round(($heightCropPX / $zoom) * $ratiohight));
                            //var_dump($path);i
                            $path = str_replace('upload/', 'upload/w_' . round($cloudinaryWidht) . ',h_' . round($cloudinaryHeight) . ',c_scale/x_' . round(abs($leftOriginal / $zoom) * $ratioHight) . ',y_' . round(abs($topOriginal / $zoom) * $ratiohight) . ',w_' . round(($widthCropPX / $zoom) * $ratioHight) . ',h_' . round(($heightCropPX / $zoom) * $ratiohight) . ',c_crop/', $path);
                            //var_dump($path);i
                            //                $path=str_replace( 'upload/', 'upload/w_'.round($widthOriginal).',h_'.round($heightOriginal).'/x_'.round(abs($leftOriginal)).',y_'.round(abs($topOriginal)).',w_'.round($widthCropPX).',h_'.round($heightCropPX).',c_crop/',$path);
                            //var_dump($path);
                            $path = str_replace("l_Logo5Sur5White_nh6tyk,o_10,fl_relative.tiled,", '', $path);
                            $path = str_replace("l_Logo5Sur5White_nh6tyk,o_35,fl_relative.tiled,", '', $path);
                            $path = str_replace("l_Logo5Sur5White_nh6tyk,o_35,", '', $path);
                            $path = str_replace("l_Logo5Sur5White_nh6tyk,o_10,", '', $path);
                            //  var_dump("//00");
                            //  var_dump("avant 1.4 widh: ".round($widthCropPX)." "."avant 1.4 height : ".round($heightCropPX));
                            //  var_dump("avant 1.4 top: ".round(abs($top*37.7952755906))." avant 1.4 final left : ".round(abs($left*37.7952755906)));
                            //    var_dump("//00");
                            //   var_dump("final widh: ".round($widthCropPX*1.4)." "."final height : ".round($heightCropPX*1.4));
                            //  var_dump("final top: ".round(abs($top*37.7952755906*1.4))." "."final left : ".round(abs($left*37.7952755906*1.4)));
                            var_dump($zoom);
                            var_dump($path);
                            // $path="https://res.cloudinary.com/af5sur5sejour/image/upload/w_691,h_356,c_crop/a_exif/v1587482806/newprod/crepes-au-chocolat_re9wvk.jpg";
                            $linkFile = parse_url($path, PHP_URL_PATH);       // get path from url
                            $extension = pathinfo($linkFile, PATHINFO_EXTENSION);
                            $path = preg_replace('/(?:png|gif)$/i', 'jpg', $path);
                            $pdf->Image($path, $positionX, $positionY, $widthImg, $heightImg, 'JPG', '', '', false, 200, '', false, false, 0, false, false, false);
                            //                $pdf->Rect($positionX,$positionY ,$widthImg, $heightImg, 'F', array(), array(264,200,67));
                        } else {
                            $pathArray = explode("/", $path);
                            $idsArray =  $pathArray[sizeof($pathArray) - 1];
                            $url = $this->params->get('nodeImaginaryHost') . '/info?url=' . $this->params->get('imaginaryHost') . '/upload/original/' . $idsArray;
                            $headers = array('Accept' => 'application/json');
                            Unirest\Request::verifyPeer(false);
                            $resultMetadata = Unirest\Request::get($url, $headers);
                            var_dump($resultMetadata);
                            if (isset(json_decode($resultMetadata->raw_body)->width)) {
                                var_dump("imaginary_metadata");
                                $cloudinaryWidht = json_decode($resultMetadata->raw_body)->width;
                                $cloudinaryHeight = json_decode($resultMetadata->raw_body)->height;
                            } else {
                                $cloudinaryWidht = $widthOriginal;
                                $cloudinaryHeight = $heightOriginal;
                            }
                            $ratiohight = $cloudinaryWidht / $widthOriginal;
                            $ratioHight = $cloudinaryHeight / $heightOriginal;
                            var_dump($cloudinaryWidht);
                            var_dump($cloudinaryHeight);
                            var_dump($ratioHight);
                            var_dump($zoom);
                            var_dump($leftOriginal);
                            var_dump('x_' . round(abs($leftOriginal / $zoom) * $ratioHight));
                            var_dump('y_' . round(abs($topOriginal / $zoom) * $ratiohight));
                            var_dump('w_' . round(($widthCropPX / $zoom) * $ratioHight));
                            var_dump('h_' . round(($heightCropPX / $zoom) * $ratiohight));
                            $topYImaginary = floor(abs($topOriginal / $zoom) * $ratiohight);
                            $leftXimaginary = floor(abs($leftOriginal / $zoom) * $ratioHight);
                            $widthimaginary = floor(($widthCropPX / $zoom) * $ratioHight);
                            $heigthimaginary = floor(($heightCropPX / $zoom) * $ratiohight);
                            var_dump($cloudinaryWidht . '<' . $widthimaginary . '=============' . $cloudinaryHeight . '<' . $heigthimaginary);
                            if ($cloudinaryWidht < $widthimaginary) {
                                $widthimaginary = $cloudinaryWidht - $leftXimaginary;
                            }
                            if ($cloudinaryHeight < $heigthimaginary) {
                                $heigthimaginary = $cloudinaryHeight - $topYImaginary;
                            }
                            if ($widthimaginary + $leftXimaginary > $cloudinaryWidht) {
                                $leftXimaginary = $cloudinaryWidht - $widthimaginary;
                            }
                            if ($heigthimaginary + $topYImaginary > $cloudinaryHeight) {
                                $topYImaginary = $cloudinaryHeight - $heigthimaginary;
                            }
                            $newpath = $this->params->get('nodeImaginaryHost') . '/extract?areawidth=' . $widthimaginary . '&areaheight=' . $heigthimaginary . '&top=' . $topYImaginary . '&left=' . $leftXimaginary . '&url=' . $this->params->get('imaginaryHost') . '/upload/original/' . rawurlencode($idsArray);
                            $path = str_replace('upload/', 'upload/w_' . round($cloudinaryWidht) . ',h_' . round($cloudinaryHeight) . ',c_scale/x_' . round(abs($leftOriginal / $zoom) * $ratioHight) . ',y_' . round(abs($topOriginal / $zoom) * $ratiohight) . ',w_' . round(($widthCropPX / $zoom) * $ratioHight) . ',h_' . round(($heightCropPX / $zoom) * $ratiohight) . ',c_crop/', $path);
                            $path = str_replace("l_Logo5Sur5White_nh6tyk,o_10,fl_relative.tiled,", '', $path);
                            $path = str_replace("l_Logo5Sur5White_nh6tyk,o_35,fl_relative.tiled,", '', $path);
                            $path = str_replace("l_Logo5Sur5White_nh6tyk,o_10,", '', $path);
                            $path = str_replace("l_Logo5Sur5White_nh6tyk,o_35,", '', $path);
                            var_dump($path);
                            var_dump($newpath);
                            //http://51.83.99.222:81/api/upload/w_1600,h_1200,c_scale/x_0,y_80,w_1600,h_1041,c_crop/a_exif/IMG_20201024_152305761%20(Copier)._5fcf3575800e7.jpg
                            //  $path = preg_replace('/(?:png|gif)$/i', 'jpg', $path);
                            $imgdata = file_get_contents($newpath);
                            if (preg_match('/\.(Png|png|PNG)$/', $newpath)) {
                                $pdf->Image('@' . $imgdata, $positionX, $positionY, $widthImg, $heightImg, 'PNG', '', '', false, 200, '', false, false, 0, false, false, false);
                            } else {
                                $pdf->Image('@' . $imgdata, $positionX, $positionY, $widthImg, $heightImg, 'JPG', '', '', false, 200, '', false, false, 0, false, false, false);
                            }
                        }
                        //
                    }
                }
                //    $pdf->Image('https://demo.appsfactor.fr/images/ClipArt_SVG/Etoilerose.svg',3,  2, 19, 13, 'SVG', '', '', false, 300, '', false, false, 0, $fitbox, false, false);
                for ($i = 0; $i < sizeof($tabtxt); $i++) {
                    $txt = json_decode($tabtxt[$i]);
                    $fontSize = floatval(str_replace('px', '', $txt->fontSize)) * 0.75;
                    //$txt->rotation
                    $check = explode(',', $txt->fontFamily);
                    //                    if (sizeof($check) > 1) {
                    //                        //default
                    //                        if (($check[0] == "-apple-system") && ($txt->fontWeight == 400)) {
                    //                            $finalFont = "helvetica";
                    //                            $weight = '';
                    //                        }
                    //                        //classique
                    //                        if (($check[0] == "Georgia") && ($txt->fontWeight == 400)) {
                    //                            $finalFont = "times";
                    //                            $weight = '';
                    //                        }
                    //                        //creative
                    //                        if (($check[0] == "-apple-system") && ($txt->fontWeight == 700)) {
                    //                            $finalFont = "helveticaB";
                    //                            $weight = '';
                    //                        }
                    //                        // manuscrite
                    //                        if (($check[0] == "Comic Sans") && ($txt->fontWeight == 400)) {
                    //                            $finalFont = "Courier";
                    //                            $weight = '';
                    //                        }
                    //
                    //
                    //                        //c.s-microsoft.com/static/fonts/segoe-ui/west-european/light/latest.woff2
                    //                    } else {
                    //                        //baton
                    //                        if (($txt->fontFamily == 'Impact') && ($txt->fontWeight == 400)) {
                    //                            $finalFont = "helveticaB";
                    //                            $weight = 'B';
                    //                        }
                    //                    }
                    $finalFont = 'helvetica';
                    $weight = '';
                    $pdf->SetFont($finalFont, $weight, $fontSize);
                    $leftTxt = $txt->left;
                    $topTxt = $txt->top;
                    $heightClips = $txt->height;
                    $widthClips = $txt->width;
                    $heightTxt = floatval(str_replace('cm', '', $heightClips));
                    $topTxt = floatval(str_replace('cm', '', $topTxt));
                    $leftTxt = floatval(str_replace('cm', '', $leftTxt));
                    $widthTxt = floatval(str_replace('cm', '', $widthClips));
                    if ($key == 0) {
                        $heightTxt = 2;
                        $widthTxt = 7;
                        $leftTxt = (21) - ($widthTxt / 2);
                        $topTxt = 11.8;
                        $pdf->SetXY(0, $topTxt, true);
                        $pdf->Cell(21, 0, trim($txt->contenu), 0, 0, 'C', 0, '', 0);
                    } else {
                        $heightTxt = 2;
                        $widthTxt = 7;
                        $leftTxt = (21) - ($widthTxt / 2);
                        $topTxt = 11.8;
                        $pdf->SetXY(13.6, 12.4, true);
                        $pdf->Cell(6, 0, trim($txt->contenu), 0, 0, 'L', 0, '', 0);
                    }
                    //                    var_dump($leftTxt);
                    //                  var_dump($topTxt);
                    // var_dump(floatval(str_replace('rad','',$txt->rotation))*57,2958);
                    //                var_dump("text");
                    //$pdf->StartTransform();
                    //$txt->rotation=0;
                    //var_dump($txt->rotation);
                    //var_dump(str_replace('rad','',$txt->rotation));
                    //var_dump(floatval(str_replace('rad','',$txt->rotation))*57.2958);
                    // $pdf->Rotate((floatval(str_replace('rad','',$txt->rotation))*57.2958)*-1,$leftTxt+($widthTxt/2),$topTxt+($heightTxt/2));
                    // $pdf->Rotate(45);
                    //   $pdf->Text($leftTxt, $topTxt, $txt->contenu);
                    //$pdf->SetTextColor(200);
                    //$pdf->Text($leftTxt, $topTxt, $txt->contenu);
                    // $pdf->MultiCell($leftTxt, $topTxt,  $txt->contenu, 0, $ln=0, 'C', 0, '', 0, false, 'C', 'C');
                    //                $pdf->Write(str_replace('cm','',$txt->height),trim($txt->contenu));
                    // $pdf->writeHTML("<p>".$txt->contenu."</p>", true, false, false, false, '');
                    //$pdf->StopTransform();
                }
                //Positionner text
                //            $pdf->ImageSVG("C:\\Users\\AppsFactor12\\Desktop\\5sur5\\5sur5Sejour\\public\\images\\ClipArt_SVG\\Etoilerose.svg",100,200,500, 500, '', '', '', 0, false);
                //Positionner clipart
                //            dd($tabClips);
                for ($i = 0; $i < sizeof($tabClips); $i++) {
                    $Clips = json_decode($tabClips[$i]);
                    $heightClips = $Clips->height;
                    $topClips = $Clips->top;
                    $leftClips = $Clips->left;
                    $widthClips = $Clips->width;
                    $path = $Clips->path;
                    $pathClips = str_replace('"', '', $path);
                    $heightClips = floatval(str_replace('cm', '', $heightClips));
                    $topClips = floatval(str_replace('cm', '', $topClips));
                    $leftClips = floatval(str_replace('cm', '', $leftClips));
                    $widthClips = floatval(str_replace('cm', '', $widthClips));
                    $heightClipsPX = round($heightClips * 37.7952755906);
                    $widthClipsPX = round($widthClips * 37.7952755906);
                    ////                $positionXclips = $positionX + $leftClips;
                    ////                $positionYclips = $positionY + $topClips;
                    //
                    //$pdf->ImageSVG("images/ClipArt_SVG/Ete4.svg",$leftClips,$topClips,$widthClips, $heightClips);
                    //https://res.cloudinary.com/af5sur5sejour/image/private/s--EdExAzx8--/v1588758453/GlobeFooter_c4duua.svg
                    // https://res.cloudinary.com/af5sur5sejour/image/upload/v1588764528/Groupe_113_pcjyj4.png
                    var_dump($pathClips);
                    $pdf->StartTransform();
                    $pdf->Rotate((floatval(str_replace('rad', '', $Clips->rotation)) * 57.2958) * -1, $leftClips + ($widthClips / 2), $topClips + ($heightClips / 2));
                    $pdf->Image($this->newPAthCLipart($pathClips, $heightClipsPX, $widthClipsPX), $leftClips, $topClips, $widthClips, $heightClips, '', '', '', false, 200);
                    $pdf->StopTransform();
                }
            }
        }
        // echo '</pre>';
        //return new response("yezi");
        $projectRoot = $this->params->get('projectDir');
        $pdf->Output($projectRoot . '/public/pdfDocs/' . $numCommande . '_' . $type . '_' . $idPrdt . '.pdf', 'F');
        return $numCommande . '_' . $type . '_' . $idPrdt . '.pdf';
        //return $pdf->Output('example_009.pdf', 'I');
    }
    public function TcPdfPhotoR($idPrdt, $numCommande, $type)
    {
        ini_set("max_execution_time", -1);
        $pageLayout = array(9, 10);
        $pdf = $this->tcpdf->create('P', 'CM', $pageLayout, true, 'UTF-8', false);
        //$pdf = $this->container->get("white_october.tcpdf")->create('P', 'CM', $pageLayout, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Nicola Asuni');
        $pdf->SetTitle('TCPDF Example 009');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->setJPEGQuality(200);
        $horizontal_alignments = array('L', 'C', 'R');
        $vertical_alignments = array('T', 'M', 'B');
        $em = $this->em;
        $Album = $em->getRepository(Produit::class)->findOneBy(['id' => $idPrdt]);
        $AllPages = $em->getRepository(Page::class)->findBy(['idproduit' => $Album]);
        foreach ($AllPages as $keyPage => $p) {
            $pdf->AddPage();
            $pdf->setJPEGQuality(200);
            $contenu = json_decode(json_decode($p->getCouleurbordure())[0]);
            $nbatach = $contenu->nbrAttc;
            $nbatach = intval(str_replace('"', '', $nbatach));
            $color = $contenu->color;
            $tabphoto = json_decode($contenu->attache);
            // get the current page break margin
            $bMargin = $pdf->getBreakMargin();
            // get current auto-page-break mode
            $auto_page_break = $pdf->getAutoPageBreak();
            // disable auto-page-break
            $pdf->SetAutoPageBreak(false, 0);
            // test all combinations of alignments
            $fitbox = $horizontal_alignments[1] . ' ';
            $fitbox[1] = $vertical_alignments[1];
            for ($i = 0; $i < sizeof($tabphoto); $i++) {
                $photo = json_decode($tabphoto[$i]);
                //les coordonnées de l'image réel:
                $hght = $photo->height;
                $top = $photo->top;
                $left = $photo->left;
                $width = $photo->width;
                $ordre = $photo->ordre;
                $zoom = 1;
                $path = $photo->path;
                $hght = floatval(str_replace('cm', '', $hght));
                $top = floatval(str_replace('cm', '', $top));
                $left = floatval(str_replace('cm', '', $left));
                $width = floatval(str_replace('cm', '', $width));
                //les coordonnées dropzone:
                $heightOriginal = $photo->height;
                $widthOriginal = $photo->width;
                $heightOriginal = floatval(str_replace('cm', '', $heightOriginal));
                $widthOriginal = floatval(str_replace('cm', '', $widthOriginal));
                $heightOriginal = $heightOriginal * 37.7952755906;
                $widthOriginal = $widthOriginal * 37.7952755906;
                $topOriginal = $top * 37.7952755906;
                $leftOriginal = $left * 37.7952755906;
                $heightCrop = $photo->heightCrop;
                $topCrop = $photo->topCrop;
                $leftCrop = $photo->leftCrop;
                $widthCrop = $photo->widthCrop;
                $path = $photo->path;
                $heightCrop = floatval(str_replace('cm', '', $heightCrop));
                $topCrop = floatval(str_replace('cm', '', $topCrop));
                $leftCrop = floatval(str_replace('cm', '', $leftCrop));
                $widthCrop = floatval(str_replace('cm', '', $widthCrop));
                $widthCropPX = $widthCrop * 37.7952755906;
                $heightCropPX = $heightCrop * 37.7952755906;
                $topCropPX = $topCrop * 37.7952755906;
                $leftCropPX = $leftCrop * 37.7952755906;
                var_dump("original widh: " . $widthCropPX . " " . "original height : " . $heightCropPX);
                //Calculer position des images selon nombres images par page:
                $positionX = 0.55;
                $positionY = 0.5;
                $widthImg = 7.9;
                $heightImg = 6.9;
                //Recarder l'image :
                if (strpos($path, 'res.cloudinary.com') !== false) {
                    $pathArray = explode("/", $path);
                    $idsArray = explode(".", $pathArray[sizeof($pathArray) - 1]);
                    $idImage = "";
                    foreach ($idsArray as $key => $elem) {
                        if ($key != (sizeof($idsArray) - 1)) {
                            $idImage = $idImage . $elem;
                        }
                    }
                    $idImage = 'newprod/' . $idImage;
                    //var_dump($idImage);
                    if ((strstr($path, "af5sur5sejour"))) {
                        Unirest\Request::auth('263346742199243', 'jYw-jg0FOJGv89-o5Wo0Fa3rQWU');
                        $url = 'https://api.cloudinary.com/v1_1/af5sur5sejour/resources/image/upload/' . $idImage;
                    } else {
                        Unirest\Request::auth('319835665915435', 'xmlIYw147bjaGTtgk4D4UtiGBlg');
                        $url = 'https://api.cloudinary.com/v1_1/dknprksho/resources/image/upload/' . $idImage;
                    }
                    $headers = array('Accept' => 'application/json');
                    $data = array("public_ids" => array($idImage));
                    $body = Unirest\Request\Body::form($data);
                    Unirest\Request::verifyPeer(false);
                    $resultMetadata = Unirest\Request::post($url, $headers, $body);
                    if (isset(json_decode($resultMetadata->raw_body)->width)) {
                        $cloudinaryWidht = json_decode($resultMetadata->raw_body)->width;
                        $cloudinaryHeight = json_decode($resultMetadata->raw_body)->height;
                    } else {
                        $cloudinaryWidht = $widthOriginal;
                        $cloudinaryHeight = $heightOriginal;
                    }
                    $ratiohight = $cloudinaryWidht / $widthOriginal;
                    $ratioHight = $cloudinaryHeight / $heightOriginal;
                    //$cloudinaryHeight=$cloudinaryHeight*$zoom;
                    //$cloudinaryWidht=$cloudinaryWidht*$zoom;
                    $zoom = 1;
                    var_dump($cloudinaryWidht);
                    var_dump($cloudinaryHeight);
                    var_dump($ratioHight);
                    var_dump($zoom);
                    var_dump($leftOriginal);
                    var_dump('x_' . round(abs($leftOriginal / $zoom) * $ratioHight));
                    var_dump('y_' . round(abs($topOriginal / $zoom) * $ratiohight));
                    var_dump('w_' . round(($widthCropPX / $zoom) * $ratioHight));
                    var_dump('h_' . round(($heightCropPX / $zoom) * $ratiohight));
                    //var_dump($path);i
                    $path = str_replace('upload/', 'upload/w_' . round($cloudinaryWidht) . ',h_' . round($cloudinaryHeight) . ',c_scale/x_' . round(abs($leftOriginal / $zoom) * $ratioHight) . ',y_' . round(abs($topOriginal / $zoom) * $ratiohight) . ',w_' . round(($widthCropPX / $zoom) * $ratioHight) . ',h_' . round(($heightCropPX / $zoom) * $ratiohight) . ',c_crop/', $path);
                    $path = str_replace("l_Logo5Sur5White_nh6tyk,o_10,fl_relative.tiled,", '', $path);
                    $path = str_replace("l_Logo5Sur5White_nh6tyk,o_35,fl_relative.tiled,", '', $path);
                    $path = str_replace("l_Logo5Sur5White_nh6tyk,o_10,", '', $path);
                    $path = str_replace("l_Logo5Sur5White_nh6tyk,o_35,", '', $path);
                    //  var_dump("//00");
                    //  var_dump("avant 1.4 widh: ".round($widthCropPX)." "."avant 1.4 height : ".round($heightCropPX));
                    //  var_dump("avant 1.4 top: ".round(abs($top*37.7952755906))." avant 1.4 final left : ".round(abs($left*37.7952755906)));
                    //    var_dump("//00");
                    //   var_dump("final widh: ".round($widthCropPX*1.4)." "."final height : ".round($heightCropPX*1.4));
                    //  var_dump("final top: ".round(abs($top*37.7952755906*1.4))." "."final left : ".round(abs($left*37.7952755906*1.4)));
                    var_dump($zoom);
                    var_dump($path);
                    // $path="https://res.cloudinary.com/af5sur5sejour/image/upload/w_691,h_356,c_crop/a_exif/v1587482806/newprod/crepes-au-chocolat_re9wvk.jpg";
                    $linkFile = parse_url($path, PHP_URL_PATH);       // get path from url
                    $extension = pathinfo($linkFile, PATHINFO_EXTENSION);
                    $path = preg_replace('/(?:png|gif)$/i', 'jpg', $path);
                    $pdf->Image($path, $positionX, $positionY, $widthImg, $heightImg, 'JPG', '', '', false, 200, '', false, false, 0, false, false, false);
                } else {
                    var_dump("********************  " . $keyPage . "  ********************");
                    $pathArray = explode("/", $path);
                    $idsArray = $pathArray[sizeof($pathArray) - 1];
                    $headers = array('Accept' => 'application/json');
                    //$data = array("public_ids" => array($idImage));
                    //$body = Unirest\Request\Body::form($data);
                    Unirest\Request::verifyPeer(false);
                    // $resultMetadata=  \Cloudinary::Api.resources_by_ids([$idImage]);
                    //  var_dump($url);
                    //   $resultMetadata = Unirest\Request::post($url, $headers, $body);
                    $url = $this->params->get('nodeImaginaryHost') . '/info?url=' . $this->params->get('imaginaryHost') . '/upload/original/' . $idsArray;
                    //     $idImage = 'newprod/' . $idImage;
                    $resultMetadata = Unirest\Request::get($url, $headers);
                    $headers = array('Accept' => 'application/json');
                    Unirest\Request::verifyPeer(false);
                    $resultMetadata = Unirest\Request::get($url, $headers);
                    if (isset(json_decode($resultMetadata->raw_body)->width)) {
                        var_dump("imaginary_metadata");
                        var_dump('---------- Rotation --------------');
                        if (json_decode($resultMetadata->raw_body)->height > json_decode($resultMetadata->raw_body)->width) {
                            var_dump('---------- Taille reverse --------------');
                            $cloudinaryWidht = json_decode($resultMetadata->raw_body)->height;
                            $cloudinaryHeight = json_decode($resultMetadata->raw_body)->width;
                        } else {
                            var_dump('---------- Taille originale --------------');
                            $cloudinaryWidht = json_decode($resultMetadata->raw_body)->width;
                            $cloudinaryHeight = json_decode($resultMetadata->raw_body)->height;
                        }
                    } else {
                        $cloudinaryWidht = $widthOriginal;
                        $cloudinaryHeight = $heightOriginal;
                    }
                    $ratioWidth = $cloudinaryWidht / $widthOriginal;
                    $ratioHight = $cloudinaryHeight / $heightOriginal;
                    $zoom = 1;
                    var_dump($cloudinaryWidht);
                    var_dump($cloudinaryHeight);
                    var_dump($ratioHight);
                    var_dump($zoom);
                    var_dump($leftOriginal);
                    var_dump('ratio_height  ' . $ratioHight);
                    var_dump('ratio_width   ' . $ratioWidth);
                    if (floor(($widthCropPX / $zoom) * $ratioHight) < floor(($heightCropPX / $zoom) * $ratioWidth)) {
                        $ratioWidth = $cloudinaryWidht / $heightOriginal;
                        $ratioHight = $cloudinaryHeight / $widthOriginal;
                        var_dump('recalcul ratio_height  ' . $ratioHight);
                        var_dump('recalcul ratio_width   ' . $ratioWidth);
                        var_dump('x_' . round(abs($leftOriginal / $zoom) * $ratioHight));
                        var_dump('y_' . round(abs($topOriginal / $zoom) * $ratioWidth));
                        var_dump('w_' . round(($widthCropPX / $zoom) * $ratioHight));
                        var_dump('h_' . round(($heightCropPX / $zoom) * $ratioWidth));
                        var_dump('width_crop ' . $widthCropPX);
                        var_dump('height_crop ' . $heightCropPX);
                        $topYImaginary = floor(abs($topOriginal / $zoom) * $ratioWidth);
                        $leftXimaginary = floor(abs($leftOriginal / $zoom) * $ratioHight);
                        $widthimaginary = floor(($widthCropPX / $zoom) * $ratioHight);
                        $heigthimaginary = floor(($heightCropPX / $zoom) * $ratioWidth);
                    } else {
                        var_dump('x_' . round(abs($leftOriginal / $zoom) * $ratioHight));
                        var_dump('y_' . round(abs($topOriginal / $zoom) * $ratioWidth));
                        var_dump('w_' . round(($widthCropPX / $zoom) * $ratioHight));
                        var_dump('h_' . round(($heightCropPX / $zoom) * $ratioWidth));
                        var_dump('width_crop ' . $widthCropPX);
                        var_dump('height_crop ' . $heightCropPX);
                        $topYImaginary = floor(abs($topOriginal / $zoom) * $ratioWidth);
                        $leftXimaginary = floor(abs($leftOriginal / $zoom) * $ratioHight);
                        $widthimaginary = floor(($widthCropPX / $zoom) * $ratioHight);
                        $heigthimaginary = floor(($heightCropPX / $zoom) * $ratioWidth);
                    }
                    if ($cloudinaryWidht < $widthimaginary) {
                        $widthimaginary = $cloudinaryWidht - $leftXimaginary;
                    }
                    if ($cloudinaryHeight < $heigthimaginary) {
                        $heigthimaginary = $cloudinaryHeight - $topYImaginary;
                    }
                    if ($widthimaginary + $leftXimaginary > $cloudinaryWidht) {
                        $leftXimaginary = $cloudinaryWidht - $widthimaginary;
                    }
                    if ($ratioHight < $ratioWidth) {
                        var_dump("*******  prblm ratio  *******");
                        $newpath = $this->params->get('nodeImaginaryHost') . '/extract?areawidth=' . $widthimaginary . '&areaheight=' . $heigthimaginary . '&top=' . $topYImaginary . '&left=' . $leftXimaginary . '&url=' . $this->params->get('imaginaryHost') . '/upload/original/' . rawurlencode($idsArray);
                    } else {
                        var_dump("******* SANS prblm ratio  *******");
                        $newpath = $this->params->get('nodeImaginaryHost') . '/extract?areawidth=' . $widthimaginary . '&areaheight=' . $heigthimaginary . '&top=' . $topYImaginary . '&left=' . $leftXimaginary . '&url=' . $this->params->get('imaginaryHost') . '/upload/original/' . rawurlencode($idsArray);
                    }
                    if ($ratioHight < $ratioWidth) {
                        $path = str_replace('upload/', 'upload/w_' . round($cloudinaryHeight) . ',h_' . round($cloudinaryWidht) . ',c_scale/x_' . round(abs($topOriginal / $zoom) * $ratioWidth) . ',y_' . round(abs($leftOriginal / $zoom) * $ratioHight) . ',w_' . round(($heightCropPX / $zoom) * $ratioWidth) . ',h_' .  round(($widthCropPX / $zoom) * $ratioHight) . ',c_crop/', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_10,fl_relative.tiled,", '', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_35,fl_relative.tiled,", '', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_10,", '', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_35,", '', $path);
                        var_dump($path);
                        var_dump($newpath);
                        $imgdata = file_get_contents($newpath);
                        $path = $newpath;
                    } else {
                        $path = str_replace('upload/', 'upload/w_' . round($cloudinaryWidht) . ',h_' . round($cloudinaryHeight) . ',c_scale/x_' . round(abs($leftOriginal / $zoom) * $ratioHight) . ',y_' . round(abs($topOriginal / $zoom) * $ratioWidth) . ',w_' . round(($widthCropPX / $zoom) * $ratioHight) . ',h_' . round(($heightCropPX / $zoom) * $ratioWidth) . ',c_crop/', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_10,fl_relative.tiled,", '', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_35,fl_relative.tiled,", '', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_10,", '', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_35,", '', $path);
                        var_dump($path);
                        var_dump($newpath);
                        $imgdata = file_get_contents($newpath);
                        $path = $newpath;
                    }
                    $imgdata = file_get_contents($newpath);
                    //$imgdata=$imgrotate->rotate(90)
                    if (preg_match('/\.(Png|png|PNG)$/', $newpath)) {
                        $pdf->Image('@' . $imgdata, $positionX, $positionY, $widthImg, $heightImg, 'PNG', '', '', false, 200, '', false, false, 0, false, false, false);
                    } else {
                        if (preg_match('/(?:webp|WEBP)$/i', $newpath)) {
                            $im = imagecreatefromwebp($newpath);
                            $basejpeg = str_replace(".webp", ".jpg", basename($newpath));
                            $newpath = $this->params->get('projectDir') . "/public/images/" . $basejpeg;
                            imagejpeg($im, $newpath, 100);
                            $imgdata = file_get_contents($newpath);
                        }
                        if (preg_match('/(?:bmp|BMP)$/i', $newpath)) {
                            $im = imagecreatefrombmp($newpath);
                            $basejpeg = str_replace(".bmp", ".jpg", basename($newpath));
                            $newpath = $this->params->get('projectDir') . "/public/images/" . $basejpeg;
                            imagejpeg($im, $newpath, 100);
                            $imgdata = file_get_contents($newpath);
                        }
                        $pdf->Image('@' . $imgdata, $positionX, $positionY, $widthImg, $heightImg, 'JPG', '', '', false, 200, '', false, false, 0, false, false, false);
                    }
                }
            }
        }
        // echo '</pre>';
        //return new response("yezi");
        $projectRoot = $this->params->get('projectDir');
        $pdf->Output($projectRoot . '/public/pdfDocs/' . $numCommande . '_' . $type . '_' . $idPrdt . '.pdf', 'F');
        return $numCommande . '_' . $type . '_' . $idPrdt . '.pdf';
        //return $pdf->Output('example_009.pdf', 'I');
    }
    public function PhotoSejourParPartenaire($idSejour)
    {
        $em = $this->em;
        ini_set("max_execution_time", -1);
        $arrayFiles = [];
        $projectRoot = $this->params->get('projectDir');
        $sejour = $em->getRepository(Sejour::class)->find($idSejour);
        //var_dump(sizeof($sejour));
        //    foreach ($sejours as $sejour) {
        if (!file_exists($projectRoot . '/public/PartenireAttach/' . $sejour->getId() . '/' . $sejour->getCodeSejour() . '.zip')) {
            $arrayFiles = [];
            $sejouattach = $sejour->getAttachements();
            var_dump($sejour->getCodeSejour());
            if (!file_exists($projectRoot . '/public/PartenireAttach/' . $sejour->getId())) {
                mkdir($projectRoot . '/public/PartenireAttach/' . $sejour->getId(), 0777, true);
            }
            foreach ($sejouattach as $attach) {
                try {
                    if (($attach->getStatut() == "public") && ($attach->getIdAttchment()->getIdref() != null) && (in_array($attach->getIdAttchment()->getIdref()->getId(), [6, 7, 30]))) {
                        $path = $attach->getIdAttchment()->getPath();
                        file_get_contents(str_replace("https://media.5sur5sejour.com/api/upload/a_exif", "http://54.36.104.133/api/upload", $path));
                        $filename = basename($path);
                        array_push($arrayFiles, $filename);
                        if (!file_exists($projectRoot . '/public/PartenireAttach/' . $sejour->getId() . '/' . $filename)) {
                            file_put_contents($projectRoot . '/public/PartenireAttach/' . $sejour->getId() . '/' . $filename, file_get_contents(str_replace("https://media.5sur5sejour.com/api/upload/a_exif", "http://54.36.104.133/api/upload", $path)));
                        }
                    }
                } catch (\Exception $e) {
                    var_dump($path);
                }
            }
            $zip = new ZipArchive;
            var_dump($projectRoot . '/public/PartenireAttach/' . $sejour->getId() . '/' . $sejour->getCodeSejour() . '.zip');
            if ($zip->open($projectRoot . '/public/PartenireAttach/' . $sejour->getId() . '/' . $sejour->getCodeSejour() . '.zip', ZipArchive::CREATE) === TRUE) {
                // Add files to the zip file
                foreach ($arrayFiles as $fileNames) {
                    $zip->addFile($projectRoot . '/public/PartenireAttach/' . $sejour->getId() . '/' . $fileNames, $fileNames);
                }
                $zip->close();
            }
            var_dump($sejour->getCodeSejour() . '.zip');
        }
        //   }
        return 'done';
    }
    function emojie($code, $height)
    {
        $listEmojie = [
            "\ud83d\ude00" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128512_wubktl.png\"  height=\"' . $height . '\" /&gt;',
            "\ud83d\ude01" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128513_bqd9lr.png\"  height=\"' . $height . '\" /&gt;',
            "\ud83d\ude02" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128514_eug4qy.png\"  height=\"' . $height . '\" /&gt;',
            "\ud83d\ude03" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128515_olv4ax.png\"  height=\"' . $height . '\" /&gt;',
            "\ud83d\ude04" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128516_ksnijp.png\"  height=\"' . $height . '\" /&gt;',
            "\ud83d\ude05" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128517_y2hmva.png\"  height=\"' . $height . '\" /&gt;',
            "\ud83d\ude06" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128518_enqvjy.png\"  height=\"' . $height . '\" /&gt;',
            "\ud83d\ude07" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128519_lufqd9.png\"  height=\"' . $height . '\" /&gt;',
            "\ud83d\ude08" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128520_vqbmjs.png\"  height=\"' . $height . '\"  /&gt;',
            "\ud83d\ude09" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128521_wukjse.png\"  height=\"' . $height . '\" /&gt;',
            "\ud83d\ude0a" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128522_hs9jxw.png\"  height=\"' . $height . '\" /&gt;',
            "\ud83d\ude0b" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128523_hk3b9h.png\"  height=\"' . $height . '\" /&gt;',
            "\ud83d\ude0c" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128524_ydo8k1.png\"  height=\"' . $height . '\" /&gt;',
            "\ud83d\ude0d" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128525_qky6rg.png\"   height=\"' . $height . '\" /&gt;',
            "\ud83d\ude0e" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128526_mcizdq.png\"  height=\"' . $height . '\" /&gt;',
            "\ud83d\ude0f" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128527_drulzc.png\"  height=\"' . $height . '\" /&gt;',
            "\ud83d\ude10" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128528_alq8rg.png\"  height=\"' . $height . '\" /&gt;',
            "\ud83d\ude11" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128529_bnbw55.png\"  height=\"' . $height . '\"  /&gt;',
            "\ud83d\ude12" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128530_fqr8qo.png\"  height=\"' . $height . '\"  /&gt;',
            "\ud83d\ude13" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128531_wyc2ns.png\" height=\"' . $height . '\" /&gt;',
            "\ud83d\ude14" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128532_y305gu.png\"  height=\"' . $height . '\" /&gt;',
            "\ud83d\ude15" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128533_qxgukg.png\"  height=\"' . $height . '\" /&gt;',
            "\ud83d\ude16" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128534_mxhgcb.png\"  height=\"' . $height . '\"  /&gt;',
            "\ud83d\ude17" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128535_m2nkuq.png\"  height=\"' . $height . '\" /&gt;',
            "\ud83d\ude18" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128536_vw77p8.png\"  height=\"' . $height . '\" /&gt;',
            "\ud83d\ude19" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128537_vqkswl.png\"  height=\"' . $height . '\" /&gt;',
            "\ud83d\ude1a" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128538_fi3oh3.png\"  height=\"' . $height . '\" /&gt;',
            "\ud83d\ude1b" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128539_pybfon.png\"  height=\"' . $height . '\" /&gt;',
            "\ud83d\ude1c" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128540_smhr5u.png\"  height=\"' . $height . '\" /&gt;',
            "\ud83d\ude1d" => '&lt;img src=\"https://res.cloudinary.com/af5sur5sejour/image/upload/w_500/v1603467532/newprod/emoji/128541_x0pote.png\"  height=\"' . $height . '\"  /&gt;',
        ];
        return $listEmojie[$code];
    }
    /*******************************/
    // helper functions
    function checkJson($title)
    {
        if (json_decode($title, true) !== null) {
            $title =  str_replace('"', '', json_decode($title));
        } else {
            $title = str_replace('"', '', $title);
        }
        return $title;
    }
    function decodeEmoticons($src, $height)
    {
        // $replaced = preg_replace("/\\\\u([0-9A-F]{1,4})/i",  "&#x$1;", $src);
        var_dump($src);
        var_dump(json_encode($src));
        $pattern = '/((?:\\\\u[\da-f]{4}){2})/';
        preg_match_all($pattern, json_encode($src), $mtchs);
        var_dump($mtchs);
        $src = json_encode($src);
        foreach ($mtchs[0] as $emojie) {
            var_dump($this->emojie($emojie, $height));
            $emogie = str_replace('u', '\\u', $emojie);
            //$emogieN=str_replace('u','\\\\\\u',$emojie);
            var_dump($emojie);
            $src = preg_replace("/" . $emogie . "/i",  $this->emojie($emojie, $height), $src);
        }
        $src = str_replace('< 3', '&lt;img src=\"https://5sur5sejour.com/redheartPDF.png\"  height=\"15px\" /&gt;', $src);
        var_dump(html_entity_decode(json_decode($src)));
        var_dump($src);
        //var_dump($find);
        // $ret = html_entity_decode($src, ENT_COMPAT, 'UTF-8');
        //mb_ord("⽇", "utf8");
        // var_dump(utf8_decode($src));
        return html_entity_decode(json_decode($src));
    }
    public function TcPdfOldVersion($idPrdt, $numCommande, $type)
    {
        ini_set("max_execution_time", -1);
        $em = $this->em;
        $Album = $em->getRepository(Produit::class)->findOneBy(['id' => $idPrdt]);
        $AllPages = $em->getRepository(Page::class)->findBy(['idproduit' => $Album]);
        if (sizeof($AllPages) > 24) {
            $pageLayout = array(22, 15.85);
        } else {
            $pageLayout = array(22, 16);
        }
        $pdf = $this->tcpdf->create('L', 'CM', $pageLayout, true, 'UTF-8', false);
        //$pdf = $this->container->get("white_october.tcpdf")->create('L', 'CM', $pageLayout, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Nicola Asuni');
        $pdf->SetTitle('TCPDF Example 009');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        //        $pdf->AddPage();
        $pdf->setJPEGQuality(200);
        $horizontal_alignments = array('L', 'C', 'R');
        $vertical_alignments = array('T', 'M', 'B');
        //var_dump($pdf->getPageWidth());
        //var_dump($pdf->getPageHeight());die();
        //dd($AllPages);
        //        $fP=[];
        //        array_push($fP,$AllPages[0]);
        //dd($AllPages);
        foreach ($AllPages as $p) {
            $contenu = json_decode(json_decode($p->getCouleurbordure())[0]);
            $nbatach = $contenu->nbrAttc;
            var_dump("nbrattcha________________" . $nbatach);
            if (($AllPages[sizeof($AllPages) - 1] == $p) && (($nbatach == "\"last\"") || ($nbatach == '0'))) {
                $pdf->AddPage();
                $bMargin = $pdf->getBreakMargin();
                $auto_page_break = $pdf->getAutoPageBreak();
                $pdf->SetAutoPageBreak(false, 0);
                $pdf->setJPEGQuality(200);
                $color = $contenu->color;
                // $color="rgb(255,255,255)";
                $color = str_replace('"rgb(', '', $color);
                $color = str_replace(')"', '', $color);
                $color = explode(",", $color);
                $colorp = array(intval($color[0]), intval($color[1]), intval($color[2]));
                if (intval($color[0] !== 0)) {
                    $pdf->Rect(0, 0, 22, 16, 'F', array(), $colorp);
                }
                $style = array(
                    'position' => '',
                    'align' => 'C',
                    'stretch' => false,
                    'fitwidth' => true,
                    'cellfitalign' => '',
                    'border' => false,
                    'hpadding' => 'auto',
                    'vpadding' => 'auto',
                    'fgcolor' => array(0, 0, 0),
                    'bgcolor' => false, //array(255,255,255),
                    'text' => true,
                    'font' => 'helvetica',
                    'fontsize' => 6,
                    'stretchtext' => 1
                );
                var_dump("hello");
                $exist = false;
                $path = 'https://res.cloudinary.com/af5sur5sejour/image/upload/v1589679918/newprod/logoHeader_yirn8n.png';
                if ($Album->getIdsjour()->getIdPartenaire() != null) {
                    if (($Album->getIdsjour()->getIdPartenaire()->getLogourl() != null) && (trim(($Album->getIdsjour()->getIdPartenaire()->getLogourl() != null)) != "")) {
                        $pdf->SetFont('helvetica', '', 15);
                        $pdf->SetXY(5, 4);
                        $pdf->write(1.5, 'Gardez un souvenir de votre voyage avec');
                        $pdf->SetXY(8, 7);
                        $path = $Album->getIdsjour()->getIdPartenaire()->getLogourl();
                        $path = preg_replace('/(?:png|gif)$/i', 'jpg', $path);
                        var_dump("im here");
                        if (preg_match('/(?:png|PNG)$/i', $path)) {
                            var_dump("png");
                            $pdf->Image($path, 7.5, 6, 6, 5, 'PNG', '', '', false, 200, '', false, false, 1, false, false, false);
                        } else {
                            var_dump("jpg");
                            $pdf->Image($path, 7.5, 6, 6, 5, 'JPG', '', '', false, 200, '', false, false, 1, false, false, false);
                        }
                        $exist = true;
                    }
                }
                if (!($exist)) {
                    $widhtL = 294 * 0.0264583333;
                    $hightL = 110 * 0.0264583333;
                    $xL = (22 - $widhtL) / 2;
                    $yL = (16 - $hightL) / 2;
                    $path = 'https://res.cloudinary.com/af5sur5sejour/image/upload/v1589679918/newprod/logoHeader_yirn8n.png';
                    $pdf->Image($path, $xL, $yL, $widhtL, $hightL, 'PNG', '', '', false, 200, '', false, false, 1, false, false, false);
                }
                //Touhemi 08-07:position cut
            } else {
                $pdf->AddPage();
                $pdf->setJPEGQuality(200);
                $contenu = json_decode(json_decode($p->getCouleurbordure())[0]);
                $checkBAck = str_replace('"', '', $nbatach);
                $checkbackground = false;
                if (substr($checkBAck, 1, 1) == 'F') {
                    $checkbackground = true;
                }
                $nbatach = $contenu->nbrAttc;
                $nbatach = intval(str_replace('"', '', $nbatach));
                $color = $contenu->color;
                // $color="rgb(255,255,255)";
                $color = str_replace('"rgb(', '', $color);
                $color = str_replace(')"', '', $color);
                $color = explode(",", $color);
                $colorp = array(intval($color[0]), intval($color[1]), intval($color[2]));
                $tabtxt = json_decode($contenu->txt);
                $tabphoto = json_decode($contenu->attache);
                //
                //dd($tabtxt);
                //         dd($tabtxt);
                //dd($tabphoto);
                $tabClips = json_decode($contenu->clips);
                // dd($tabClips);
                $x = 1.1;
                $y = 1.3;
                $w = 19.8;
                $h = 13.4;
                // get the current page break margin
                $bMargin = $pdf->getBreakMargin();
                // get current auto-page-break mode
                $auto_page_break = $pdf->getAutoPageBreak();
                // disable auto-page-break
                $pdf->SetAutoPageBreak(false, 0);
                // test all combinations of alignments
                $fitbox = $horizontal_alignments[1] . ' ';
                $fitbox[1] = $vertical_alignments[1];
                $pdf->Rect(0, 0, 22, 16, 'F', array(), $colorp);
                //    sizeof($tabphoto)
                for ($i = 0; $i < sizeof($tabphoto); $i++) {
                    $photo = json_decode($tabphoto[$i]);
                    //les coordonnées de l'image réel:
                    $hght = $photo->height;
                    $top = $photo->top;
                    $left = $photo->left;
                    $width = $photo->width;
                    $ordre = $photo->ordre;
                    $zoom = 1;
                    $path = $photo->path;
                    $hght = floatval(str_replace('cm', '', $hght));
                    $top = floatval(str_replace('cm', '', $top));
                    $left = floatval(str_replace('cm', '', $left));
                    $width = floatval(str_replace('cm', '', $width));
                    //les coordonnées dropzone:
                    $heightOriginal = $photo->height;
                    $widthOriginal = $photo->width;
                    $top = $photo->top;
                    $left = $photo->left;
                    $heightOriginal = floatval(str_replace('cm', '', $heightOriginal));
                    $widthOriginal = floatval(str_replace('cm', '', $widthOriginal));
                    $top = floatval(str_replace('cm', '', $top));
                    $left = floatval(str_replace('cm', '', $left));
                    $heightOriginal = $heightOriginal * 37.7952755906;
                    $widthOriginal = $widthOriginal * 37.7952755906;
                    $topOriginal = $top * 37.7952755906;
                    $leftOriginal = $left * 37.7952755906;
                    $heightCrop = $photo->heightCrop;
                    $topCrop = $photo->topCrop;
                    $leftCrop = $photo->leftCrop;
                    $widthCrop = $photo->widthCrop;
                    $path = $photo->path;
                    $heightCrop = floatval(str_replace('cm', '', $heightCrop));
                    $topCrop = floatval(str_replace('cm', '', $topCrop));
                    $leftCrop = floatval(str_replace('cm', '', $leftCrop));
                    $widthCrop = floatval(str_replace('cm', '', $widthCrop));
                    $widthCropPX = $widthCrop * 37.7952755906;
                    $heightCropPX = $heightCrop * 37.7952755906;
                    $topCropPX = $topCrop * 37.7952755906;
                    $leftCropPX = $leftCrop * 37.7952755906;
                    // var_dump("original widh: ".$widthCrop." "."original height : ".$heightCrop);
                    // var_dump("original left: ".$topCrop." "."original top : ".$leftCrop);
                    // var_dump("//00");
                    // var_dump("multip 37 widh: ".$widthCropPX." "."multip 37 height : ".$heightCropPX);
                    // var_dump("multip 37 top: ".$topCropPX." "."multip 37 left : ".$leftCropPX);
                    //Calculer position des images selon nombres images par page:
                    $positionX = 0;
                    $positionY = 0;
                    $widthImg = 0;
                    $heightImg = 0;
                    if ($checkbackground == true) {
                        $positionX = 0.5;
                        $positionY = 0.5;
                        $widthImg = 21;
                        $heightImg = 15;
                    } elseif ($nbatach == 1) {
                        $positionX = 2;
                        $positionY = 2;
                        $widthImg = 18;
                        $heightImg = 12;
                    } elseif ($nbatach == 2) {
                        if ($ordre == 1) {
                            $positionX = 2;
                            $positionY = 2;
                            $widthImg = 8.7;
                            $heightImg = 12;
                        }
                        if ($ordre == 2) {
                            $positionX = 11.3;
                            $positionY = 2;
                            $widthImg = 8.7;
                            $heightImg = 12;
                        }
                    } elseif ($nbatach == 3) {
                        if ($ordre == 1) {
                            $positionX = 2;
                            $positionY = 2;
                            $widthImg = 8.7;
                            $heightImg = 12;
                        }
                        if ($ordre == 2) {
                            $positionX = 11.3;
                            $positionY = 2;
                            $widthImg = 8.7;
                            $heightImg = 5.7;
                        }
                        if ($ordre == 3) {
                            $positionX = 11.3;
                            $positionY = 8.3;
                            $widthImg = 8.7;
                            $heightImg = 5.7;
                        }
                    } elseif ($nbatach == 4) {
                        if ($ordre == 1) {
                            $positionX = 2;
                            $positionY = 2;
                            $widthImg = 8.7;
                            $heightImg = 5.7;
                        }
                        if ($ordre == 2) {
                            $positionX = 2;
                            $positionY = 8.3;
                            $widthImg = 8.7;
                            $heightImg = 5.7;
                        }
                        if ($ordre == 3) {
                            $positionX = 11.3;
                            $positionY = 2;
                            $widthImg = 8.7;
                            $heightImg = 5.7;
                        }
                        if ($ordre == 4) {
                            $positionX = 11.3;
                            $positionY = 8.3;
                            $widthImg = 8.7;
                            $heightImg = 5.7;
                        }
                    } elseif ($nbatach == 5) {
                        if ($ordre == 1) {
                            $positionX = 2;
                            $positionY = 2;
                            $widthImg = 18;
                            $heightImg = 8;
                        }
                        if ($ordre == 2) {
                            $positionX = 2;
                            $positionY = 10.6;
                            $widthImg = 4.05;
                            $heightImg = 3.4;
                        }
                        if ($ordre == 3) {
                            $positionX = 6.65;
                            $positionY = 10.6;
                            $widthImg = 4.05;
                            $heightImg = 3.4;
                        }
                        if ($ordre == 4) {
                            $positionX = 11.3;
                            $positionY = 10.6;
                            $widthImg = 4.05;
                            $heightImg = 3.4;
                        }
                        if ($ordre == 5) {
                            $positionX = 15.95;
                            $positionY = 10.6;
                            $widthImg = 4.05;
                            $heightImg = 3.4;
                        }
                    } elseif ($nbatach == 6) {
                        if ($ordre == 1) {
                            $positionX = 2;
                            $positionY = 2;
                            $widthImg = 5.53;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 2) {
                            $positionX = 2;
                            $positionY = 6.2;
                            $widthImg = 5.53;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 3) {
                            $positionX = 2;
                            $positionY = 10.4;
                            $widthImg = 5.53;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 4) {
                            $positionX = 8.13;
                            $positionY = 2;
                            $widthImg = 5.53;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 5) {
                            $positionX = 8.13;
                            $positionY = 6.2;
                            $widthImg = 5.53;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 6) {
                            $positionX = 8.13;
                            $positionY = 10.4;
                            $widthImg = 5.53;
                            $heightImg = 3.6;
                        }
                    } elseif ($nbatach == 12) {
                        if ($ordre == 1) {
                            $positionX = 2;
                            $positionY = 2;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 2) {
                            $positionX = 2;
                            $positionY = 6.2;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 3) {
                            $positionX = 2;
                            $positionY = 10.4;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 4) {
                            $positionX = 6.65;
                            $positionY = 2;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 5) {
                            $positionX = 6.65;
                            $positionY = 6.2;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 6) {
                            $positionX = 6.65;
                            $positionY = 10.4;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 7) {
                            $positionX = 11.3;
                            $positionY = 2;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 8) {
                            $positionX = 11.3;
                            $positionY = 6.2;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 9) {
                            $positionX = 11.3;
                            $positionY = 10.4;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 10) {
                            $positionX = 15.95;
                            $positionY = 2;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 11) {
                            $positionX = 15.95;
                            $positionY = 6.2;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                        if ($ordre == 12) {
                            $positionX = 15.95;
                            $positionY = 10.4;
                            $widthImg = 4.05;
                            $heightImg = 3.6;
                        }
                    }
                    //Recarder l'image :
                    // $path=str_replace( 'upload/', 'upload/ar_1.1'.',c_crop/q_auto:good/',$path);<
                    //                $path=str_replace( 'upload/', 'upload/ar_1,c_crop,x_'.round($left*37.7952755906).',y_'.round($top*37.7952755906).',w_'.round($widthCropPX).',h_'.round($heightCropPX).',g_north_east/',$path);
                    if (strpos($path, 'res.cloudinary.com') !== false) {
                        $pathArray = explode("/", $path);
                        $idsArray = explode(".", $pathArray[sizeof($pathArray) - 1]);
                        $idImage = "";
                        foreach ($idsArray as $key => $elem) {
                            if ($key != (sizeof($idsArray) - 1)) {
                                $idImage = $idImage . $elem;
                            }
                        }
                        //   $cloudinaryWidht=$widthOriginal;
                        // $cloudinaryHeight=$widthOriginal;
                        $idImage = 'newprod/' . $idImage;
                        //var_dump($idImage);
                        if ((strstr($path, "af5sur5sejour"))) {
                            Unirest\Request::auth('263346742199243', 'jYw-jg0FOJGv89-o5Wo0Fa3rQWU');
                            $url = 'https://api.cloudinary.com/v1_1/af5sur5sejour/resources/image/upload/' . $idImage;
                        } else {
                            Unirest\Request::auth('319835665915435', 'xmlIYw147bjaGTtgk4D4UtiGBlg');
                            $url = 'https://api.cloudinary.com/v1_1/dknprksho/resources/image/upload/' . $idImage;
                        }
                        $headers = array('Accept' => 'application/json');
                        $data = array("public_ids" => array($idImage));
                        $body = Unirest\Request\Body::form($data);
                        Unirest\Request::verifyPeer(false);
                        // $resultMetadata=  \Cloudinary::Api.resources_by_ids([$idImage]);
                        //  var_dump($url);
                        $resultMetadata = Unirest\Request::post($url, $headers, $body);
                        if (isset(json_decode($resultMetadata->raw_body)->width)) {
                            $cloudinaryWidht = json_decode($resultMetadata->raw_body)->width;
                            $cloudinaryHeight = json_decode($resultMetadata->raw_body)->height;
                        } else {
                            $cloudinaryWidht = $widthOriginal;
                            $cloudinaryHeight = $heightOriginal;
                        }
                        $ratiohight = $cloudinaryWidht / $widthOriginal;
                        $ratioHight = $cloudinaryHeight / $heightOriginal;
                        //$cloudinaryHeight=$cloudinaryHeight*$zoom;
                        //$cloudinaryWidht=$cloudinaryWidht*$zoom;
                        var_dump($cloudinaryWidht);
                        var_dump($cloudinaryHeight);
                        var_dump($ratioHight);
                        var_dump($zoom);
                        var_dump($leftOriginal);
                        var_dump('x_' . round(abs($leftOriginal / $zoom) * $ratioHight));
                        var_dump('y_' . round(abs($topOriginal / $zoom) * $ratiohight));
                        var_dump('w_' . round(($widthCropPX / $zoom) * $ratioHight));
                        var_dump('h_' . round(($heightCropPX / $zoom) * $ratiohight));
                        //var_dump($path);i
                        //        $xFormule=($leftOriginal+())
                        $path = str_replace('upload/', 'upload/w_' . round($cloudinaryWidht) . ',h_' . round($cloudinaryHeight) . ',c_scale/x_' . round(abs($leftOriginal / $zoom) * $ratioHight) . ',y_' . round(abs($topOriginal / $zoom) * $ratiohight) . ',w_' . round(($widthCropPX / $zoom) * $ratioHight) . ',h_' . round(($heightCropPX / $zoom) * $ratiohight) . ',c_crop/', $path);
                        //var_dump($path);i
                        //                $path=str_replace( 'upload/', 'upload/w_'.round($widthOriginal).',h_'.round($heightOriginal).'/x_'.round(abs($leftOriginal)).',y_'.round(abs($topOriginal)).',w_'.round($widthCropPX).',h_'.round($heightCropPX).',c_crop/',$path);
                        //var_dump($path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_10,fl_relative.tiled,", '', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_35,fl_relative.tiled,", '', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_10,", '', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_35,", '', $path);
                        //  var_dump("//00");
                        //  var_dump("avant 1.4 widh: ".round($widthCropPX)." "."avant 1.4 height : ".round($heightCropPX));
                        //  var_dump("avant 1.4 top: ".round(abs($top*37.7952755906))." avant 1.4 final left : ".round(abs($left*37.7952755906)));
                        //    var_dump("//00");
                        //   var_dump("final widh: ".round($widthCropPX*1.4)." "."final height : ".round($heightCropPX*1.4));
                        //  var_dump("final top: ".round(abs($top*37.7952755906*1.4))." "."final left : ".round(abs($left*37.7952755906*1.4)));
                        var_dump($zoom);
                        var_dump($path);
                        // $path="https://res.cloudinary.com/af5sur5sejour/image/upload/w_691,h_356,c_crop/a_exif/v1587482806/newprod/crepes-au-chocolat_re9wvk.jpg";
                        $path = preg_replace('/(?:png|gif)$/i', 'jpg', $path);
                        $pdf->Image($path, $positionX, $positionY, $widthImg, $heightImg, 'JPG', '', '', false, 200, '', false, false, 0, false, false, false);
                        //                $pdf->Rect($positionX,$positionY ,$widthImg, $heightImg, 'F', array(), array(264,200,67));
                        //
                    } else {
                        //IMAGINARY
                        $pathArray = explode("/", $path);
                        $idsArray =  $pathArray[sizeof($pathArray) - 1];
                        $url = $this->params->get('nodeImaginaryHost') . '/info?url=' . $this->params->get('imaginaryHost') . '/upload/original/' . $idsArray;
                        //$idImage = 'newprod/' . $idImage;
                        $headers = array('Accept' => 'application/json');
                        Unirest\Request::verifyPeer(false);
                        $resultMetadata = Unirest\Request::get($url, $headers);
                        var_dump($resultMetadata);
                        if (isset(json_decode($resultMetadata->raw_body)->width)) {
                            var_dump("imaginary_metadata");
                            $cloudinaryWidht = json_decode($resultMetadata->raw_body)->width;
                            $cloudinaryHeight = json_decode($resultMetadata->raw_body)->height;
                        } else {
                            $cloudinaryWidht = $widthOriginal;
                            $cloudinaryHeight = $heightOriginal;
                        }
                        $ratiohight = $cloudinaryWidht / $widthOriginal;
                        $ratioHight = $cloudinaryHeight / $heightOriginal;
                        var_dump($cloudinaryWidht);
                        var_dump($cloudinaryHeight);
                        var_dump($ratioHight);
                        var_dump($zoom);
                        var_dump($leftOriginal);
                        var_dump('x_' . round(abs($leftOriginal / $zoom) * $ratioHight));
                        var_dump('y_' . round(abs($topOriginal / $zoom) * $ratiohight));
                        var_dump('w_' . round(($widthCropPX / $zoom) * $ratioHight));
                        var_dump('h_' . round(($heightCropPX / $zoom) * $ratiohight));
                        $topYImaginary = floor(abs($topOriginal / $zoom) * $ratiohight);
                        $leftXimaginary = floor(abs($leftOriginal / $zoom) * $ratioHight);
                        $widthimaginary = floor(($widthCropPX / $zoom) * $ratioHight);
                        $heigthimaginary = floor(($heightCropPX / $zoom) * $ratiohight);
                        if ($cloudinaryWidht < $widthimaginary) {
                            $widthimaginary = $cloudinaryWidht - $leftXimaginary;
                        }
                        if ($cloudinaryHeight < $heigthimaginary) {
                            $heigthimaginary = $cloudinaryHeight - $topYImaginary;
                        }
                        $newpath = $this->params->get('nodeImaginaryHost') . '/extract?areawidth=' . $widthimaginary . '&areaheight=' . $heigthimaginary . '&top=' . $topYImaginary . '&left=' . $leftXimaginary . '&url=' . $this->params->get('imaginaryHost') . '/upload/original/' . rawurlencode($idsArray);
                        $path = str_replace('upload/', 'upload/w_' . round($cloudinaryWidht) . ',h_' . round($cloudinaryHeight) . ',c_scale/x_' . round(abs($leftOriginal / $zoom) * $ratioHight) . ',y_' . round(abs($topOriginal / $zoom) * $ratiohight) . ',w_' . round(($widthCropPX / $zoom) * $ratioHight) . ',h_' . round(($heightCropPX / $zoom) * $ratiohight) . ',c_crop/', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_10,fl_relative.tiled,", '', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_35,fl_relative.tiled,", '', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_10,", '', $path);
                        $path = str_replace("l_Logo5Sur5White_nh6tyk,o_35,", '', $path);
                        var_dump($path);
                        var_dump($newpath);
                        //http://51.83.99.222:81/api/upload/w_1600,h_1200,c_scale/x_0,y_80,w_1600,h_1041,c_crop/a_exif/IMG_20201024_152305761%20(Copier)._5fcf3575800e7.jpg
                        //  $path = preg_replace('/(?:png|gif)$/i', 'jpg', $path);
                        $imgdata = file_get_contents($newpath);
                        if (preg_match('/\.(Png|png|PNG)$/', $newpath)) {
                            $pdf->Image('@' . $imgdata, $positionX, $positionY, $widthImg, $heightImg, 'PNG', '', '', false, 200, '', false, false, 0, false, false, false);
                        } else {
                            $pdf->Image('@' . $imgdata, $positionX, $positionY, $widthImg, $heightImg, 'JPG', '', '', false, 200, '', false, false, 0, false, false, false);
                        }
                    }
                }
                //    $pdf->Image('https://demo.appsfactor.fr/images/ClipArt_SVG/Etoilerose.svg',3,  2, 19, 13, 'SVG', '', '', false, 300, '', false, false, 0, $fitbox, false, false);
                for ($i = 0; $i < sizeof($tabtxt); $i++) {
                    $txt = json_decode($tabtxt[$i]);
                    $fontSize = floatval(str_replace('px', '', $txt->fontSize)) * 0.75;
                    //$txt->rotation
                    $check = explode(',', $txt->fontFamily);
                    $finalFont = "times";
                    $weight = '';
                    if (sizeof($check) > 1) {
                        //default
                        if (($check[0] == "-apple-system") && ($txt->fontWeight == 400)) {
                            $finalFont = "helvetica";
                            $weight = '';
                        }
                        //classique
                        if (($check[0] == "Georgia") && ($txt->fontWeight == 400)) {
                            $finalFont = "times";
                            $weight = '';
                        }
                        //creative
                        if (($check[0] == "-apple-system") && ($txt->fontWeight == 700)) {
                            $finalFont = "helveticaB";
                            $weight = '';
                        }
                        // manuscrite
                        if (($check[0] == "Comic Sans") && ($txt->fontWeight == 400)) {
                            $finalFont = "Courier";
                            $weight = '';
                        }
                        //c.s-microsoft.com/static/fonts/segoe-ui/west-european/light/latest.woff2
                    } else {
                        //baton
                        if (($txt->fontFamily == 'Impact') && ($txt->fontWeight == 400)) {
                            $finalFont = "helveticaB";
                            $weight = 'B';
                        }
                    }
                    $pdf->SetFont($finalFont, $weight, $fontSize);
                    $leftTxt = $txt->left;
                    $topTxt = $txt->top;
                    $heightClips = $txt->height;
                    $widthClips = $txt->width;
                    $heightTxt = floatval(str_replace('cm', '', $heightClips));
                    $topTxt = floatval(str_replace('cm', '', $topTxt)) + 0.5;
                    $leftTxt = floatval(str_replace('cm', '', $leftTxt)) + 0.5;
                    $widthTxt = floatval(str_replace('cm', '', $widthClips));
                    $pdf->SetXY($leftTxt, $topTxt, true);
                    //                    var_dump($leftTxt);
                    //                  var_dump($topTxt);
                    // var_dump(floatval(str_replace('rad','',$txt->rotation))*57,2958);
                    //                var_dump("text");
                    $pdf->StartTransform();
                    var_dump($txt->rotation);
                    var_dump(str_replace('rad', '', $txt->rotation));
                    var_dump(floatval(str_replace('rad', '', $txt->rotation)) * 57.2958);
                    $pdf->Rotate((floatval(str_replace('rad', '', $txt->rotation)) * 57.2958) * -1, $leftTxt + ($widthTxt / 2), $topTxt + ($heightTxt / 2));
                    // $pdf->Rotate(45);
                    //   $pdf->Text($leftTxt, $topTxt, $txt->contenu);
                    if ($colorp == [0, 0, 0]) {
                        $pdf->SetTextColor(255, 255, 255);
                    }
                    //$pdf->Text($leftTxt, $topTxt, $txt->contenu);
                    // $pdf->MultiCell($leftTxt, $topTxt,  $txt->contenu, 0, $ln=0, 'C', 0, '', 0, false, 'C', 'C');
                    $pdf->Write(str_replace('cm', '', $txt->height), trim($txt->contenu));
                    // $pdf->writeHTML("<p>".$txt->contenu."</p>", true, false, false, false, '');
                    $pdf->StopTransform();
                }
                //Positionner text
                //            $pdf->ImageSVG("C:\\Users\\AppsFactor12\\Desktop\\5sur5\\5sur5Sejour\\public\\images\\ClipArt_SVG\\Etoilerose.svg",100,200,500, 500, '', '', '', 0, false);
                //Positionner clipart
                //            dd($tabClips);
                for ($i = 0; $i < sizeof($tabClips); $i++) {
                    $Clips = json_decode($tabClips[$i]);
                    $heightClips = $Clips->height;
                    $topClips = $Clips->top;
                    $leftClips = $Clips->left;
                    $widthClips = $Clips->width;
                    $path = $Clips->path;
                    $pathClips = str_replace('"', '', $path);
                    $heightClips = floatval(str_replace('cm', '', $heightClips));
                    $topClips = floatval(str_replace('cm', '', $topClips)) + 0.5;
                    $leftClips = floatval(str_replace('cm', '', $leftClips)) + 0.5;
                    $widthClips = floatval(str_replace('cm', '', $widthClips));
                    $heightClipsPX = round($heightClips * 37.7952755906);
                    $widthClipsPX = round($widthClips * 37.7952755906);
                    ////                $positionXclips = $positionX + $leftClips;
                    ////                $positionYclips = $positionY + $topClips;
                    //
                    //$pdf->ImageSVG("images/ClipArt_SVG/Ete4.svg",$leftClips,$topClips,$widthClips, $heightClips);
                    //https://res.cloudinary.com/af5sur5sejour/image/private/s--EdExAzx8--/v1588758453/GlobeFooter_c4duua.svg
                    // https://res.cloudinary.com/af5sur5sejour/image/upload/v1588764528/Groupe_113_pcjyj4.png
                    var_dump($pathClips);
                    $pdf->StartTransform();
                    $pdf->Rotate((floatval(str_replace('rad', '', $Clips->rotation)) * 57.2958) * -1, $leftClips + ($widthClips / 2), $topClips + ($heightClips / 2));
                    $pdf->Image($this->newPAthCLipart($pathClips, $heightClipsPX, $widthClipsPX), $leftClips, $topClips, $widthClips, $heightClips, '', '', '', false, 200);
                    $pdf->StopTransform();
                }
                //Touhemi 08-07:position cut
                ////Touhemi:fin position cut
            }
            if ($AllPages[sizeof($AllPages) - 1] == $p) {
                // PRINT VARIOUS 1D BARCODES
                $style = array(
                    'position' => '',
                    'align' => 'C',
                    'stretch' => false,
                    'fitwidth' => true,
                    'cellfitalign' => '',
                    'border' => false,
                    'hpadding' => 'auto',
                    'vpadding' => 'auto',
                    'fgcolor' => array(0, 0, 0),
                    'bgcolor' => array(255, 255, 255), //false
                    'text' => true,
                    'font' => 'helvetica',
                    'fontsize' => 6,
                    'stretchtext' => 1
                );
                $pdf->SetFont('helvetica', '', 4);
                $pdf->StartTransform();
                $pdf->Rotate(270, 21.6, 10.5);
                $pdf->write1DBarcode($numCommande, 'C39',  21.6, 10.5, 3.5, 1.5, 0.4, $style, 'N');
                $pdf->StopTransform();
            }
            if (sizeof($AllPages) > 24) {
                $pdf->cropMark(0.5, 0.5, 0.5, 0.5, 'TL', array(124, 252, 0));
                $pdf->cropMark(21.5, 0.5, 0.5, 0.5, 'TR', array(124, 252, 0));
                $pdf->cropMark(0.5, 15.35, 0.5, 0.5, 'BL', array(124, 252, 0));
                $pdf->cropMark(21.5, 15.35, 0.5, 0.5, 'BR', array(124, 252, 0));
            } else {
                $pdf->cropMark(0.5, 0.5, 0.5, 0.5, 'TL', array(124, 252, 0));
                $pdf->cropMark(21.5, 0.5, 0.5, 0.5, 'TR', array(124, 252, 0));
                $pdf->cropMark(0.5, 15.5, 0.5, 0.5, 'BL', array(124, 252, 0));
                $pdf->cropMark(21.5, 15.5, 0.5, 0.5, 'BR', array(124, 252, 0));
            }
        }
        // echo '</pre>';
        //return new response("yezi");
        $projectRoot = $this->params->get('projectDir');
        $pdf->Output($projectRoot . '/public/pdfDocs/' . $numCommande . '_' . $type . '_' . $idPrdt . '.pdf', 'F');
        return $numCommande . '_' . $type . '_' . $idPrdt . '.pdf';
        //return $pdf->Output('example_009.pdf', 'I');
    }
    public function PackPhotosNumerique($idSejour, $namezip, $idproduit)
    {
        $em = $this->em;
        ini_set("max_execution_time", -1);
        $arrayFiles = [];
        $projectRoot = $this->params->get('projectDir');
        $sejour = $em->getRepository(Sejour::class)->find($idSejour);
        $attachements = $em->getRepository(Photonsumeriques::class)->findBy(array('idProduit' => $idproduit));
        //var_dump(sizeof($sejour));
        //    foreach ($sejours as $sejour) {
        if (!file_exists($projectRoot . '/public/ParentPhotosNumerique/' . $namezip . '/' . $namezip . '.zip')) {
            $arrayFiles = [];
            $sejouattach = $sejour->getAttachements();
            var_dump($sejour->getCodeSejour());
            if (!file_exists($projectRoot . '/public/ParentPhotosNumerique/' . $namezip)) {
                mkdir($projectRoot . '/public/ParentPhotosNumerique/' . $namezip, 0777, true);
            }
            foreach ($attachements as $a) {
                $attach = $a->getIdSejourAttachement();
                try {
                    if (($attach->getIdAttchment()->getIdref() != null) && (in_array($attach->getIdAttchment()->getIdref()->getId(), [6, 7, 30]))) {
                        $path = $attach->getIdAttchment()->getPath();
                        file_get_contents(str_replace("https://media.5sur5sejour.com/api/upload/a_exif", "http://54.36.104.133/api/upload", $path));
                        $filename = basename($path);
                        array_push($arrayFiles, $filename);
                        if (!file_exists($projectRoot . '/public/ParentPhotosNumerique/' . $namezip . '/' . $filename)) {
                            file_put_contents($projectRoot . '/public/ParentPhotosNumerique/' . $namezip . '/' . $filename, file_get_contents(str_replace("https://media.5sur5sejour.com/api/upload/a_exif", "http://54.36.104.133/api/upload", $path)));
                        }
                    }
                } catch (\Exception $e) {
                    var_dump($path);
                }
            }
            $zip = new ZipArchive;
            //  var_dump($projectRoot . '/public/ParentPhotosNumerique/' . $sejour->getId() . '/' . $sejour->getCodeSejour() . '.zip');
            if ($zip->open($projectRoot . '/public/ParentPhotosNumerique/' . $namezip . '/' . $namezip . '.zip', ZipArchive::CREATE) === TRUE) {
                // Add files to the zip file
                foreach ($arrayFiles as $fileNames) {
                    $zip->addFile($projectRoot . '/public/ParentPhotosNumerique/' . $namezip . '/' . $fileNames, $fileNames);
                }
                $zip->close();
            }
            //     var_dump($sejour->getCodeSejour() . '.zip');
        }
        //   }
        return 'done';
    }
}
