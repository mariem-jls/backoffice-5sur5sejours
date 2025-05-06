<?php

namespace App\Repository;
use App\Entity\Attachment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/**
 * @method Attachment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Attachment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Attachment[]    findAll()
 * @method Attachment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttachmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attachment::class);
    }
    public function searshSejourAtachment($id)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
        SELECT *
        FROM attachment AS A, sejour_attachment AS sa, sejour AS s, ref As r
        WHERE A.id = sa.id_attchment
        AND A.idref= r.id
        AND r.libiller like :word 
        AND sa.id_sejour= s.id
        AND sa.statut like :word2 
        AND s.id = ' . $id;
        $stmt = $conn->executeQuery($sql, ['word' =>  '%' . 'photo' . '%', 'word2' =>  '%' . 'public' . '%']);
        return $stmt->fetchAllAssociative();
    }
    
    public function searshSejourAtachmentEpaceParent($id, $idParent)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
        SELECT *
        FROM attachment AS A, sejour_attachment AS sa, sejour AS s, ref As r ,user AS u
        WHERE A.id = sa.id_attchment 
        AND sa.idParent_id = u.id
        AND A.idref= r.id
        AND r.libiller like :word 
        AND sa.id_sejour= s.id
        AND sa.statut like :word2 
        AND s.id = ' . $id . '
        AND sa.idParent_id = ' . $idParent;
        $stmt = $conn->executeQuery($sql, ['word' =>  '%' . 'photo' . '%', 'word2' =>  '%' . 'private' . '%']);
        return $stmt->fetchAllAssociative();
    }
    
    public function searshSejourMessage($id)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
        SELECT *
        FROM attachment AS A, sejour_attachment AS sa, sejour AS s, ref As r
        WHERE A.id = sa.id_attchment
        AND A.idref= r.id
        AND r.libiller like :word
        AND sa.id_sejour= s.id
        AND s.id = ' . $id;
        $stmt = $conn->executeQuery($sql, ['word' =>  '%' . 'message' . '%']);
        return $stmt->fetchAllAssociative();
    }
    public function searshSejourVideo($id)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
        SELECT *
        FROM attachment AS A, sejour_attachment AS sa, sejour AS s, ref As r
        WHERE A.id = sa.id_attchment
        AND A.idref= r.id
        AND r.libiller like :word 
        AND sa.id_sejour= s.id
        AND s.id = ' . $id;
        $stmt = $conn->executeQuery($sql, ['word' =>  '%' . 'video' . '%']);
        return $stmt->fetchAllAssociative();
    }
    public function CountsearshSejourAtachment($id)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
        SELECT COUNT(*)
        FROM attachment AS A, sejour_attachment AS sa, sejour AS s, ref As r
        WHERE A.id = sa.id_attchment
        AND A.idref= r.id
        AND r.libiller like :word 
        AND sa.id_sejour= s.id
        AND sa.statut like :word2 
        AND s.id = ' . $id;
        $stmt = $conn->executeQuery($sql, ['word' =>  '%' . 'photo' . '%', 'word2' =>  '%' . 'public' . '%']);
        return $stmt->fetchAllAssociative();
    }
    public function CountsearshSejourVideo($id)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
        SELECT COUNT(*)
        FROM attachment AS A, sejour_attachment AS sa, sejour AS s, ref As r
        WHERE A.id = sa.id_attchment
        AND A.idref= r.id
        AND r.libiller like :word 
        AND sa.id_sejour= s.id
        AND s.id = ' . $id;
        $stmt = $conn->executeQuery($sql, ['word' =>  '%' . 'video' . '%']);
        return $stmt->fetchAllAssociative();
    }
    public function CountsearshSejourMessage($id)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
        SELECT COUNT(*)
        FROM attachment AS A, sejour_attachment AS sa, sejour AS s, ref As r
        WHERE A.id = sa.id_attchment
        AND A.idref= r.id
        AND r.libiller like :word
        AND sa.id_sejour= s.id
        AND s.id = ' . $id;
        $stmt = $conn->executeQuery($sql, ['word' =>  '%' . 'message' . '%']);
        return $stmt->fetchAllAssociative();
    }
}
