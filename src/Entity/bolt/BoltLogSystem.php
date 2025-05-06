<?php

namespace App\Entity\bolt;

use Doctrine\ORM\Mapping as ORM;

/**
 * BoltLogSystem
 *
 * @ORM\Table(name="bolt_log_system", indexes={@ORM\Index(name="IDX_805C16D99AEACC13", columns={"level"}), @ORM\Index(name="IDX_805C16D975DAD987", columns={"ownerid"}), @ORM\Index(name="IDX_805C16D9AA9E377A", columns={"date"}), @ORM\Index(name="IDX_805C16D9E25D857E", columns={"context"})})
 * @ORM\Entity
 */
class BoltLogSystem
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="level", type="integer", nullable=false)
     */
    private $level;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=1024, nullable=false)
     */
    private $message;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ownerid", type="integer", nullable=true)
     */
    private $ownerid;

    /**
     * @var string
     *
     * @ORM\Column(name="requesturi", type="string", length=128, nullable=false)
     */
    private $requesturi;

    /**
     * @var string
     *
     * @ORM\Column(name="route", type="string", length=128, nullable=false)
     */
    private $route;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=45, nullable=false)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="context", type="string", length=32, nullable=false)
     */
    private $context;

    /**
     * @var json
     *
     * @ORM\Column(name="source", type="json", nullable=false)
     */
    private $source;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getOwnerid(): ?int
    {
        return $this->ownerid;
    }

    public function setOwnerid(?int $ownerid): self
    {
        $this->ownerid = $ownerid;

        return $this;
    }

    public function getRequesturi(): ?string
    {
        return $this->requesturi;
    }

    public function setRequesturi(string $requesturi): self
    {
        $this->requesturi = $requesturi;

        return $this;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function setRoute(string $route): self
    {
        $this->route = $route;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function setContext(string $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function getSource(): ?array
    {
        return $this->source;
    }

    public function setSource(array $source): self
    {
        $this->source = $source;

        return $this;
    }


}
