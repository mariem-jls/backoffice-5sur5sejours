<?php

namespace App\Entity;

use App\Repository\ResetPasswordRequestRepository;
use Doctrine\ORM\Mapping as ORM;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use DateTimeInterface;

/**
 * ResetPasswordRequest
 *
 * @ORM\Table(name="reset_password_request", indexes={@ORM\Index(name="IDX_reset_password_user", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\ResetPasswordRequestRepository")
 */
class ResetPasswordRequest implements ResetPasswordRequestInterface
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(name="expires_at", type="datetime", nullable=false)
     */
    private $expiresAt;

    /**
     * @var string
     *
     * @ORM\Column(name="selector", type="string", length=20, nullable=false)
     */
    private $selector;

    /**
     * @var string
     *
     * @ORM\Column(name="hashed_token", type="string", length=100, nullable=false)
     */
    private $hashedToken;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(name="requested_at", type="datetime", nullable=false)
     */
    private $requestedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getExpiresAt(): DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(DateTimeInterface $expiresAt): self
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    public function getSelector(): string
    {
        return $this->selector;
    }

    public function setSelector(string $selector): self
    {
        $this->selector = $selector;
        return $this;
    }

    public function getHashedToken(): string
    {
        return $this->hashedToken;
    }

    public function setHashedToken(string $hashedToken): self
    {
        $this->hashedToken = $hashedToken;
        return $this;
    }

    public function getRequestedAt(): DateTimeInterface
    {
        return $this->requestedAt;
    }

    public function setRequestedAt(DateTimeInterface $requestedAt): self
    {
        $this->requestedAt = $requestedAt;
        return $this;
    }

    public function isExpired(): bool
    {
        return new \DateTime() > $this->getExpiresAt();
    }
}
