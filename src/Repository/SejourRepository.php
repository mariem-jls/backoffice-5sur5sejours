<?php


namespace App\Repository;

use App\Entity\Sejour;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use \DateTime;
use \DateInterval;
use Doctrine\DBAL\Types\Types;

/**
 * @method Sejour|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sejour|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sejour[]    findAll()
 * @method Sejour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SejourRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sejour::class);
    }
    public function searshTypeEtab($value)
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.idEtablisment', "etablissement")
            ->innerJoin('s.statut', "ref")
            ->andWhere('etablissement.typeetablisment = :val')
            ->andWhere('ref.id != :val2')
            ->setParameter('val', $value)
            ->setParameter('val2', 14)
            ->getQuery()
            ->getResult();
    }
    public function searshTypeEtabByDate($value, $datedebut, $datefin)
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.idEtablisment', "etablissement")
            ->innerJoin('s.statut', "ref")
            ->andWhere('etablissement.typeetablisment = :val')
            ->andWhere('(s.dateDebutSejour BETWEEN :datedebut AND :datefin) OR (s.dateFinSejour BETWEEN :datedebut AND :datefin) OR (s.dateDebutSejour <= :datedebut AND s.dateFinSejour >= :datefin) ')
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->andWhere('ref.id <> :val3')
            ->setParameter('val', $value)
            ->setParameter('val3', 14)
            ->getQuery()
            ->getResult();
    }
    public function searshTypeEtabRep($value)
    {
        $typ = "Active";
        return $this->createQueryBuilder('s')
            ->innerJoin('s.idEtablisment', "etablissement")
            ->innerJoin('s.statut', "Ref")
            ->andWhere('etablissement.typeetablisment = :val')
            ->andWhere('Ref.libiller = :typ')
            ->andWhere('s.etatAcompAlbum = true OR s.etatAdresseCarte = true ')
            ->setParameter('val', $value)
            ->setParameter('typ', $typ)
            ->getQuery()
            ->getResult();
    }
    public function searshTypeEtabRepByDate($value, $datedebut, $datefin)
    {
        $typ = "Active";
        return $this->createQueryBuilder('s')
            ->innerJoin('s.idEtablisment', "etablissement")
            ->innerJoin('s.statut', "Ref")
            ->andWhere('etablissement.typeetablisment = :val')
            ->andWhere('(s.dateDebutSejour BETWEEN :datedebut AND :datefin) OR (s.dateFinSejour BETWEEN :datedebut AND :datefin) OR (s.dateDebutSejour <= :datedebut AND s.dateFinSejour >= :datefin) ')
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->andWhere('Ref.libiller = :typ')
            ->andWhere('s.etatAcompAlbum = true OR s.etatAdresseCarte = true ')
            ->setParameter('val', $value)
            ->setParameter('typ', $typ)
            ->getQuery()
            ->getResult();
    }
    public function searshTypeAndeDate($datedebut, $datefin)
    {
        $typ = "Active";
        return $this->createQueryBuilder('s')
            ->innerJoin('s.statut', "Ref")
            ->innerJoin('s.idEtablisment', "Etablisment")
            ->andWhere('s.dateDebutSejour BETWEEN :datedebut AND :datefin')
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->andWhere('Ref.libiller = :typ')
            ->setParameter('typ', $typ)
            ->getQuery()
            ->getResult();
    }
    public function searshTypeAndesansDate()
    {
        $typ = "Active";
        return $this->createQueryBuilder('s')
            ->innerJoin('s.statut', "Ref")
            ->innerJoin('s.idEtablisment', "Etablisment")
            ->andWhere('Ref.libiller = :typ')
            ->setParameter('typ', $typ)
            ->getQuery()
            ->getResult();
    }
    public function searshTypeDateanduser($value, $user)
    {
        $typ = "Active";
        return $this->createQueryBuilder('s')
            ->innerJoin('s.statut', "Ref")
            ->andWhere('s.dateDebutSejour >= :val')
            ->andWhere('Ref.libiller = :typ')
            ->andWhere('s.idPartenaire = :use')
            ->setParameter('val', $value)
            ->setParameter('typ', $typ)
            ->setParameter('use', $user)
            ->getQuery()
            ->getResult();
    }
    //liste des sejour par partenaire date et selon type
    public function searshTypeDateanduserSejour($datedebut, $datefin, $user, $typ)
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.statut', "Ref")
            ->andWhere('Ref.libiller = :typ')
            ->andWhere('s.idPartenaire = :use')
            ->andWhere('(s.dateDebutSejour BETWEEN :datedebut AND :datefin) OR (s.dateFinSejour BETWEEN :datedebut AND :datefin) OR (s.dateDebutSejour <= :datedebut AND s.dateFinSejour >= :datefin) ')
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('typ', $typ)
            ->setParameter('use', $user)
            ->getQuery()
            ->getResult();
    }
    public function searshDate($datedebut, $datefin)
    {
        $typ = "Active";
        return $this->createQueryBuilder('s')
            ->innerJoin('s.statut', "Ref")
            ->andWhere('Ref.id <> :val2')
            ->andWhere('(s.dateDebutSejour BETWEEN :datedebut AND :datefin) OR (s.dateFinSejour BETWEEN :datedebut AND :datefin) OR (s.dateDebutSejour <= :datedebut AND s.dateFinSejour >= :datefin) ')
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('val2', 14)
            ->getQuery()
            ->getResult();
    }
    public function searshAllSejour()
    {
        $typ = "Active";
        return $this->createQueryBuilder('s')
            ->innerJoin('s.statut', "Ref")
            //->andWhere('s.dateDebutSejour >= :val')
            ->andWhere('Ref.id <> :val2')
            ->setParameter('val2', 14)
            //->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }
    public function searshNbperson($value)
    {
        return $this->createQueryBuilder('s')
            ->select('s.nbenfan')
            ->andWhere('s.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getSingleScalarResult();
    }
    /*->andWhere('ps.payment =1')
     */
    // /**
    //  * @return Article[] Returns an array of Article objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function listAcco($du, $au, $code, $ville)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.dateDebutSejour >= :du')
            ->andWhere('s.dateFinSejour <= :au')
            ->andWhere('s.ville = :ville')
            ->andWhere('s.codePostal= :code')
            ->setParameter('du', $du)
            ->setParameter('au', $au)
            ->setParameter('code', $code)
            ->setParameter('ville', $ville)
            ->getQuery()
            ->getResult();
    }
    public function USeOldCodes($CodeSejour)
    {
        return $this->createQueryBuilder('s')
            ->Where('s.codeSejour like :code')
            ->setParameter('code', '__' . $CodeSejour)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function NombreofsejourParten($id)
    {
        //        $typ = "archivé";
        return $this->createQueryBuilder('s')
            ->innerJoin('s.statut', "Ref")
            ->andWhere('Ref.libiller <> :typ')
            ->andWhere('s.idPartenaire = :use')
            ->setParameter('typ', 'supprimé')
            ->setParameter('use', $id)
            ->orderBy('s.dateDebutSejour', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function findAllGreaterThanPrice($price): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT p
            FROM App\Entity\Product p
            WHERE p.price > :price
            ORDER BY p.price ASC'
        )->setParameter('price', $price);
        // returns an array of Product objects
        return $query->getResult();
    }
    //select count(*) from sejour S , sejour_attachment A where S.id=A.`id_sejour` HAVING count(A.id)>1
    public function findSejourEncourDetail()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s WHERE :now>=s.dateDebutSejour and :now<=s.dateFinSejour '
        )->setParameter('now', new \DateTime(), Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncour()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Sejour s WHERE :now>=s.dateDebutSejour and :now<=s.dateFinSejour '
        )->setParameter('now', new \DateTime('midnight'), Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourMonthEncour($datefin, $datedebut)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s WHERE ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.statut<>:statut'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', 39);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourParPart($part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Sejour s WHERE :now>=s.dateDebutSejour and :now<=s.dateFinSejour and s.idEtablisment=:etab '
        )->setParameter('now', new \DateTime('midnight'), Types::DATETIME_MUTABLE)
            ->setParameter('etab', $part);;
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourFree()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Sejour s WHERE :now>=s.dateDebutSejour and :now<=s.dateFinSejour and s.codeSejour LIKE \'_F%\' '
        )->setParameter('now', new \DateTime('midnight'), Types::DATETIME_MUTABLE);;
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourMonthFree($datefin, $datedebut)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Sejour s WHERE ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.statut<>:statut and s.codeSejour LIKE \'_F%\' '
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', 39);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourMonthFreeEcole($datefin, $datedebut)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Sejour s WHERE ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.statut<>:statut and s.codeSejour LIKE \'EF%\' '
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', 39);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourMonthFreePartenaire($datefin, $datedebut)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Sejour s WHERE ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.statut<>:statut and s.codeSejour LIKE \'PF%\' '
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', 39);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourFreeParPart_PF($datefin, $datedebut, $part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Sejour s WHERE s.statut<>:statut and ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.codeSejour LIKE \'PF%\' and  s.idEtablisment=:etab '
        )->setParameter('etab', $part)
            ->setParameter('statut', 39)
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourFreeParPart_EF($datefin, $datedebut, $part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Sejour s WHERE s.statut<>:statut and ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.codeSejour LIKE \'EF%\' and  s.idEtablisment=:etab '
        )->setParameter('etab', $part)
            ->setParameter('statut', 39)
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourFreeParPart($datefin, $datedebut, $part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Sejour s WHERE s.statut<>:statut and ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.codeSejour LIKE \'_F%\' and  s.idEtablisment=:etab '
        )->setParameter('etab', $part)
            ->setParameter('statut', 39)
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourFreeParTypePart($datefin, $datedebut, $typepart)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Sejour s, App\Entity\Etablisment e WHERE ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.codeSejour LIKE \'_F%\'  AND (s.idEtablisment=e.id)and (e.typeetablisment=:type) and s.statut<>:statut '
        )->setParameter('type', $typepart)
            ->setParameter('statut', 39)
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourFreeParTypePartEF($datefin, $datedebut, $typepart)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Sejour s, App\Entity\Etablisment e WHERE ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.codeSejour LIKE \'EF%\'  AND (s.idEtablisment=e.id)and (e.typeetablisment=:type) and s.statut<>:statut '
        )->setParameter('type', $typepart)
            ->setParameter('statut', 39)
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourFreeParTypePartPF($datefin, $datedebut, $typepart)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Sejour s, App\Entity\Etablisment e WHERE ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.codeSejour LIKE \'PF%\'  AND (s.idEtablisment=e.id)and (e.typeetablisment=:type) and s.statut<>:statut '
        )->setParameter('type', $typepart)
            ->setParameter('statut', 39)
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourPayant()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Sejour s WHERE :now>=s.dateDebutSejour and :now<=s.dateFinSejour  and s.codeSejour LIKE \'_P%\' '
        )->setParameter('now', new \DateTime('midnight'), Types::DATETIME_MUTABLE);;
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourMonthPayant($datefin, $datedebut)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Sejour s WHERE ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.statut<>:statut  and s.codeSejour LIKE \'_P%\' '
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', 39);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourPayantParPart($datefin, $datedebut, $part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Sejour s WHERE s.statut<>:statut and ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin))  and s.codeSejour LIKE \'_P%\' and  s.idEtablisment=:etab '
        )->setParameter('etab', $part)
            ->setParameter('statut', 39)
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourPayantParTypePart($datefin, $datedebut, $typepart)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Sejour s, App\Entity\Etablisment e WHERE ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin))  and s.codeSejour LIKE \'_P%\'  AND (s.idEtablisment=e.id)and (e.typeetablisment=:type) and s.statut<>:statut '
        )->setParameter('type', $typepart)
            ->setParameter('statut', 39)
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourHaveAttach()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Sejour s where :now>=s.dateDebutSejour and :now<=s.dateFinSejour and s.statut=:statut'
        )->setParameter('now', new \DateTime('midnight'), Types::DATETIME_MUTABLE)->setParameter('statut', 5);;
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourMonthHaveAttach($datefin, $datedebut)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s where ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.statut in (:statut)'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)->setParameter('statut', [5, 14]);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourMonthHaveAttachParPart($datefin, $datedebut, $part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s where ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.statut in (:statut) and s.idEtablisment=:etab'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', [5, 14])
            ->setParameter('etab', $part);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejour($array)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s where s.id in (:array) '
        )->setParameter('array', $array);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourMonthHaveAttachTypePart($datefin, $datedebut, $type)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s,App\Entity\Etablisment e where ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.statut in (:statut) AND (s.idEtablisment=e.id)and (e.typeetablisment=:type)'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', [5, 14])
            ->setParameter('type', $type);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourMonthHaveAttachFree($datefin, $datedebut)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s where ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.statut in (:statut)  and s.codeSejour LIKE \'_F%\' '
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)->setParameter('statut', [5, 14]);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourMonthHaveAttachFree_ECOLE($datefin, $datedebut)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s where ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.statut in (:statut)  and s.codeSejour LIKE \'EF%\' '
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)->setParameter('statut', [5, 14]);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourMonthHaveAttachFree_PARTENAIRE($datefin, $datedebut)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s where ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.statut in (:statut)  and s.codeSejour LIKE \'PF%\' '
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)->setParameter('statut', [5, 14]);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourMonthHaveAttachFreeParPart($datefin, $datedebut, $part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s where ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.statut in (:statut) and s.idEtablisment=:etab and s.codeSejour LIKE \'_F%\' '
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', [5, 14])
            ->setParameter('etab', $part);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourMonthHaveAttachFreeParPartEF($datefin, $datedebut, $part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s where ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.statut in (:statut) and s.idEtablisment=:etab and s.codeSejour LIKE \'EF%\' '
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', [5, 14])
            ->setParameter('etab', $part);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourMonthHaveAttachFreeParPartPF($datefin, $datedebut, $part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s where ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.statut in (:statut) and s.idEtablisment=:etab and s.codeSejour LIKE \'PF%\' '
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', [5, 14])
            ->setParameter('etab', $part);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourMonthHaveAttachFreeTypePart($datefin, $datedebut, $type)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s,App\Entity\Etablisment e where ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.statut in (:statut) AND (s.idEtablisment=e.id)and (e.typeetablisment=:type) and s.codeSejour LIKE \'_F%\''
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', [5, 14])
            ->setParameter('type', $type);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourMonthHaveAttachFreeTypePartEF($datefin, $datedebut, $type)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s,App\Entity\Etablisment e where ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.statut in (:statut) AND (s.idEtablisment=e.id)and (e.typeetablisment=:type) and s.codeSejour LIKE \'EF%\''
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', [5, 14])
            ->setParameter('type', $type);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourMonthHaveAttachFreeTypePartPF($datefin, $datedebut, $type)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s,App\Entity\Etablisment e where ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.statut in (:statut) AND (s.idEtablisment=e.id)and (e.typeetablisment=:type) and s.codeSejour LIKE \'PF%\''
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', [5, 14])
            ->setParameter('type', $type);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourMonthHaveAttachPay($datefin, $datedebut)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s where ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.statut in (:statut) and s.codeSejour LIKE \'_P%\''
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)->setParameter('statut', [5, 14]);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourMonthHaveAttachPayParPart($datefin, $datedebut, $part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s where ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.statut in (:statut) and s.idEtablisment=:etab and s.codeSejour LIKE \'_P%\''
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', [5, 14])
            ->setParameter('etab', $part);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourMonthHaveAttachPayTypePart($datefin, $datedebut, $type)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s,App\Entity\Etablisment e where ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.statut in (:statut) AND (s.idEtablisment=e.id)and (e.typeetablisment=:type) and s.codeSejour LIKE \'_P%\''
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', [5, 14])
            ->setParameter('type', $type);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourHaveAttachByPart($part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Sejour s where :now>=s.dateDebutSejour and :now<=s.dateFinSejour and s.statut=:statut and s.idEtablisment=:etab'
        )->setParameter('now', new \DateTime('midnight'), Types::DATETIME_MUTABLE)->setParameter('statut', 5)
            ->setParameter(':etab', $part);;
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourFreeHaveAttach()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Sejour s where :now>=s.dateDebutSejour and :now<=s.dateFinSejour and s.codeSejour LIKE \'_F%\' and s.statut=:statut'
        )->setParameter('now', new \DateTime('midnight'), Types::DATETIME_MUTABLE)
            ->setParameter('statut', 5);;
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourFreeHaveAttachByPart($part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Sejour s where :now>=s.dateDebutSejour and :now<=s.dateFinSejour and s.codeSejour LIKE \'_F%\' and s.statut=:statut and s.idEtablisment=:etab'
        )->setParameter('now', new \DateTime('midnight'), Types::DATETIME_MUTABLE)
            ->setParameter('statut', 5)
            ->setParameter(':etab', $part);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourListEncourFree()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s WHERE :now>=s.dateDebutSejour and :now<=s.dateFinSejour and s.codeSejour LIKE \'_F%\' '
        )->setParameter('now', new \DateTime('midnight'), Types::DATETIME_MUTABLE);;
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourListEncourFreeParPart($part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s WHERE :now>=s.dateDebutSejour and :now<=s.dateFinSejour and s.codeSejour LIKE \'_F%\' and s.idEtablisment=:etab'
        )->setParameter('now', new \DateTime('midnight'), Types::DATETIME_MUTABLE)
            ->setParameter(':etab', $part);;
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourListEncourPay()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s WHERE :now>=s.dateDebutSejour and :now<=s.dateFinSejour  and s.codeSejour LIKE \'_P%\' '
        )->setParameter('now', new \DateTime('midnight'), Types::DATETIME_MUTABLE);;
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourListEncourPayParPart($part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s WHERE :now>=s.dateDebutSejour and :now<=s.dateFinSejour  and s.codeSejour LIKE \'_P%\' and s.idEtablisment=:etab'
        )->setParameter('now', new \DateTime('midnight'), Types::DATETIME_MUTABLE)
            ->setParameter(':etab', $part);;
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourPayantHaveAttach()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Sejour s where :now>=s.dateDebutSejour and :now<=s.dateFinSejour  and s.codeSejour LIKE \'_P%\' and s.statut=:statut'
        )->setParameter('now', new DateTime('midnight'), Types::DATETIME_MUTABLE)->setParameter('statut', 5);;
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourPayantHaveAttachByPart($part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Sejour s where :now>=s.dateDebutSejour and :now<=s.dateFinSejour  and s.codeSejour LIKE \'_P%\' and s.statut=:statut and s.idEtablisment=:etab'
        )->setParameter('now', new DateTime('midnight'), Types::DATETIME_MUTABLE)->setParameter('statut', 5)
            ->setParameter(':etab', $part);;
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourParent()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(p) FROM App\Entity\Sejour s,App\Entity\ParentSejour p WHERE p.idSejour=s.id and :now>=s.dateDebutSejour and :now<=s.dateFinSejour and s.statut<>:statut '
        )->setParameter('now', new \DateTime('midnight'), Types::DATETIME_MUTABLE)
            ->setParameter('statut', 39);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourParentParPart($part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(p) FROM App\Entity\Sejour s,App\Entity\ParentSejour p WHERE p.idSejour=s.id and :now>=s.dateDebutSejour and :now<=s.dateFinSejour and  s.idEtablisment=:etab '
        )->setParameter('now', new \DateTime('midnight'), Types::DATETIME_MUTABLE)
            ->setParameter('etab', $part);;
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourFreeParent()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(p) FROM App\Entity\Sejour s,App\Entity\ParentSejour p WHERE p.idSejour=s.id and :now >= s.dateDebutSejour and :now<=s.dateFinSejour and s.codeSejour LIKE \'_F%\' '
        )->setParameter('now', new \DateTime('midnight'), Types::DATETIME_MUTABLE);;
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourFreeParentBetween($datefin, $datedebut)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(p) FROM App\Entity\Sejour s,App\Entity\ParentSejour p WHERE p.idSejour=s.id and :datedeb<=p.dateCreation and :datefin>=p.dateCreation and s.statut<>:statut and s.codeSejour LIKE \'_F%\' '
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', 39);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourFreeParentBetween_ECOLE($datefin, $datedebut)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(p) FROM App\Entity\Sejour s,App\Entity\ParentSejour p WHERE p.idSejour=s.id and :datedeb<=p.dateCreation and :datefin>=p.dateCreation and s.statut<>:statut and s.codeSejour LIKE \'EF%\' '
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', 39);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourFreeParentBetween_PARTENAIRE($datefin, $datedebut)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(p) FROM App\Entity\Sejour s,App\Entity\ParentSejour p WHERE p.idSejour=s.id and :datedeb<=p.dateCreation and :datefin>=p.dateCreation and s.statut<>:statut and s.codeSejour LIKE \'PF%\' '
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', 39);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourFreeParentParPart($datefin, $datedebut, $part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(p) FROM App\Entity\Sejour s,App\Entity\ParentSejour p WHERE p.idSejour=s.id and :datedeb<=p.dateCreation and :datefin>=p.dateCreation and s.codeSejour LIKE \'_F%\' and s.idEtablisment=:etab and s.statut<>:statut'
        )->setParameter('etab', $part)
            ->setParameter('statut', 39)
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourFreeParentParPart_EF($datefin, $datedebut, $part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(p) FROM App\Entity\Sejour s,App\Entity\ParentSejour p WHERE p.idSejour=s.id and :datedeb<=p.dateCreation and :datefin>=p.dateCreation and s.codeSejour LIKE \'EF%\' and s.idEtablisment=:etab and s.statut<>:statut'
        )->setParameter('etab', $part)
            ->setParameter('statut', 39)
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourFreeParentParPart_PF($datefin, $datedebut, $part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(p) FROM App\Entity\Sejour s,App\Entity\ParentSejour p WHERE p.idSejour=s.id and :datedeb<=p.dateCreation and :datefin>=p.dateCreation and s.codeSejour LIKE \'PF%\' and s.idEtablisment=:etab and s.statut<>:statut'
        )->setParameter('etab', $part)
            ->setParameter('statut', 39)
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourFreeParentParTypePart($datefin, $datedebut, $type)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(p) FROM App\Entity\Sejour s,App\Entity\ParentSejour p,App\Entity\Etablisment e WHERE p.idSejour=s.id and s.statut<>:statut and :datedeb<=p.dateCreation and :datefin>=p.dateCreation and s.codeSejour LIKE \'_F%\' AND(s.idEtablisment=e.id)and (e.typeetablisment=:type) '
        )->setParameter('type', $type)
            ->setParameter('statut', 39)
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourFreeParentParTypePartEF($datefin, $datedebut, $type)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(p) FROM App\Entity\Sejour s,App\Entity\ParentSejour p,App\Entity\Etablisment e WHERE p.idSejour=s.id and s.statut<>:statut and :datedeb<=p.dateCreation and :datefin>=p.dateCreation and s.codeSejour LIKE \'EF%\' AND(s.idEtablisment=e.id)and (e.typeetablisment=:type) '
        )->setParameter('type', $type)
            ->setParameter('statut', 39)
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourFreeParentParTypePartPF($datefin, $datedebut, $type)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(p) FROM App\Entity\Sejour s,App\Entity\ParentSejour p,App\Entity\Etablisment e WHERE p.idSejour=s.id and s.statut<>:statut and :datedeb<=p.dateCreation and :datefin>=p.dateCreation and s.codeSejour LIKE \'PF%\' AND(s.idEtablisment=e.id)and (e.typeetablisment=:type) '
        )->setParameter('type', $type)
            ->setParameter('statut', 39)
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourPayantParent()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(p) FROM App\Entity\Sejour s,App\Entity\ParentSejour p WHERE p.idSejour=s.id and :now>=s.dateDebutSejour and :now<=s.dateFinSejour and s.statut<>:statut  and s.codeSejour LIKE \'_P%\' '
        )->setParameter('now', new \DateTime('midnight'), Types::DATETIME_MUTABLE)->setParameter('statut', 39);;
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourPayantParentParPart($part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(p) FROM App\Entity\Sejour s,App\Entity\ParentSejour p WHERE p.idSejour=s.id and :now>=s.dateDebutSejour and :now<=s.dateFinSejour  and s.codeSejour LIKE \'_P%\' and  s.idEtablisment=:etab'
        )->setParameter('now', new \DateTime('midnight'), Types::DATETIME_MUTABLE)
            ->setParameter('etab', $part);;
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourPayantParentPaye()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(p) FROM App\Entity\Sejour s,App\Entity\ParentSejour p WHERE p.idSejour=s.id and :now<=s.dateDebutSejour and :now>=s.dateFinSejour  and s.codeSejour LIKE \'_P%\' and p.payment=1'
        )->setParameter('now', new \DateTime('midnight'), Types::DATETIME_MUTABLE);;
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourPayantParentPayeBetween($datefin, $datedebut)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(p) FROM App\Entity\Sejour s,App\Entity\ParentSejour p WHERE p.idSejour=s.id and :datedeb<=p.dateCreation and :datefin>=p.dateCreation and s.statut<>:statut  and s.codeSejour LIKE \'_P%\' and p.payment=1'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', 39);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourPayantParentPayeParPart($datefin, $datedebut, $part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(p) FROM App\Entity\Sejour s,App\Entity\ParentSejour p WHERE p.idSejour=s.id and :datedeb<=p.dateCreation and :datefin>=p.dateCreation  and s.codeSejour LIKE \'_P%\' and p.payment=1 and  s.idEtablisment=:etab and s.statut<>:statut'
        )->setParameter('etab', $part)
            ->setParameter('statut', 39)
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourPayantParentPayeParTypePart($datefin, $datedebut, $type)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(p) FROM App\Entity\Sejour s,App\Entity\ParentSejour p,App\Entity\Etablisment e WHERE p.idSejour=s.id and :datedeb<=p.dateCreation and :datefin>=p.dateCreation  and s.codeSejour LIKE \'_P%\' and p.payment=1 AND(s.idEtablisment=e.id)and (e.typeetablisment=:type)and (s.statut<>:statut)'
        )->setParameter('type', $type)
            ->setParameter('statut', 39)
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findListeSejourEncourPayantParentBetween($datefin, $datedebut)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT p FROM App\Entity\Sejour s,App\Entity\ParentSejour p WHERE p.idSejour=s.id and s.statut<>:statut and :datedeb<=p.dateCreation and :datefin>=p.dateCreation  and (s.codeSejour LIKE \'_F%\' OR p.payment=1)'
        )->setParameter('statut', 39)
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findListeSejourEncourPayantParentParPart($datefin, $datedebut, $part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT p FROM App\Entity\Sejour s,App\Entity\ParentSejour p WHERE p.idSejour=s.id and s.statut<>:statut and :datedeb<=p.dateCreation and :datefin>=p.dateCreation  and (s.codeSejour LIKE \'_F%\' and p.payment=1) and  s.idEtablisment=:etab'
        )->setParameter('etab', $part)
            ->setParameter('statut', 39)
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findListeSejourEncourPayantParentParTypePart($datefin, $datedebut, $type)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT p FROM App\Entity\Sejour s,App\Entity\ParentSejour p,App\Entity\Etablisment e WHERE p.idSejour=s.id and s.statut<>:statut and :datedeb<=p.dateCreation and :datefin>=p.dateCreation  and (s.codeSejour LIKE \'_F%\' OR p.payment=1) AND(s.idEtablisment=e.id)and (e.typeetablisment=:type)'
        )->setParameter('type', $type)
            ->setParameter('statut', 39)
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function getNbrSejourCree($datedebut, $datefin)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s WHERE ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin))  and s.statut<>:statut'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', 39);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function getNbrSejourCreeParPart($datedebut, $datefin, $part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s WHERE s.statut<>:statut and ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin))  and s.idEtablisment=:etab'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('etab', $part)
            ->setParameter('statut', 39);;
        // returns an array of Product objects
        return $query->getResult();
    }
    public function getNbrSejourCreeParTypePart($datedebut, $datefin, $type)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s,App\Entity\Etablisment e WHERE ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin))  AND(s.idEtablisment=e.id)and (e.typeetablisment=:type) and s.statut<>:statut'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('type', $type)
            ->setParameter('statut', 39);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourBetween($datedebut, $datefin)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s  WHERE (s.dateDebutSejour BETWEEN :datedebut AND :datefin) OR (s.dateFinSejour BETWEEN :datedebut AND :datefin) OR (s.dateDebutSejour <= :datedebut AND s.dateFinSejour >= :datefin)'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourBetweenParPart($datedebut, $datefin, $part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s  WHERE  (s.idEtablisment=:etab and s.statut<>:statut) and ((s.dateDebutSejour BETWEEN :datedebut AND :datefin) OR (s.dateFinSejour BETWEEN :datedebut AND :datefin) OR (s.dateDebutSejour <= :datedebut AND s.dateFinSejour >= :datefin))'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('etab', $part)
            ->setParameter('statut', 39);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourEncourBetweenParTypePart($datedebut, $datefin, $type)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s,App\Entity\Etablisment e  WHERE ((s.dateDebutSejour BETWEEN :datedebut AND :datefin) OR (s.dateFinSejour BETWEEN :datedebut AND :datefin) OR (s.dateDebutSejour <= :datedebut AND s.dateFinSejour >= :datefin) AND(s.idEtablisment=e.id)and (e.typeetablisment=:type))'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('type', $type);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourActiveBetween($datedebut, $datefin)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s  WHERE ((s.dateDebutSejour BETWEEN :datedebut AND :datefin) OR (s.dateFinSejour BETWEEN :datedebut AND :datefin) OR (s.dateDebutSejour <= :datedebut AND s.dateFinSejour >= :datefin) )and(s.statut=:stat)'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('stat', 5);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourActiveBetweenParPart($datedebut, $datefin, $part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s  WHERE ((s.dateDebutSejour BETWEEN :datedebut AND :datefin) OR (s.dateFinSejour BETWEEN :datedebut AND :datefin) OR (s.dateDebutSejour <= :datedebut AND s.dateFinSejour >= :datefin) )and(s.statut=:stat) and s.idEtablisment=:etab'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('stat', 5)
            ->setParameter(':etab', $part);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findSejourActiveBetweenParTypePart($datedebut, $datefin, $type)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s,App\Entity\Etablisment e  WHERE ((s.dateDebutSejour BETWEEN :datedebut AND :datefin) OR (s.dateFinSejour BETWEEN :datedebut AND :datefin) OR (s.dateDebutSejour <= :datedebut AND s.dateFinSejour >= :datefin) )and(s.statut=:stat) AND(s.idEtablisment=e.id)and (e.typeetablisment=:type)'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('stat', 5)
            ->setParameter('type', $type);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findcountAllSejour()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Sejour s'
        );
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findcountAllSejourParPart($part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Sejour s where s.idEtablisment=:etab'
        )->setParameter('etab', $part);;
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findCountAllSejourFree()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Sejour s WHERE s.codeSejour LIKE \'_F%\' '
        );;
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findCountAllSejourFreeParPart($part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Sejour s WHERE s.codeSejour LIKE \'_F%\' and s.idEtablisment=:etab '
        )->setParameter('etab', $part);;;;
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findCountAllSejourPayant()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Sejour s WHERE s.codeSejour LIKE \'_P%\' '
        );;
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findCountAllSejourPayantParPart($part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Sejour s WHERE s.codeSejour LIKE \'_P%\' and s.idEtablisment=:etab '
        )->setParameter('etab', $part);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findPartrttr($part)
    {
        //        $entityManager = $this->getEntityManager();
        //        $query = $entityManager->createQuery(
        //            'SELECT COUNT(s) FROM App\Entity\Sejour s WHERE s.codeSejour LIKE \'_F%\' and s.idPartenaire=:etab '
        //        )->setParameter('etab',$part);
        //
        //        // returns an array of Product objects
        //        return $query->getResult();
        return $this->createQueryBuilder('s')
            ->andWhere(' s.codeSejour LIKE :val')
            ->andWhere('s.idPartenaire = :use')
            ->setParameter('val', '_F%')
            ->setParameter('use', $part)
            ->getQuery()
            ->getResult();
    }
    public function findSejourListWithAttache($datefin)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('select count(DISTINCT(S)) from App\Entity\Sejour S , App\Entity\SejourAttachment A where S.statut<>:statut and S.dateFinSejour<=:DateFin and S.id=A.idSejour HAVING count(A.id)>0')
            ->setParameter('DateFin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('statut', 39);
        return $query->getResult();
    }
    public function findSejourListWithAttacheParPart($datefin, $part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('select count(DISTINCT(S)) from App\Entity\Sejour S , App\Entity\SejourAttachment A where S.statut<>:statut and  S.dateFinSejour<=:DateFin  and S.idEtablisment=:etab and S.id=A.idSejour HAVING count(A.id)>0 ')
            ->setParameter('DateFin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('etab', $part)
            ->setParameter('statut', 39);;
        return $query->getResult();
    }
    public function findSejourListWithAttacheParType($datefin, $type)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('select count(DISTINCT(S)) from App\Entity\Sejour S , App\Entity\SejourAttachment A, App\Entity\Etablisment e where S.dateFinSejour<=:DateFin AND(S.idEtablisment=e.id)and (e.typeetablisment=:type)and S.id=A.idSejour HAVING count(A.id)>0 ')
            ->setParameter('DateFin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('type', $type);
        return $query->getResult();
    }
    public function findFinishSejour()
    {
        $entityManager = $this->getEntityManager();
        $dateCreationArch = new DateTime();
        $ThreeWeek = new DateInterval('P21D');
        $dateCreationArch->sub($ThreeWeek);
        $dateCreationArch->setTime(0, 0);
        $query = $entityManager->createQuery('select S from App\Entity\Sejour S where S.dateFinSejour<=:dateCreationArch')
            ->setParameter('dateCreationArch', $dateCreationArch, Types::DATETIME_MUTABLE);
        return $query->getResult();
    }
    /*** salim fonction Recherche sejour */
    public function RecherchesejourAdmin($codeSejour, $dateDebutSejour, $dateFinSejour, $Statut, $typeEtablissement, $paym, $idPartenaire)
    {
        $entityManager = $this->getEntityManager();
        $requete = 'SELECT s FROM App\Entity\Sejour s,App\Entity\Etablisment e ';
        if ($codeSejour != "") {
            $requete = $requete . ' where s.codeSejour LIKE :codeSejour ';
        }
        if ($dateDebutSejour != "" && $dateFinSejour != "") {
            $dateDebutSejour = new \DateTime($dateDebutSejour);
            $dateFinSejour = new \DateTime($dateFinSejour);
            if ($codeSejour != "") {
                $requete = $requete . ' and ((s.dateDebutSejour BETWEEN :dateDebutSejour AND :dateFinSejour) OR (s.dateFinSejour BETWEEN :dateDebutSejour AND :dateFinSejour) OR (s.dateDebutSejour <= :dateDebutSejour AND s.dateFinSejour >= :dateFinSejour) ) ';
            } else {
                $requete = $requete . ' where ((s.dateDebutSejour BETWEEN :dateDebutSejour AND :dateFinSejour) OR (s.dateFinSejour BETWEEN :dateDebutSejour AND :dateFinSejour) OR (s.dateDebutSejour <= :dateDebutSejour AND s.dateFinSejour >= :dateFinSejour) )';
            }
        }
        $i = 0;
        if ($Statut != "") {
            foreach ($Statut as $statut) {
                if ($i == 0) {
                    if ($statut != "ALL") {
                        if ($codeSejour != "" || $dateDebutSejour != "" || $dateFinSejour != "") {
                            $requete = $requete . ' and s.statut =:Statut' . $i . ' ';
                        } else {
                            $requete = $requete . ' where s.statut =:Statut' . $i . ' ';
                        }
                    }
                } else {
                    $requete = $requete . ' or s.statut =:Statut' . $i . ' ';
                }
                $i++;
            }
        }
        if ($typeEtablissement != "") {
            if ($codeSejour != "" || $dateDebutSejour != "" || $dateFinSejour != "" || $Statut != "") {
                if ($Statut[0] == "ALL") {
                    $requete = $requete . 'where s.idEtablisment = e.id and e.typeetablisment=:typeEtablissement';
                } else {
                    $requete = $requete . 'and s.idEtablisment = e.id and e.typeetablisment=:typeEtablissement ';
                }
            } else {
                $requete = $requete . 'where s.idEtablisment = e.id and e.typeetablisment=:typeEtablissement ';
            }
        }
        if ($paym != "") {
            if ($codeSejour != "" || $dateDebutSejour != "" || $dateFinSejour != "" || $Statut != "" || $typeEtablissement != "") {
                if ($Statut[0] != "ALL") {
                    $requete = $requete . ' and s.paym =:paym ';
                } else {
                    $requete = $requete . ' where s.paym =:paym ';
                }
            } else {
                $requete = $requete . ' where s.paym =:paym ';
            }
        }
        if ($idPartenaire != "") {
            if ($codeSejour != "" || $dateDebutSejour != "" || $dateFinSejour != "" || $Statut != "" || $typeEtablissement != "" || $paym != "") {
                if ($Statut[0] != "ALL") {
                    $requete = $requete . ' and s.idEtablisment =:idPart ';
                } else {
                    $requete = $requete . ' where s.idEtablisment =:idPart ';
                }
            } else {
                $requete = $requete . ' where s.idEtablisment =:idPart ';
            }
        }
        $requete = $requete . " ORDER BY s.dateDebutSejour DESC ";
        //  var_dump($requete);
        $result = $entityManager->createQuery($requete);
        if ($codeSejour != "") {
            $result->setParameter('codeSejour', '%' . $codeSejour . '%');
        }
        if ($dateDebutSejour != "" && $dateFinSejour != "") {
            $result->setParameter('dateDebutSejour', $dateDebutSejour, Types::DATETIME_MUTABLE);
            $result->setParameter('dateFinSejour', $dateFinSejour, Types::DATETIME_MUTABLE);
        }
        $i = 0;
        if ($Statut != "") {
            foreach ($Statut as $statut) {
                if ($statut != "ALL") {
                    $result->setParameter('Statut' . $i . '', $statut);
                }
                $i++;
            }
        }
        if ($typeEtablissement != "") {
            if ($typeEtablissement == 1) {
                $type = "ECOLES/AUTRES";
            }
            if ($typeEtablissement == 2) {
                $type = "PARTENAIRES/VOYAGISTES";
            }
            if ($typeEtablissement == 3) {
                $type = "CSE";
            }
            $result->setParameter('typeEtablissement', '%' . $type . '%');
            $result->setParameter('typeEtablissement', $type);
        }
        if ($paym != "") {
            if ($paym == 1) {
                $result->setParameter('paym', 1);
            }
            if ($paym == 0) {
                $result->setParameter('paym', 0);
            }
        }
        if ($idPartenaire != "") {
            $result->setParameter('idPart', $idPartenaire);
        }
        return $result->getResult();
    }
    public function findSejourThisDay($dateDebut)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('select S from App\Entity\Sejour S where   S.dateDebutSejour <= :dateDebut and  S.statut=:statut')
            ->setParameter('dateDebut', $dateDebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', 15);
        return $query->getResult();
    }
    public function findSejourTerminerDemainETActive($demain)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('select S from App\Entity\Sejour S where S.dateFinCode <=  :demain and  S.statut=:statut')
            ->setParameter('demain', $demain, Types::DATETIME_MUTABLE)
            ->setParameter('statut', 5);
        return $query->getResult();
    }
    public function findCodeSejourTerminerDemainETCreer($demain)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('select S from App\Entity\Sejour S where S.dateFinSejour <=  :demain and  S.statut=:statut')
            ->setParameter('demain', $demain, Types::DATETIME_MUTABLE)
            ->setParameter('statut', 15);
        return $query->getResult();
    }
    public function findSejourListForSearch()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('select S.id,S.codeSejour from App\Entity\Sejour S  where S.statut<>:statut')
            ->setParameter('statut', 39);
        return $query->getResult();
    }
    public function getSejoursPP($datedebut, $datefin)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s WHERE ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.statut in(:statut)   and s.codeSejour LIKE \'_F%\'  and s.prixcnxparent=2.9'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', [5, 14]);
        // returns an array of Product objects
        return $query->getResult();
    }
 
    public function getSejoursEF($datedebut, $datefin)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s WHERE ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.statut in(:statut)  and s.prixcnxpartenaire = :prixcnxpartenaire   '
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', [5, 14])
            ->setParameter('prixcnxpartenaire', "1.58");
        return $query->getResult();
    }
