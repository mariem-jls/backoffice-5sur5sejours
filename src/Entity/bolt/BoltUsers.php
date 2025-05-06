<?php

namespace App\Entity\bolt;

use Doctrine\ORM\Mapping as ORM;

/**
 * BoltUsers
 *
 * @ORM\Table(name="bolt_users", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_5585B54F85E0677", columns={"username"}), @ORM\UniqueConstraint(name="UNIQ_5585B54E7927C74", columns={"email"})}, indexes={@ORM\Index(name="IDX_5585B5450F9BB84", columns={"enabled"})})
 * @ORM\Entity
 */
class BoltUsers
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
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=32, nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=128, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=254, nullable=false)
     */
    private $email;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="lastseen", type="datetime", nullable=true)
     */
    private $lastseen;

    /**
     * @var string|null
     *
     * @ORM\Column(name="lastip", type="string", length=45, nullable=true)
     */
    private $lastip;

    /**
     * @var string
     *
     * @ORM\Column(name="displayname", type="string", length=32, nullable=false)
     */
    private $displayname;

    /**
     * @var json
     *
     * @ORM\Column(name="stack", type="json", nullable=false)
     */
    private $stack;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false, options={"default"="1"})
     */
    private $enabled = '1';

    /**
     * @var string|null
     *
     * @ORM\Column(name="shadowpassword", type="string", length=128, nullable=true)
     */
    private $shadowpassword;

    /**
     * @var string|null
     *
     * @ORM\Column(name="shadowtoken", type="string", length=128, nullable=true)
     */
    private $shadowtoken;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="shadowvalidity", type="datetime", nullable=true)
     */
    private $shadowvalidity;

    /**
     * @var int
     *
     * @ORM\Column(name="failedlogins", type="integer", nullable=false)
     */
    private $failedlogins = '0';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="throttleduntil", type="datetime", nullable=true)
     */
    private $throttleduntil;

    /**
     * @var json
     *
     * @ORM\Column(name="roles", type="json", nullable=false)
     */
    private $roles;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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

    public function getLastip(): ?string
    {
        return $this->lastip;
    }

    public function setLastip(?string $lastip): self
    {
        $this->lastip = $lastip;

        return $this;
    }

    public function getDisplayname(): ?string
    {
        return $this->displayname;
    }

    public function setDisplayname(string $displayname): self
    {
        $this->displayname = $displayname;

        return $this;
    }

    public function getStack(): ?array
    {
        return $this->stack;
    }

    public function setStack(array $stack): self
    {
        $this->stack = $stack;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getShadowpassword(): ?string
    {
        return $this->shadowpassword;
    }

    public function setShadowpassword(?string $shadowpassword): self
    {
        $this->shadowpassword = $shadowpassword;

        return $this;
    }

    public function getShadowtoken(): ?string
    {
        return $this->shadowtoken;
    }

    public function setShadowtoken(?string $shadowtoken): self
    {
        $this->shadowtoken = $shadowtoken;

        return $this;
    }

    public function getShadowvalidity(): ?\DateTimeInterface
    {
        return $this->shadowvalidity;
    }

    public function setShadowvalidity(?\DateTimeInterface $shadowvalidity): self
    {
        $this->shadowvalidity = $shadowvalidity;

        return $this;
    }

    public function getFailedlogins(): ?int
    {
        return $this->failedlogins;
    }

    public function setFailedlogins(int $failedlogins): self
    {
        $this->failedlogins = $failedlogins;

        return $this;
    }

    public function getThrottleduntil(): ?\DateTimeInterface
    {
        return $this->throttleduntil;
    }

    public function setThrottleduntil(?\DateTimeInterface $throttleduntil): self
    {
        $this->throttleduntil = $throttleduntil;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }


}
