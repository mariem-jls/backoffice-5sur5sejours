<?php
/**
 * Created by PhpStorm.
 * User: Appsfact-02
 * Date: 01/07/2020
 * Time: 11:08
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="emailrelance")
 * @ORM\Entity(repositoryClass="App\Repository\EmailrelanceRepository")
 */
class Emailrelance
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="dateCreation",type="datetime", nullable=true)
     */
    private $dateCreation;

    /**
     * @ORM\Column(name="dateSend",type="datetime", nullable=true)
     */
    private $dateSend;
    /**
     * @ORM\Column(name="flagdepot",type="integer", nullable=true)
     */
    private $flagDepot;

    /**
     * @ORM\Column(name="sendTo", type="string", length=255, nullable=true)
     */
    private $sendTo;

    /**
     * @return mixed
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * @param mixed $dateCreation
     */
    public function setDateCreation($dateCreation): void
    {
        $this->dateCreation = $dateCreation;
    }

    /**
     * @return mixed
     */
    public function getDateSend()
    {
        return $this->dateSend;
    }

    /**
     * @param mixed $dateSend
     */
    public function setDateSend($dateSend): void
    {
        $this->dateSend = $dateSend;
    }

    /**
     * @return mixed
     */
    public function getFlagDepot()
    {
        return $this->flagDepot;
    }

    /**
     * @param mixed $FlagDepot
     */
    public function setFlagDepot($FlagDepot): void
    {
        $this->flagDepot = $FlagDepot;
    }

    /**
     * @return mixed
     */
    public function getSendTo()
    {
        return $this->sendTo;
    }

    /**
     * @param mixed $sendTo
     */
    public function setSendTo($sendTo): void
    {
        $this->sendTo = $sendTo;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }



}