public function getSejours($datefin, $datedebut)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('(s.dateDebutSejour BETWEEN :datedebut AND :datefin) OR (s.dateFinSejour BETWEEN :datedebut AND :datefin) OR (s.dateDebutSejour <= :datedebut AND s.dateFinSejour >= :datefin) ')
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->getQuery()
            ->getResult();
    }
    public function getSejoursPartenaires($datefin, $datedebut)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('(s.dateDebutSejour BETWEEN :datedebut AND :datefin) OR (s.dateFinSejour BETWEEN :datedebut AND :datefin) OR (s.dateDebutSejour <= :datedebut AND s.dateFinSejour >= :datefin) ')
            ->andWhere('s.prixcnxpartenaire like :prixcnxpar')
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('prixcnxpar', '1.58')
            ->getQuery()
            ->getResult();
    }
   
    public function getSejoursPayants($datefin, $datedebut)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('(s.dateDebutSejour BETWEEN :datedebut AND :datefin) OR (s.dateFinSejour BETWEEN :datedebut AND :datefin) OR (s.dateDebutSejour <= :datedebut AND s.dateFinSejour >= :datefin) ')
            ->andWhere('s.prixcnxparent like :prixcnxpar')
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('prixcnxpar', '2.9')
            ->getQuery()
            ->getResult();
    }
    public function getLSejoursFree($datefin, $datedebut)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('(s.dateDebutSejour BETWEEN :datedebut AND :datefin) OR (s.dateFinSejour BETWEEN :datedebut AND :datefin) OR (s.dateDebutSejour <= :datedebut AND s.dateFinSejour >= :datefin) ')
            ->andWhere('s.prixcnxpartenaire like :prixcnxpar')
            ->andWhere('s.prixcnxparent like :prixcnxparent')
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('prixcnxpar', '0')
            ->setParameter('prixcnxparent', '0')
            ->getQuery()
            ->getResult();
    }
   
    public function findCodeSejourForCodePromo(): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s.id,s.codeSejour FROM App\Entity\Sejour s where s.statut in (:statut)'
        )->setParameter('statut', [5, 14]);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function getListeAccoDeCeMois($datefin, $datedebut)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('(s.dateDebutSejour BETWEEN :datedebut AND :datefin)  ')
            ->andWhere('s.statut =:statut OR  s.statut =:statut2')
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', 15)
            ->setParameter('statut2', 5)
            ->getQuery()
            ->getResult();
    }
    public function getListeAccoOntSejourActive($datefin, $datedebut)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('(s.dateDebutSejour BETWEEN :datedebut AND :datefin) OR (s.dateFinSejour BETWEEN :datedebut AND :datefin) OR (s.dateDebutSejour <= :datedebut AND s.dateFinSejour >= :datefin)')
            ->andWhere('s.statut =:statut OR  s.statut =:statut2')
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', 14)
            ->setParameter('statut2', 5)
            ->getQuery()
            ->getResult();
    }
    public function getSejoursFree($datedebut, $datefin)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s WHERE ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.statut<>:statut and s.codeSejour LIKE \'_F%\'  and s.prixcnxpartenaire=0'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', 39);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function getSejoursMessage($datedebut, $datefin)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s WHERE ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin))'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function getSejoursFreeActifs($datedebut, $datefin)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s WHERE ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) and s.statut in(:statut)   and s.codeSejour LIKE \'_F%\'  and s.prixcnxpartenaire=0'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', [5, 14]);
        // returns an array of Product objects
        return $query->getResult();
    }
  
    public function getSejourNonActifsCeMois($datedebut,$dateDebutMois)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s WHERE ((s.dateDebutSejour BETWEEN :datedeb AND :dateDebutMois) OR  (s.dateDebutSejour <= :datedeb AND s.dateDebutSejour >= :dateDebutMois)) and s.statut in(:statut) '
        )->setParameter('dateDebutMois', $dateDebutMois, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', [15]);
        // returns an array of Product objects
        return $query->getResult();
    }
   
    //
    public function getsejourActifsEnCours($datedebut)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s WHERE ( (s.dateFinSejour >= :datedeb )) and s.statut in(:statut) '
        )
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', [5]);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function getsejourActifsFinis($datedebut)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s WHERE ( (s.dateFinSejour < :datedeb )) and s.statut in(:statut) '
        )
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', [5]);
        // returns an array of Product objects
        return $query->getResult();
    }
  
    public function getSejourNonActifsHier($datedebut, $datefin,$dateAjour)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s WHERE ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin   )) and s.statut in(:statut) and (s.dateFinSejour  < :dateNow)  '
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('dateNow', $dateAjour, Types::DATETIME_MUTABLE)
            ->setParameter('statut', [15]);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function getAlbumsSejoursCreesMois($datedebut, $datefin)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s WHERE ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin   )) and s.statut in(:statut) and s.albumgratuie in(:albumgratuie)  and (s.dateFinSejour  < :datefin)  '
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statut', [5])
            ->setParameter('albumgratuie', [9]);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function getAlbumsSejoursCrees($datedebut)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s WHERE (s.albumgratuie in(:albumgratuie))  '
        )
            ->setParameter('albumgratuie', [9]);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function getSejourCeMois($datedebut, $datefin, $dateNow = null)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s WHERE ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin   )) and s.statut in(:statut) '
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
          
            ->setParameter('statut', [15]);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function getSejourCeMoisCreesEncours($datedebut,$datefin,$dateAjour)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s WHERE ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin   )) and s.statut in(:statut) and (s.dateFinSejour  > :dateNow)  '
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('dateNow', $dateAjour, Types::DATETIME_MUTABLE)
            ->setParameter('statut', [15]);
        // returns an array of Product objects
        return $query->getResult();
    }
 
    public function getSejourEtab($datedebut, $datefin)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Sejour s WHERE ((s.dateDebutSejour BETWEEN :datedeb AND :datefin) OR (s.dateFinSejour BETWEEN :datedeb AND :datefin) OR (s.dateDebutSejour <= :datedeb AND s.dateFinSejour >= :datefin)) AND s.idEtablisment= :etab'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('etab', 793);
        // returns an array of Product objects
        return $query->getResult();
    }
}
