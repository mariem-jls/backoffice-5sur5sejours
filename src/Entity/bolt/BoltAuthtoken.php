<?php

namespace App\Entity\bolt;

use Doctrine\ORM\Mapping as ORM;

/**
 * BoltAuthtoken
 *
 * @ORM\Table(name="bolt_authtoken", indexes={@ORM\Index(name="IDX_740AC52FA76ED395", columns={"user_id"})})
 * @ORM\Entity
 */
class BoltAuthtoken
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
     * @var int|null
     *
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    private $userId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="username", type="string", length=32, nullable=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=128, nullable=false)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=128, nullable=false)
     */
    private $salt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="lastseen", type="datetime", nullable=true)
     */
    private $lastseen;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ip", type="string", length=45, nullable=true)
     */
    private $ip;

    /**
     * @var string|null
     *
     * @ORM\Column(name="useragent", type="string", length=128, nullable=true)
     */
    private $useragent;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="validity", type="datetime", nullable=true)
     */
    private $validity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    public function getLastseen(): ?\DateTimeInterface
    {
        return $this->lastseen;
    }

    public function setLastseen(?\DateTimeInterface $lastseen): self
    {
        $this->lastseen = $lastseen;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getUseragent(): ?string
    {
        return $this->useragent;
    }

    public function setUseragent(?string $useragent): self
    {
        $this->useragent = $useragent;

        return $this;
    }

    public function getValidity(): ?\DateTimeInterface
    {
        return $this->validity;
    }

    public function setValidity(?\DateTimeInterface $validity): self
    {
        $this->validity = $validity;

        return $this;
    }


}
