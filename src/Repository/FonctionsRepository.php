<?php

namespace App\Repository;
use App\Entity\Fonctions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/**
 * @method Fonctions|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fonctions|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fonctions[]    findAll()
 * @method Fonctions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FonctionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fonctions::class);
    }
    // /**
    //  * @return Fonctions[] Returns an array of Fonctions objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
    /*
    public function findOneBySomeField($value): ?Fonctions
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
