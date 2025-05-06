<?php

namespace App\Repository;

use App\Entity\SejourAttachment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method SejourAttachment|null find($id, $lockMode = null, $lockVersion = null)
 * @method SejourAttachment|null findOneBy(array $criteria, array $orderBy = null)
 * @method SejourAttachment[]    findAll()
 * @method SejourAttachment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SejourAttachmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SejourAttachment::class);
    }
    public function getSejourAttachById(string $id)
    {
        $entityManager = $this->getEntityManager();
        $dql = 'SELECT sa, a, r
        FROM App\Entity\SejourAttachment sa
        JOIN sa.idAttchment a
        JOIN a.idref r
        WHERE sa.idSejour = :id AND sa.statut = :statut
        AND r.libiller IN (:types)';
        $query = $entityManager->createQuery($dql)
            ->setParameter('id', $id)
            ->setParameter('statut', 'public')
            ->setParameter('types', ['photo', 'video']);
            
        $results = $query->getResult();
        return $results;
    }
    public function getSejourAttachByPhoto(string $id)
    {
        $entityManager = $this->getEntityManager();
        $dql = 'SELECT sa, a, r
        FROM App\Entity\SejourAttachment sa
        JOIN sa.idAttchment a
        JOIN a.idref r
        WHERE sa.idSejour = :id AND sa.statut = :statut
        AND r.libiller IN (:types)'
       
         ;
        $query = $entityManager->createQuery($dql)
            ->setParameter('id', $id)
            ->setParameter('statut', 'public')
            ->    setFirstResult    (0)    
            ->setMaxResults(60)
            ->setParameter('types', ['photo']);
            
        $results = $query->getResult();
        return $results;
    }
    public function getSejourAttachByPhotoPagination(string $id)
    {
        $entityManager = $this->getEntityManager();
        $dql = 'SELECT sa, a, r
        FROM App\Entity\SejourAttachment sa
        JOIN sa.idAttchment a
        JOIN a.idref r
        WHERE sa.idSejour = :id AND sa.statut = :statut
        AND r.libiller IN (:types)'
       
         ;
        $query = $entityManager->createQuery($dql)
            ->setParameter('id', $id)
            ->setParameter('statut', 'public')
            ->setMaxResults(60)
            ->setParameter('types', ['photo']);
            $paginator         =         new         Paginator    (    $query    )    ;    
            $paginator    ->    getQuery    (    )    
              ->    setFirstResult    (0)    
              ->    setMaxResults    (10)    ;    
            return     $paginator; 
      
    }
    public function getSejourAttachByMessage(string $id)
    {
        $entityManager = $this->getEntityManager();
        $dql = 'SELECT sa, a, r
        FROM App\Entity\SejourAttachment sa
        JOIN sa.idAttchment a
        JOIN a.idref r
        WHERE sa.idSejour = :id AND r.libiller IN (:types)';
        $query = $entityManager->createQuery($dql)
            ->setParameter('id', $id)
            ->setParameter('types', ['message']);
        $results = $query->getResult();
        return $results;
    }
    public function searshTypeMessagtpart()
    {
        $word = "PARTENAIRES/VOYAGISTES";
        $value = "message";
        $result = $this->createQueryBuilder('k')
            ->select('SUM(k.nbpartenaireattch)')
            ->innerJoin('k.idAttchment', "Attachment")
            ->innerJoin('Attachment.idref', "ref")
            ->innerJoin('k.idSejour', "Sejour")
            ->innerJoin('Sejour.idEtablisment', "Etablisment")
            ->innerJoin('Sejour.statut', "refA")
            ->Where('Etablisment.typeetablisment LIKE :word')
            ->AndWhere('ref.libiller = :val')
            ->AndWhere('refA.id <> :val3')
            ->setParameter('val', $value)
            ->setParameter('val3', 14)
            ->setParameter('word', '%' . $word . '%')
            ->getQuery()
            ->getSingleScalarResult();
        if ($result == null) {
            return 0;
        } else {
            return $result;
        }
    }
    public function searshTypeMessagtpartByDate($date)
    {
        $word = "PARTENAIRES/VOYAGISTES";
        $value = "message";
        $result = $this->createQueryBuilder('k')
            ->select('SUM(k.nbpartenaireattch)')
            ->innerJoin('k.idAttchment', "Attachment")
            ->innerJoin('Attachment.idref', "ref")
            ->innerJoin('k.idSejour', "Sejour")
            ->innerJoin('Sejour.idEtablisment', "Etablisment")
            ->innerJoin('Sejour.statut', "refA")
            ->Where('Etablisment.typeetablisment LIKE :word')
            ->andWhere('Sejour.dateDebutSejour >= :val2')
            ->andWhere('ref.libiller = :val')
            ->andWhere('refA.id <> :val3')
            ->setParameter('val2', $date)
            ->setParameter('val', $value)
            ->setParameter('val3', 14)
            ->setParameter('word', '%' . $word . '%')
            ->getQuery()
            ->getSingleScalarResult();
        if ($result == null) {
            return 0;
        } else {
            return $result;
        }
    }
    public function searshTypeMessagtecole()
    {
        $word = "ECOLES/AUTRES";
        $value = "message";
        $result = $this->createQueryBuilder('k')
            ->select('SUM(k.nbpartenaireattch)')
            ->innerJoin('k.idAttchment', "Attachment")
            ->innerJoin('Attachment.idref', "ref")
            ->innerJoin('k.idSejour', "Sejour")
            ->innerJoin('Sejour.idEtablisment', "Etablisment")
            ->innerJoin('Sejour.statut', "refA")
            ->Where('Etablisment.typeetablisment LIKE :word')
            ->AndWhere('ref.libiller = :val')
            ->AndWhere('ref.id <> :val3')
            ->setParameter('val', $value)
            ->setParameter('val3', 14)
            ->setParameter('word', '%' . $word . '%')
            ->getQuery()
            ->getSingleScalarResult();
        if ($result == null) {
            return 0;
        } else {
            return $result;
        }
    }
    public function searshTypeMessagtecoleByDate($date)
    {
        $word = "ECOLES/AUTRES";
        $value = "message";
        $result = $this->createQueryBuilder('k')
            ->select('SUM(k.nbpartenaireattch)')
            ->innerJoin('k.idAttchment', "Attachment")
            ->innerJoin('Attachment.idref', "ref")
            ->innerJoin('k.idSejour', "Sejour")
            ->innerJoin('Sejour.idEtablisment', "Etablisment")
            ->innerJoin('Sejour.statut', "refA")
            ->Where('Etablisment.typeetablisment LIKE :word')
            ->andWhere('Sejour.dateDebutSejour >= :val2')
            ->andWhere('ref.libiller = :val')
            ->andWhere('refA.id <> :val3')
            ->setParameter('val2', $date)
            ->setParameter('val', $value)
            ->setParameter('word', '%' . $word . '%')
            ->setParameter('val3', 14)
            ->getQuery()
            ->getSingleScalarResult();
        if ($result == null) {
            return 0;
        } else {
            return $result;
        }
    }
    public function searshTypeMessagtcse()
    {
        $word = "CSE";
        $value = "message";
        $result = $this->createQueryBuilder('k')
            ->select('SUM(k.nbpartenaireattch)')
            ->innerJoin('k.idAttchment', "Attachment")
            ->innerJoin('Attachment.idref', "ref")
            ->innerJoin('k.idSejour', "Sejour")
            ->innerJoin('Sejour.idEtablisment', "Etablisment")
            ->innerJoin('Sejour.statut', "refA")
            ->Where('Etablisment.typeetablisment LIKE :word')
            ->AndWhere('ref.libiller = :val')
            ->AndWhere('refA.id <> :val3')
            ->setParameter('val', $value)
            ->setParameter('val3', 14)
            ->setParameter('word', '%' . $word . '%')
            ->getQuery()
            ->getSingleScalarResult();
        if ($result == null) {
            return 0;
        } else {
            return $result;
        }
    }
    public function searshTypeMessagtcseByDate($date)
    {
        $word = "CSE";
        $value = "message";
        $result = $this->createQueryBuilder('k')
            ->select('SUM(k.nbpartenaireattch)')
            ->innerJoin('k.idAttchment', "Attachment")
            ->innerJoin('Attachment.idref', "ref")
            ->innerJoin('k.idSejour', "Sejour")
            ->innerJoin('Sejour.idEtablisment', "Etablisment")
            ->innerJoin('Sejour.statut', "refA")
            ->Where('Etablisment.typeetablisment LIKE :word')
            ->andWhere('Sejour.dateDebutSejour >= :val2')
            ->andWhere('ref.libiller = :val')
            ->andWhere('refA.id <> :val3')
            ->setParameter('val2', $date)
            ->setParameter('val', $value)
            ->setParameter('val3', 14)
            ->setParameter('word', '%' . $word . '%')
            ->getQuery()
            ->getSingleScalarResult();
        if ($result == null) {
            return 0;
        } else {
            return $result;
        }
    }
    public function searshTypePhotogtcse()
    {
        $word = "CSE";
        $value = "photo";
        $result = $this->createQueryBuilder('k')
            ->select('SUM(k.nbpartenaireattch)')
            ->innerJoin('k.idAttchment', "Attachment")
            ->innerJoin('Attachment.idref', "ref")
            ->innerJoin('k.idSejour', "Sejour")
            ->innerJoin('Sejour.idEtablisment', "Etablisment")
            ->innerJoin('Sejour.statut', "refA")
            ->Where('Etablisment.typeetablisment LIKE :word')
            ->AndWhere('ref.libiller = :val')
            ->AndWhere('refA.id <> :val3')
            ->setParameter('val', $value)
            ->setParameter('word', '%' . $word . '%')
            ->setParameter('val3', 14)
            ->getQuery()
            ->getSingleScalarResult();
        if ($result == null) {
            return 0;
        } else {
            return $result;
        }
    }
    public function searshTypePhotogtcseByDate($date)
    {
        $word = "CSE";
        $value = "photo";
        $result = $this->createQueryBuilder('k')
            ->select('SUM(k.nbpartenaireattch)')
            ->innerJoin('k.idAttchment', "Attachment")
            ->innerJoin('Attachment.idref', "ref")
            ->innerJoin('k.idSejour', "Sejour")
            ->innerJoin('Sejour.idEtablisment', "Etablisment")
            ->innerJoin('Sejour.statut', "refA")
            ->Where('Etablisment.typeetablisment LIKE :word')
            ->AndWhere('refA.id <> :val3')
            ->andWhere('Sejour.dateDebutSejour >= :val2')
            ->andWhere('ref.libiller = :val')
            ->setParameter('val2', $date)
            ->setParameter('val', $value)
            ->setParameter('word', '%' . $word . '%')
            ->setParameter('val3', 14)
            ->getQuery()
            ->getSingleScalarResult();
        if ($result == null) {
            return 0;
        } else {
            return $result;
        }
    }
    public function searshTypePhotogtecole()
    {
        $word = "ECOLES/AUTRES";
        $value = "photo";
        $result = $this->createQueryBuilder('k')
            ->select('SUM(k.nbpartenaireattch)')
            ->innerJoin('k.idAttchment', "Attachment")
            ->innerJoin('Attachment.idref', "ref")
            ->innerJoin('k.idSejour', "Sejour")
            ->innerJoin('Sejour.idEtablisment', "Etablisment")
            ->innerJoin('Sejour.statut', "refA")
            ->Where('Etablisment.typeetablisment LIKE :word')
            ->AndWhere('ref.libiller = :val')
            ->andWhere('refA.id <> :val3')
            ->setParameter('val', $value)
            ->setParameter('val3', 14)
            ->setParameter('word', '%' . $word . '%')
            ->getQuery()
            ->getSingleScalarResult();
        if ($result == null) {
            return 0;
        } else {
            return $result;
        }
    }
    public function searshTypePhotogtecoleByDate($date)
    {
        $word = "ECOLES/AUTRES";
        $value = "photo";
        $result = $this->createQueryBuilder('k')
            ->select('SUM(k.nbpartenaireattch)')
            ->innerJoin('k.idAttchment', "Attachment")
            ->innerJoin('Attachment.idref', "ref")
            ->innerJoin('k.idSejour', "Sejour")
            ->innerJoin('Sejour.statut', "refA")
            ->innerJoin('Sejour.idEtablisment', "Etablisment")
            ->Where('Etablisment.typeetablisment LIKE :word')
            ->andWhere('Sejour.dateDebutSejour >= :val2')
            ->andWhere('ref.libiller = :val')
            ->andWhere('refA.id <> :val3')
            ->setParameter('val2', $date)
            ->setParameter('val', $value)
            ->setParameter('word', '%' . $word . '%')
            ->setParameter('val3', 14)
            ->getQuery()
            ->getSingleScalarResult();
        if ($result == null) {
            return 0;
        } else {
            return $result;
        }
    }
    public function searshTypePhotogtpart()
    {
        $word = "PARTENAIRES/VOYAGISTES";
        $value = "photo";
        $result = $this->createQueryBuilder('k')
            ->select('SUM(k.nbpartenaireattch)')
            ->innerJoin('k.idAttchment', "Attachment")
            ->innerJoin('Attachment.idref', "ref")
            ->innerJoin('k.idSejour', "Sejour")
            ->innerJoin('Sejour.idEtablisment', "Etablisment")
            ->innerJoin('Sejour.statut', "refA")
            ->Where('Etablisment.typeetablisment LIKE :word')
            ->AndWhere('ref.libiller = :val')
            ->AndWhere('refA.id <> :val3')
            ->setParameter('val', $value)
            ->setParameter('word', '%' . $word . '%')
            ->setParameter('val3', 14)
            ->getQuery()
            ->getSingleScalarResult();
        if ($result == null) {
            return 0;
        } else {
            return $result;
        }
    }
    public function searshTypePhotogtpartByDate($date)
    {
        $word = "PARTENAIRES/VOYAGISTES";
        $value = "photo";
        $result = $this->createQueryBuilder('k')
            ->select('SUM(k.nbpartenaireattch)')
            ->innerJoin('k.idAttchment', "Attachment")
            ->innerJoin('Attachment.idref', "ref")
            ->innerJoin('k.idSejour', "Sejour")
            ->innerJoin('Sejour.idEtablisment', "Etablisment")
            ->innerJoin('Sejour.statut', "refA")
            ->Where('Etablisment.typeetablisment LIKE :word')
            ->andWhere('Sejour.dateDebutSejour >= :val2')
            ->andWhere('ref.libiller = :val')
            ->andWhere('refA.id <> :val3')
            ->setParameter('val2', $date)
            ->setParameter('val', $value)
            ->setParameter('word', '%' . $word . '%')
            ->setParameter('val3', 14)
            ->getQuery()
            ->getSingleScalarResult();
        if ($result == null) {
            return 0;
        } else {
            return $result;
        }
    }
    public function searshTypeconsultationSejour($valuetypr, $valueparent)
    {
        $result = $this->createQueryBuilder('k')
            ->select('SUM(k.nbpartenaireattch)')
            ->innerJoin('k.idAttchment', "Attachment")
            ->innerJoin('k.idSejour', "Sejour")
            ->innerJoin('Attachment.idref', "ref")
            ->andWhere('Sejour.idPartenaire = :val2')
            ->andWhere('ref.libiller = :val1')
            ->setParameter('val1', $valuetypr)
            ->setParameter('val2', $valueparent)
            ->getQuery()
            ->getSingleScalarResult();
        if ($result == null) {
            return 0;
        } else {
            return $result;
        }
    }
    public function searshTypeconsultationSejourDate($valuetypr, $valueparent, $datedebut, $datefin)
    {
        $result = $this->createQueryBuilder('k')
            ->select('SUM(k.nbpartenaireattch)')
            ->innerJoin('k.idAttchment', "Attachment")
            ->innerJoin('k.idSejour', "Sejour")
            ->innerJoin('Attachment.idref', "ref")
            ->andWhere('Sejour.idPartenaire = :val2')
            ->andWhere('(Sejour.dateDebutSejour BETWEEN :datedebut AND :datefin) OR (Sejour.dateFinSejour BETWEEN :datedebut AND :datefin) OR (Sejour.dateDebutSejour <= :datedebut AND Sejour.dateFinSejour >= :datefin) ')
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->andWhere('ref.libiller = :val1')
            ->setParameter('val1', $valuetypr)
            ->setParameter('val2', $valueparent)
            ->getQuery()
            ->getSingleScalarResult();
        if ($result == null) {
            return 0;
        } else {
            return $result;
        }
    }
    public function  SearchNombrephotoonsejour($id)
    {
        return $this->createQueryBuilder('C')
            ->select('COUNT(C.id)')
            ->innerJoin('C.idSejour', "sejour")
            ->innerJoin('C.idAttchment', "attachment")
            ->Where('sejour.id =:val')
            ->andWhere('attachment.idref =:val2')
            // ->andWhere('k.dateDebutSejour >=:val1')
            ->setParameter('val', $id)
            ->setParameter('val2', 6)
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getSingleScalarResult();
    }
    public function  NombreMessageSejour($id)
    {
        return $this->createQueryBuilder('C')
            ->select('COUNT(C.id)')
            ->innerJoin('C.idSejour', "sejour")
            ->innerJoin('C.idAttchment', "attachment")
            ->Where('sejour.id =:val')
            ->andWhere('attachment.idref =:val2')
            // ->andWhere('k.dateDebutSejour >=:val1')
            ->setParameter('val2', 7)
            ->setParameter('val', $id)
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getSingleScalarResult();
    }
    public function  SearchNombreAttachementnsejour($id)
    {
        return $this->createQueryBuilder('C')
            ->select('COUNT(C.id)')
            ->innerJoin('C.idSejour', "sejour")
            ->innerJoin('C.idAttchment', "attachment")
            ->Where('sejour.id =:val')
            ->setParameter('val', $id)
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getSingleScalarResult();
    }
}
