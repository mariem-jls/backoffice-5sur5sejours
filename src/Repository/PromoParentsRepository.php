<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\PromoParents;

/**
 * @method PromoParents|null find($id, $lockMode = null, $lockVersion = null)
 * @method PromoParents|null findOneBy(array $criteria, array $orderBy = null)
 * @method PromoParents[]    findAll()
 * @method PromoParents[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromoParentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PromoParents::class);
    }

    
}