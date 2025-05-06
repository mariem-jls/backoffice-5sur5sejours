<?php

namespace App\Service;


use App\Entity\Emailing;
use App\Entity\SejourAttachment;
use Swift_Image;
use Twig\Environment;
use App\Entity\Sejour;
use App\Entity\Produit;
use App\Entity\Attachment;
use App\Entity\Commande;
use App\Entity\PromoParents;
use App\Entity\PromoSejour;
use App\Entity\ComandeProduit;
use App\Entity\ParentSejour;
use App\Entity\Ref;
use App\Entity\Position;
use App\Entity\Likephoto;
use App\Entity\User;
use App\Entity\Etablisment;
use App\Entity\Jourdescripdate;
use App\Entity\LogPromotions;
use App\Entity\PanierProduit;
use App\Entity\Promotions;
use Swift_Attachment;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use \DatePeriod;
use \DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SejourService
{
    private $em;
    // private $mailer;
    private $templating;
    private $projectDir;
    private $params;
    private $client;
    public function __construct(EntityManagerInterface  $em, Environment $templating, KernelInterface $kernel, ParameterBagInterface $params, HttpClientInterface $client)
    {
        $this->em = $em;
        // $this->mailer = $mailer;
        $this->templating = $templating;
        $this->projectDir = $kernel->getProjectDir();
        $this->params = $params;
        $this->client = $client;
    }
    function CreationNouveauSejour($themSejour, $adressSejour, $codePostal, $dateDebut, $FinSejour, $AgeDugroupe, $type, $userid, $NbEnfant, $connpay, $pays, $ville, $prixcnxparent = null, $prixcnxpartenaire = null, $reversecnxpart = null, $reverseventepart = null)
    {
        $sejour = new Sejour();
        $sejour->setCodeSejour($this->GenrateCodeSejour($type, $connpay));
        $sejour->setDateCreationCode(new \DateTime());
        $sejour->setThemSejour($themSejour);
        $sejour->setAdresseSejour($adressSejour);
        if ($codePostal == "") {
            $codePostal = 0;
        }
        $sejour->setCodePostal($codePostal);
        $dateDebutFormat = date_create_from_format('Y-m-d', $dateDebut);
        $sejour->setDateDebutSejour($dateDebutFormat);
        $FinSejourSejour = date_create_from_format('Y-m-d', $FinSejour);
        $sejour->setDateFinSejour($FinSejourSejour);
        $FinSejourSejour->modify('+2 month');
        $sejour->setDateFinCode($FinSejourSejour);
        $Statut = $this->em->getRepository(Ref::class)->findOneBy(array('libiller' => "Crée"));
        $sejour->setStatut($Statut);
        $AgeDugroupeRef = $this->em->getRepository(Ref::class)->findOneBy(array('libiller' => $AgeDugroupe));
        $sejour->setAgeGroup($AgeDugroupeRef);
        $sejour->setNbenfan($NbEnfant);
        $sejour->setNbenfantencours($NbEnfant);
        $sejour->setPays($pays);
        $sejour->setVille($ville);
        $sejour->setPrixcnxparent($prixcnxparent);
        $sejour->setPrixcnxpartenaire($prixcnxpartenaire);
        $sejour->setReversecnxpart($reversecnxpart);
        $sejour->setReverseventepart($reverseventepart);
        //setif sejour gratuie ou non
        $sejour->setPaym($connpay);
        $this->em->persist($sejour);
        $this->em->flush();
        return $sejour;
    }
    function CreationNouveauSejourParAccompagnateur($themSejour, $adressSejour, $dateDebut, $FinSejour, $AgeDugroupe, $type, $NbEnfant, $connpay, $pays, $ville, $prixcnxparent, $prixcnxpartenaire, $reversecnxpart, $reverseventepart)
    {
        $sejour = new Sejour();
        $sejour->setCodeSejour($this->GenrateCodeSejour($type, $connpay));
        $sejour->setDateCreationCode(new \DateTime());
        $sejour->setThemSejour($themSejour);
        $sejour->setAdresseSejour($adressSejour);
        $dateDebutFormat = date_create_from_format('Y-m-d', $dateDebut);
        $sejour->setDateDebutSejour($dateDebutFormat);
        $FinSejourSejour = date_create_from_format('Y-m-d', $FinSejour);
        $dateFinCode = new \DateTime($FinSejour);
        $dateFinCode->modify('+2 month');
        $sejour->setDateFinCode($dateFinCode);
        $sejour->setDateFinSejour($FinSejourSejour);
        $Statut = $this->em->getRepository(Ref::class)->findOneBy(array('libiller' => "Crée"));
        $sejour->setStatut($Statut);
        $AgeDugroupeRef = $this->em->getRepository(Ref::class)->findOneBy(array('libiller' => $AgeDugroupe));
        $sejour->setAgeGroup($AgeDugroupeRef);
        $sejour->setNbenfan($NbEnfant);
        $sejour->setNbenfantencours($NbEnfant);
        $sejour->setPays($pays);
        $sejour->setVille($ville);
        $sejour->setPrixcnxparent($prixcnxparent);
        $sejour->setPrixcnxpartenaire($prixcnxpartenaire);
        $sejour->setReversecnxpart($reversecnxpart);
        $sejour->setReverseventepart($reverseventepart);
        //set if sejour gratuie ou non 
        $sejour->setPaym($connpay);
        $this->em->persist($sejour);
        $this->em->flush();
        return $sejour;
    }
    function EnvoyerEmailCodeSejour($idSejour, $TypeSejour)
    {
        $Sejour = $this->em->getRepository(Sejour::class)->find($idSejour);
        $RefEmail = $this->em->getRepository(Ref::class)->find(28);
        $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
        $sendTo = $Sejour->getIdEtablisment()->getEmail();
        $message = (new \Swift_Message('Votre code séjour 5sur5sejour'))
            ->setFrom('no-reply@5sur5sejour.com')
            ->setTo($sendTo);
        //->setBcc(["contact@5sur5sejour.com"]);
        $pathImage2 = $Email->getIdImage2()->getPath();
        $pathImage1 = $Email->getIdImage1()->getPath();
        $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
        $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
        $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
        $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
        $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
        $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
        $message->setBody(
            $this->templating->render(
                'emails/SendCode.html.twig',
                [
                    "code" => $Sejour->getCodeSejour(),
                    "dateCreation" => $Sejour->getDateCreationCode(),
                    "dateFinCode" => $Sejour->getDateFinCode(),
                    "lieu" => $Sejour->getAdresseSejour(),
                    "image1" => $image1,
                    "image2" => $image2,
                    "iconfooter" => $iconfooter,
                    "iconphoto" => $iconphoto,
                    "iconloca" => $iconloca,
                    "iconmsg" => $iconmsg,
                    "destinataire" => $Sejour->getIdEtablisment()->getNometab()
                ]
            ),
            'text/html'
        );
        $signMail = $this->params->get('signMail');
        if ($signMail == 'yes') {
            $domainName = $this->params->get('domaine');
            $selector = $this->params->get('selector');
            $PrivateKey = file_get_contents($this->params->get('pathDKIM'));
            $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
            $message->attachSigner($signer);
        }
        try {
            // $done = $this->mailer->send($message);
            $resend = Resend::client('re_3ijtfF8W_AQyRsePq3jmmAaEohQig9Se8');

            $resend->emails->send($message);
            //var_dump($done);  
            // var_dump("send it ");
        } catch (\Swift_SwiftException $ex) {
            //var_dump( $ex->getMessage());
        }
    }
    function EnvoyerEmailCodeSejourAccompagnateur($idSejour)
    {
        $logo = '';
        $nom = '';
        $RefEmail = $this->em->getRepository(Ref::class)->find(21);
        $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
        $Sejour = $this->em->getRepository(Sejour::class)->find($idSejour);
        if ($Sejour) {
            //dd($sejour);
            $pdf1 = new Fpdi();
            $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
            $pdfFilepath1 = $publicDirectory . "Mode_emploi_5sur5sejour_v5_.pdf";
            $pageCount = $pdf1->setSourceFile($pdfFilepath1);
            $pageId = $pdf1->importPage(1, PdfReader\PageBoundaries::MEDIA_BOX);
            $pdf1->addPage();
            $pdf1->useImportedPage($pageId, 0.5, 0.5, 0.5);
            $testText  = "abcdefghijklmnopqrstuvwxyz0123456789";
            $current_date = date('d-M-Y');
            $DateDebut =  "";
            $DateFin = "";
            $Theme = "";
            $Lieu = "";
            $CodeSejour =  "";
            $MotPass =  "";
            $Ville = "";
            $Paye = "";
            if ($Sejour) {
                $DateDebut = $Sejour->getDateDebutSejour();
                $DateFin = $Sejour->getDateFinSejour();
                $Theme = $Sejour->getThemSejour();
                $Lieu = $Sejour->getAdresseSejour();
                $CodeSejour = $Sejour->getCodeSejour();
                $MotPass = $Sejour->getIdAcommp()->getPasswordNonCripted();
                $Ville = $Sejour->getVille();
                $Paye = $Sejour->getPays();
            }
            for ($i = 0; $i < $pageCount; $i++) {
                $tplIdx = $pdf1->importPage(1 + $i);
                $pdf1->useTemplate($tplIdx, 0, 0, 200, 300, true);
                $theme = $Theme;
                if (strlen($theme) > 25) {
                    $pdf1->SetFont("Arial", "", 5);
                } else {
                    $pdf1->SetFont("Arial", "", 7);
                }
                $strTheme = iconv("UTF-8", "WINDOWS-1252", $theme);
                $pdf1->Text(35, 262, $strTheme);
                $pdf1->SetFont("Arial", "", 8);
                $lieu = $Lieu;
                $ville = $Ville;
                $paye = $Paye;
                if ($Paye && $Ville) {
                    $strLieu = iconv("UTF-8", "WINDOWS-1252", $ville . "  " . $paye);
                    $pdf1->Text(125, 262, $strLieu);
                } else if ($Paye) {
                    $strLieu = iconv("UTF-8", "WINDOWS-1252", $paye);
                    $pdf1->Text(125, 262, $strLieu);
                } else {
                    $strLieu = iconv("UTF-8", "WINDOWS-1252", $ville);
                    $pdf1->Text(125, 262, $strLieu);
                }
                $pdf1->SetFont("Arial", "", 10);
                $pdf1->Text(50, 252, $DateDebut->format('d/m/Y'));
                $pdf1->SetFont("Arial", "", 10);
                $pdf1->Text(90, 252, $DateFin->format('d/m/Y'));
                $pdf1->SetFont("Arial", "", 10);
                $pdf1->Text(90, 271, $CodeSejour);
                $pdf1->SetFont("Arial", "", 10);
                $pdf1->Text(75, 279, $MotPass);
            }
            $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
            $pdfFilepath1 = $publicDirectory . "Mode d'emploi 5sur5sejour" . $idSejour . ".pdf";
            $pdf1->Output($pdfFilepath1, "F");
            $pdf = new Fpdi();
            $paym = $Sejour->getPaym();
            if ($paym == 1) {
                $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
                $pdfFilepath = $publicDirectory . "Mode_emploi_Parent_5sur5sejour_v5_.pdf";
                $pageCount = $pdf->setSourceFile($pdfFilepath);
            } else {
                $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
                $pdfFilepath = $publicDirectory . "Mode_emploi_Parent_G_5sur5sejou_v5_.pdf";
                $pageCount = $pdf->setSourceFile($pdfFilepath);
            }
            $pageId = $pdf->importPage(1, PdfReader\PageBoundaries::MEDIA_BOX);
            $pdf->addPage();
            $pdf->useImportedPage($pageId, 0.5, 0.5, 0.5);
            $testText  = "abcdefghijklmnopqrstuvwxyz0123456789";
            $current_date = date('d-M-Y');
            $DateDebut =  "";
            $DateFin = "";
            $Theme = "";
            $Lieu = "";
            $CodeSejour =  "";
            $Ville = "";
            $Paye = "";
            if ($Sejour) {
                $DateDebut = $Sejour->getDateDebutSejour();
                $DateFin = $Sejour->getDateFinSejour();
                $Theme = $Sejour->getThemSejour();
                $Lieu = $Sejour->getAdresseSejour();
                $CodeSejour = $Sejour->getCodeSejour();
                $Ville = $Sejour->getVille();
                $Paye = $Sejour->getPays();
            }
            for ($i = 0; $i < $pageCount; $i++) {
                $tplIdx = $pdf->importPage($i + 1);
                $pdf->useTemplate($tplIdx, 0, 0, 200, 300, true);
                $theme = $Theme;
                if (strlen($theme) > 25) {
                    $pdf->SetFont("Arial", "", 5);
                } else {
                    $pdf->SetFont("Arial", "", 7);
                }
                $strTheme = iconv("UTF-8", "WINDOWS-1252", $theme);
                $pdf->Text(35, 268, $strTheme);
                $pdf->SetFont("Arial", "", 8);
                $lieu = $Lieu;
                $ville = $Ville;
                $paye = $Paye;
                if ($Paye && $Ville) {
                    $strLieu = iconv("UTF-8", "WINDOWS-1252", $ville . "  " . $paye);
                    $pdf->Text(125, 268, $strLieu);
                } else if ($Paye) {
                    $strLieu = iconv("UTF-8", "WINDOWS-1252", $paye);
                    $pdf->Text(125, 268, $strLieu);
                } else {
                    $strLieu = iconv("UTF-8", "WINDOWS-1252", $ville);
                    $pdf->Text(125, 268, $strLieu);
                }
                $pdf->SetFont("Arial", "", 10);
                $pdf->Text(80, 279, $CodeSejour);
                $pdf->SetFont("Arial", "", 10);
                $pdf->Text(48, 258, $DateDebut->format('d/m/Y'));
                $pdf->SetFont("Arial", "", 10);
                $pdf->Text(89, 258, $DateFin->format('d/m/Y'));
            }
            if ($paym == 1) {
                $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
                $pdfFilepath = $publicDirectory . "Mode d'emploi parent  5sur5séjour" . $idSejour . ".pdf";
                $pdf->Output($pdfFilepath, "F");
            } else {
                $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
                $pdfFilepath = $publicDirectory . "Mode d'emploi parent G 5sur5séjour " . $idSejour . ".pdf";
                $pdf->Output($pdfFilepath, "F");
            }
            $logo = $Sejour->getIdEtablisment()->getLogo();
            $nom = $Sejour->getIdEtablisment()->getNometab();
            $sendTo = $Sejour->getIdAcommp()->getReponseemail();
            $message = (new \Swift_Message('Votre code séjour 5sur5sejour'))
                ->setFrom('no-reply@5sur5sejour.com')
                ->setTo($sendTo);
            //->setBcc(["contact@5sur5sejour.com"]);
            $pathImage2 = $Email->getIdImage2()->getPath();
            $pathImage1 = $Email->getIdImage1()->getPath();
            $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
            $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
            $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
            $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
            $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
            $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
            if ($paym == 1) {
                $message->attach(\Swift_Attachment::fromPath($pdfFilepath));
                $message->attach(\Swift_Attachment::fromPath($pdfFilepath1));
            } else {
                $message->attach(\Swift_Attachment::fromPath($pdfFilepath));
                $message->attach(\Swift_Attachment::fromPath($pdfFilepath1));
            }
            $message->setBody(
                $this->templating->render(
                    'emails/SendCode.html.twig',
                    [
                        "Password" => $Sejour->getIdAcommp()->getPasswordNonCripted(),
                        "code" => $Sejour->getCodeSejour(),
                        "dateCreation" => $Sejour->getDateCreationCode(),
                        "dateFinCode" => $Sejour->getDateFinCode(),
                        "lieu" => $Sejour->getAdresseSejour(),
                        "image1" => $image1,
                        "image2" => $image2,
                        "iconfooter" => $iconfooter,
                        "iconphoto" => $iconphoto,
                        "iconloca" => $iconloca,
                        "iconmsg" => $iconmsg,
                        'logo' => $logo,
                        'nom' => $nom,
                        "identifiant" => $Sejour->getCodeSejour(),
                        'roles' => $Sejour->getIdAcommp()->getRoles(),
                    ]
                ),
                'text/html'
            );
            $signMail = $this->params->get('signMail');
            if ($signMail == 'yes') {
                $domainName = $this->params->get('domaine');
                $selector = $this->params->get('selector');
                $PrivateKey = file_get_contents($this->params->get('pathDKIM'));
                $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
                $message->attachSigner($signer);
            }
            try {
                // $this->mailer->send($message);
                $resend = Resend::client('re_3ijtfF8W_AQyRsePq3jmmAaEohQig9Se8');

                $resend->emails->send($message);
            } catch (\Swift_SwiftException $ex) {
                $ex->getMessage();
            }
        }
    }
    function EnvoyerEmailNewAcco($emailsend, $accompagnateur, $idSejour, $passAcommpa)
    {
        $RefEmail = $this->em->getRepository(Ref::class)->find(16);
        $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
        $Sejour = $this->em->getRepository(Sejour::class)->find($idSejour);
        $sendTo = $accompagnateur->getEmail();
        $message = (new \Swift_Message('Bienvenue à 5sur5 séjour '))
            ->setFrom('no-reply@5sur5sejour.com')
            ->setTo($emailsend);
        //->setBcc(["contact@5sur5sejour.com"]);
        $pathImage2 = $Email->getIdImage2()->getPath();
        $pathImage1 = $Email->getIdImage1()->getPath();
        $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
        $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
        $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
        $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
        $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
        $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
        $message->setBody(
            $this->templating->render(
                'emails/DemandeCreationAcc.html.twig',
                [
                    "Nomdestinataire" => $accompagnateur->getNom(),
                    "Predestinataire" => $accompagnateur->getPrenom(),
                    "code" => $Sejour->getCodeSejour(),
                    "image1" => $image1,
                    "image2" => $image2,
                    "iconfooter" => $iconfooter,
                    "iconphoto" => $iconphoto,
                    "iconloca" => $iconloca,
                    "iconmsg" => $iconmsg,
                    "identifiant" => $sendTo,
                    "roles" => $accompagnateur->getRoles(),
                    'accompagnateur' => $accompagnateur,
                    "passAcommpa" => $passAcommpa,
                ]
            ),
            'text/html'
        );
        $signMail = $this->params->get('signMail');
        if ($signMail == 'yes') {
            $domainName = $this->params->get('domaine');
            $selector = $this->params->get('selector');
            $PrivateKey = file_get_contents($this->params->get('pathDKIM'));
            $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
            $message->attachSigner($signer);
        }
        try {
            // $this->mailer->send($message);
            $resend = Resend::client('re_3ijtfF8W_AQyRsePq3jmmAaEohQig9Se8');

            $resend->emails->send($message);
        } catch (\Swift_SwiftException $ex) {
            $ex->getMessage();
        }
    }
    function EnvoyerEmailNewPartenaire($partenaire)
    {
        $logo = '';
        $nom = '';
        if ($partenaire->hasRole('ROLE_PARTENAIRE')) {
            $logo = $partenaire->getLogourl();
            $nom = $partenaire->getNometablisment();
        }
        $RefEmail = $this->em->getRepository(Ref::class)->find(17);
        $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail));
        $sendTo = $partenaire->getEmail();
        $message = (new \Swift_Message('Nouveau partenaire'))
            ->setFrom('partenariat-no-reply@5sur5sejour.com')
            ->setTo($sendTo)
            ->setBcc(["contact@5sur5sejour.com"]);
        $pathImage2 = $Email->getIdImage2()->getPath();
        $pathImage1 = $Email->getIdImage1()->getPath();
        $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
        $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
        $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
        $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
        $icon2 = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
        $icon3 = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
        $message->setBody(
            $this->templating->render(
                'emails/NewPartenaireEmail.html.twig',
                [
                    "Nomdestinataire" => $partenaire->getNom(),
                    "Predestinataire" => $partenaire->getPrenom(),
                    "password" => $partenaire->getPasswordNonCripted(),
                    "image1" => $image1,
                    "image2" => $image2,
                    "icon2" => $icon2,
                    "icon3" => $icon3,
                    "iconfooter" => $iconfooter,
                    "iconmsg" => $iconmsg,
                    "idPartenaire" => $partenaire->getId(),
                    "identifiant" => $sendTo,
                    "password" => $partenaire->getPasswordNonCripted(),
                    'logo' => $logo,
                    'nom' => $nom,
                ]
            ),
            'text/html'
        );
        $signMail = $this->params->get('signMail');
        if ($signMail == 'yes') {
            $domainName = $this->params->get('domaine');
            $selector = $this->params->get('selector');
            $PrivateKey = file_get_contents($this->params->get('pathDKIM'));
            $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
            $message->attachSigner($signer);
        }
        try {
            // $this->mailer->send($message);
            $resend = Resend::client('re_3ijtfF8W_AQyRsePq3jmmAaEohQig9Se8');

            $resend->emails->send($message);
        } catch (\Swift_SwiftException $ex) {
            $ex->getMessage();
        }
    }
    function affecterEtablissement($sejour, $etablissement)
    {
        $sejour->setIdEtablisment($etablissement);
        $this->em->persist($sejour);
        $this->em->flush();
    }
    function affecterAccompaniateur($sejour, $accomp)
    {
        $sejour->setIdAcommp($accomp);
        $this->em->persist($sejour);
        $this->em->flush();
    }
    function affecterPartenaire($sejour, $etabUser)
    {
        $sejour->setIdPartenaire($etabUser);
        $this->em->persist($sejour);
        $this->em->flush();
    }
    function affecteretablisment($sejour, $Etablisment)
    {
        $sejour->setIdEtablisment($Etablisment);
        $this->em->persist($sejour);
        $this->em->flush();
    }
    function GenrateCodeSejour($type, $connpay)
    {
        $Typeabr = "IN";
        $pay = "";
        if ($type == "ECOLES/AUTRES") {
            $Typeabr = "E";
        } elseif ($type == "PARTENAIRES/VOYAGISTES") {
            $Typeabr = "P";
        } elseif ($type == "CSE") {
            $Typeabr = "C";
        }
        if ($connpay == 0) {
            $pay = "F";
        }
        if ($connpay == 1) {
            $pay = "P";
        }
        $date = new \Datetime();
        $Milliseconde = $date->format('u');
        $code = $Typeabr . $pay . $Milliseconde;
        return $code;
    }
    function index($dateTimeDebut, $dateTimeFin)
    {
        if ($dateTimeDebut == null) {
            $Sejours = $this->em->getRepository(Sejour::class)->searshAllSejour();
            //  dd(count($Sejours));
        } else {
            $Sejours = $this->em->getRepository(Sejour::class)->searshDate($dateTimeDebut, $dateTimeFin);
        }
        return $Sejours;
    }
    function getsejour($id)
    {
        $Sejour = $this->em->getRepository(Sejour::class)->find($id);
        return $Sejour;
    }
    function getsejourByAcc($id, $user)
    {
        $Sejour = $this->em->getRepository(Sejour::class)->findOneBy(array("id" => $id, "idAcommp" => $user));
        return $Sejour;
    }
    function getsejourByItab($idEtablisment)
    {
        $Sejour = $this->em->getRepository(Sejour::class)->findBy(array("idEtablisment" => $idEtablisment));
        return $Sejour;
    }
    function getNbreconnxparsejour($id)
    {
        $Sejourfind = $this->em->getRepository(Sejour::class)->find($id);
        if ((substr($Sejourfind->getCodeSejour(), 1, 1) == 'P')) {
            $Sejour = $this->em->getRepository(ParentSejour::class)->findby(['idSejour' => $id, 'payment' => 1]);
        } elseif ((substr($Sejourfind->getCodeSejour(), 1, 1) == 'F')) {
            $Sejour = $this->em->getRepository(ParentSejour::class)->findby(['idSejour' => $id]);
        }
        $NB = sizeof($Sejour);
        return $NB;
    }
    //a terminier le nombre enfant par sejour
    function Nbpersonnecnnx($id)
    {
        $Nbpconnx = $this->em->getRepository(ComandeProduit::class)->searshNbpersoneparNbcnnx($id);
        return $Nbpconnx;
    }
    function Nbpersonnesejour($id)
    {
        $Nbpersone = $this->em->getRepository(ParentSejour::class)->searshNbperpersonneSjour($id);
        return $Nbpersone;
    }
    function nombreenfantparsejour($id)
    {
        $Nbenfant = $this->em->getRepository(sejour::class)->searshNbperson($id);
        return $Nbenfant;
    }
    function Nbpecnnxsejourtype($type, $date = null)
    {
        if ($date == null) {
            $Nbcnxnxpartype = $this->em->getRepository(ParentSejour::class)->searshNBcnnxParSejour($type);
        } else {
            $Nbcnxnxpartype = $this->em->getRepository(ParentSejour::class)->searshNBcnnxParSejourByDate($type, $date);
        }
        return $Nbcnxnxpartype;
    }
    function Nbcommandepartypesejour($type, $date = null)
    {
        if ($date == null) {
            $Nbdemandpartype = $this->em->getRepository(ParentSejour::class)->searshNBcommandeParSejour($type);
        } else {
            $Nbdemandpartype = $this->em->getRepository(ParentSejour::class)->searshNBcommandeParSejourByDate($type, $date);
        }
        return $Nbdemandpartype;
    }
    function Moyenpainierpartypesejour($type, $dateTimeDebut, $dateTimeFin)
    {
        if ($dateTimeDebut == null || $dateTimeFin == null) {
            $panier = $this->em->getRepository(Produit::class)->searshPanierMoyen($type);
        } else {
            $panier = $this->em->getRepository(Produit::class)->searshPanierMoyenByDate($type, $dateTimeDebut, $dateTimeFin);
        }
        return $panier;
    }
    public function typeproduitsevice($id)
    {
        $prod = $this->em->getRepository(Produit::class)->findby(array('idsjour' => $id));
        return $prod;
    }
    public function getatachmentsejour($id): array
    {
        $liste = $this->em->getRepository(Attachment::class)->searshSejourAtachment($id);
        return $liste;
    }
    /**
     * Gets the list of attachements based on the sejour id and attachement type
     * @param string $id The sejour id
     * @param string $type The attachement type
     * @return array The list of photos avec pagination
     */
    public function getCombinedattachSejourPaginee(string $id, string $type = 'photo')
    {
        $liste = [];
        $results = $this->em->getRepository(SejourAttachment::class)->getSejourAttachByPhotoPagination($id, $type);
        return $results;
    }
    /**
     * Gets the list of attachements based on the sejour id and attachement type
     * @param string $id The sejour id
     * @param string $type The attachement type
     * @return array The list of attachements
     */
    public function getCombinedattachSejour(string $id, string $type = 'photo'): array
    {
        if ($type == 'photoVideo') {
            $groupedAttachments = [];
            $results = $this->em->getRepository(SejourAttachment::class)->getSejourAttachById($id, $type);
            $attachCount = count($results);
            foreach ($results as $result) {
                $date = $result->getDateDepotAttachement()->format('Y-m-d');
                $preListe = [
                    'date_depot_attachement' => $result->getDateDepotAttachement(),
                    'path'          => $result->getIdAttchment()->getPath(),
                    'id_attchment'  => $result->getIdAttchment()->getId(),
                    'idposition'    => $result->getIdAttchment()->getIdposition(),
                    'libiller'      => $result->getIdAttchment()->getIdref()->getLibiller(),
                    'descreption'   => $result->getIdAttchment()->getDescreption()
                ];
                // Group attachments by date
                if (!isset($groupedAttachments[$date])) {
                    $groupedAttachments[$date] = [];
                }
                $groupedAttachments[$date][] = $preListe;
            }
            uksort($groupedAttachments, function ($a, $b) {
                return strtotime($b) - strtotime($a);
            });
            if ($attachCount > 0) {
                $groupedAttachments['total'] = $attachCount;
            }
            return $groupedAttachments;
        }
        if ($type == 'photo') {
            $liste = [];
            $results = $this->em->getRepository(SejourAttachment::class)->getSejourAttachByPhoto($id, $type);
            foreach ($results as $result) {
                $preListe = [
                    'id' => $result->getIdAttchment()->getId(),
                    'date_depot_attachement' => $result->getDateDepotAttachement(),
                    'path' => $result->getIdAttchment()->getPath()
                ];
                $liste[] = $preListe;
            }
            return $liste;
        }
        if ($type == 'message') {
            $liste = [];
            $results = $this->em->getRepository(SejourAttachment::class)->getSejourAttachByMessage($id, $type);
            foreach ($results as $result) {
                $preListe = [
                    'date_depot_attachement' => $result->getDateDepotAttachement(),
                    'path' => $result->getIdAttchment()->getPath(),
                    'id_attchment' => $result->getIdAttchment()->getId(),
                    'idposition' => $result->getIdAttchment()->getIdposition(),
                    'libiller' => $result->getIdAttchment()->getIdref()->getLibiller(),
                    'descreption' => $result->getIdAttchment()->getDescreption()
                ];
                $liste[] = $preListe;
            }
            return $liste;
        }
    }
    public function generateTimestampsBetweenDates(DateTime $startDate, DateTime $endDate, $interval = '1 day')
    {
        $endDate->add(new DateInterval('P1D'));
        $timestamps = range($endDate->getTimestamp(), $startDate->getTimestamp(), 86400);
        return $timestamps;
    }
    public function getatachmentsejourparent($id, $idParent)
    {
        //$liste = $this->em->getRepository(SejourAttachment::class)->findBy(array('idSejour' => $id,'idParent'=>$idParent,'statut'=>"private"));
        $liste = $this->em->getRepository(Attachment::class)->searshSejourAtachmentEpaceParent($id, $idParent);
        return $liste;
    }
    public function getVideosejour($id)
    {
        $liste = $this->em->getRepository(Attachment::class)->searshSejourVideo($id);
        return $liste;
    }
    public function getsejourposition($id)
    {
        $liste = $this->em->getRepository(Position::class)->searshSejourPosition($id);
        return $liste;
    }
    public function getsejourmessage($id)
    {
        $listeaudio = $this->em->getRepository(Attachment::class)->searshSejourMessage($id);
        return $listeaudio;
    }
    function NBpersonnayaoncomande($id)
    {
        $Nbpconnx = $this->em->getRepository(ComandeProduit::class)->searshNbpersoneayontcomande($id);
        return $Nbpconnx;
    }
    function montantttc($id)
    {
        $somme = $this->em->getRepository(Commande::class)->searshMttc($id);
        return $somme;
    }
    function affectationAttachement($idSejou, $attachement)
    {
        $sejour = $this->em->getRepository(Sejour::class)->find($idSejou);
        $sejAattch = new SejourAttachment();
        $sejAattch->setIdSejour($sejour);
        $sejAattch->setIdAttchment($attachement);
        $sejAattch->setDateDepotAttachement($attachement->getDate());
        $sejAattch->setStatut("public");
        $this->em->persist($sejAattch);
        $this->em->flush();
        return $sejAattch;
    }
    function monpropreattachement($idSejou, $attachement, $user)
    {
        $sejour = $this->em->getRepository(Sejour::class)->find($idSejou);
        $sejAattch = new SejourAttachment();
        $sejAattch->setIdSejour($sejour);
        $sejAattch->setIdAttchment($attachement);
        $sejAattch->setDateDepotAttachement($attachement->getDate());
        $sejAattch->setIdParent($user);
        $sejAattch->setStatut("private");
        $this->em->persist($sejAattch);
        $this->em->flush();
        return $sejAattch;
    }
    function affectationnbvisiteattachement($idSejou, $idattachement)
    {
        $nbs = 0;
        $sejour = $this->em->getRepository(Sejour::class)->find($idSejou);
        $attachement = $this->em->getRepository(Attachment::class)->find($idattachement);
        $sejAattch = $this->em->getRepository(SejourAttachment::class)->findOneBy(array('idAttchment' => $attachement, 'idSejour' => $sejour));
        if ($sejAattch->getNbpartenaireattch() === null) {
            $nbs = 0;
        } else {
            $nbs = $sejAattch->getNbpartenaireattch();
        }
        $sejAattch->setNbpartenaireattch($nbs + 1);
        $this->em->persist($sejAattch);
        $this->em->flush();
        return $sejAattch;
    }
    function EnvoyerEmailNewAccoNewSejour($emailacc, $accompagnateur, $idSejour, $passAcommpa)
    {
        $RefEmail = $this->em->getRepository(Ref::class)->find(21);
        $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
        $Sejour = $this->em->getRepository(Sejour::class)->find($idSejour);
        $sendTo = $accompagnateur->getEmail();
        $message = (new \Swift_Message('Bienvenue à 5sur5 séjour '))
            ->setFrom('no-reply@5sur5sejour.com')
            ->setTo($emailacc);
        //->setBcc(["contact@5sur5sejour.com"]);
        $pathImage2 = $Email->getIdImage2()->getPath();
        $pathImage1 = $Email->getIdImage1()->getPath();
        $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
        $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
        $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
        $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
        $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
        $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
        $message->setBody(
            $this->templating->render(
                'emails/DemandeCreationAcc.html.twig',
                [
                    "Nomdestinataire" => $accompagnateur->getNom(),
                    "Predestinataire" => $accompagnateur->getPrenom(),
                    "code" => $Sejour->getCodeSejour(),
                    "dateCreation" => $Sejour->getDateCreationCode(),
                    "dateFinCode" => $Sejour->getDateFinCode(),
                    "lieu" => $Sejour->getAdresseSejour(),
                    "image1" => $image1,
                    "image2" => $image2,
                    "iconfooter" => $iconfooter,
                    "iconphoto" => $iconphoto,
                    "iconloca" => $iconloca,
                    "iconmsg" => $iconmsg,
                    'roles' => $accompagnateur->getRoles(),
                    "identifiant" => $Sejour->getCodeSejour(),
                    'accompagnateur' => $accompagnateur,
                    "passAcommpa" => $passAcommpa,
                ]
            ),
            'text/html'
        );
        $signMail = $this->params->get('signMail');
        if ($signMail == 'yes') {
            $domainName = $this->params->get('domaine');
            $selector = $this->params->get('selector');
            $PrivateKey = file_get_contents($this->params->get('pathDKIM'));
            $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
            $message->attachSigner($signer);
        }
        try {
            // $this->mailer->send($message);
            $resend = Resend::client('re_3ijtfF8W_AQyRsePq3jmmAaEohQig9Se8');

            $resend->emails->send($message);
        } catch (\Swift_SwiftException $ex) {
            $ex->getMessage();
        }
    }
    // authentification parent 
    function getsejourpourparent($id)
    {
        $Sejour = $this->em->getRepository(Sejour::class)->find($id);
        return $Sejour;
    }
    function getparentsejour($userId, $idSejour)
    {
        $Sejour = $this->em->getRepository(ParentSejour::class)->findOneBy(array('idParent' => $userId, 'idSejour' => $idSejour));
        return $Sejour;
    }
    function getlikephotosejour($userId, $idSejour)
    {
        $liste = $this->em->getRepository(Likephoto::class)->searshlikephotousersejour($userId, $idSejour);
        return $liste;
    }
    function getToutesLesphotoSejour($userId, $idSejour)
    {
        $liste = $this->em->getRepository(Likephoto::class)->searshlikephotousersejour($userId, $idSejour);
        return $liste;
    }
    function inserparentsejour($userId, $idSejour)
    {
        $user = $this->em->getRepository(User::class)->find($userId);
        $sejour = $this->em->getRepository(Sejour::class)->find($idSejour);
        $notifsms = $user->getSmsnotif();
        $notifmail  = $user->getMailnotif();
        $ParentSejour = new ParentSejour();
        if ($sejour->getNbenfan() == null || $sejour->getNbenfan() == 0 || $sejour->getNbenfan() == NULL) {
            $ParentSejour->setFlagPrix(1);
        } else {
            if ((sizeof($sejour->getParentSejour()) < $sejour->getNbenfan()) && ((substr($sejour->getCodeSejour(), 1, 1) == 'F'))) {
                $ParentSejour->setFlagPrix(1);
            } elseif (substr($sejour->getCodeSejour(), 1, 1) == 'F') {
                $ParentSejour->setFlagPrix(0);
            }
        }
        $ParentSejour->setIdSejour($sejour);
        $ParentSejour->setIdParent($user);
        $ParentSejour->setSmsnotif($notifsms);
        if (strpos($sejour->getCodeSejour(), "EF") !== false) {
            $ParentSejour->setSmsnotif(0);
        }
        $ParentSejour->setMailnotif($notifmail);
        $ParentSejour->setDateCreation(new \DateTime());
        $ParentSejour->setPayment(0);
        $this->em->persist($ParentSejour);
        $this->em->flush();
        return $ParentSejour;
    }
    function inserparentsejourPayenet($userId, $idSejour)
    {
        $parntsejour = $this->em->getRepository(ParentSejour::class)->findOneBy(array('idParent' => $userId, 'idSejour' => $idSejour));
        if ($parntsejour == NULL) {
        }
        $parntsejour->setPayment(1);
        $this->em->persist($parntsejour);
        $this->em->flush();
        return $parntsejour;
    }
    function sejoursansattach($sejId)
    {
        $sejour = $this->em->getRepository(Sejour::class)->find($sejId);
        return $sejour;
    }
    function EnvoyerEmailCodeSejourAccompagnateurviapartenaireenmasse($emailAcommpa, $idSejour, $TypeSejour, $passAcommpa)
    {
        $Sejour = $this->em->getRepository(Sejour::class)->find($idSejour);
        $RefEmail = $this->em->getRepository(Ref::class)->find(29);
        $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
        $sendTo = $emailAcommpa;
        $message = (new \Swift_Message('Votre code séjour 5sur5sejour'))
            ->setFrom('no-reply@5sur5sejour.com')
            ->setTo($sendTo);
        // ->setBcc(["contact@5sur5sejour.com"]);
        $pathImage2 = $Email->getIdImage2()->getPath();
        $pathImage1 = $Email->getIdImage1()->getPath();
        $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
        $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
        $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
        $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
        $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
        $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
        $message->setBody(
            $this->templating->render(
                'emails/Demandecreationacompaviapartenaire.html.twig',
                [
                    "code" => $Sejour->getCodeSejour(),
                    "dateCreation" => $Sejour->getDateCreationCode(),
                    "dateFinCode" => $Sejour->getDateFinCode(),
                    "lieu" => $Sejour->getAdresseSejour(),
                    "image1" => $image1,
                    "image2" => $image2,
                    "iconfooter" => $iconfooter,
                    "iconphoto" => $iconphoto,
                    "iconloca" => $iconloca,
                    "iconmsg" => $iconmsg,
                    'passAcommpa' => $passAcommpa,
                    "destinataire" => $Sejour->getIdAcommp()->getUsername()
                ]
            ),
            'text/html'
        );
        $signMail = $this->params->get('signMail');
        if ($signMail == 'yes') {
            $domainName = $this->params->get('domaine');
            $selector = $this->params->get('selector');
            $PrivateKey = file_get_contents($this->params->get('pathDKIM'));
            $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
            $message->attachSigner($signer);
        }
        try {
            // $this->mailer->send($message);
            $resend = Resend::client('re_3ijtfF8W_AQyRsePq3jmmAaEohQig9Se8');

            $resend->emails->send($message);
        } catch (\Swift_SwiftException $ex) {
            $ex->getMessage();
        }
    }
    function EnvoyerEmailSejourAccompa($emailAcommpa, $idSejour, $TypeSejour, $passAcommpa)
    {
        $Sejour = $this->em->getRepository(Sejour::class)->find($idSejour);
        $RefEmail = $this->em->getRepository(Ref::class)->find(29);
        $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
        $sendTo = $emailAcommpa;
        $message = (new \Swift_Message('Votre code séjour 5sur5sejour'))
            ->setFrom('no-reply@5sur5sejour.com')
            ->setTo($sendTo);
        //->setBcc(["contact@5sur5sejour.com"]);
        $pathImage2 = $Email->getIdImage2()->getPath();
        $pathImage1 = $Email->getIdImage1()->getPath();
        $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
        $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
        $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
        $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
        $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
        $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
        $message->setBody(
            $this->templating->render(
                // Firas : completer Twig
                'emails/EnvoyerEmailSejourAccompa.html.twig',
                [
                    "code" => $Sejour->getCodeSejour(),
                    "dateCreation" => $Sejour->getDateCreationCode(),
                    "dateFinCode" => $Sejour->getDateFinCode(),
                    "lieu" => $Sejour->getAdresseSejour(),
                    "image1" => $image1,
                    "image2" => $image2,
                    "iconfooter" => $iconfooter,
                    "iconphoto" => $iconphoto,
                    "iconloca" => $iconloca,
                    "iconmsg" => $iconmsg,
                    'passAcommpa' => $passAcommpa,
                    "destinataire" => $Sejour->getIdAcommp()->getUsername()
                ]
            ),
            'text/html'
        );
        $signMail = $this->params->get('signMail');
        if ($signMail == 'yes') {
            $domainName = $this->params->get('domaine');
            $selector = $this->params->get('selector');
            $PrivateKey = file_get_contents($this->params->get('pathDKIM'));
            $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
            $message->attachSigner($signer);
        }
        try {
            // $this->mailer->send($message);
            $resend = Resend::client('re_3ijtfF8W_AQyRsePq3jmmAaEohQig9Se8');

            $resend->emails->send($message);
        } catch (\Swift_SwiftException $ex) {
            $ex->getMessage();
        }
    }
    function EnvoyerEmailSejourPartnaire($partnaire, $etab2, $err, $ok, $nomfilevalid, $nomfileinvalid)
    {
        $logo = '';
        $nom = '';
        if ($partnaire->hasRole('ROLE_PARTENAIRE')) {
            $logo = $partnaire->getLogourl();
            $nom = $partnaire->getNometablisment();
        }
        $RefEmail = $this->em->getRepository(Ref::class)->find(29);
        $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
        $sendTo = $partnaire->getEmail();
        $message = (new \Swift_Message('Vos codes séjour  5sur5Séjour'))
            ->setFrom('partenariat-no-reply@5sur5sejour.com')
            ->setTo($sendTo)
            ->setBcc(["contact@5sur5sejour.com"]);
        $pathImage2 = $Email->getIdImage2()->getPath();
        $pathImage1 = $Email->getIdImage1()->getPath();
        $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
        $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
        $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
        $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
        $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
        $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
        if ($ok > 1) {
            $message->attach(Swift_Attachment::fromPath($this->projectDir . '/public/message//' . $nomfilevalid)->setFilename('Recap_Excel_' . $etab2->getNometab() . '.xlsx'));
        }
        if ($err > 1) {
            $message->attach(Swift_Attachment::fromPath($this->projectDir . '/public/message//' . $nomfileinvalid)->setFilename('Liste_Sejour_Invalide_' . $etab2->getNometab() . '.xlsx'));
        }
        $message->setBody(
            $this->templating->render(
                // Firas : completer Twig
                'emails/EnvoyerEmailSejourPartnaire.html.twig',
                [
                    "image1" => $image1,
                    "image2" => $image2,
                    "iconfooter" => $iconfooter,
                    "iconphoto" => $iconphoto,
                    "iconloca" => $iconloca,
                    "iconmsg" => $iconmsg,
                    'logo' => $logo,
                    'nom' => $nom,
                ]
            ),
            'text/html'
        );
        $signMail = $this->params->get('signMail');
        if ($signMail == 'yes') {
            $domainName = $this->params->get('domaine');
            $selector = $this->params->get('selector');
            $PrivateKey = file_get_contents($this->params->get('pathDKIM'));
            $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
            $message->attachSigner($signer);
        }
        try {
            // $this->mailer->send($message);
            $resend = Resend::client('re_3ijtfF8W_AQyRsePq3jmmAaEohQig9Se8');

            $resend->emails->send($message);
        } catch (\Swift_SwiftException $ex) {
            $ex->getMessage();
        }
    }
    function modif_lbel_Produit($id, $lbel)
    {
        $Album = $this->em->getRepository(Produit::class)->find($id);
        $Album->setLabele($lbel);
        $this->em->persist($Album);
        $this->em->flush();
    }
    function AugmenterNombreVu($array)
    {
        $sejourPartenaire = $array[0]->getIdSejour()->getIdPartenaire();
        $etablism = $this->em->getRepository(Etablisment::class)->findby(['user' => $sejourPartenaire]);
        $type = $etablism[0]->getTypeetablisment();
        foreach ($array as $e) {
            if ($type == "ECOLES/AUTRES") {
                if ($e->getNbecoleattch() == NULL) {
                    $e->setNbecoleattch(1);
                    $this->em->persist($e);
                    $this->em->flush();
                } else {
                    $e->setNbecoleattch($e->getNbecoleattch() + 1);
                    $this->em->persist($e);
                    $this->em->flush();
                }
            }
            if ($type == "CSE") {
                if ($e->getNbconsomateurattch() == NULL) {
                    $e->setNbconsomateurattch(1);
                    $this->em->persist($e);
                    $this->em->flush();
                } else {
                    $e->setNbconsomateurattch($e->getNbconsomateurattch() + 1);
                    $this->em->persist($e);
                    $this->em->flush();
                }
            }
            if ($type == "PARTENAIRES/VOYAGISTES") {
                if ($e->getNbpartenaireattch() == NULL) {
                    $e->setNbpartenaireattch(1);
                    $this->em->persist($e);
                    $this->em->flush();
                } else {
                    $e->setNbpartenaireattch($e->getNbpartenaireattch() + 1);
                    $this->em->persist($e);
                    $this->em->flush();
                }
            }
        }
    }
    function Nbrephotosejour($id)
    {
        $nombre = $this->em->getRepository(SejourAttachment::class)->SearchNombrephotoonsejour($id);
        return $nombre;
    }
    function NombreMessageSejour($id)
    {
        $nombre = $this->em->getRepository(SejourAttachment::class)->NombreMessageSejour($id);
        return $nombre;
    }
    function Nbpersone_commandepartypesejour($type, $dateTimeDebut, $dateTimeFin)
    {
        if ($dateTimeDebut == null || $dateTimeFin == null) {
            $Nbcnxnxpartype = $this->em->getRepository(ComandeProduit::class)->Nbpersone_commandepartypesejoursansdate($type);
        } else {
            $Nbcnxnxpartype = $this->em->getRepository(ComandeProduit::class)->Nbpersone_commandepartypesejourAvecdate($type, $dateTimeDebut, $dateTimeFin);
        }
        return $Nbcnxnxpartype;
    }
    function NombreConnexionParSejour($Sejours)
    {
        $NbParent = $this->em->getRepository(ParentSejour::class)->findBy(array('idSejour' => $Sejours));
        return $NbParent;
    }
    function rechercheLesCommandesParSejour($Sejours)
    {
        $Commandes = $this->em->getRepository(Commande::class)->findBy(array('idSejour' => $Sejours));
        return $Commandes;
    }
    function sejourParentcnx($idSejour, $idParent)
    {
        $Sejour = $this->em->getRepository(ParentSejour::class)->findOneBy(array("idSejour" => $idSejour, "idParent" => $idParent));
        return $Sejour;
    }
    //cette fonction envoi seulement le code sejour aux partenaire et acco s'il existe
    function EnvoyerEmailCodeSejourToPartenaireAndAcco($idSejour)
    {
        $logo = '';
        $nom = '';
        $RefEmail = $this->em->getRepository(Ref::class)->find(21);
        $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
        $Sejour = $this->em->getRepository(Sejour::class)->find($idSejour);
        if ($Sejour) {
            //dd($sejour);
            $pdf1 = new Fpdi();
            $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
            $pdfFilepath1 = $publicDirectory . "Mode_emploi_5sur5sejour_v5_.pdf";
            $pageCount = $pdf1->setSourceFile($pdfFilepath1);
            $pageId = $pdf1->importPage(1, PdfReader\PageBoundaries::MEDIA_BOX);
            $pdf1->addPage();
            $pdf1->useImportedPage($pageId, 0.5, 0.5, 0.5);
            $testText  = "abcdefghijklmnopqrstuvwxyz0123456789";
            $current_date = date('d-M-Y');
            $DateDebut =  "";
            $DateFin = "";
            $Theme = "";
            $Lieu = "";
            $CodeSejour =  "";
            $MotPass =  "";
            $Ville = "";
            $Paye = "";
            if ($Sejour) {
                $DateDebut = $Sejour->getDateDebutSejour();
                $DateFin = $Sejour->getDateFinSejour();
                $Theme = $Sejour->getThemSejour();
                $Lieu = $Sejour->getAdresseSejour();
                $CodeSejour = $Sejour->getCodeSejour();
                $MotPass = $Sejour->getIdAcommp()->getPasswordNonCripted();
                $Ville = $Sejour->getVille();
                $Paye = $Sejour->getPays();
            }
            for ($i = 0; $i < $pageCount; $i++) {
                $tplIdx = $pdf1->importPage(1 + $i);
                $pdf1->useTemplate($tplIdx, 0, 0, 200, 300, true);
                $theme = $Theme;
                if (strlen($theme) > 25) {
                    $pdf1->SetFont("Arial", "", 5);
                } else {
                    $pdf1->SetFont("Arial", "", 7);
                }
                $strTheme = iconv("UTF-8", "WINDOWS-1252", $theme);
                $pdf1->Text(35, 262, $strTheme);
                $pdf1->SetFont("Arial", "", 8);
                $lieu = $Lieu;
                $ville = $Ville;
                $paye = $Paye;
                if ($Paye && $Ville) {
                    $strLieu = iconv("UTF-8", "WINDOWS-1252", $ville . "  " . $paye);
                    $pdf1->Text(125, 262, $strLieu);
                } else if ($Paye) {
                    $strLieu = iconv("UTF-8", "WINDOWS-1252", $paye);
                    $pdf1->Text(125, 262, $strLieu);
                } else {
                    $strLieu = iconv("UTF-8", "WINDOWS-1252", $ville);
                    $pdf1->Text(125, 262, $strLieu);
                }
                $pdf1->SetFont("Arial", "", 10);
                $pdf1->Text(50, 252, $DateDebut->format('d/m/Y'));
                $pdf1->SetFont("Arial", "", 10);
                $pdf1->Text(90, 252, $DateFin->format('d/m/Y'));
                $pdf1->SetFont("Arial", "", 10);
                $pdf1->Text(90, 271, $CodeSejour);
                $pdf1->SetFont("Arial", "", 10);
                $pdf1->Text(75, 279, $MotPass);
            }
            $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
            $pdfFilepath1 = $publicDirectory . "Mode d'emploi 5sur5sejour" . $idSejour . ".pdf";
            $pdf1->Output($pdfFilepath1, "F");
            $pdf = new Fpdi();
            $paym = $Sejour->getPaym();
            if ($paym == 1) {
                $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
                $pdfFilepath = $publicDirectory . "Mode_emploi_Parent_5sur5sejour_v5_.pdf";
                $pageCount = $pdf->setSourceFile($pdfFilepath);
            } else {
                $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
                $pdfFilepath = $publicDirectory . "Mode_emploi_Parent_G_5sur5sejou_v5_.pdf";
                $pageCount = $pdf->setSourceFile($pdfFilepath);
            }
            $pageId = $pdf->importPage(1, PdfReader\PageBoundaries::MEDIA_BOX);
            $pdf->addPage();
            $pdf->useImportedPage($pageId, 0.5, 0.5, 0.5);
            $testText  = "abcdefghijklmnopqrstuvwxyz0123456789";
            $current_date = date('d-M-Y');
            $DateDebut =  "";
            $DateFin = "";
            $Theme = "";
            $Lieu = "";
            $CodeSejour =  "";
            $Ville = "";
            $Paye = "";
            if ($Sejour) {
                $DateDebut = $Sejour->getDateDebutSejour();
                $DateFin = $Sejour->getDateFinSejour();
                $Theme = $Sejour->getThemSejour();
                $Lieu = $Sejour->getAdresseSejour();
                $CodeSejour = $Sejour->getCodeSejour();
                $Ville = $Sejour->getVille();
                $Paye = $Sejour->getPays();
            }
            for ($i = 0; $i < $pageCount; $i++) {
                $tplIdx = $pdf->importPage($i + 1);
                $pdf->useTemplate($tplIdx, 0, 0, 200, 300, true);
                $theme = $Theme;
                if (strlen($theme) > 25) {
                    $pdf->SetFont("Arial", "", 5);
                } else {
                    $pdf->SetFont("Arial", "", 7);
                }
                $strTheme = iconv("UTF-8", "WINDOWS-1252", $theme);
                $pdf->Text(35, 268, $strTheme);
                $pdf->SetFont("Arial", "", 8);
                $lieu = $Lieu;
                $ville = $Ville;
                $paye = $Paye;
                if ($Paye && $Ville) {
                    $strLieu = iconv("UTF-8", "WINDOWS-1252", $ville . "  " . $paye);
                    $pdf->Text(125, 268, $strLieu);
                } else if ($Paye) {
                    $strLieu = iconv("UTF-8", "WINDOWS-1252", $paye);
                    $pdf->Text(125, 268, $strLieu);
                } else {
                    $strLieu = iconv("UTF-8", "WINDOWS-1252", $ville);
                    $pdf->Text(125, 268, $strLieu);
                }
                $pdf->SetFont("Arial", "", 10);
                $pdf->Text(80, 279, $CodeSejour);
                $pdf->SetFont("Arial", "", 10);
                $pdf->Text(48, 258, $DateDebut->format('d/m/Y'));
                $pdf->SetFont("Arial", "", 10);
                $pdf->Text(89, 258, $DateFin->format('d/m/Y'));
            }
            if ($paym == 1) {
                $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
                $pdfFilepath = $publicDirectory . "Mode d'emploi parent  5sur5séjour" . $idSejour . ".pdf";
                $pdf->Output($pdfFilepath, "F");
            } else {
                $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
                $pdfFilepath = $publicDirectory . "Mode d'emploi parent G 5sur5séjour " . $idSejour . ".pdf";
                $pdf->Output($pdfFilepath, "F");
            }
            if ($Sejour->getIdEtablisment()) {
                if (!is_null($Sejour->getIdEtablisment()->getEmail())) {
                    $emailPartenaire = $Sejour->getIdEtablisment()->getEmail();
                } else {
                    $emailPartenaire = $Sejour->getIdPartenaire()->getEmail();
                }
                $partenaire = $Sejour->getIdEtablisment()->getNometab();
                $partenaire = $Sejour->getIdEtablisment()->getLogo();
                $logo = $Sejour->getIdEtablisment()->getLogo();
                $nom = $Sejour->getIdEtablisment()->getNometab();
                $message = (new \Swift_Message('Votre code séjour 5sur5sejour'))
                    ->setFrom('partenariat-no-reply@5sur5sejour.com')
                    ->setTo($emailPartenaire)
                    ->setBcc(["contact@5sur5sejour.com"]);
                $pathImage2 = $Email->getIdImage2()->getPath();
                $pathImage1 = $Email->getIdImage1()->getPath();
                $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
                $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
                $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
                $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
                $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
                $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
                if ($paym == 1) {
                    $message->attach(\Swift_Attachment::fromPath($pdfFilepath));
                    $message->attach(\Swift_Attachment::fromPath($pdfFilepath1));
                } else {
                    $message->attach(\Swift_Attachment::fromPath($pdfFilepath));
                    $message->attach(\Swift_Attachment::fromPath($pdfFilepath1));
                }
                $message->setBody(
                    $this->templating->render(
                        'emails/EnvoiCodeSejourUniquementToAccoAndPartenaire.html.twig',
                        [
                            "Password" => $Sejour->getIdAcommp()->getPasswordNonCripted(),
                            "code" => $Sejour->getCodeSejour(),
                            "dateCreation" => $Sejour->getDateCreationCode(),
                            "dateFinCode" => $Sejour->getDateFinCode(),
                            "lieu" => $Sejour->getAdresseSejour(),
                            "image1" => $image1,
                            "image2" => $image2,
                            "iconfooter" => $iconfooter,
                            "iconphoto" => $iconphoto,
                            "iconloca" => $iconloca,
                            "iconmsg" => $iconmsg,
                            "identifiant" => $Sejour->getCodeSejour(),
                            'logo' => $logo,
                            'nom' => $nom,
                            'roles' => $Sejour->getIdEtablisment()->getUser()->getRoles(),
                        ]
                    ),
                    'text/html'
                );
                $signMail = $this->params->get('signMail');
                if ($signMail == 'yes') {
                    $domainName = $this->params->get('domaine');
                    $selector = $this->params->get('selector');
                    $PrivateKey = file_get_contents($this->params->get('pathDKIM'));
                    $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
                    $message->attachSigner($signer);
                }
                try {
                    // $this->mailer->send($message);
                    $resend = Resend::client('re_3ijtfF8W_AQyRsePq3jmmAaEohQig9Se8');

                    $resend->emails->send($message);
                } catch (\Swift_SwiftException $ex) {
                    $ex->getMessage();
                }
                if ($emailPartenaire != $Sejour->getIdAcommp()->getReponseemail()) {
                    $emailAcco = $Sejour->getIdAcommp()->getReponseemail();
                    $message = (new \Swift_Message('Votre code séjour 5sur5sejour'))
                        ->setFrom('no-reply@5sur5sejour.com')
                        ->setTo($emailAcco);
                    //->setBcc(["contact@5sur5sejour.com"]);
                    $pathImage2 = $Email->getIdImage2()->getPath();
                    $pathImage1 = $Email->getIdImage1()->getPath();
                    $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
                    $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
                    $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
                    $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
                    $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
                    $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
                    if ($paym == 1) {
                        $message->attach(\Swift_Attachment::fromPath($pdfFilepath));
                        $message->attach(\Swift_Attachment::fromPath($pdfFilepath1));
                    } else {
                        $message->attach(\Swift_Attachment::fromPath($pdfFilepath));
                        $message->attach(\Swift_Attachment::fromPath($pdfFilepath1));
                    }
                    $message->setBody(
                        $this->templating->render(
                            'emails/EnvoiCodeSejourUniquementToAccoAndPartenaire.html.twig',
                            [
                                "Password" => $Sejour->getIdAcommp()->getPasswordNonCripted(),
                                "code" => $Sejour->getCodeSejour(),
                                "dateCreation" => $Sejour->getDateCreationCode(),
                                "dateFinCode" => $Sejour->getDateFinCode(),
                                "lieu" => $Sejour->getAdresseSejour(),
                                "image1" => $image1,
                                "image2" => $image2,
                                "iconfooter" => $iconfooter,
                                "iconphoto" => $iconphoto,
                                "iconloca" => $iconloca,
                                "iconmsg" => $iconmsg,
                                'logo' => $logo,
                                'nom' => $nom,
                                "identifiant" => $Sejour->getCodeSejour(),
                                'roles' => $Sejour->getIdAcommp()->getRoles(),
                            ]
                        ),
                        'text/html'
                    );
                    $signMail = $this->params->get('signMail');
                    if ($signMail == 'yes') {
                        $domainName = $this->params->get('domaine');
                        $selector = $this->params->get('selector');
                        $PrivateKey = file_get_contents($this->params->get('pathDKIM'));
                        $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
                        $message->attachSigner($signer);
                    }
                    try {
                        // $this->mailer->send($message);
                        $resend = Resend::client('re_3ijtfF8W_AQyRsePq3jmmAaEohQig9Se8');

                        $resend->emails->send($message);
                    } catch (\Swift_SwiftException $ex) {
                        $ex->getMessage();
                    }
                }
            }
        }
    }
    //cette fonction envoi seulement le password sejour aux partenaire et acco s'il existe
    function EnvoyerEmailPasswordSejourToPartenaireAndAcco($idSejour)
    {
        $logo = '';
        $nom = '';
        $Sejour = $this->em->getRepository(Sejour::class)->find($idSejour);
        $RefEmail = $this->em->getRepository(Ref::class)->find(28);
        $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
        if ($Sejour) {
            if ($Sejour->getIdEtablisment()) {
                $emailPartenaire = $Sejour->getIdEtablisment()->getEmail();
                $partenaire = $Sejour->getIdEtablisment()->getNometab();
                $partenaire = $Sejour->getIdEtablisment()->getLogo();
                $logo = $Sejour->getIdEtablisment()->getLogo();
                $nom = $Sejour->getIdEtablisment()->getNometab();
                $message = (new \Swift_Message('Votre mot de passe 5sur5sejour'))
                    ->setFrom('partenariat-no-reply@5sur5sejour.com')
                    ->setTo($emailPartenaire);
                //->setBcc(["contact@5sur5sejour.com"]);
                $pathImage2 = $Email->getIdImage2()->getPath();
                $pathImage1 = $Email->getIdImage1()->getPath();
                $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
                $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
                $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
                $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
                $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
                $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
                $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
                $pdfFilepath = $publicDirectory . "Mode d'emploi 5sur5sejour" . $idSejour . ".pdf";
                $message->attach(\Swift_Attachment::fromPath($pdfFilepath));
                $message->setBody(
                    $this->templating->render(
                        'emails/SendPasswordSejour.html.twig',
                        [
                            "Password" => $Sejour->getIdAcommp()->getPasswordNonCripted(),
                            "image1" => $image1,
                            "image2" => $image2,
                            "iconfooter" => $iconfooter,
                            "iconphoto" => $iconphoto,
                            "iconloca" => $iconloca,
                            "iconmsg" => $iconmsg,
                            'logo' => $logo,
                            'nom' => $nom,
                            'roles' => $Sejour->getIdEtablisment()->getUser()->getRoles(),
                        ]
                    ),
                    'text/html'
                );
                $signMail = $this->params->get('signMail');
                if ($signMail == 'yes') {
                    $domainName = $this->params->get('domaine');
                    $selector = $this->params->get('selector');
                    $PrivateKey = file_get_contents($this->params->get('pathDKIM'));
                    $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
                    $message->attachSigner($signer);
                }
                try {
                    // $this->mailer->send($message);
                    $resend = Resend::client('re_3ijtfF8W_AQyRsePq3jmmAaEohQig9Se8');

                    $resend->emails->send($message);
                } catch (\Swift_SwiftException $ex) {
                    $ex->getMessage();
                }
                if ($emailPartenaire != $Sejour->getIdAcommp()->getReponseemail()) {
                    $emailAcco = $Sejour->getIdAcommp()->getReponseemail();
                    $message = (new \Swift_Message('Votre mot de passe 5sur5sejour'))
                        ->setFrom('no-reply@5sur5sejour.com')
                        ->setTo($emailAcco);
                    // ->setBcc(["contact@5sur5sejour.com"]);
                    $pathImage2 = $Email->getIdImage2()->getPath();
                    $pathImage1 = $Email->getIdImage1()->getPath();
                    $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
                    $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
                    $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
                    $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
                    $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
                    $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
                    $message->attach(\Swift_Attachment::fromPath('pdf/'));
                    $message->setBody(
                        $this->templating->render(
                            'emails/SendPasswordSejour.html.twig',
                            [
                                "Password" => $Sejour->getIdAcommp()->getPasswordNonCripted(),
                                "image1" => $image1,
                                "image2" => $image2,
                                "iconfooter" => $iconfooter,
                                "iconphoto" => $iconphoto,
                                "iconloca" => $iconloca,
                                "iconmsg" => $iconmsg,
                                'logo' => $logo,
                                'nom' => $nom,
                                'roles' => $Sejour->getIdAcommp()->getRoles(),
                            ]
                        ),
                        'text/html'
                    );
                    $signMail = $this->params->get('signMail');
                    if ($signMail == 'yes') {
                        $domainName = $this->params->get('domaine');
                        $selector = $this->params->get('selector');
                        $PrivateKey = file_get_contents($this->params->get('pathDKIM'));
                        $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
                        $message->attachSigner($signer);
                    }
                    try {
                        // $done = $this->mailer->send($message);
                        $resend = Resend::client('re_3ijtfF8W_AQyRsePq3jmmAaEohQig9Se8');

                        $resend->emails->send($message);
                        //var_dump($done);
                        // var_dump("send it ");
                    } catch (\Swift_SwiftException $ex) {
                        //var_dump( $ex->getMessage());
                    }
                }
            }
        }
    }
    //cette fonction envoi seulement le code sejour aux partenaire
    function EnvoyerEmailCodeSejourToPartenaire($idSejour)
    {
        $logo = '';
        $nom = '';
        $RefEmail = $this->em->getRepository(Ref::class)->find(21);
        $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
        $Sejour = $this->em->getRepository(Sejour::class)->find($idSejour);
        if ($Sejour) {
            //dd($sejour);
            $pdf = new Fpdi();
            $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
            $pdfFilepath = $publicDirectory . "Mode_emploi_5sur5sejour_v5_.pdf";
            $pageCount = $pdf->setSourceFile($pdfFilepath);
            $pageId = $pdf->importPage(1, PdfReader\PageBoundaries::MEDIA_BOX);
            $pdf->addPage();
            $pdf->useImportedPage($pageId, 0.5, 0.5, 0.5);
            $testText  = "abcdefghijklmnopqrstuvwxyz0123456789";
            $current_date = date('d-M-Y');
            $DateDebut =  "";
            $DateFin = "";
            $Theme = "";
            $Lieu = "";
            $CodeSejour =  "";
            $MotPass =  "";
            $Ville = "";
            $Paye = "";
            if ($Sejour) {
                $DateDebut = $Sejour->getDateDebutSejour();
                $DateFin = $Sejour->getDateFinSejour();
                $Theme = $Sejour->getThemSejour();
                $Lieu = $Sejour->getAdresseSejour();
                $CodeSejour = $Sejour->getCodeSejour();
                $MotPass = $Sejour->getIdAcommp()->getPasswordNonCripted();
                $Ville = $Sejour->getVille();
                $Paye = $Sejour->getPays();
            }
            for ($i = 0; $i < $pageCount; $i++) {
                $tplIdx = $pdf->importPage(1 + $i);
                $pdf->useTemplate($tplIdx, 0, 0, 200, 300, true);
                $theme = $Theme;
                if (strlen($theme) > 25) {
                    $pdf->SetFont("Arial", "", 5);
                } else {
                    $pdf->SetFont("Arial", "", 7);
                }
                $strTheme = iconv("UTF-8", "WINDOWS-1252", $theme);
                $pdf->Text(35, 262, $strTheme);
                $pdf->SetFont("Arial", "", 10);
                $pdf->Text(50, 252, $DateDebut->format('d/m/Y'));
                $pdf->SetFont("Arial", "", 10);
                $pdf->Text(90, 252, $DateFin->format('d/m/Y'));
                $pdf->SetFont("Arial", "", 8);
                $lieu = $Lieu;
                $ville = $Ville;
                $paye = $Paye;
                if ($Paye && $Ville) {
                    $strLieu = iconv("UTF-8", "WINDOWS-1252", $ville . "  " . $paye);
                    $pdf->Text(125, 262, $strLieu);
                } else if ($Paye) {
                    $strLieu = iconv("UTF-8", "WINDOWS-1252", $paye);
                    $pdf->Text(125, 262, $strLieu);
                } else {
                    $strLieu = iconv("UTF-8", "WINDOWS-1252", $ville);
                    $pdf->Text(125, 262, $strLieu);
                }
                $pdf->SetFont("Arial", "", 10);
                $pdf->Text(90, 271, $CodeSejour);
                $pdf->SetFont("Arial", "", 10);
                $pdf->Text(75, 279, $MotPass);
            }
            $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
            $pdfFilepath = $publicDirectory . "Mode d'emploi 5sur5sejour" . $idSejour . ".pdf";
            $pdf->Output($pdfFilepath, "F");
            $pdf = new Fpdi();
            $paym = $Sejour->getPaym();
            if ($paym == 1) {
                $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
                $pdfFilepath = $publicDirectory . "Mode_emploi_Parent_5sur5sejour_v5_.pdf";
                $pageCount = $pdf->setSourceFile($pdfFilepath);
            } else {
                $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
                $pdfFilepath = $publicDirectory . "Mode_emploi_Parent_G_5sur5sejou_v5_.pdf";
                $pageCount = $pdf->setSourceFile($pdfFilepath);
            }
            $pageId = $pdf->importPage(1, PdfReader\PageBoundaries::MEDIA_BOX);
            $pdf->addPage();
            $pdf->useImportedPage($pageId, 0.5, 0.5, 0.5);
            $testText  = "abcdefghijklmnopqrstuvwxyz0123456789";
            $current_date = date('d-M-Y');
            $DateDebut =  "";
            $DateFin = "";
            $Theme = "";
            $Lieu = "";
            $CodeSejour =  "";
            $Ville = "";
            $Paye = "";
            if ($Sejour) {
                $DateDebut = $Sejour->getDateDebutSejour();
                $DateFin = $Sejour->getDateFinSejour();
                $Theme = $Sejour->getThemSejour();
                $Lieu = $Sejour->getAdresseSejour();
                $CodeSejour = $Sejour->getCodeSejour();
                $Ville = $Sejour->getVille();
                $Paye = $Sejour->getPays();
            }
            for ($i = 0; $i < $pageCount; $i++) {
                $tplIdx = $pdf->importPage($i + 1);
                $pdf->useTemplate($tplIdx, 0, 0, 200, 300, true);
                $theme = $Theme;
                if (strlen($theme) > 25) {
                    $pdf->SetFont("Arial", "", 5);
                } else {
                    $pdf->SetFont("Arial", "", 7);
                }
                $strTheme = iconv("UTF-8", "WINDOWS-1252", $theme);
                $pdf->Text(35, 268, $strTheme);
                $pdf->SetFont("Arial", "", 8);
                $lieu = $Lieu;
                $ville = $Ville;
                $paye = $Paye;
                if ($Paye  && $Ville) {
                    $strLieu = iconv("UTF-8", "WINDOWS-1252", $ville . "  " . $paye);
                    $pdf->Text(125, 268, $strLieu);
                } else if ($Paye) {
                    $strLieu = iconv("UTF-8", "WINDOWS-1252", $paye);
                    $pdf->Text(125, 268, $strLieu);
                } else {
                    $strLieu = iconv("UTF-8", "WINDOWS-1252", $ville);
                    $pdf->Text(125, 268, $strLieu);
                }
                $pdf->SetFont("Arial", "", 10);
                $pdf->Text(80, 279, $CodeSejour);
                $pdf->SetFont("Arial", "", 10);
                $pdf->Text(48, 258, $DateDebut->format('d/m/Y'));
                $pdf->SetFont("Arial", "", 10);
                $pdf->Text(89, 258, $DateFin->format('d/m/Y'));
            }
            if ($paym == 1) {
                $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
                $pdfFilepath = $publicDirectory . "Mode d'emploi parent  5sur5séjour" . $idSejour . ".pdf";
                $pdf->Output($pdfFilepath, "F");
            } else {
                $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
                $pdfFilepath = $publicDirectory . "Mode d'emploi parent  5sur5séjour" . $idSejour . ".pdf";
                $pdf->Output($pdfFilepath, "F");
            }
            if ($Sejour->getIdEtablisment()) {
                $emailPartenaire = $Sejour->getIdEtablisment()->getEmail();
                $partenaire = $Sejour->getIdEtablisment()->getNometab();
                $partenaire = $Sejour->getIdEtablisment()->getLogo();
                $logo = $Sejour->getIdEtablisment()->getLogo();
                $nom = $Sejour->getIdEtablisment()->getNometab();
                $message = (new \Swift_Message('Votre code séjour 5sur5sejour'))
                    ->setFrom('partenariat-no-reply@5sur5sejour.com')
                    ->setTo($emailPartenaire);
                //->setBcc(["contact@5sur5sejour.com"]);
                $pathImage2 = $Email->getIdImage2()->getPath();
                $pathImage1 = $Email->getIdImage1()->getPath();
                $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
                $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
                $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
                $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
                $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
                $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
                if ($paym == 1) {
                    $message->attach(\Swift_Attachment::fromPath($pdfFilepath));
                } else {
                    $message->attach(\Swift_Attachment::fromPath($pdfFilepath));
                }
                $message->setBody(
                    $this->templating->render(
                        'emails/EnvoiCodeSejourUniquementToAccoAndPartenaire.html.twig',
                        [
                            "Password" => $Sejour->getIdAcommp()->getPasswordNonCripted(),
                            "code" => $Sejour->getCodeSejour(),
                            "dateCreation" => $Sejour->getDateCreationCode(),
                            "dateFinCode" => $Sejour->getDateFinCode(),
                            "lieu" => $Sejour->getAdresseSejour(),
                            "image1" => $image1,
                            "image2" => $image2,
                            "iconfooter" => $iconfooter,
                            "iconphoto" => $iconphoto,
                            "iconloca" => $iconloca,
                            "iconmsg" => $iconmsg,
                            "identifiant" => $Sejour->getCodeSejour(),
                            'logo' => $logo,
                            'nom' => $nom,
                            'roles' => $Sejour->getIdEtablisment()->getUser()->getRoles(),
                        ]
                    ),
                    'text/html'
                );
                $signMail = $this->params->get('signMail');
                if ($signMail == 'yes') {
                    $domainName = $this->params->get('domaine');
                    $selector = $this->params->get('selector');
                    $PrivateKey = file_get_contents($this->params->get('pathDKIM'));
                    $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
                    $message->attachSigner($signer);
                }
                try {
                    // $this->mailer->send($message);
                    $resend = Resend::client('re_3ijtfF8W_AQyRsePq3jmmAaEohQig9Se8');

                    $resend->emails->send($message);
                } catch (\Swift_SwiftException $ex) {
                    $ex->getMessage();
                }
            }
        }
    }
    //cette fonction envoi seulement le code sejour aux acccompagnateur
    function EnvoyerEmailCodeSejourToAccomp($idSejour)
    {
        $RefEmail = $this->em->getRepository(Ref::class)->find(21);
        $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
        $Sejour = $this->em->getRepository(Sejour::class)->find($idSejour);
        if ($Sejour) {
            $pdf = new Fpdi();
            $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
            $pdfFilepath = $publicDirectory . "Mode_emploi_5sur5sejour_v5_.pdf";
            $pageCount = $pdf->setSourceFile($pdfFilepath);
            $pageId = $pdf->importPage(1, PdfReader\PageBoundaries::MEDIA_BOX);
            $pdf->addPage();
            $pdf->useImportedPage($pageId, 0.5, 0.5, 0.5);
            $testText  = "abcdefghijklmnopqrstuvwxyz0123456789";
            $current_date = date('d-M-Y');
            $DateDebut =  "";
            $DateFin = "";
            $Theme = "";
            $Lieu = "";
            $CodeSejour =  "";
            $MotPass =  "";
            $Ville = "";
            $Paye = "";
            if ($Sejour) {
                $DateDebut = $Sejour->getDateDebutSejour();
                $DateFin = $Sejour->getDateFinSejour();
                $Theme = $Sejour->getThemSejour();
                $Lieu = $Sejour->getAdresseSejour();
                $CodeSejour = $Sejour->getCodeSejour();
                $MotPass = $Sejour->getIdAcommp()->getPasswordNonCripted();
                $Ville = $Sejour->getVille();
                $Paye = $Sejour->getPays();
            }
            for ($i = 0; $i < $pageCount; $i++) {
                $tplIdx = $pdf->importPage(1 + $i);
                $pdf->useTemplate($tplIdx, 0, 0, 200, 300, true);
                $theme = $Theme;
                if (strlen($theme) > 25) {
                    $pdf->SetFont("Arial", "", 5);
                } else {
                    $pdf->SetFont("Arial", "", 7);
                }
                $strTheme = iconv("UTF-8", "WINDOWS-1252", $theme);
                $pdf->Text(35, 262, $strTheme);
                $pdf->SetFont("Arial", "", 8);
                $lieu = $Lieu;
                $ville = $Ville;
                $paye = $Paye;
                if ($Paye  && $Ville) {
                    $strLieu = iconv("UTF-8", "WINDOWS-1252", $ville . "  " . $paye);
                    $pdf->Text(125, 262, $strLieu);
                } elseif ($Paye) {
                    $strLieu = iconv("UTF-8", "WINDOWS-1252", $paye);
                    $pdf->Text(125, 262, $strLieu);
                } else {
                    $strLieu = iconv("UTF-8", "WINDOWS-1252", $ville);
                    $pdf->Text(125, 262, $strLieu);
                }
                $pdf->SetFont("Arial", "", 10);
                $pdf->Text(50, 252, $DateDebut->format('d/m/Y'));
                $pdf->SetFont("Arial", "", 10);
                $pdf->Text(90, 252, $DateFin->format('d/m/Y'));
                $pdf->SetFont("Arial", "", 10);
                $pdf->Text(90, 271, $CodeSejour);
                $pdf->SetFont("Arial", "", 10);
                $pdf->Text(75, 279, $MotPass);
            }
            $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
            $pdfFilepath = $publicDirectory . "Mode d'emploi 5sur5sejour" . $idSejour . ".pdf";
            // $filename= $this->params->get('kernel.project_dir').'/public/pdf/pdfaccompagnateur.pdf';
            $pdf->Output($pdfFilepath, "F");
            $pdf = new Fpdi();
            $paym = $Sejour->getPaym();
            if ($paym == 1) {
                $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
                $pdfFilepath = $publicDirectory . "Mode_emploi_Parent_5sur5sejour_v5_.pdf";
                $pageCount = $pdf->setSourceFile($pdfFilepath);
            } else {
                $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
                $pdfFilepath = $publicDirectory . "Mode_emploi_Parent_G_5sur5sejou_v5_.pdf";
                $pageCount = $pdf->setSourceFile($pdfFilepath);
            }
            $pageId = $pdf->importPage(1, PdfReader\PageBoundaries::MEDIA_BOX);
            $pdf->addPage();
            $pdf->useImportedPage($pageId, 0.5, 0.5, 0.5);
            $testText  = "abcdefghijklmnopqrstuvwxyz0123456789";
            $current_date = date('d-M-Y');
            $DateDebut =  "";
            $DateFin = "";
            $Theme = "";
            $Lieu = "";
            $CodeSejour =  "";
            $Ville = "";
            $Paye = "";
            if ($Sejour) {
                $DateDebut = $Sejour->getDateDebutSejour();
                $DateFin = $Sejour->getDateFinSejour();
                $Theme = $Sejour->getThemSejour();
                $Lieu = $Sejour->getAdresseSejour();
                $CodeSejour = $Sejour->getCodeSejour();
                $Ville = $Sejour->getVille();
                $Paye = $Sejour->getPays();
            }
            for ($i = 0; $i < $pageCount; $i++) {
                $tplIdx = $pdf->importPage($i + 1);
                $pdf->useTemplate($tplIdx, 0, 0, 200, 300, true);
                $theme = $Theme;
                if (strlen($theme) > 25) {
                    $pdf->SetFont("Arial", "", 5);
                } else {
                    $pdf->SetFont("Arial", "", 7);
                }
                $strTheme = iconv("UTF-8", "WINDOWS-1252", $theme);
                $pdf->Text(35, 268, $strTheme);
                $pdf->SetFont("Arial", "", 8);
                $lieu = $Lieu;
                $ville = $Ville;
                $paye = $Paye;
                if ($Paye  && $Ville) {
                    $strLieu = iconv("UTF-8", "WINDOWS-1252", $ville . "  " . $paye);
                    $pdf->Text(125, 268, $strLieu);
                } else if ($Paye) {
                    $strLieu = iconv("UTF-8", "WINDOWS-1252", $paye);
                    $pdf->Text(125, 268, $strLieu);
                } else {
                    $strLieu = iconv("UTF-8", "WINDOWS-1252", $ville);
                    $pdf->Text(125, 268, $strLieu);
                }
                $pdf->SetFont("Arial", "", 10);
                $pdf->Text(80, 279, $CodeSejour);
                $pdf->SetFont("Arial", "", 10);
                $pdf->Text(48, 258, $DateDebut->format('d/m/Y'));
                $pdf->SetFont("Arial", "", 10);
                $pdf->Text(89, 258, $DateFin->format('d/m/Y'));
            }
            if ($paym == 1) {
                $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
                $pdfFilepath = $publicDirectory . "Mode d'emploi parent  5sur5séjour" . $idSejour . ".pdf";
                $pdf->Output($pdfFilepath, "F");
            } else {
                $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
                $pdfFilepath = $publicDirectory . "Mode d'emploi parent  5sur5séjour" . $idSejour . ".pdf";
                $pdf->Output($pdfFilepath, "F");
            }
            if ($Sejour->getIdAcommp()->getReponseemail()) {
                $emailAcco = $Sejour->getIdAcommp()->getReponseemail();
                $partenaire = $Sejour->getIdEtablisment()->getNometab();
                $partenaire = $Sejour->getIdEtablisment()->getLogo();
                $logo = $Sejour->getIdEtablisment()->getLogo();
                $nom = $Sejour->getIdEtablisment()->getNometab();
                $message = (new \Swift_Message('Votre code séjour 5sur5sejour'))
                    ->setFrom('no-reply@5sur5sejour.com')
                    ->setTo($emailAcco);
                //->setBcc(["contact@5sur5sejour.com"]);
                $pathImage2 = $Email->getIdImage2()->getPath();
                $pathImage1 = $Email->getIdImage1()->getPath();
                $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
                $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
                $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
                $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
                $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
                $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
                if ($paym == 1) {
                    $message->attach(\Swift_Attachment::fromPath($pdfFilepath));
                } else {
                    $message->attach(\Swift_Attachment::fromPath($pdfFilepath));
                }
                $message->setBody(
                    $this->templating->render(
                        'emails/EnvoiCodeSejourUniquementToAccoAndPartenaire.html.twig',
                        [
                            "Password" => $Sejour->getIdAcommp()->getPasswordNonCripted(),
                            "code" => $Sejour->getCodeSejour(),
                            "dateCreation" => $Sejour->getDateCreationCode(),
                            "dateFinCode" => $Sejour->getDateFinCode(),
                            "lieu" => $Sejour->getAdresseSejour(),
                            "image1" => $image1,
                            "image2" => $image2,
                            "iconfooter" => $iconfooter,
                            "iconphoto" => $iconphoto,
                            "iconloca" => $iconloca,
                            "iconmsg" => $iconmsg,
                            'logo' => $logo,
                            'nom' => $nom,
                            "identifiant" => $Sejour->getCodeSejour(),
                            'roles' => $Sejour->getIdAcommp()->getRoles(),
                        ]
                    ),
                    'text/html'
                );
                $signMail = $this->params->get('signMail');
                if ($signMail == 'yes') {
                    $domainName = $this->params->get('domaine');
                    $selector = $this->params->get('selector');
                    $PrivateKey = file_get_contents($this->params->get('pathDKIM'));
                    $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
                    $message->attachSigner($signer);
                }
                try {
                    // $this->mailer->send($message);
                    $resend = Resend::client('re_3ijtfF8W_AQyRsePq3jmmAaEohQig9Se8');

                    $resend->emails->send($message);
                } catch (\Swift_SwiftException $ex) {
                    $ex->getMessage();
                }
            }
        }
    }
    //cette fonction envoi seulement le password sejour aux partenaire et acco s'il existe
    function EnvoyerEmailPasswordSejourToPartenaire($idSejour)
    {
        $Sejour = $this->em->getRepository(Sejour::class)->find($idSejour);
        $RefEmail = $this->em->getRepository(Ref::class)->find(28);
        $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
        if ($Sejour) {
            if ($Sejour->getIdEtablisment()) {
                $emailPartenaire = $Sejour->getIdEtablisment()->getEmail();
                $partenaire = $Sejour->getIdEtablisment()->getNometab();
                $partenaire = $Sejour->getIdEtablisment()->getLogo();
                $logo = $Sejour->getIdEtablisment()->getLogo();
                $nom = $Sejour->getIdEtablisment()->getNometab();
                $message = (new \Swift_Message('Votre mot de passe 5sur5sejour'))
                    ->setFrom('partenariat-no-reply@5sur5sejour.com')
                    ->setTo($emailPartenaire);
                //->setBcc(["contact@5sur5sejour.com"]);
                $pathImage2 = $Email->getIdImage2()->getPath();
                $pathImage1 = $Email->getIdImage1()->getPath();
                $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
                $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
                $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
                $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
                $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
                $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
                $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
                $pdfFilepath = $publicDirectory . "Mode d'emploi 5sur5sejour" . $idSejour . ".pdf";
                $message->attach(\Swift_Attachment::fromPath($pdfFilepath));
                $message->setBody(
                    $this->templating->render(
                        'emails/SendPasswordSejour.html.twig',
                        [
                            "Password" => $Sejour->getIdAcommp()->getPasswordNonCripted(),
                            "image1" => $image1,
                            "image2" => $image2,
                            "iconfooter" => $iconfooter,
                            "iconphoto" => $iconphoto,
                            "iconloca" => $iconloca,
                            "iconmsg" => $iconmsg,
                            'logo' => $logo,
                            'nom' => $nom,
                            'roles' => $Sejour->getIdEtablisment()->getUser()->getRoles(),
                        ]
                    ),
                    'text/html'
                );
                $signMail = $this->params->get('signMail');
                if ($signMail == 'yes') {
                    $domainName = $this->params->get('domaine');
                    $selector = $this->params->get('selector');
                    $PrivateKey = file_get_contents($this->params->get('pathDKIM'));
                    $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
                    $message->attachSigner($signer);
                }
                try {
                    // $this->mailer->send($message);
                    $resend = Resend::client('re_3ijtfF8W_AQyRsePq3jmmAaEohQig9Se8');

                    $resend->emails->send($message);
                } catch (\Swift_SwiftException $ex) {
                    $ex->getMessage();
                }
            }
        }
    }
    //cette fonction envoi seulement le password sejour aux partenaire et acco s'il existe
    function EnvoyerEmailPasswordSejourToAcco($idSejour)
    {
        $Sejour = $this->em->getRepository(Sejour::class)->find($idSejour);
        $RefEmail = $this->em->getRepository(Ref::class)->find(28);
        $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
        if ($Sejour) {
            if ($Sejour->getIdAcommp()->getReponseemail()) {
                $emailAcco = $Sejour->getIdAcommp()->getReponseemail();
                $partenaire = $Sejour->getIdEtablisment()->getNometab();
                $partenaire = $Sejour->getIdEtablisment()->getLogo();
                $logo = $Sejour->getIdEtablisment()->getLogo();
                $nom = $Sejour->getIdEtablisment()->getNometab();
                $message = (new \Swift_Message('Votre mot de passe 5sur5sejour'))
                    ->setFrom('no-reply@5sur5sejour.com')
                    ->setTo($emailAcco);
                //->setBcc(["contact@5sur5sejour.com"]);
                $pathImage2 = $Email->getIdImage2()->getPath();
                $pathImage1 = $Email->getIdImage1()->getPath();
                $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
                $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
                $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
                $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
                $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
                $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
                $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
                $pdfFilepath = $publicDirectory . "Mode d'emploi 5sur5sejour" . $idSejour . ".pdf";
                $message->attach(\Swift_Attachment::fromPath($pdfFilepath));
                $message->setBody(
                    $this->templating->render(
                        'emails/SendPasswordSejour.html.twig',
                        [
                            "Password" => $Sejour->getIdAcommp()->getPasswordNonCripted(),
                            "image1" => $image1,
                            "image2" => $image2,
                            "iconfooter" => $iconfooter,
                            "iconphoto" => $iconphoto,
                            "iconloca" => $iconloca,
                            "iconmsg" => $iconmsg,
                            'logo' => $logo,
                            'nom' => $nom,
                            'roles' => $Sejour->getIdAcommp()->getRoles(),
                        ]
                    ),
                    'text/html'
                );
                $signMail = $this->params->get('signMail');
                if ($signMail == 'yes') {
                    $domainName = $this->params->get('domaine');
                    $selector = $this->params->get('selector');
                    $PrivateKey = file_get_contents($this->params->get('pathDKIM'));
                    $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
                    $message->attachSigner($signer);
                }
                try {
                    // $done = $this->mailer->send($message);
                    $resend = Resend::client('re_3ijtfF8W_AQyRsePq3jmmAaEohQig9Se8');

                    $resend->emails->send($message);
                    //var_dump($done);
                    // var_dump("send it ");
                } catch (\Swift_SwiftException $ex) {
                    //var_dump( $ex->getMessage());
                }
            }
        }
    }
    //Touhemi 03-07: Modifier les services et les twig des mails séjour en masse pour afficher le logo et le nom de partenaire.
    //Fonction 1:cette fonction envoi seulement le code sejour aux partenaire et acco s'il existe
    function EnvoyerEmailCodeSejourToPartenaireAndAccoAvecNomLogoPartenaire($idSejour)
    {
        $logo = '';
        $nom = '';
        $RefEmail = $this->em->getRepository(Ref::class)->find(21);
        $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
        $Sejour = $this->em->getRepository(Sejour::class)->find($idSejour);
        if ($Sejour) {
            if ($Sejour->getIdEtablisment()) {
                $pdf1 = new Fpdi();
                $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
                $pdfFilepath1 = $publicDirectory . "Mode_emploi_5sur5sejour_v5_.pdf";
                $pageCount = $pdf1->setSourceFile($pdfFilepath1);
                $pageId = $pdf1->importPage(1, PdfReader\PageBoundaries::MEDIA_BOX);
                $pdf1->addPage();
                $pdf1->useImportedPage($pageId, 0.5, 0.5, 0.5);
                $testText  = "abcdefghijklmnopqrstuvwxyz0123456789";
                $current_date = date('d-M-Y');
                $DateDebut =  "";
                $DateFin = "";
                $Theme = "";
                $Lieu = "";
                $CodeSejour =  "";
                $MotPass =  "";
                $Ville = "";
                $Paye = "";
                if ($Sejour) {
                    $DateDebut = $Sejour->getDateDebutSejour();
                    $DateFin = $Sejour->getDateFinSejour();
                    $Theme = $Sejour->getThemSejour();
                    $Lieu = $Sejour->getAdresseSejour();
                    $CodeSejour = $Sejour->getCodeSejour();
                    $MotPass = $Sejour->getIdAcommp()->getPasswordNonCripted();
                    $Ville = $Sejour->getVille();
                    $Paye = $Sejour->getPays();
                }
                for ($i = 0; $i < $pageCount; $i++) {
                    $tplIdx = $pdf1->importPage(1 + $i);
                    $pdf1->useTemplate($tplIdx, 0, 0, 200, 300, true);
                    $theme = $Theme;
                    if (strlen($theme) > 25) {
                        $pdf1->SetFont("Arial", "", 5);
                    } else {
                        $pdf1->SetFont("Arial", "", 7);
                    }
                    $strTheme = iconv("UTF-8", "WINDOWS-1252", $theme);
                    $pdf1->Text(35, 262, $strTheme);
                    $pdf1->SetFont("Arial", "", 8);
                    $lieu = $Lieu;
                    $ville = $Ville;
                    $paye = $Paye;
                    if ($Paye  && $Ville) {
                        $strLieu = iconv("UTF-8", "WINDOWS-1252", $ville . "  " . $paye);
                        $pdf1->Text(125, 262, $strLieu);
                    } elseif ($Paye) {
                        $strLieu = iconv("UTF-8", "WINDOWS-1252", $paye);
                        $pdf1->Text(125, 262, $strLieu);
                    } else {
                        $strLieu = iconv("UTF-8", "WINDOWS-1252", $ville);
                        $pdf1->Text(125, 262, $strLieu);
                    }
                    $pdf1->SetFont("Arial", "", 10);
                    $pdf1->Text(50, 252, $DateDebut->format('d/m/Y'));
                    $pdf1->SetFont("Arial", "", 10);
                    $pdf1->Text(90, 252, $DateFin->format('d/m/Y'));
                    $pdf1->SetFont("Arial", "", 10);
                    $pdf1->Text(90, 271, $CodeSejour);
                    $pdf1->SetFont("Arial", "", 10);
                    $pdf1->Text(75, 279, $MotPass);
                }
                $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
                $pdfFilepath1 = $publicDirectory . "Mode d'emploi 5sur5sejour" . $idSejour . ".pdf";
                // $filename= $this->params->get('kernel.project_dir').'/public/pdf/pdfaccompagnateur.pdf';
                $pdf1->Output($pdfFilepath1, "F");
                $emailPartenaire = $Sejour->getIdEtablisment()->getEmail();
                $logo = $Sejour->getIdEtablisment()->getLogo();
                $nom = $Sejour->getIdEtablisment()->getNometab();
                $pdf = new Fpdi();
                $paym = $Sejour->getPaym();
                if ($paym == 1) {
                    $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
                    $pdfFilepath = $publicDirectory . "Mode_emploi_Parent_5sur5sejour_v5_.pdf";
                    $pageCount = $pdf->setSourceFile($pdfFilepath);
                } else {
                    $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
                    $pdfFilepath = $publicDirectory . "Mode_emploi_Parent_G_5sur5sejou_v5_.pdf";
                    $pageCount = $pdf->setSourceFile($pdfFilepath);
                }
                $pageId = $pdf->importPage(1, PdfReader\PageBoundaries::MEDIA_BOX);
                $pdf->addPage();
                $pdf->useImportedPage($pageId, 0.5, 0.5, 0.5);
                $testText  = "abcdefghijklmnopqrstuvwxyz0123456789";
                $current_date = date('d-M-Y');
                $DateDebut =  "";
                $DateFin = "";
                $Theme = "";
                $Lieu = "";
                $CodeSejour =  "";
                $Ville = "";
                $Paye = "";
                if ($Sejour) {
                    $DateDebut = $Sejour->getDateDebutSejour();
                    $DateFin = $Sejour->getDateFinSejour();
                    $Theme = $Sejour->getThemSejour();
                    $Lieu = $Sejour->getAdresseSejour();
                    $CodeSejour = $Sejour->getCodeSejour();
                    $Ville = $Sejour->getVille();
                    $Paye = $Sejour->getPays();
                }
                for ($i = 0; $i < $pageCount; $i++) {
                    $tplIdx = $pdf->importPage($i + 1);
                    $pdf->useTemplate($tplIdx, 0, 0, 200, 300, true);
                    $theme = $Theme;
                    if (strlen($theme) > 25) {
                        $pdf->SetFont("Arial", "", 5);
                    } else {
                        $pdf->SetFont("Arial", "", 7);
                    }
                    $strTheme = iconv("UTF-8", "WINDOWS-1252", $theme);
                    $pdf->Text(35, 268, $strTheme);
                    $pdf->SetFont("Arial", "", 8);
                    $lieu = $Lieu;
                    $ville = $Ville;
                    $paye = $Paye;
                    if ($Paye  && $Ville) {
                        $strLieu = iconv("UTF-8", "WINDOWS-1252", $ville . "  " . $paye);
                        $pdf->Text(125, 268, $strLieu);
                    } else if ($Paye) {
                        $strLieu = iconv("UTF-8", "WINDOWS-1252", $paye);
                        $pdf->Text(125, 268, $strLieu);
                    } else {
                        $strLieu = iconv("UTF-8", "WINDOWS-1252", $ville);
                        $pdf->Text(125, 268, $strLieu);
                    }
                    $pdf->SetFont("Arial", "", 10);
                    $pdf->Text(80, 279, $CodeSejour);
                    $pdf->SetFont("Arial", "", 10);
                    $pdf->Text(48, 258, $DateDebut->format('d/m/Y'));
                    $pdf->SetFont("Arial", "", 10);
                    $pdf->Text(89, 258, $DateFin->format('d/m/Y'));
                }
                if ($paym == 1) {
                    $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
                    $pdfFilepath = $publicDirectory . "Mode d'emploi parent  5sur5séjour" . $idSejour . ".pdf";
                    $pdf->Output($pdfFilepath, "F");
                } else {
                    $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
                    $pdfFilepath = $publicDirectory . "Mode d'emploi parent  5sur5séjour" . $idSejour . ".pdf";
                    $pdf->Output($pdfFilepath, "F");
                }
                $message = (new \Swift_Message('Votre code séjour 5sur5sejour'))
                    ->setFrom('partenariat-no-reply@5sur5sejour.com')
                    ->setTo($emailPartenaire);
                //->setBcc(["contact@5sur5sejour.com"]);
                $pathImage2 = $Email->getIdImage2()->getPath();
                $pathImage1 = $Email->getIdImage1()->getPath();
                $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
                $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
                $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
                $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
                $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
                $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
                if ($paym == 1) {
                    $message->attach(\Swift_Attachment::fromPath($pdfFilepath));
                } else {
                    $message->attach(\Swift_Attachment::fromPath($pdfFilepath));
                }
                $message->setBody(
                    $this->templating->render(
                        'emails/EnvoiCodeSejourEnMassAcc.html.twig',
                        [
                            "code" => $Sejour->getCodeSejour(),
                            "dateCreation" => $Sejour->getDateCreationCode(),
                            "dateFinCode" => $Sejour->getDateFinCode(),
                            "lieu" => $Sejour->getAdresseSejour(),
                            "image1" => $image1,
                            "image2" => $image2,
                            "iconfooter" => $iconfooter,
                            "iconphoto" => $iconphoto,
                            "iconloca" => $iconloca,
                            "iconmsg" => $iconmsg,
                            "identifiant" => $Sejour->getCodeSejour(),
                            'logo' => $logo,
                            'nom' => $nom,
                            'roles' => $Sejour->getIdEtablisment()->getUser()->getRoles(),
                        ]
                    ),
                    'text/html'
                );
                $signMail = $this->params->get('signMail');
                if ($signMail == 'yes') {
                    $domainName = $this->params->get('domaine');
                    $selector = $this->params->get('selector');
                    $PrivateKey = file_get_contents($this->params->get('pathDKIM'));
                    $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
                    $message->attachSigner($signer);
                }
                try {
                    // $this->mailer->send($message);
                    $resend = Resend::client('re_3ijtfF8W_AQyRsePq3jmmAaEohQig9Se8');

                    $resend->emails->send($message);
                } catch (\Swift_SwiftException $ex) {
                    $ex->getMessage();
                }
                if ($emailPartenaire != $Sejour->getIdAcommp()->getReponseemail()) {
                    $emailAcco = $Sejour->getIdAcommp()->getReponseemail();
                    $message = (new \Swift_Message('Votre code séjour 5sur5sejour'))
                        ->setFrom('no-reply@5sur5sejour.com')
                        ->setTo($emailAcco);
                    //->setBcc(["contact@5sur5sejour.com"]);
                    $pathImage2 = $Email->getIdImage2()->getPath();
                    $pathImage1 = $Email->getIdImage1()->getPath();
                    $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
                    $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
                    $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
                    $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
                    $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
                    $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
                    $message->attach(\Swift_Attachment::fromPath($pdfFilepath1));
                    if ($paym == 1) {
                        $message->attach(\Swift_Attachment::fromPath($pdfFilepath));
                    } else {
                        $message->attach(\Swift_Attachment::fromPath($pdfFilepath));
                    }
                    $message->setBody(
                        $this->templating->render(
                            'emails/EnvoiCodeSejourUniquementToAccoAndPartenaire.html.twig',
                            [
                                "Password" => $Sejour->getIdAcommp()->getPasswordNonCripted(),
                                "code" => $Sejour->getCodeSejour(),
                                "dateCreation" => $Sejour->getDateCreationCode(),
                                "dateFinCode" => $Sejour->getDateFinCode(),
                                "lieu" => $Sejour->getAdresseSejour(),
                                "image1" => $image1,
                                "image2" => $image2,
                                "iconfooter" => $iconfooter,
                                "iconphoto" => $iconphoto,
                                "iconloca" => $iconloca,
                                "iconmsg" => $iconmsg,
                                "identifiant" => $Sejour->getCodeSejour(),
                                'logo' => $logo,
                                'nom' => $nom,
                                'roles' => $Sejour->getIdEtablisment()->getUser()->getRoles(),
                            ]
                        ),
                        'text/html'
                    );
                    $signMail = $this->params->get('signMail');
                    if ($signMail == 'yes') {
                        $domainName = $this->params->get('domaine');
                        $selector = $this->params->get('selector');
                        $PrivateKey = file_get_contents($this->params->get('pathDKIM'));
                        $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
                        $message->attachSigner($signer);
                    }
                    try {
                        // $this->mailer->send($message);
                        $resend = Resend::client('re_3ijtfF8W_AQyRsePq3jmmAaEohQig9Se8');

                        $resend->emails->send($message);
                    } catch (\Swift_SwiftException $ex) {
                        $ex->getMessage();
                    }
                }
            }
        }
    }
    // Fonction 2: cette fonction envoi seulement le password sejour aux partenaire et acco s'il existe
    function EnvoyerEmailPasswordSejourToPartenaireAndAccoAvecNomLogoPartenaire($idSejour)
    {
        $logo = '';
        $nom = '';
        $Sejour = $this->em->getRepository(Sejour::class)->find($idSejour);
        $RefEmail = $this->em->getRepository(Ref::class)->find(28);
        $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
        if ($Sejour) {
            if ($Sejour->getIdEtablisment()) {
                $emailPartenaire = $Sejour->getIdEtablisment()->getEmail();
                $logo = $Sejour->getIdEtablisment()->getLogo();
                $nom = $Sejour->getIdEtablisment()->getNometab();
                //dd($sejour);
                $pdf = new Fpdi();
                $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
                $pdfFilepath = $publicDirectory . "Mode_emploi_5sur5sejour_v5_.pdf";
                $pageCount = $pdf->setSourceFile($pdfFilepath);
                $pageId = $pdf->importPage(1, PdfReader\PageBoundaries::MEDIA_BOX);
                $pdf->addPage();
                $pdf->useImportedPage($pageId, 0.5, 0.5, 0.5);
                $testText  = "abcdefghijklmnopqrstuvwxyz0123456789";
                $current_date = date('d-M-Y');
                $DateDebut =  "";
                $DateFin = "";
                $Theme = "";
                $Lieu = "";
                $CodeSejour =  "";
                $MotPass =  "";
                $Ville = "";
                $Paye = "";
                if ($Sejour) {
                    $DateDebut = $Sejour->getDateDebutSejour();
                    $DateFin = $Sejour->getDateFinSejour();
                    $Theme = $Sejour->getThemSejour();
                    $Lieu = $Sejour->getAdresseSejour();
                    $CodeSejour = $Sejour->getCodeSejour();
                    $MotPass = $Sejour->getIdAcommp()->getPasswordNonCripted();
                    $Ville = $Sejour->getVille();
                    $Paye = $Sejour->getPays();
                }
                for ($i = 0; $i < $pageCount; $i++) {
                    $tplIdx = $pdf->importPage(1 + $i);
                    $pdf->useTemplate($tplIdx, 0, 0, 200, 300, true);
                    $theme = $Theme;
                    if (strlen($theme) > 25) {
                        $pdf->SetFont("Arial", "", 5);
                    } else {
                        $pdf->SetFont("Arial", "", 7);
                    }
                    $strTheme = iconv("UTF-8", "WINDOWS-1252", $theme);
                    $pdf->Text(35, 262, $strTheme);
                    $pdf->SetFont("Arial", "", 8);
                    $lieu = $Lieu;
                    $ville = $Ville;
                    $paye = $Paye;
                    if ($Paye && $Ville) {
                        $strLieu = iconv("UTF-8", "WINDOWS-1252", $ville . "  " . $paye);
                        $pdf->Text(125, 262, $strLieu);
                    } else if ($Paye) {
                        $strLieu = iconv("UTF-8", "WINDOWS-1252", $paye);
                        $pdf->Text(125, 262, $strLieu);
                    } else {
                        $strLieu = iconv("UTF-8", "WINDOWS-1252", $ville);
                        $pdf->Text(125, 262, $strLieu);
                    }
                    $pdf->SetFont("Arial", "", 10);
                    $pdf->Text(50, 252, $DateDebut->format('d/m/Y'));
                    $pdf->SetFont("Arial", "", 10);
                    $pdf->Text(90, 252, $DateFin->format('d/m/Y'));
                    $pdf->SetFont("Arial", "", 10);
                    $pdf->Text(90, 271, $CodeSejour);
                    $pdf->SetFont("Arial", "", 10);
                    $pdf->Text(75, 279, $MotPass);
                }
                $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
                $pdfFilepath = $publicDirectory . "Mode d'emploi 5sur5sejour" . $idSejour . ".pdf";
                // $filename= $this->params->get('kernel.project_dir').'/public/pdf/pdfaccompagnateur.pdf';
                $pdf->Output($pdfFilepath, "F");
                $message = (new \Swift_Message('Votre mot de passe 5sur5sejour'))
                    ->setFrom('partenariat-no-reply@5sur5sejour.com')
                    ->setTo($emailPartenaire);
                //->setBcc(["contact@5sur5sejour.com"]);
                $pathImage2 = $Email->getIdImage2()->getPath();
                $pathImage1 = $Email->getIdImage1()->getPath();
                $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
                $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
                $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
                $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
                $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
                $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
                $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
                $pdfFilepath = $publicDirectory . "Mode d'emploi 5sur5sejour" . $idSejour . ".pdf";
                $message->attach(\Swift_Attachment::fromPath($pdfFilepath));
                $message->setBody(
                    $this->templating->render(
                        'emails/EnvoiMotdePassSejouEnMassAcc.html.twig',
                        [
                            "Password" => $Sejour->getIdAcommp()->getPasswordNonCripted(),
                            "image1" => $image1,
                            "image2" => $image2,
                            "iconfooter" => $iconfooter,
                            "iconphoto" => $iconphoto,
                            "iconloca" => $iconloca,
                            "iconmsg" => $iconmsg,
                            'logo' => $logo,
                            'nom' => $nom,
                            'roles' => $Sejour->getIdEtablisment()->getUser()->getRoles(),
                        ]
                    ),
                    'text/html'
                );
                $signMail = $this->params->get('signMail');
                if ($signMail == 'yes') {
                    $domainName = $this->params->get('domaine');
                    $selector = $this->params->get('selector');
                    $PrivateKey = file_get_contents($this->params->get('pathDKIM'));
                    $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
                    $message->attachSigner($signer);
                }
                try {
                    // $this->mailer->send($message);
                    $resend = Resend::client('re_3ijtfF8W_AQyRsePq3jmmAaEohQig9Se8');

                    $resend->emails->send($message);
                } catch (\Swift_SwiftException $ex) {
                    $ex->getMessage();
                }
                if ($emailPartenaire != $Sejour->getIdAcommp()->getReponseemail()) {
                    $emailAcco = $Sejour->getIdAcommp()->getReponseemail();
                    $message = (new \Swift_Message('Votre mot de passe 5sur5sejour'))
                        ->setFrom('no-reply@5sur5sejour.com')
                        ->setTo($emailAcco);
                    // ->setBcc(["contact@5sur5sejour.com"]);
                    $pathImage2 = $Email->getIdImage2()->getPath();
                    $pathImage1 = $Email->getIdImage1()->getPath();
                    $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
                    $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
                    $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
                    $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
                    $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
                    $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
                    $publicDirectory = $this->params->get('kernel.project_dir') . '/public/pdf/';
                    $pdfFilepath = $publicDirectory . "Mode d'emploi 5sur5sejour" . $idSejour . ".pdf";
                    $message->attach(\Swift_Attachment::fromPath($pdfFilepath));
                    $message->setBody(
                        $this->templating->render(
                            'emails/EnvoiMotdePassSejouEnMassAcc.html.twig',
                            [
                                "Password" => $Sejour->getIdAcommp()->getPasswordNonCripted(),
                                "image1" => $image1,
                                "image2" => $image2,
                                "iconfooter" => $iconfooter,
                                "iconphoto" => $iconphoto,
                                "iconloca" => $iconloca,
                                "iconmsg" => $iconmsg,
                                'logo' => $logo,
                                'nom' => $nom,
                                'roles' => $Sejour->getIdAcommp()->getRoles(),
                            ]
                        ),
                        'text/html'
                    );
                    $signMail = $this->params->get('signMail');
                    if ($signMail == 'yes') {
                        $domainName = $this->params->get('domaine');
                        $selector = $this->params->get('selector');
                        $PrivateKey = file_get_contents($this->params->get('pathDKIM'));
                        $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
                        $message->attachSigner($signer);
                    }
                    try {
                        // $done = $this->mailer->send($message);
                        $resend = Resend::client('re_3ijtfF8W_AQyRsePq3jmmAaEohQig9Se8');

                        $resend->emails->send($message);
                        //var_dump($done);
                        // var_dump("send it ");
                    } catch (\Swift_SwiftException $ex) {
                        //var_dump( $ex->getMessage());
                    }
                }
            }
        }
    }
    //Le nombre de séjours créés
    public function getNbrSejourCree($dateDeb, $dateFin)
    {
        $nbrSejourCree = $this->em->getRepository(Sejour::class)->getNbrSejourCree($dateDeb, $dateFin);
        return $nbrSejourCree;
    }
    public function getNbrSejourCreeParPart($dateDeb, $dateFin, $part)
    {
        $nbrSejourCree = $this->em->getRepository(Sejour::class)->getNbrSejourCreeParPart($dateDeb, $dateFin, $part);
        return $nbrSejourCree;
    }
    public function getNbrSejourCreeParTypePart($dateDeb, $dateFin, $type)
    {
        $nbrSejourCree = $this->em->getRepository(Sejour::class)->getNbrSejourCreeParTypePart($dateDeb, $dateFin, $type);
        return $nbrSejourCree;
    }
    //Le nombre de séjours actifs
    public function findSejourEncourBetween($dateDeb, $dateFin)
    {
        $SejourEncourBetween = $this->em->getRepository(Sejour::class)->findSejourEncourBetween($dateDeb, $dateFin);
        return $SejourEncourBetween;
    }
    public function findSejourEncourBetweenParPart($dateDeb, $dateFin, $part)
    {
        $SejourEncourBetween = $this->em->getRepository(Sejour::class)->findSejourEncourBetweenParPart($dateDeb, $dateFin, $part);
        return $SejourEncourBetween;
    }
    //nommbre de sejour encour
    public function findSejourActiveBetween($dateDeb, $dateFin)
    {
        $SejourActiveBetween = $this->em->getRepository(Sejour::class)->findSejourActiveBetween($dateDeb, $dateFin);
        return $SejourActiveBetween;
    }
    public function findSejourActiveBetweenParPart($dateDeb, $dateFin, $part)
    {
        $SejourActiveBetween = $this->em->getRepository(Sejour::class)->findSejourActiveBetweenParPart($dateDeb, $dateFin, $part);
        return $SejourActiveBetween;
    }
    public function findSejourActiveBetweenParTypePart($dateDeb, $dateFin, $type)
    {
        $SejourActiveBetween = $this->em->getRepository(Sejour::class)->findSejourActiveBetweenParTypePart($dateDeb, $dateFin, $type);
        return $SejourActiveBetween;
    }
    //Le nombre d’enfant declare  moyen par séjour encour
    public function enfantsdeclarer($sejours)
    {
        $nbrEnfantSejours = 0;
        foreach ($sejours as $sejour) {
            $nbrEnfantSejours = $nbrEnfantSejours + $sejour->getNbenfan();
        }
        return $nbrEnfantSejours;
    }
    public function parentConnecte($sejours)
    {
        $nbrPArentSejours = 0;
        foreach ($sejours as $sejour) {
            $parent_Sejour = $this->em->getRepository(ParentSejour::class)->findBy(array('idSejour' => $sejour));
            $nbrPArentSejours = $nbrPArentSejours + sizeof($parent_Sejour);
        }
        return $nbrPArentSejours;
    }
    public function findParentDateBetween($dateDeb, $dateFin)
    {
        $findParentDateBetween = $this->em->getRepository(ParentSejour::class)->findParentDateBetween($dateDeb, $dateFin);
        return $findParentDateBetween;
    }
    public function findParentDateBetweenParPart($dateDeb, $dateFin, $part)
    {
        $findParentDateBetween = $this->em->getRepository(ParentSejour::class)->findParentDateBetweenParPart($dateDeb, $dateFin, $part);
        return $findParentDateBetween;
    }
    public function findParentDateBetweenParTypePart($dateDeb, $dateFin, $type)
    {
        $findParentDateBetween = $this->em->getRepository(ParentSejour::class)->findParentDateBetweenParTypePart($dateDeb, $dateFin, $type);
        return $findParentDateBetween;
    }
    public function FindeUSerParentBetween($dateDeb, $dateFin)
    {
        $users = $this->em->getRepository(User::class)->FindeUSerParentBetween($dateDeb, $dateFin);
        return $users;
    }
    public function FindeUSerParentActiveBetween($dateDeb, $dateFin)
    {
        $users = $this->em->getRepository(User::class)->FindeUSerParentActiveBetween($dateDeb, $dateFin);
        return $users;
    }
    public function FindeUSerParentActiveBetweenParPart($dateDeb, $dateFin, $part)
    {
        $users = $this->em->getRepository(ParentSejour::class)->FindeUSerParentActiveBetweenParPart($dateDeb, $dateFin, $part);
        return $users;
    }
    public function FindeUSerParentActiveBetweenParTypePart($dateDeb, $dateFin, $type)
    {
        $users = $this->em->getRepository(ParentSejour::class)->FindeUSerParentActiveBetweenParTypePart($dateDeb, $dateFin, $type);
        return $users;
    }
    public function findSejourEncourBetweenParTypePart($dateDeb, $dateFin, $type)
    {
        $users = $this->em->getRepository(ParentSejour::class)->FindeUSerParentActiveBetweenParPart($dateDeb, $dateFin, $type);
        return $users;
    }
    function caConnexionTotalFree()
    {
        $etabs = $this->em->getRepository(Etablisment::class)->findAll();
        $cconnexionFree = 0;
        $nbrConexxion = 0;
        foreach ($etabs as $etab) {
            $sejours = $this->em->getRepository(Sejour::class)->findBy(array("idEtablisment" => $etab));
            $prixtotPArt = 0;
            $flag = false;
            foreach ($sejours as $sejour) {
                $lastday = \DateTime::createFromFormat("Y-m-d H:i:s", date("Y-08-01 00:00:00"));
                if ((substr($sejour->getCodeSejour(), 1, 1) == 'F')) {
                    $flag = true;
                    $parent_sejours = $this->em->getRepository(ParentSejour::class)->findBy(array("idSejour" => $sejour));
                    $prixConnPArt = $sejour->getPrixcnxpartenaire();
                    $nbrConexxion = $nbrConexxion + sizeof($parent_sejours);
                    $prixtotal = $prixConnPArt * sizeof($parent_sejours);
                    $prixtotPArt = $prixtotPArt + $prixtotal;
                }
            }
            $cconnexionFree = $cconnexionFree + $prixtotPArt;
        }
        return [$cconnexionFree, $nbrConexxion];
    }
    function caConnexionTotalFreeParPart($part)
    {
        $etabs = $this->em->getRepository(Etablisment::class)->findAll();
        $cconnexionFree = 0;
        $nbrConexxion = 0;
        foreach ($etabs as $etab) {
            if ($etab->getId() == $part) {
                $sejours = $this->em->getRepository(Sejour::class)->findBy(array("idEtablisment" => $etab));
                $prixtotPArt = 0;
                $flag = false;
                foreach ($sejours as $sejour) {
                    $lastday = \DateTime::createFromFormat("Y-m-d H:i:s", date("Y-08-01 00:00:00"));
                    if ((substr($sejour->getCodeSejour(), 1, 1) == 'F')) {
                        $flag = true;
                        $parent_sejours = $this->em->getRepository(ParentSejour::class)->findBy(array("idSejour" => $sejour));
                        $prixConnPArt = $sejour->getPrixcnxpartenaire();
                        $nbrConexxion = $nbrConexxion + sizeof($parent_sejours);
                        $prixtotal = $prixConnPArt * sizeof($parent_sejours);
                        $prixtotPArt = $prixtotPArt + $prixtotal;
                    }
                }
                $cconnexionFree = $cconnexionFree + $prixtotPArt;
            }
        }
        return [$cconnexionFree, $nbrConexxion];
    }
    function caConnexionTotalPaye()
    {
        $prixtotPArt = 0;
        $flag = false;
        $parent_sejours = $this->em->getRepository(ParentSejour::class)->findBy(array("payment" => 1));
        $prixConnPArt = 2.9;
        $nbrConexxion = sizeof($parent_sejours);
        $prixtotal = $prixConnPArt * sizeof($parent_sejours);
        $prixtotPArt = $prixtotPArt + $prixtotal;
        return [$prixtotPArt, $nbrConexxion];
    }
    function caConnexionTotalPayeParPart($part)
    {
        $prixtotPArt = 0;
        $flag = false;
        $parent_sejours = $this->em->getRepository(ParentSejour::class)->findBy(array("payment" => 1));
        $prixConnPArt = 2.9;
        $nbrConexxion = 0;
        foreach ($parent_sejours as $sej) {
            if ($sej->getIdSejour()->getIdEtablisment()->getId() == $part) {
                $nbrConexxion = $nbrConexxion + 1;
            }
        }
        $prixtotal = $prixConnPArt * $nbrConexxion;
        $prixtotPArt = $prixtotPArt + $prixtotal;
        return [$prixtotPArt, $nbrConexxion];
    }
    function caConnexionTotal()
    {
        return [$this->caConnexionTotalPaye()[0] + $this->caConnexionTotalFree()[0], $this->caConnexionTotalPaye()[1] + $this->caConnexionTotalFree()[1]];
    }
    function caConnexionTotalParPart($part)
    {
        return [$this->caConnexionTotalPayeParPart($part)[0] + $this->caConnexionTotalFreeParPart($part)[0], $this->caConnexionTotalPayeParPart($part)[1] + $this->caConnexionTotalFreeParPart($part)[1]];
    }
    function caProduit()
    {
        return $this->em->getRepository(Commande::class)->caProduit();
    }
    //Le nombre de comptes ouverts
    //Le nombre comptes activés
    //Le nombre de commande en cours
    //Le nombre de commande moyen par sejour
    //Le CA connexion en cours (décomposé en connexion payé partenaire et connexion payé par parent)
    //Le montant du panier moyen
    //Le nombre moyen de produits par commande
    //Montant du reversement connexion
    //Montant de reversement commande
    function caConnexionTotalFreeEncour()
    {
        $cconnexionFree = 0;
        $nbrConexxion = 0;
        $sejours = $this->em->getRepository(Sejour::class)->findSejourListEncourFree();
        // dd($sejours);
        $prixtotPArt = 0;
        $flag = false;
        foreach ($sejours as $sejour) {
            if ((substr($sejour->getCodeSejour(), 1, 1) == 'F')) {
                $flag = true;
                $parent_sejours = $this->em->getRepository(ParentSejour::class)->findBy(array("idSejour" => $sejour->getId()));
                $prixConnPArt = $sejour->getPrixcnxpartenaire();
                $nbrConexxion = $nbrConexxion + sizeof($parent_sejours);
                $prixtotal = $prixConnPArt * sizeof($parent_sejours);
                $prixtotPArt = $prixtotPArt + $prixtotal;
            }
        }
        return $prixtotPArt;
    }
    function caConnexionTotalFreeEncourParPart($part)
    {
        $cconnexionFree = 0;
        $nbrConexxion = 0;
        $sejours = $this->em->getRepository(Sejour::class)->findSejourListEncourFreeParPart($part);
        // dd($sejours);
        $prixtotPArt = 0;
        $flag = false;
        foreach ($sejours as $sejour) {
            if ((substr($sejour->getCodeSejour(), 1, 1) == 'F')) {
                $flag = true;
                $parent_sejours = $this->em->getRepository(ParentSejour::class)->findBy(array("idSejour" => $sejour->getId()));
                $prixConnPArt = $sejour->getPrixcnxpartenaire();
                $nbrConexxion = $nbrConexxion + sizeof($parent_sejours);
                $prixtotal = $prixConnPArt * sizeof($parent_sejours);
                $prixtotPArt = $prixtotPArt + $prixtotal;
            }
        }
        return $prixtotPArt;
    }
    function caConnexionTotalPayEncour()
    {
        $sejours = $this->em->getRepository(Sejour::class)->findSejourListEncourPay();
        $someparsejour = 0;
        foreach ($sejours as $sejour) {
            $parent_sejours = $this->em->getRepository(ParentSejour::class)->findBy(array("idSejour" => $sejour->getId(), 'payment' => 1));
            $someparsejour = $someparsejour + (sizeof($parent_sejours) * 2.9);
        }
        //    var_dump($someparsejour);
        return $someparsejour;
    }
    function caConnexionTotalPayEncourParPart($part)
    {
        $sejours = $this->em->getRepository(Sejour::class)->findSejourListEncourPay();
        $someparsejour = 0;
        foreach ($sejours as $sejour) {
            // var_dump($sejour);
            if ($sejour->getIdEtablisment()->getId() == $part) {
                $parent_sejours = $this->em->getRepository(ParentSejour::class)->findBy(array("idSejour" => $sejour->getId(), 'payment' => 1));
                $someparsejour = $someparsejour + (sizeof($parent_sejours) * 2.9);
            }
        }
        //    var_dump($someparsejour);
        return $someparsejour;
    }
    public function Formaterarraydate($arraytoFormat, $chantest)
    {
        $arrayFInal = array();
        foreach ($arraytoFormat as $entit) {
            // var_dump($entit->$chantest()->format('d/m/Y'));
            if (array_key_exists($entit->$chantest()->format('d/m/Y'), $arrayFInal)) {
                array_push($arrayFInal, $entit);
            } else {
                $arrayFInal[$entit->$chantest()->format('d/m/Y')] = array($entit);
            }
        }
        return $arrayFInal;
    }
    public function FormaterarraydateBetween($arraytoFormat, $chanttestDEb, $chantestEnd, $dateDebut, $dateFinoriginal)
    {
        $arrayFinal = array();
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

        if ($diffDays <= 31) {
            // Use days if the difference is less than or equal to 31 days
            $period = new DatePeriod(
                $dateDebut,
                new DateInterval('P1D'),
                $dateFin
            );

            foreach ($period as $value) {
                $day = (int)$value->format('j');
                $arrayFinal[$day] = 0;

                foreach ($arraytoFormat as $entit) {
                    if ($value <= $entit->$chantestEnd() && $value >= $entit->$chanttestDEb()) {
                        $arrayFinal[$day]++;
                    }
                }
            }
        } else {
            // Use months if the difference is more than 31 days
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
                $startMonthKey = $entit->$chanttestDEb()->format('m');
                $endMonthKey = $entit->$chantestEnd()->format('m');

                foreach (range((int)$startMonthKey, (int)$endMonthKey) as $month) {
                    $monthName = $months[str_pad($month, 2, '0', STR_PAD_LEFT)];
                    if (!isset($arrayFinal[$monthName])) {
                        $arrayFinal[$monthName] = 0;
                    }
                    $arrayFinal[$monthName]++;
                }
            }
        }

        return $arrayFinal;
    }


    public function FormaterarraydateBetweennbrEnfant($arraytoFormat, $chanttestDEb, $chantestEnd, $dateDEbut, $dateFin)
    {
        $arrayFInal = array();
        $totalenf = 0;
        foreach ($arraytoFormat as $entit) {
            if (array_key_exists($entit->getCodeSejour(), $arrayFInal)) {
                $arrayFInal[$entit->getCodeSejour()]['nbrenfdec'] = $arrayFInal[$entit->getCodeSejour()]['nbrenfdec'] + $entit->getNbenfan();
                $arrayFInal[$entit->getCodeSejour()]['nbrParentCncte'] = $arrayFInal[$entit->getCodeSejour()]['nbrParentCncte'] + $entit->getCountParentSejour();
            } else {
                $arrayFInal[$entit->getCodeSejour()] = array('nbrenfdec' => $entit->getNbenfan(), 'nbrParentCncte' => $entit->getCountParentSejour());
            }
            $totalenf = $totalenf + $entit->getNbenfan();
        }
        $arrayFInal = $this->sort_col($arrayFInal, 'nbrParentCncte');
        $arrayFInal['totenf'] = $totalenf;
        return  $arrayFInal;
    }
    public function FormaterarraydateBetweennbrPArentCncte($arraytoFormat, $chanttest, $dateDebut, $dateFinoriginal)
    {
        $arrayFinal = array();
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
                    if ($value->format('d/m/Y') == $entit->$chanttest()->format('d/m/Y')) {
                        $arrayFinal[$day] += 1;
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
                $monthKey = $entit->$chanttest()->format('m');
                $monthName = $months[$monthKey];
                if (!isset($arrayFinal[$monthName])) {
                    $arrayFinal[$monthName] = 0;
                }
                $arrayFinal[$monthName] += 1;
            }
        }

        return $arrayFinal;
    }

    function sort_col($table, $colname)
    {
        $tn = $ts = $temp_num = $temp_str = array();
        //    dd($table);
        foreach ($table as $key => $row) {
            /*        var_dump($colname);
        var_dump($key);
        var_dump($row[$colname]);
        var_dump(substr($row[$colname],0,1));
        var_dump(is_numeric(substr($row[$colname],0,1)));
        var_dump($key);
 
        echo '<br>';
        */
            if (is_numeric(substr($row[$colname], 0, 1))) {
                $tn[$key] = $row[$colname];
                $temp_num[$key] = $row;
            } else {
                $ts[$key] = $row[$colname];
                $temp_str[$key] = $row;
            }
        }
        unset($table);
        array_multisort($tn, SORT_DESC, SORT_NUMERIC, $temp_num);
        array_multisort($ts, SORT_DESC, SORT_STRING, $temp_str);
        return array_merge($temp_num, $temp_str);
    }
    function EnvoyerEmailNewAccompagnateurPlus($partenaire)
    {
        $logo = '';
        $nom = '';
        if ($partenaire->hasRole('ROLE_PARTENAIRE')) {
            $logo = $partenaire->getLogourl();
            $nom = $partenaire->getNometablisment();
        }
        $RefEmail = $this->em->getRepository(Ref::class)->find(17);
        $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail));
        $sendTo = $partenaire->getEmail();
        $message = (new \Swift_Message('Nouveau partenaire'))
            ->setFrom('partenariat-no-reply@5sur5sejour.com')
            ->setTo($sendTo);
        //->setBcc(["contact@5sur5sejour.com"]);
        $pathImage2 = $Email->getIdImage2()->getPath();
        $pathImage1 = $Email->getIdImage1()->getPath();
        $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
        $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
        $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
        $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
        $icon2 = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
        $icon3 = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
        $message->setBody(
            $this->templating->render(
                'emails/AccompagnateurPlusNew.html.twig',
                [
                    "Nomdestinataire" => $partenaire->getNom(),
                    "Predestinataire" => $partenaire->getPrenom(),
                    "password" => $partenaire->getPasswordNonCripted(),
                    "image1" => $image1,
                    "image2" => $image2,
                    "icon2" => $icon2,
                    "icon3" => $icon3,
                    "iconfooter" => $iconfooter,
                    "iconmsg" => $iconmsg,
                    "idPartenaire" => $partenaire->getId(),
                    "identifiant" => $sendTo,
                    "password" => $partenaire->getPasswordNonCripted(),
                    'logo' => $logo,
                    'nom' => $nom,
                ]
            ),
            'text/html'
        );
        $signMail = $this->params->get('signMail');
        if ($signMail == 'yes') {
            $domainName = $this->params->get('domaine');
            $selector = $this->params->get('selector');
            $PrivateKey = file_get_contents($this->params->get('pathDKIM'));
            $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
            $message->attachSigner($signer);
        }
        try {
            // $this->mailer->send($message);
            $resend = Resend::client('re_3ijtfF8W_AQyRsePq3jmmAaEohQig9Se8');

            $resend->emails->send($message);
        } catch (\Swift_SwiftException $ex) {
            $ex->getMessage();
        }
    }
    function EnvoyerEmailSejourRecapProduitsSansCommandes($nomfilevalid)
    {
        $logo = '';
        $nom = '';
        $RefEmail = $this->em->getRepository(Ref::class)->find(29);
        $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
        $sendTo = "touhemib@gmail.com";
        $message = (new \Swift_Message('Produits sans commanes'))
            ->setFrom('touhemib@gmail.com')
            ->setTo($sendTo);
        $pathImage2 = $Email->getIdImage2()->getPath();
        $pathImage1 = $Email->getIdImage1()->getPath();
        $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
        $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
        $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
        $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
        $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
        $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
        $message->attach(Swift_Attachment::fromPath($this->projectDir . '/public/message//' . $nomfilevalid)->setFilename('Recap_Excel_ProduitsSansCommandes.xlsx'));
        $message->setBody(
            $this->templating->render(
                // Firas : completer Twig
                'emails/RecapParentsCreerProduitsSansCommande.html.twig',
                [
                    "image1" => $image1,
                    "image2" => $image2,
                    "iconfooter" => $iconfooter,
                    "iconphoto" => $iconphoto,
                    "iconloca" => $iconloca,
                    "iconmsg" => $iconmsg,
                    'logo' => $logo,
                    'nom' => $nom,
                ]
            ),
            'text/html'
        );
        $signMail = $this->params->get('signMail');
        if ($signMail == 'yes') {
            $domainName = $this->params->get('domaine');
            $selector = $this->params->get('selector');
            $PrivateKey = file_get_contents($this->params->get('pathDKIM'));
            $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
            $message->attachSigner($signer);
        }
        try {
            // $this->mailer->send($message);
            $resend = Resend::client('re_3ijtfF8W_AQyRsePq3jmmAaEohQig9Se8');

            $resend->emails->send($message);
        } catch (\Swift_SwiftException $ex) {
            $ex->getMessage();
        }
    }
    function EnvoyerMailExport($nomfilevalid)
    {
        $logo = '';
        $nom = '';
        $RefEmail = $this->em->getRepository(Ref::class)->find(29);
        $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));
        $sendTo = "touhemib@gmail.com";
        $message = (new \Swift_Message('Export'))
            ->setFrom('no-reply@5sur5sejour.com')
            ->setTo(['touhemib@gmail.com', 'yousra.tlich@gmail.com']);
        $pathImage2 = $Email->getIdImage2()->getPath();
        $pathImage1 = $Email->getIdImage1()->getPath();
        $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
        $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
        $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
        $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
        $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
        $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
        $message->attach(Swift_Attachment::fromPath($this->projectDir . '/public/message//' . $nomfilevalid)->setFilename('Recap_Excel_ProduitsSansCommandes.xlsx'));
        $message->setBody(
            $this->templating->render(
                // Firas : completer Twig
                'emails/RecapParentsCreerProduitsSansCommande.html.twig',
                [
                    "image1" => $image1,
                    "image2" => $image2,
                    "iconfooter" => $iconfooter,
                    "iconphoto" => $iconphoto,
                    "iconloca" => $iconloca,
                    "iconmsg" => $iconmsg,
                    'logo' => $logo,
                    'nom' => $nom,
                ]
            ),
            'text/html'
        );
        $signMail = $this->params->get('signMail');
        if ($signMail == 'yes') {
            $domainName = $this->params->get('domaine');
            $selector = $this->params->get('selector');
            $PrivateKey = file_get_contents($this->params->get('pathDKIM'));
            $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
            $message->attachSigner($signer);
        }
        try {
            // $this->mailer->send($message);
            $resend = Resend::client('re_3ijtfF8W_AQyRsePq3jmmAaEohQig9Se8');

            $resend->emails->send($message);
        } catch (\Swift_SwiftException $ex) {
            $ex->getMessage();
        }
    }
    public function creationPromotion($code, $type, $nbreMaxGeneral, $nbreMaxParUser, $dateDebut, $dateFin, $etat, $pourcentage, $nbreApplique, $strategie)
    {
        $statutCreate = $this->em->getRepository(Ref::class)->find(1);
        $promotion = new Promotions();
        $promotion->setCode($code);
        $promotion->setType($type);
        $promotion->setNbreMaxGeneral(intval($nbreMaxGeneral));
        $promotion->setNbreMaxParUser(intval($nbreMaxParUser));
        $promotion->setDateCreation(new \DateTime());
        $promotion->setStatut($statutCreate);
        $dateDebutFormat = date_create_from_format('Y-m-d', $dateDebut);
        $promotion->setDateDebut($dateDebutFormat);
        $dateFinFormat = date_create_from_format('Y-m-d', $dateFin);
        $promotion->setDateFin($dateFinFormat);
        $promotion->setEtat(intval($etat));
        $promotion->setPourcentage(intval($pourcentage));
        $promotion->setStrategie($strategie);
        $promotion->setNbreApplicable($nbreApplique);
        $this->em->persist($promotion);
        $this->em->flush();
        return $promotion;
    }
    public function listePromotions()
    {
        $promotions = $this->em->getRepository(Promotions::class)->findBy(array('statut' => 1));
        return $promotions;
    }
    public function detailsPromotion($idPromo)
    {
        $promotion = $this->em->getRepository(Promotions::class)->find($idPromo);
        return $promotion;
    }
    public  function deletePromotion($idPromo)
    {
        $promotion = $this->em->getRepository(Promotions::class)->find($idPromo);
        $statutDelete = $this->em->getRepository(Ref::class)->find(39);
        if ($promotion) {
            $promotion->setStatut($statutDelete);
            $this->em->persist($promotion);
            $this->em->flush();
        }
        return $promotion;
    }
    public function updatePromotion
    ($idPromo, $type, $nbreMaxGeneral, $nbreMaxParUser, $dateDebut, 
    $dateFin, $etat, $pourcentage, $nbreApplique)
    {
        $promotion = $this->em->getRepository(Promotions::class)->find($idPromo);
        if ($promotion) {
            if ($promotion->getType() == "parents") {
                $tabParentsPromo = $this->em->getRepository(PromoParents::class)->findBy(array('promotion' => $promotion));
                if (sizeof($tabParentsPromo)) {
                    foreach ($tabParentsPromo as $pp) {
                        $this->em->remove($pp);
                        $this->em->flush();
                    }
                }
            }
            if ($promotion->getType() == "codeSejour") {
                $tabSejourParents = $this->em->getRepository(PromoSejour::class)->findBy(array('promotion' => $promotion));
                if (sizeof($tabSejourParents)) {
                    foreach ($tabSejourParents as $pp) {
                        $this->em->remove($pp);
                        $this->em->flush();
                    }
                }
            }
            $promotion->setType($type);
            $promotion->setNbreMaxGeneral(intval($nbreMaxGeneral));
            $promotion->setNbreMaxParUser(intval($nbreMaxParUser));
            $dateDebutFormat = date_create_from_format('Y-m-d', $dateDebut);
if ($dateDebutFormat === false) {
    // Handle the error, for example, log it or throw an exception
    throw new \Exception("Invalid date format for dateDebut: $dateDebut");
} else {
    $promotion->setDateDebut($dateDebutFormat);
}

$dateFinFormat = date_create_from_format('Y-m-d', $dateFin);
if ($dateFinFormat === false) {
    // Handle the error similarly for dateFin
    throw new \Exception("Invalid date format for dateFin: $dateFin");
} else {
    $promotion->setDateFin($dateFinFormat);
}

            $promotion->setEtat(intval($etat));
            $promotion->setPourcentage(intval($pourcentage));
            $promotion->setNbreApplicable($nbreApplique);
            $this->em->persist($promotion);
            $this->em->flush();
        }
        return $promotion;
    }
    public function findPromotionByCode($code)
    {
        $promotion = $this->em->getRepository(Promotions::class)->findOneBy(array('code' => $code, 'etat' => 1));
        return $promotion;
    }
    public function checkCodePromoForCreation($code)
    {
        $promotion = $this->em->getRepository(Promotions::class)->findOneBy(array('code' => $code));
        if ($promotion) {
            $tab['test'] = true;
            $tab['pourcentage'] = $promotion->getPourcentage();
            return $tab;
        } else {
            $tab['test'] = false;
            $tab['pourcentage'] = 0;
            return $tab;
        }
    }
    public function checkCodePromoPrents($code, $user)
    {
        $promotion = $this->em->getRepository(Promotions::class)->findOneBy(array('code' => $code, 'etat' => 1));
        if ($promotion && $user->getShowdetailsphotos() != "4") {
            $tab['test'] = true;
            $tab['pourcentage'] = $promotion->getPourcentage();
            return $tab;
        } else {
            $tab['test'] = false;
            $tab['pourcentage'] = 0;
            return $tab;
        }
    }
    public function checkCodePromo($code)
    {
        $promotion = $this->em->getRepository(Promotions::class)->findOneBy(array('code' => $code, 'etat' => 1));
        if ($promotion) {
            $tab['test'] = true;
            $tab['pourcentage'] = $promotion->getPourcentage();
            return $tab;
        } else {
            $tab['test'] = false;
            $tab['pourcentage'] = 0;
            return $tab;
        }
    }
    public function changerEtatPromotion($idPromotion, $etat)
    {
        $promotion = $this->em->getRepository(Promotions::class)->find($idPromotion);
        if ($promotion) {
            $promotion->setEtat(intval($etat));
            $this->em->persist($promotion);
            $this->em->flush();
        }
        return $promotion;
    }

    public function checkCodePromoParUser($idPromo, $idClient)
    {
        $promotion = $this->em->getRepository(Promotions::class)->find($idPromo);
        $tabLogPromo = $this->em->getRepository(LogPromotions::class)->findBy(array('idPromotion' => $idPromo, 'idClient' => $idClient));
        $nbreMaxParUser = $promotion->getNbreMaxParUser();
        $nbreLog = sizeOf($tabLogPromo);
        if ($nbreLog == $nbreMaxParUser) {
            return false;
        } else {
            return true;
        }
    }


    public function caclRemiseInPanier($panier, $strategie, $nbreApplique, $pourcentage)
    {
        $ttc = 0;
        $qte = 0;
        $tabPrices = array();
        $remise = 0;
        if ($nbreApplique == 1) {
            foreach ($panier as $item) {
                $ttc += $item['mnt'] * $item['qte'];
            }
            $remise = ($ttc / 100) * $pourcentage;
        } else {
            foreach ($panier as $item) {
                // $ttc += $item['mnt'] * $item['qte'];
                for ($i = 0; $i <  $item['qte']; $i++) {
                    array_push($tabPrices, $item['mnt']);
                }
                $qte += $item['qte'];
            }
            //    dd($tabPrices);
            rsort($tabPrices);
            //    dd($tabPrices);
            $x = intval($qte / $nbreApplique);
            $tabPricesPromo =   array_slice($tabPrices, -$x, $x, true);
            //    $tabPricesSansPromo=   array_slice($tabPrices,0,sizeof($tabPrices)-$x,true);
            foreach ($tabPricesPromo as $p) {
                $remise += ($p / 100) * $pourcentage;
            }
        }
        return $remise;
    }
}
