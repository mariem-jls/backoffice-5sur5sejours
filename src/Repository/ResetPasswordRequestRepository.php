<?php

namespace App\Repository;

use App\Entity\ResetPasswordRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use DateTimeInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\DBAL\Types\Types;

class ResetPasswordRequestRepository extends ServiceEntityRepository implements ResetPasswordRequestRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResetPasswordRequest::class);
    }

    /**
     * Creates a new reset password request and persists it.
     *
     * @param object $user
     * @param DateTimeInterface $expiresAt
     * @param string $selector
     * @param string $hashedToken
     *
     * @return ResetPasswordRequestInterface
     */
    public function createResetPasswordRequest(object $user, DateTimeInterface $expiresAt, string $selector, string $hashedToken): ResetPasswordRequestInterface
    {
        $resetPasswordRequest = new ResetPasswordRequest();
        $resetPasswordRequest->setUser($user);
        $resetPasswordRequest->setExpiresAt($expiresAt);
        $resetPasswordRequest->setSelector($selector);
        $resetPasswordRequest->setHashedToken($hashedToken);
        $resetPasswordRequest->setRequestedAt( new \DateTimeImmutable());

        $this->_em->persist($resetPasswordRequest);
        $this->_em->flush();

        return $resetPasswordRequest;
    }

    /**
     * Removes the reset password request.
     *
     * @param ResetPasswordRequestInterface $resetPasswordRequest
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function removeResetPasswordRequest(ResetPasswordRequestInterface $resetPasswordRequest): void
    {
        $this->_em->remove($resetPasswordRequest);
        $this->_em->flush();
    }

    /**
     * Finds a reset password request by selector.
     *
     * @param string $selector
     *
     * @return ResetPasswordRequestInterface|null
     */
    public function findResetPasswordRequest(string $selector): ?ResetPasswordRequestInterface
    {
        return $this->findOneBy([
            'selector' => $selector,
        ]);
    }

    /**
     * Removes expired reset password requests.
     *
     * @return int Number of deleted requests
     */
    public function removeExpiredResetPasswordRequests(): int
    {
        $now = new \DateTimeImmutable();

        $qb = $this->createQueryBuilder('r')
            ->delete()
            ->where('r.expiresAt <= :now')
            ->setParameter('now', $now);

        return $qb->getQuery()->execute();
    }

    /**
     * Get the user identifier.
     *
     * @param object $user
     *
     * @return string
     */
    public function getUserIdentifier(object $user): string
    {
        return $user->getUserIdentifier();
    }

    /**
     * Persist the reset password request.
     *
     * @param ResetPasswordRequestInterface $resetPasswordRequest
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function persistResetPasswordRequest(ResetPasswordRequestInterface $resetPasswordRequest): void
    {
        $this->_em->persist($resetPasswordRequest);
        $this->_em->flush();
    }

    /**
     * Get the most recent non-expired request date for a user.
     *
     * @param object $user
     *
     * @return \DateTimeInterface|null
     */
    public function getMostRecentNonExpiredRequestDate(object $user): ?\DateTimeInterface
    {
        $qb = $this->createQueryBuilder('r')
            ->select('r.requestedAt')
            ->where('r.user = :user')
            ->andWhere('r.expiresAt > :now')
            ->setParameter('user', $user)
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('r.requestedAt', 'DESC')
            ->setMaxResults(1);

        $result = $qb->getQuery()->getOneOrNullResult();

        return $result ? $result['requestedAt'] : null;
    }
}
