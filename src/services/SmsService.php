<?php

namespace App\Service;

use App\Entity\SmsNotif;
use Unirest;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ParentSejour;
use App\Entity\Sejour;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Message\SendEmail;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class SmsService
{
    private $em;
    private $params;
    private MessageBusInterface $messageBus;
    public function __construct(ManagerRegistry $em, ParameterBagInterface $params , MessageBusInterface $messageBus)
    {
        $this->em = $em;
        $this->params = $params;
        $this->messageBus = $messageBus;
    }
    function testSmsSend($numberListe)
    {
        $date = new \Datetime();
        $Milliseconde = $date->format('u');
        $url =  $this->params->get('OpteloUrl');;
        $params = [
            "ClientLogin" => $this->params->get('OpteloClientLogin'),
            "ClientKey" => $this->params->get('OpteloClientKey'),
            "IdSend" => $Milliseconde,
            "Name" => 'Notification Sejour',
            "Expeditor" => '5sur5sejour',
            "Object" => "service",
            "Message" => "Cher Parent, connectez-vous à votre compte 5sur5sejour ! L'accompagnateur du séjour de votre enfant vient tout juste de déposer de nouvelles images et/ou de nouveaux messages!",
            "NumberList" => $numberListe
        ];
        $response = Unirest\Request::post($url, $headers = array(), $params);
        var_dump($response);
        return $response;
    }
    function SmsPromoSend($numberListe,$codeSej,$codePromo)
    {
        $date = new \Datetime();
        $Milliseconde = $date->format('u');
        $url =  $this->params->get('OpteloUrl');;
        $params = [
            "ClientLogin" => $this->params->get('OpteloClientLogin'),
            "ClientKey" => $this->params->get('OpteloClientKey'),
            "IdSend" => $Milliseconde,
            "Name" => 'Notification Sejour',
            "Expeditor" => '5sur5sejour',
            "Object" => "service",
            "Message" => "Cher Parent, connectez-vous à votre compte 5sur5sejour ! L'accompagnateur du séjour de votre enfant vient tout juste de déposer de nouvelles images et/ou de nouveaux messages!",
            "NumberList" => $numberListe
        ];
        $response = Unirest\Request::post($url, $headers = array(), $params);
        var_dump($response);
        return $response;
    }
   
    function  saveSmsNotifPromo($idSejour, $type,$reduction,$codSej)
    {
        $ListeUsersANotifier = $this->em->getRepository(ParentSejour::class)->findBy(["idSejour" => $idSejour, "smsnotif" => 1]);
        $sejour = $this->em->getRepository(Sejour::class)->find($idSejour);
        $numbers = array();
        $UserNotif = array();
        foreach ($ListeUsersANotifier as $userSej) {
            if ($userSej->getIdParent() != null) {
                if ($userSej->getIdParent()->getNummobile()) {
                    $number = $userSej->getIdParent()->getNummobile();
                    $UserNotif = $this->em->getRepository(SmsNotif::class)->findByNumber($idSejour, $number);
                    if (sizeof($UserNotif) == 0) {
                        array_push($numbers, $number);
                    }
                }
            }
        }
        if (sizeof($numbers) != 0) {
            $stringNumbers = implode(';', $numbers);
            $SmsNotif = new SmsNotif();
            $SmsNotif->setStatus("New");
            $SmsNotif->setNumbers($stringNumbers);
            $SmsNotif->setType($type);
            $SmsNotif->setIdSejour($sejour);
            $SmsNotif->setDateCreat(new \DateTime());
            $this->em->getManager()->persist($SmsNotif);
            $this->em->getManager()->flush();
            return "sms saved";
        } else {
            return "sms number empty";
        }
    }
   
    function saveSmsNotif($idSejour, $type)
    {
        $ListeUsersANotifier = $this->em->getRepository(ParentSejour::class)->findBy(["idSejour" => $idSejour, "smsnotif" => 1]);
        $sejour = $this->em->getRepository(Sejour::class)->find($idSejour);
        $numbers = array();
        $UserNotif = array();
        foreach ($ListeUsersANotifier as $userSej) {
            if ($userSej->getIdParent() != null) {
                if ($userSej->getIdParent()->getNummobile()) {
                    $number = $userSej->getIdParent()->getNummobile();
                    $UserNotif = $this->em->getRepository(SmsNotif::class)->findByNumber($idSejour, $number);
                    if (sizeof($UserNotif) == 0) {
                        array_push($numbers, $number);
                    }
                }
            }
        }
        if (sizeof($numbers) != 0) {
            $stringNumbers = implode(';', $numbers);
            $SmsNotif = new SmsNotif();
            $SmsNotif->setStatus("New");
            $SmsNotif->setNumbers($stringNumbers);
            $SmsNotif->setType($type);
            $SmsNotif->setIdSejour($sejour);
            $SmsNotif->setDateCreat(new \DateTime());
            $this->em->getManager()->persist($SmsNotif);
            $this->em->getManager()->flush();
            return "sms saved";
        } else {
            return "sms number empty";
        }
    }
    function SmsSendNotifAlbum($idSejour)
    {
        $date = new \Datetime();
        $numbers = array();
        $UserNotif = array();
        $finaluniquenumber = array();
        $Milliseconde = $date->format('u');
        $url =  $this->params->get('OpteloUrl');
        $ListeUsersANotifier = $this->em->getRepository(ParentSejour::class)->findBy(["idSejour" => $idSejour, "smsnotif" => 1]);
        $sejour = $this->em->getRepository(Sejour::class)->find(["id"=>$idSejour]);
        foreach ($ListeUsersANotifier as $userSej) {
            if ($userSej->getIdParent() != null) {
                if ($userSej->getIdParent()->getNummobile()) {
                    $number = $userSej->getIdParent()->getNummobile();
                    $UserNotif = $this->em->getRepository(SmsNotif::class)->findByNumber($idSejour, $number);
                    if (sizeof($UserNotif) == 0) {
                        array_push($numbers, $number);
                        
                    }
                }
            }
        }
        if (sizeof($numbers) != 0) {
            $stringNumbers = implode(';', $numbers);
            $allnumbers = explode(";", $stringNumbers);
            $finalarray = array_unique($allnumbers);
            foreach ($finalarray as $uniqueNumber) {
                if ((strlen($uniqueNumber) == 10) && (is_numeric($uniqueNumber)) && ($uniqueNumber[0] == 0)) {
                    array_push($finaluniquenumber, (string)'33' . substr($uniqueNumber, 1));
                }
            }
            $count = count($finaluniquenumber);
            $finalNumbers = implode(';', $finaluniquenumber);
        $params = [
            "ClientLogin" => $this->params->get('OpteloClientLogin'),
            "ClientKey" => $this->params->get('OpteloClientKey'),
            "IdSend" => $Milliseconde,
            "Name" => 'Notification Sejour',
            "Expeditor" => '5sur5sejour',
            "Object" => "service",
            "Message" => "Cher Parent, nous sommes ravis de vous annoncer que l'album photo du séjour " . $sejour->getCodesejour()." de votre enfant est maintenant disponible à l'achat sur notre plateforme https://5sur5sejour.com/Parent/login ",
            "NumberList" => $finalNumbers
        ];
   $response = Unirest\Request::post($url, $headers = array(), $params);
        $message = new SendEmail("yousra.tlich@gmail.com", 'Le Nombre de sms envoyés'.$count ,"", []);
        $this->messageBus->dispatch($message);
        return $response;
    
    }
}
}
