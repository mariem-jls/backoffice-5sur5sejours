<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="sms_notif")
 * @ORM\Entity(repositoryClass="App\Repository\SmsNotifRepository")
 */
class SmsNotif
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
 * @ORM\Column(type="text", nullable=true)
 */
    private $numbers;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $status;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;
    /**
     * @var \Sejour
     *
     * @ORM\ManyToOne(targetEntity="Sejour")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Id_sejour", referencedColumnName="id")
     * })
     */
    private $idSejour;
    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_creat", type="datetime", nullable=true)
     */
    private $dateCreat;
    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_send", type="datetime", nullable=true)
     */
    private $dateSend;


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getNumbers()
    {
        return $this->numbers;
    }

    /**
     * @param mixed $numbers
     */
    public function setNumbers($numbers): void
    {
        $this->numbers = $numbers;
    }


    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return \Sejour
     */
    public function getIdSejour()
    {
        return $this->idSejour;
    }

    /**
     * @param \Sejour $idSejour
     */
    public function setIdSejour( $idSejour): void
    {
        $this->idSejour = $idSejour;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateCreat(): ?\DateTime
    {
        return $this->dateCreat;
    }

    /**
     * @param \DateTime|null $dateCreat
     */
    public function setDateCreat(?\DateTime $dateCreat): void
    {
        $this->dateCreat = $dateCreat;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateSend(): ?\DateTime
    {
        return $this->dateSend;
    }

    /**
     * @param \DateTime|null $dateSend
     */
    public function setDateSend(?\DateTime $dateSend): void
    {
        $this->dateSend = $dateSend;
    }



}
