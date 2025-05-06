<?php

namespace App\Repository;

use App\Entity\ParentSejour;
use App\Entity\User;
use App\Entity\Sejour;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use \DateTime;
use Doctrine\DBAL\Types\Types;

/**
 * @method ParentSejour|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParentSejour|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParentSejour[]    findAll()
 * @method ParentSejour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParentSejourRepository extends ServiceEntityRepository
{
    private $em;
    
    public function __construct(ManagerRegistry $registry, \Doctrine\ORM\EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct($registry, ParentSejour::class);
    }
    public function searshNbperpersonneSjour($value)
    {
        
        return $this->createQueryBuilder('t')
        ->select('Count( DISTINCT t.idParent)')
        ->andWhere('t.idSejour = :val')
        ->setParameter('val', $value)
        ->getQuery()
        ->useQueryCache(true)
        ->useResultCache(true, 3600)
        ->getSingleScalarResult()
        ;
    }
    
 // count nombre de connexion sejour partenaire sans date
    public function searshNbconnxionSejourParteanireNoDate($value)
    {
      //  $typ="Active";
      return $this->createQueryBuilder('k')
      ->select('COUNT(k.id)')
      ->innerJoin('k.idSejour',"ps")
      ->andWhere('ps.idPartenaire =:val')
      ->andWhere('k.payment =1')
      ->setParameter('val', $value)
      ->getQuery()
      ->getSingleScalarResult()
      ;
  }
    // count nombre de connexion sejour partenaire avec filtre date
  public function searshNbconnxionSejourParteanireWithDate($value,$datedebut,$datefin)
    {
      //  $typ="Active";
      return $this->createQueryBuilder('k')
      ->select('COUNT(k.id)')
      ->innerJoin('k.idSejour',"ps")
      ->where('ps.idPartenaire =:val')
      ->andWhere('k.payment =1')
      ->andWhere('(ps.dateDebutSejour BETWEEN :datedebut AND :datefin) OR (ps.dateFinSejour BETWEEN :datedebut AND :datefin) OR (ps.dateDebutSejour <= :datedebut AND ps.dateFinSejour >= :datefin) ')
      ->setParameter('datefin',$datefin, Types::DATETIME_MUTABLE)
      ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
      ->setParameter('val', $value)
      ->getQuery()
      ->getSingleScalarResult()
      ;
  }
  //nombre de connexion pour les  séjours avec filtre date 
    public function searshNbconnxionSejourAlldate($value)
    {
      //  $typ="Active";
      return $this->createQueryBuilder('k')
      ->select('COUNT(k.id)')
       ->innerJoin('k.idSejour',"ps")
      ->Where('ps.dateDebutSejour >=:val')
      ->andWhere('k.payment =1')
      ->setParameter('val', $value)
      ->getQuery()
      ->getSingleScalarResult()
      ;
  }
   //nombre de connexion pour les  séjours sans filtre date
   public function searshNbconnxionSejourAllnodate()
    {
      //  $typ="Active";
      return $this->createQueryBuilder('k')
      ->select('COUNT(k.id)')
      ->innerJoin('k.idSejour',"ps")
      ->innerJoin('ps.statut',"ref")
      ->Where('k.payment =1')
      ->andWhere('ref.id <> :val2')
      ->setParameter('val2', 14)
      ->getQuery()
      ->getSingleScalarResult()
      ;
  }
//nombre de connexion pour les  séjours Avec  filtre date
  public function searshNbconnxionSejourAvecnodate($date)
  {
    
    return $this->createQueryBuilder('k')
    ->select('COUNT(k.id)')
    ->innerJoin('k.idSejour',"ps")
    ->innerJoin('ps.statut',"ref")
    ->Where('k.payment =1')
    ->andWhere('ref.id <> :val2')
    ->andWhere('k.dateCreation >= :date')
    ->setParameter('date', $date)
    ->setParameter('val2', 14)
    ->getQuery()
    ->getSingleScalarResult()
    ;
}
 
    
   
public function searshNBcommandeParSejour($word)
{
  $result=  $this->createQueryBuilder('c')
        ->select('COUNT( DISTINCT c.id )')
        ->innerJoin('c.idSejour',"Sejour")
        ->innerJoin('Sejour.idEtablisment',"Etab")
        ->innerJoin('Sejour.statut',"ref")
        ->andWhere('Etab.typeetablisment LIKE :word')
        ->andWhere('ref.id <> :val2')
        ->setParameter('word', '%'.$word.'%')
        ->setParameter('val2', 14)
        ->getQuery()
        ->useQueryCache(true)
        ->useResultCache(true, 3600)
        ->getSingleScalarResult();
    if ($result == null) {return 0;} else {return $result;}
  }
  public function searshNBcommandeParSejourByDate($word,$date)
    {
      $result=  $this->createQueryBuilder('c')
            ->select('COUNT( DISTINCT c.id )')
            ->innerJoin('c.idSejour',"Sejour")
            ->innerJoin('Sejour.idEtablisment',"Etab")
            ->andWhere('Etab.typeetablisment LIKE :word')
            ->andWhere('c.dateCreation >= :date')
            ->andWhere('ref.id <> :val2')
            ->innerJoin('Sejour.statut',"ref")
            ->setParameter('word', '%'.$word.'%')
            ->setParameter('date', $date)
            ->setParameter('val2', 14)
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getSingleScalarResult();
        if ($result == null) {return 0;} else {return $result;}
      }
   
      public function  searshNBcnnxParSejour($type){
        $result=  $this->createQueryBuilder('c')
        ->select('COUNT( DISTINCT c.id )')
        ->innerJoin('c.idSejour',"Sejour")
        ->innerJoin('Sejour.idEtablisment',"Etab")
        ->innerJoin('Sejour.statut',"ref")
        ->andWhere('Etab.typeetablisment LIKE :word')
        ->andWhere('c.payment = :paym')
        ->andWhere('ref.id <> :val2')
        ->setParameter('word', '%'.$type.'%')
        ->setParameter('paym', 1)
        ->setParameter('val2', 14)
        ->getQuery()
        ->useQueryCache(true)
        ->useResultCache(true, 3600)
        ->getSingleScalarResult();
    if ($result == null) {return 0;} else {return $result;}
  
      
      }
   
      public function  searshNBcnnxParSejourByDate($type, $date){
        $result=  $this->createQueryBuilder('c')
        ->select('COUNT( DISTINCT c.id )')
        ->innerJoin('c.idSejour',"Sejour")
        ->innerJoin('Sejour.idEtablisment',"Etab")
        ->innerJoin('Sejour.statut',"ref")
        ->andWhere('Etab.typeetablisment LIKE :word')
        ->andWhere('c.payment = :paym')
        ->andWhere('ref.id <> :val2')
        ->andWhere('c.dateCreation >= :date')
        ->setParameter('word', '%'.$type.'%')
        ->setParameter('paym', 1)
        ->setParameter('val2', 14)
        ->setParameter('date', $date)
        ->getQuery()
        ->useQueryCache(true)
        ->useResultCache(true, 3600)
        ->getSingleScalarResult();
    if ($result == null) {return 0;} else {return $result;}
  
      }
      public function   SreachNombreConnxtionpourPartenaire($id){
        $result=  $this->createQueryBuilder('c')
        ->select('COUNT( DISTINCT c.id )')
        ->innerJoin('c.idSejour',"Sejour")
        ->innerJoin('Sejour.idEtablisment',"Part")
        ->andWhere('Part.id = :val')
        ->andWhere('c.payment = :paym')
        
        ->setParameter('val',$id)
        ->setParameter('paym', 1)
        ->getQuery()
        ->useQueryCache(true)
        ->useResultCache(true, 3600)
        ->getSingleScalarResult();
    if ($result == null) {return 0;} else {return $result;}
      
      
      }
    public function   SreachNombreConnxtionpourPartenaireV2($id){
        $result=  $this->createQueryBuilder('c')
            ->select('COUNT( DISTINCT c.id )')
            ->innerJoin('c.idSejour',"Sejour")
            ->innerJoin('Sejour.idEtablisment',"Part")
            ->andWhere('Part.id = :val')
            ->setParameter('val',$id)
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getSingleScalarResult();
        if ($result == null) {return 0;} else {return $result;}
    }
    public function   SerachCnxxDejaFacturer($id){
        
        $result=  $this->createQueryBuilder('c')
        ->select('COUNT(c)')
        ->Where('c.idSejour = :val')
        ->andWhere('c.dateModification is NOT  NULL')
        ->setParameter('val',$id)
       // ->setParameter('paym',)
        ->getQuery()
        ->useQueryCache(true)
        ->useResultCache(true, 3600)
        ->getSingleScalarResult();
    if ($result == null) {return 0;} else {return $result;}
      
      
      }
    public function  findParentDateBetween($datedebut ,$datefin){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\ParentSejour s,App\Entity\Sejour se  WHERE s.idSejour=se.id  and se.statut<>:statut and   s.dateCreation >= :datedebut AND s.dateCreation <= :datefin'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
        ->setParameter("statut",39);;
        // returns an array of Product objects
        return $query->getResult();
    }
    public function  findParentDateBetweenParPart($datedebut ,$datefin,$part){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\ParentSejour s,App\Entity\Sejour se WHERE (s.idSejour=se.id) and se.statut<>:statut and  s.dateCreation >= :datedebut AND s.dateCreation <= :datefin and se.idEtablisment=:etab '
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter(':etab',$part)
            ->setParameter("statut",39);;
        // returns an array of Product objects
        return $query->getResult();
    }
    
    public function  findParentDateBetweenParTypePart($datedebut ,$datefin,$type){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\ParentSejour s,App\Entity\Sejour se,App\Entity\Etablisment e WHERE (s.idSejour=se.id ) and se.statut<>:statut and  s.dateCreation >= :datedebut AND s.dateCreation <= :datefin AND(se.idEtablisment=e.id)and (e.typeetablisment=:type) '
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('type',$type)
            ->setParameter("statut",39);;
        // returns an array of Product objects
        return $query->getResult();
    }
    
    public function  FindeUSerParentActiveBetweenParPart($datedebut ,$datefin,$part){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\ParentSejour s,App\Entity\User p,App\Entity\Sejour se WHERE (s.idParent=p.id) and p.activatemail =:activmail and (s.idSejour=se.id) and  s.dateCreation >= :datedebut AND s.dateCreation <= :datefin and se.idEtablisment=:etab and se.statut<>:statut and(s.payment=1 or  se.codeSejour LIKE \'_F%\' ) '
            )->setParameter('activmail',1)
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('etab',$part)
            ->setParameter('statut',39);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function  FindeUSerParentActiveBetweenParTypePart($datedebut ,$datefin,$type){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\ParentSejour s,App\Entity\User p,App\Entity\Sejour se,App\Entity\Etablisment e  WHERE (s.idParent=p.id) and p.activatemail =:activmail and (s.idSejour=se.id) and se.statut<>:statut and  s.dateCreation >= :datedebut AND s.dateCreation <= :datefin and(s.payment=1 or  se.codeSejour LIKE \'_F%\' ) AND(se.idEtablisment=e.id)and (e.typeetablisment=:type)'
            )->setParameter('activmail',1)
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('type',$type)
            ->setParameter('statut',39);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function  caCnxJour(){
        $dateJ=new DateTime();
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\ParentSejour s  WHERE  s.dateCreation=:dateJ'
        )->setParameter('dateJ', $dateJ, Types::DATE_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function  caCnxJourParPart($part){
        $dateJ=new DateTime();
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\ParentSejour s,App\Entity\Sejour se WHERE (s.idSejour=se.id)and  s.dateCreation=:dateJ and se.idEtablisment=:etab '
        )->setParameter('dateJ', $dateJ, Types::DATE_MUTABLE)
            ->setParameter(':etab',$part);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function caConnexionTotal($datedebut ,$datefin){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\ParentSejour s ,App\Entity\Sejour se WHERE (s.dateCreation >= :datedebut AND s.dateCreation <= :datefin)and (s.idSejour=se.id and se.statut<>:statut )and(s.payment=1 or  se.codeSejour LIKE \'_F%\' )'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
        ->setParameter('statut',39);;
        return $query->getResult();
    }
    public function caConnexionPaye($datedebut ,$datefin){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\ParentSejour s  WHERE  (s.dateCreation >= :datedebut AND s.dateCreation <= :datefin)and (s.idSejour=se.id)and(s.payment=1)'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE);;
        return $query->getResult();
    }
    public function caConnexionFree($datedebut ,$datefin){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\ParentSejour s,App\Entity\Sejour se  WHERE  (s.dateCreation >= :datedebut AND s.dateCreation <= :datefin)and (s.idSejour=se.id)and(se.and se.codeSejour LIKE \'_F%\' )'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE);;
        return $query->getResult();
    }
    public function caConnexionTotalParPart($datedebut ,$datefin,$part){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\ParentSejour s,App\Entity\Sejour se  WHERE  (s.dateCreation >= :datedebut AND s.dateCreation <= :datefin)and (s.idSejour=se.id and se.statut<>:statut)and (se.idEtablisment=:etab)and(s.payment=1 or se.codeSejour LIKE \'_F%\' )'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('etab',$part)
            ->setParameter('statut',39);
        return $query->getResult();
    }
    public function caConnexionPayeParPart($datedebut ,$datefin,$part){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\ParentSejour s,App\Entity\Sejour se  WHERE  (s.dateCreation >= :datedebut AND s.dateCreation <= :datefin)and (s.idSejour=se.id)and (se.idEtablisment=:etab)and(s.payment=1)'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('etab',$part);;
        return $query->getResult();
    }
    public function caConnexionFreeParPart($datedebut ,$datefin,$part){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\ParentSejour s ,App\Entity\Sejour se WHERE  (s.dateCreation >= :datedebut AND s.dateCreation <= :datefin)and (s.idSejour=se.id)and(se.and se.codeSejour LIKE \'_F%\' )and (se.idEtablisment=:etab)'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('etab',$part);;
        return $query->getResult();
    }
    public function caConnexionTotalParTypePart($datedebut ,$datefin,$Type){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\ParentSejour s ,App\Entity\Etablisment e,App\Entity\Sejour se  WHERE  (s.dateCreation >= :datedebut AND s.dateCreation <= :datefin)and (s.idSejour=se.id) and (se.statut<>:statut)and(s.payment=1 or se.codeSejour LIKE \'_F%\' )AND(se.idEtablisment=e.id)and (e.typeetablisment=:type)'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('type',$Type)
            ->setParameter('statut',39)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE);;
        return $query->getResult();
    }
    public function caConnexionPayeParTypePart($datedebut ,$datefin,$Type){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\ParentSejour s ,App\Entity\Etablisment e,App\Entity\Sejour se  WHERE  (s.dateCreation >= :datedebut AND s.dateCreation <= :datefin)and (s.idSejour=se.id)and(s.payment=1)and(se.idEtablisment=e.id)and (e.typeetablisment=:type)'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('type',$Type)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE);;
        return $query->getResult();
    }
    public function caConnexionFreeParTypePart($datedebut ,$datefin,$Type){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\ParentSejour s ,App\Entity\Etablisment e ,App\Entity\Sejour se WHERE  (s.dateCreation >= :datedebut AND s.dateCreation <= :datefin)and (s.idSejour=se.id)and( se.codeSejour LIKE \'_F%\' )and(se.idEtablisment=e.id)and (e.typeetablisment=:type)'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('type',$Type)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE);;
        return $query->getResult();
    }
    public function rechercheNbCnxwxYeayMonth($id,$ym)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\ParentSejour s  WHERE s.idSejour=:id and  s.payment=:paye and s.dateCreation LIKE :ym')
            ->setParameter('paye',1)
            ->setParameter('ym','%'.$ym.'%')
            ->setParameter('id',$id);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function rechercheNbCnxFreewxYeayMonth($id,$ym)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\ParentSejour s  WHERE s.idSejour=:id  and s.dateCreation LIKE :ym '
        )
            ->setParameter('ym','%'.$ym.'%')
            ->setParameter('id',$id);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function searchNbCmdPayParPeriode($id,$ym)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\ParentSejour s  WHERE s.idSejour=:id  and s.dateCreation LIKE :ym and s.payment=:pay '
        )
            ->setParameter('pay',1)
            ->setParameter('ym','%'.$ym.'%')
            ->setParameter('id',$id);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function searchNbCmdTotalParPeriode($id,$ym)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\ParentSejour s  WHERE s.idSejour=:id  and s.dateCreation LIKE :ym '
        )
            ->setParameter('ym','%'.$ym.'%')
            ->setParameter('id',$id);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function liteCnxParPeriode($datedebut ,$datefin){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT DISTINCT(s.idSejour),s FROM App\Entity\ParentSejour s, App\Entity\Sejour se  WHERE  (s.dateCreation >= :datedebut AND s.dateCreation <= :datefin)and (s.idSejour=se.id)and se.statut<>:statutSejour and( se.codeSejour LIKE \'_F%\' )'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('statutSejour', 39);
        return $query->getResult();
    }
    public function RechercheAvancerliteCnxParPeriode($datedebut ,$datefin,$idSejour,$idPartenaire){
        $entityManager = $this->getEntityManager();
        $requete='SELECT DISTINCT(s.idSejour),s FROM App\Entity\ParentSejour s, App\Entity\Sejour se  WHERE (s.idSejour=se.id)and se.statut<>:statutSejour and( se.codeSejour LIKE \'_F%\' )';
            if($datedebut!="" && $datefin!="")
            {
                $requete.='and (s.dateCreation BETWEEN :datedebut AND :datefin)';
            }
            // if($idPartenaire!="")
            // {
            //     $requete.='and (se.idEtablisment=:idPartenaire)';
            // }
        // if($idSejour!="")
        // {
        //     $requete.='and (se.id=:idSejour)';
        // }
        $query = $entityManager->createQuery(
            $requete
        )->setParameter('statutSejour', 39);
             if($datedebut!=""&&$datefin!="")
             {$query->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE);}
        // if($idSejour!="")
        // {
        //     $query->setParameter('idSejour',$idSejour);
        // }
        // if($idPartenaire!="")
        // {
        //     $query->setParameter('idPartenaire',$idPartenaire);
        // }
        return $query->getResult();
    }
    public function searshNbperCnxParSjour($id)
    {  
        
        $Sejourfind = $this->em->getRepository(Sejour::class)->find($id);
        if ((substr($Sejourfind->getCodeSejour(), 1, 1) == 'P')) {
            $Sejour = $this->em->getRepository(ParentSejour::class)->findby(['idSejour' => $id, 'payment' => 1]);
        }
        elseif ((substr($Sejourfind->getCodeSejour(), 1, 1) == 'F')) {
            $Sejour = $this->em->getRepository(ParentSejour::class)->findby(['idSejour' => $id]);
        }
        $NB = sizeof($Sejour);
        return $NB;
    }
    public function findNbCnxParSejour($idSejour)
    {
       
        return $this->createQueryBuilder('ps')
        ->innerJoin('ps.idSejour',"sejour")
        ->where('sejour.id = :val')
        ->andWhere('ps.payment=1')
        ->setParameter('val', $idSejour)
        ->getQuery()
        ->useQueryCache(true)
        ->useResultCache(true, 3600)
        ->getSingleScalarResult();
    }
    public function getConnexionPayes($idSejour)
    {
       
        return $this->createQueryBuilder('ps')
        ->where('ps.idSejour = :val')
        ->andWhere('ps.payment=1')
        ->setParameter('val', $idSejour)
        ->getQuery()
        ->getResult();
    }
    public function parentsejour($years,$idParent)
    {
        $value="ROLE_PARENT";
        $entityManager = $this->getEntityManager();
        $result=[];
        foreach($years as $year)
        {
            $datedebut = date('01/01/'.$year);
           // Last day of the month.
            $datefin = date('31/12/'.$year);
         
            $datedebut = \DateTime::createFromFormat('d/m/Y', $datedebut)->setTime(0,0);
            $datefin = \DateTime::createFromFormat('d/m/Y', $datefin)->setTime(0,0);
            $listeParentSejour=  $this->createQueryBuilder('parent_sejour')
            ->innerJoin('parent_sejour.idSejour', "sejour")
            ->where('parent_sejour.idParent = :idParent')
            ->andwhere('sejour.dateDebutSejour BETWEEN :datedebut AND :datefin')
            ->setParameter('idParent', $idParent)
            ->setParameter('datefin',$datefin, Types::DATETIME_MUTABLE)
             ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->getQuery()
            ->getResult();
           $result[$year]['listeParentSejour']=$listeParentSejour;
    
            $nbreParentSejour= $this->createQueryBuilder('parent_sejour')
            ->select('Count(parent_sejour.id)')
            ->innerJoin('parent_sejour.idSejour', "sejour")
            ->where('parent_sejour.idParent = :idParent')
            ->andwhere('sejour.dateDebutSejour BETWEEN :datedebut AND :datefin')
            ->setParameter('idParent', $idParent)
            ->setParameter('datefin',$datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->getQuery()
            ->getSingleScalarResult();
            $result[$year]['nbreParentSejour']=$nbreParentSejour;
        }
        return $result ;
    }
}