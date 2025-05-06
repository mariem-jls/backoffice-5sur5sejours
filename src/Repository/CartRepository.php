<?php

namespace App\Repository;

use App\Entity\Cart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method Cart|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cart|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cart[]    findAll()
 * @method Cart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }
    public function searshTypeCarteecole()
    {
        $word = "ECOLES/AUTRES";
        $result = $this->createQueryBuilder('x')
            ->select('SUM(x.nbpartenaire)')
            ->innerJoin('x.idsejour', "sejour")
            ->innerJoin('sejour.idEtablisment', "Etablisment")
            ->Where('Etablisment.typeetablisment LIKE :word')
            ->setParameter('word', '%' . $word . '%')
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getSingleScalarResult();
        if ($result == null) {
            return 0;
        } else {
            return $result;
        }
    }
    public function searshTypeCarteecoleByDate($datedebut, $datefin)
    {
        $word = "ECOLES/AUTRES";
        $result = $this->createQueryBuilder('x')
            ->select('SUM(x.nbpartenaire)')
            ->innerJoin('x.idsejour', "sejour")
            ->innerJoin('sejour.idEtablisment', "Etablisment")
            ->Where('Etablisment.typeetablisment LIKE :word')
            ->andWhere('(x.date BETWEEN :datedebut AND :datefin) ')
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('word', '%' . $word . '%')
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getSingleScalarResult();
        if ($result == null) {
            return 0;
        } else {
            return $result;
        }
    }
    public function searshTypeCartcse()
    {
        $word = "CSE";
        $result = $this->createQueryBuilder('y')
            ->select('SUM(y.nbpartenaire)')
            ->innerJoin('y.idsejour', "sejour")
            ->innerJoin('sejour.idEtablisment', "Etablisment")
            ->Where('Etablisment.typeetablisment LIKE :word')
            ->setParameter('word', '%' . $word . '%')
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getSingleScalarResult();
        if ($result == null) {
            return 0;
        } else {
            return $result;
        }
    }
    public function searshTypeCartcseByDate($date)
    {
        $word = "CSE";
        $result = $this->createQueryBuilder('y')
            ->select('SUM(y.nbpartenaire)')
            ->innerJoin('y.idsejour', "sejour")
            ->innerJoin('sejour.idEtablisment', "Etablisment")
            ->Where('Etablisment.typeetablisment LIKE :word')
            ->AndWhere('y.date >= :val')
            ->setParameter('val', $date)
            ->setParameter('word', '%' . $word . '%')
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getSingleScalarResult();
        if ($result == null) {
            return 0;
        } else {
            return $result;
        }
    }
    public function searshTypeCartpart()
    {
        $word = "PARTENAIRES/VOYAGISTES";
        $result = $this->createQueryBuilder('z')
            ->select('SUM(z.nbpartenaire)')
            ->innerJoin('z.idsejour', "sejour")
            ->innerJoin('sejour.idEtablisment', "Etablisment")
            ->Where('Etablisment.typeetablisment LIKE :word')
            ->setParameter('word', '%' . $word . '%')
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getSingleScalarResult();
        if ($result == null) {
            return 0;
        } else {
            return $result;
        }
    }
    public function searshTypeCartpartByDate($date)
    {
        $word = "PARTENAIRES/VOYAGISTES";
        $result = $this->createQueryBuilder('z')
            ->select('SUM(z.nbpartenaire)')
            ->innerJoin('z.idsejour', "sejour")
            ->innerJoin('sejour.idEtablisment', "Etablisment")
            ->Where('Etablisment.typeetablisment LIKE :word')
            ->AndWhere('z.date >= :val')
            ->setParameter('val', $date)
            ->setParameter('word', '%' . $word . '%')
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getSingleScalarResult();
        if ($result == null) {
            return 0;
        } else {
            return $result;
        }
    }
    public function nbconsultationCartpart($user)
    {
        $result = $this->createQueryBuilder('z')
            ->select('SUM(z.nbpartenaire)')
            ->innerJoin('z.idsejour', "Sejour")
            ->Where('Sejour.idPartenaire = :val1')
            ->setParameter('val1', $user)
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getSingleScalarResult();
        if ($result == null) {
            return 0;
        } else {
            return $result;
        }
    }
    public function nbconsultationpartByDate($datedebut, $datefin, $user)
    {
        $result = $this->createQueryBuilder('z')
            ->select('SUM(z.nbpartenaire)')
            ->innerJoin('z.idsejour', "Sejour")
            ->Where('Sejour.idPartenaire = :val1')
            ->andWhere('(Sejour.dateDebutSejour BETWEEN :datedebut AND :datefin) OR (Sejour.dateFinSejour BETWEEN :datedebut AND :datefin) OR (Sejour.dateDebutSejour <= :datedebut AND Sejour.dateFinSejour >= :datefin) ')
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('val1', $user)
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getSingleScalarResult();
        if ($result == null) {
            return 0;
        } else {
            return $result;
        }
    }
}
