<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use \DateTime;
use \DateInterval;
use Doctrine\DBAL\Types\Types;

/**
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }
    public function searshNbconnxionSeId($value)
    {
        $typ = "Connexion";
        return $this->createQueryBuilder('k')
            ->select('COUNT(k)')
            ->innerJoin('k.idsjour', "sejour")
            ->innerJoin('k.type', "typeproduit")
            ->andWhere('k.idsjour = :val')
            ->andWhere('typeproduit.labeletype = :typ')
            ->setParameter('val', $value)
            ->setParameter('typ', $typ)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function searshNbconnxion()
    {
        $typ = "Connexion";
        return $this->createQueryBuilder('conx')
            ->select('COUNT(conx)')
            ->innerJoin('conx.type', "typeproduit")
            ->andWhere('typeproduit.labeletype = :typ')
            ->setParameter('typ', $typ)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function searshNbconnxionByDate($date)
    {
        $typ = "Connexion";
        return $this->createQueryBuilder('conx')
            ->select('COUNT(conx)')
            ->innerJoin('conx.type', "typeproduit")
            ->andWhere('typeproduit.labeletype = :typ')
            ->andWhere('conx.date >= :date')
            ->setParameter('date', $date)
            ->setParameter('typ', $typ)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function searshNBcnnxParSejour($word)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
    SELECT COUNT( DISTINCT p.id )
    FROM produit AS p,  sejour AS s, etablisment AS et, typeproduit AS ty
    WHERE p.type = ty.id
    AND p.idsjour = s.id
    AND s.id_etablisment = et.id
    AND ty.labeleType = "Connexion"
    AND et.typeetablisment LIKE :word';
        $stmt = $conn->executeQuery($sql, ['word' => '%' . $word . '%']);
        return $stmt->fetchOne();
    }
    public function searshNBcnnxParSejourByDate($word, $date)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
    SELECT COUNT( DISTINCT p.id )
    FROM produit AS p,  sejour AS s, etablisment AS et, typeproduit AS ty
    WHERE p.type = ty.id
    AND p.idsjour = s.id
    AND p.date >= :dateVal
    AND s.id_etablisment = et.id
    AND ty.labeleType = "Connexion"
    AND et.typeetablisment LIKE :word';
        $stmt = $conn->executeQuery($sql, ['word' => '%' . $word . '%', 'dateVal' => $date->format('Y-m-d H:i:s')]);
        return $stmt->fetchOne();
    }
    public function searshPanierMoyen($word)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
                SELECT AVG(  c.montantrth )
                FROM  sejour AS s, etablisment AS et, commande AS c, ref AS r
                WHERE c.id_sejour = s.id 
                AND s.id_etablisment= et.id
                AND et.typeetablisment LIKE :word';
        $stmt = $conn->executeQuery($sql, ['word' => '%' . $word . '%']);
        return $stmt->fetchOne();
    }
    public function searshPanierMoyenByDate($word, $dateTimeDebut, $dateTimeFin)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
  SELECT AVG(  c.montantrth )
  FROM   sejour AS s, etablisment AS et, commande AS c, ref AS r
  WHERE  c.date_create_commande BETWEEN :datedebut AND :datefin
  AND c.id_sejour = s.id 
  AND s.id_etablisment= et.id
  AND et.typeetablisment LIKE :word';
        $stmt = $conn->executeQuery($sql, ['word' => '%' . $word . '%', 'datedebut' => $dateTimeDebut->format('Y-m-d H:i:s'), 'datefin' => $dateTimeFin->format('Y-m-d H:i:s')]);
        return $stmt->fetchOne();
    }
    public function searshNbconnxionParSejour($value)
    {
        $typ = "Connexionn";
        return $this->createQueryBuilder('k')
            ->select('COUNT(k.id)')
            ->innerJoin('k.type', "typeproduit")
            ->andWhere('k.idsjour = :val')
            ->andWhere('typeproduit.labeletype = :typ')
            ->setParameter('val', $value)
            ->setParameter('typ', $typ)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function countProduitPartenaire($user, $typ)
    {
        return $this->createQueryBuilder('k')
            ->select('COUNT(k.id)')
            ->innerJoin('k.type', "typeproduit")
            ->innerJoin('k.idsjour', "Sejour")
            ->Where('Sejour.idPartenaire = :use')
            ->andWhere('typeproduit.labeletype = :typ')
            ->setParameter('use', $user)
            ->setParameter('typ', $typ)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function countProduitPartenairedate($date, $user, $typ)
    {
        return $this->createQueryBuilder('k')
            ->select('COUNT(k.id)')
            ->innerJoin('k.type', "typeproduit")
            ->innerJoin('k.idsjour', "Sejour")
            ->Where('Sejour.idPartenaire = :use')
            ->andWhere('typeproduit.labeletype = :typ')
            ->andWhere('Sejour.dateDebutSejour >=:val')
            ->setParameter('use', $user)
            ->setParameter('typ', $typ)
            ->setParameter('val', $date)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function SearchVersionproduit($id)
    {
        return $this->createQueryBuilder('k')
            ->select('COUNT(k.id)')
            ->Where('k.iduser = :use')
            ->setParameter('use', $id)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function  Nbpersone_commandepartypesejoursansdate($typ)
    {
        $result =  $this->createQueryBuilder('k')
            ->select('COUNT(DISTINCT k.iduser)')
            ->innerJoin('id', "comandeProduit.idProduit")
            ->innerJoin('k.iduser', "User")
            ->innerJoin('k.idsjour', "Sejour")
            ->innerJoin('Sejour.idEtablisment', "Etab")
            ->Where('Etab.typeetablisment LIKE :word')
            ->andWhere('typeproduit.labeletype = :typ')
            ->setParameter('word', '%' . $typ . '%')
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
    public function  Nbpersone_commandepartypesejourAvecdate($type, $date)
    {
        $result =  $this->createQueryBuilder('k')
            ->select('COUNT(DISTINCT k.iduser)')
            ->innerJoin('k', "ComandeProduit")
            ->innerJoin('k.iduser', "User")
            ->innerJoin('k.idsjour', "Sejour")
            ->innerJoin('Sejour.idEtablisment', "Etab")
            ->Where('Etab.typeetablisment LIKE :word')
            ->andWhere('typeproduit.labeletype = :typ')
            ->andWhere('ComandeProduit.date >=:val')
            ->setParameter('word', '%' . $type . '%')
            ->setParameter('val', $date)
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
    public function SreachNombreproduitspart($id)
    {
        $result =  $this->createQueryBuilder('k')
            ->select('k')
            ->innerJoin('k.idsjour', "Sejour")
            ->innerJoin('Sejour.idEtablisment', "Etab")
            ->Where('Etab.id  = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getResult();
        return $result;
    }
    public function getProduits($datefin, $datedebut)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('(s.date BETWEEN :datedebut AND :datefin)  ')
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->getQuery()
            ->getResult();
    }
}
