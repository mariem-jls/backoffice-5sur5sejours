<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;
use Doctrine\DBAL\Types\Types;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findUtilisatursForCodeSejour()
    {
        $qb = $this->createQueryBuilder('s')
            ->select('s.id', 's.nom', 's.prenom', 's.email'); // Adjust this condition based on your schema and logic

        return $qb->getQuery()->getResult();
    }

    /**
     * @param string $role
     * @return User[]
     */
    public function findByRole(string $role): array
    {
        $query = $this->createQueryBuilder('u')
                    ->orderBy('u.nom', 'ASC');
        if ($role) {
                $query->andWhere('u.roles LIKE :val')
                            ->setParameter('val', '%'.$role.'%');
        }
        return $query->getQuery()->getResult();

        // select * from users where roles like '%ROLE_EDITOR%'
    }

    public function  FindeUSerParentBetween($datedebut ,$datefin){
        $value="ROLE_PARENT";
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\User s  WHERE  s.dateCreation >= :datedebut AND s.dateCreation <= :datefin AND s.roles like :role'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
        ->setParameter('role','%"'.$value.'"%');
        // returns an array of Product objects
        return $query->getResult();
    }
    public function  FindeUSerParentActiveBetween($datedebut ,$datefin){
        $value="ROLE_PARENT";
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\User s  WHERE  s.dateCreation >= :datedebut AND s.dateCreation <= :datefin AND s.roles like :role and s.activatemail =:activmail'
        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('activmail',1)
            ->setParameter('role','%"'.$value.'"%');
        // returns an array of Product objects
        return $query->getResult();
    }
     
     public function findByPartenaire():array
     {
        $value="ROLE_PARENT";
         $qb = $this->_em->createQueryBuilder();
         $qb->select('u')
             ->from($this->_entityName, 'u')
             ->where('u.roles LIKE :roles')
             ->setParameter('roles', '%"'.$value.'"%');
         return $qb->getQuery()->getResult();
     }
    public function findByAccompagnateurPlus():array
    {
        $value="ROLE_PARTENAIRE";
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
            ->from($this->_entityName, 'u')
            ->where('u.roles LIKE :roles')
            ->andWhere('u.accompaplus = :val3')
            ->setParameter('roles', '%"'.$value.'"%')
            ->setParameter('val3',"oui");
        return $qb->getQuery()->getResult();
    }
    
    
/*  function retourne la liste des parents qui se sont connectés à un séjour donné  */
    
    
     
     public function RechercheFiltre($email = '', $nom = '', $prenom = '', $activatemail = '', $dateDebutCreationCode = '', $dateFinCreationCode = '', $codeSejour = '', $CommandeParent = '') {
        $dateOne = new \DateTime($dateDebutCreationCode);
        $dateTwo = new \DateTime($dateFinCreationCode);
        $value ="ROLE_PARENT";
        if ($email != '') {
            $result = $this->_em->createQueryBuilder()
                    ->select('u')->from($this->_entityName, 'u')->where('u.id IS NOT NULL')
                    ->andWhere('u.roles LIKE :roles')
                    ->andWhere('u.email LIKE :email')
                    ->setParameter('roles', '%"'.$value.'"%')
                    ->setParameter('email', '%'.$email.'%');
                      
            return $result->getQuery()->getResult();
        }
        if (($email != '') && ($nom != '')) {
            $entityManager = $this->getEntityManager();
            $result = $entityManager->createQuery('SELECT U FROM App\Entity\User U  
                                                              WHERE U.email like :email
                                                                and U.nom like :nom and U.roles like :roles')
                    ->setParameter('email', '%' . $email . '%')
                    ->setParameter('nom', '%' . $nom . '%')
                    ->setParameter('roles', '%"'.$value.'"%');
            return $result->getResult();
        }
        if (($email != '') && ($prenom != '')) {
            $entityManager = $this->getEntityManager();
            $result = $entityManager->createQuery('SELECT U FROM App\Entity\User U  
                                                                              WHERE U.email like :email
                                                                                and U.prenom like :prenom and U.roles like :roles')
                    ->setParameter('email', '%' . $email . '%')
                    ->setParameter('prenom', '%' . $prenom . '%')
                    ->setParameter('roles', '%"'.$value.'"%');
            return $result->getResult();
        }
        if (($email != '') && ($nom != '') && ($prenom != '')) {
            $entityManager = $this->getEntityManager();
            $result = $entityManager->createQuery('SELECT U FROM App\Entity\User U  
                                                                              WHERE U.email like :email
                                                                                and U.nom like :nom
                                                                                and U.prenom like :prenom and U.roles like :roles')
                    ->setParameter('email', '%' . $email . '%')
                    ->setParameter('nom', '%' . $nom . '%')
                    ->setParameter('prenom', '%' . $prenom . '%')
                    ->setParameter('roles', '%"'.$value.'"%');
            return $result->getResult();
        }
        if (($email != '') && ($activatemail != '')) {
            $entityManager = $this->getEntityManager();
            $result = $entityManager->createQuery('SELECT U FROM App\Entity\User U  
                                                                      WHERE U.email like :email
                                                                        and U.activatemail = :activatemail and U.roles like :roles')
                    ->setParameter('email', '%' . $email . '%')
                    ->setParameter('activatemail', $activatemail)
                    ->setParameter('roles', '%"'.$value.'"%');
            return $result->getResult();
        }
        if (($email != '') && ($dateDebutCreationCode != '') && ($dateFinCreationCode)) {
            $entityManager = $this->getEntityManager();
            $result = $entityManager->createQuery('SELECT U FROM App\Entity\User U  
                                                                      WHERE U.email like :email
                                                                      and U.roles like :roles
                                                                      and U.dateCreation BETWEEN :dateDebutCreationCode AND :dateFinCreationCode ')
                    ->setParameter('email', '%' . $email . '%')
                    ->setParameter('activatemail', $activatemail)
                     ->setParameter('roles', '%"'.$value.'"%')
                    ->setParameter('dateDebutCreationCode', $dateOne, Types::DATETIME_MUTABLE)
                    ->setParameter('dateFinCreationCode', $dateTwo, Types::DATETIME_MUTABLE);
            ;
            return $result->getResult();
        }
        if (($nom != '') && ($dateDebutCreationCode != '') && ($dateFinCreationCode)) {
            $entityManager = $this->getEntityManager();
            $result = $entityManager->createQuery('SELECT U FROM App\Entity\User U  
                                                                      WHERE U.nom like :nom
                                                                        and U.roles like :roles
                                                                        and U.dateCreation BETWEEN :dateDebutCreationCode AND :dateFinCreationCode ')
                    ->setParameter('nom', '%' . $nom . '%')
                     ->setParameter('roles', '%"'.$value.'"%')
                    ->setParameter('dateDebutCreationCode', $dateOne, Types::DATETIME_MUTABLE)
                    ->setParameter('dateFinCreationCode', $dateTwo, Types::DATETIME_MUTABLE);
            ;
            return $result->getResult();
        }
        if (($prenom != '') && ($nom != '') && ($dateDebutCreationCode != '') && ($dateFinCreationCode)) {
            $entityManager = $this->getEntityManager();
            $result = $entityManager->createQuery('SELECT U FROM App\Entity\User U  
                                                                      WHERE U.nom like :nom
                                                                      and U.prenom like :prenom
                                                                      and U.roles like :roles
                                                                      and U.dateCreation BETWEEN :dateDebutCreationCode AND :dateFinCreationCode ')
                    ->setParameter('nom', '%' . $nom . '%')
                    ->setParameter('prenom', '%' . $prenom . '%')
                     ->setParameter('roles', '%"'.$value.'"%')
                    ->setParameter('dateDebutCreationCode', $dateOne, Types::DATETIME_MUTABLE)
                    ->setParameter('dateFinCreationCode', $dateTwo, Types::DATETIME_MUTABLE);
            ;
            return $result->getResult();
        }
        if (($prenom != '') && ($dateDebutCreationCode != '') && ($dateFinCreationCode)) {
            $entityManager = $this->getEntityManager();
            $result = $entityManager->createQuery('SELECT U FROM App\Entity\User U  
                                                                      WHERE U.prenom like :prenom
                                                                      and U.roles like :roles
                                                                      and U.dateCreation BETWEEN :dateDebutCreationCode AND :dateFinCreationCode ')
                    ->setParameter('prenom', '%' . $prenom . '%')
                     ->setParameter('roles', '%"'.$value.'"%')
                    ->setParameter('dateDebutCreationCode', $dateOne, Types::DATETIME_MUTABLE)
                    ->setParameter('dateFinCreationCode', $dateTwo, Types::DATETIME_MUTABLE);
            ;
            return $result->getResult();
        }
        if (($email != '') && ($nom != '') && ($dateDebutCreationCode != '') && ($dateFinCreationCode)) {
            $entityManager = $this->getEntityManager();
            $result = $entityManager->createQuery('SELECT U FROM App\Entity\User U  
                                                                      WHERE U.email like :email
                                                                      and U.nom like :nom
                                                                      and U.roles like :roles
                                                                      and U.dateCreation BETWEEN :dateDebutCreationCode AND :dateFinCreationCode ')
                    ->setParameter('email', '%' . $email . '%')
                    ->setParameter('nom', '%' . $nom . '%')
                     ->setParameter('roles', '%"'.$value.'"%')
                    ->setParameter('dateDebutCreationCode', $dateOne, Types::DATETIME_MUTABLE)
                    ->setParameter('dateFinCreationCode', $dateTwo, Types::DATETIME_MUTABLE);
            ;
            return $result->getResult();
        }
        if (($email != '') && ($nom != '') && ($prenom != '') && ($dateDebutCreationCode != '') && ($dateFinCreationCode)) {
            $entityManager = $this->getEntityManager();
            $result = $entityManager->createQuery('SELECT U FROM App\Entity\User U  
                                                                      WHERE U.email like :email
                                                                      and U.nom like :nom
                                                                      and U.prenom like :prenom
                                                                      and U.roles like :roles
                                                                      and U.dateCreation BETWEEN :dateDebutCreationCode AND :dateFinCreationCode ')
                    ->setParameter('email', '%' . $email . '%')
                    ->setParameter('nom', '%' . $nom . '%')
                    ->setParameter('prenom', '%' . $prenom . '%')
                     ->setParameter('roles', '%"'.$value.'"%')
                    ->setParameter('dateDebutCreationCode', $dateOne, Types::DATETIME_MUTABLE)
                    ->setParameter('dateFinCreationCode', $dateTwo, Types::DATETIME_MUTABLE);
            ;
            return $result->getResult();
        }
        if (($dateDebutCreationCode != '') && ($dateFinCreationCode != '')) {
            $result = $this->_em->createQueryBuilder()
                    ->select('u')->from($this->_entityName, 'u')->where('u.id IS NOT NULL')
                    ->andWhere('u.roles LIKE :roles')
                    ->andWhere('u.dateCreation BETWEEN :dateDebutCreationCode AND :dateFinCreationCode')
                     ->setParameter('roles', '%"'.$value.'"%')
                    ->setParameter('dateDebutCreationCode', $dateOne, Types::DATETIME_MUTABLE)
                    ->setParameter('dateFinCreationCode', $dateTwo, Types::DATETIME_MUTABLE);
            return $result->getQuery()->getResult();
        }
        if (($email != '') && ($nom != '') && ($activatemail != '')) {
            $entityManager = $this->getEntityManager();
            $result = $entityManager->createQuery('SELECT U FROM App\Entity\User U  
                                                                              WHERE U.email like :email
                                                                                and U.nom like :nom
                                                                                and U.roles like :roles
                                                                                and U.activatemail = :activatemail')
                    ->setParameter('email', '%' . $email . '%')
                    ->setParameter('nom', '%' . $nom . '%')
                     ->setParameter('roles', '%"'.$value.'"%')
                    ->setParameter('activatemail', $activatemail);
            return $result->getResult();
        }
        if (($email != '') && ($prenom != '') && ($activatemail != '')) {
            $entityManager = $this->getEntityManager();
            $result = $entityManager->createQuery('SELECT U FROM App\Entity\User U  
                                                                              WHERE U.email like :email
                                                                                and U.prenom like :prenom
                                                                                and U.roles like :roles
                                                                                and U.activatemail = :activatemail')
                    ->setParameter('email', '%' . $email . '%')
                    ->setParameter('prenom', '%' . $prenom . '%')
                     ->setParameter('roles', '%"'.$value.'"%')
                    ->setParameter('activatemail', $activatemail);
            return $result->getResult();
        }
        if (($email != '') && ($nom != '') && ($prenom != '') && ($activatemail != '')) {
            $entityManager = $this->getEntityManager();
            $result = $entityManager->createQuery('SELECT U FROM App\Entity\User U  
                                                                              WHERE U.email like :email
                                                                                and U.nom like :nom
                                                                                and U.prenom like :prenom
                                                                                and U.roles like :roles
                                                                                activatemail = :activatemail')
                    ->setParameter('email', '%' . $email . '%')
                    ->setParameter('nom', '%' . $nom . '%')
                    ->setParameter('prenom', '%' . $prenom . '%')
                     ->setParameter('roles', '%"'.$value.'"%')
                    ->setParameter('activatemail', '%' . $activatemail . '%');
            return $result->getResult();
        }
        if ($nom != '') {
            $result = $this->_em->createQueryBuilder()
                    ->select('u')->from($this->_entityName, 'u')->where('u.id IS NOT NULL')
                    ->andWhere('u.roles LIKE :roles')
                    ->andWhere('u.nom LIKE :nom')
                     ->setParameter('roles', '%"'.$value.'"%')
                    ->setParameter('nom', '%' . $nom . '%');
            return $result->getQuery()->getResult();
        }
        if ($prenom != '') {
            $result = $this->_em->createQueryBuilder()
                    ->select('u')->from($this->_entityName, 'u')->where('u.id IS NOT NULL')
                    ->andWhere('u.roles LIKE :roles')
                    ->andWhere('u.prenom LIKE :prenom')
                     ->setParameter('roles', '%"'.$value.'"%')
                    ->setParameter('prenom', '%' . $prenom . '%');
            return $result->getQuery()->getResult();
        }
        if ($codeSejour != '') {
            $entityManager = $this->getEntityManager();
            $result = $entityManager->createQuery('SELECT U FROM App\Entity\ParentSejour  PS ,
                                                                          App\Entity\Sejour  S,
                                                                          App\Entity\User U 
                                                                           WHERE PS.idSejour=S.id 
                                                                           and PS.idParent=U.id 
                                                                           and U.roles LIKE :roles
                                                                           and S.codeSejour LIKE :codeSejour')
                    ->setParameter('roles', '%"'.$value.'"%')
                    ->setParameter('codeSejour', '%' . $codeSejour . '%');
            return $result->getResult();
        }
        if ($CommandeParent != '') {
            $entityManager = $this->getEntityManager();
            $result = $entityManager->createQuery('SELECT U FROM App\Entity\User U ,
                                                                          App\Entity\Commande C,
                                                                          App\Entity\ParentSejour PS 
                                                                           WHERE C.idUser=U.id 
                                                                           and PS.idParent=U.id
                                                                           and U.roles LIKE :roles
                                                                           and C.numComande LIKE :CommandeParent')
                    ->setParameter('roles', '%"'.$value.'"%')
                    ->setParameter('CommandeParent', '%' . $CommandeParent . '%');
            return $result->getResult();
        }
        if (($CommandeParent != '') && ($email != '')) {
            $entityManager = $this->getEntityManager();
            $result = $entityManager->createQuery('SELECT U FROM App\Entity\User U ,
                                                                          App\Entity\Commande C,
                                                                          App\Entity\ParentSejour PS 
                                                                           WHERE C.idUser=U.id 
                                                                           and PS.idParent=U.id 
                                                                           and U.email like :email
                                                                           and U.roles LIKE :roles
                                                                           and C.numComande LIKE :CommandeParent')
                    ->setParameter('email', '%' . $email . '%')
                    ->setParameter('roles', '%"'.$value.'"%')
                    ->setParameter('CommandeParent', '%' . $CommandeParent . '%');
            return $result->getResult();
        }
        if (($CommandeParent != '') && ($nom != '')) {
            $entityManager = $this->getEntityManager();
            $result = $entityManager->createQuery('SELECT U FROM App\Entity\User U ,
                                                                          App\Entity\Commande C,
                                                                          App\Entity\ParentSejour PS 
                                                                           WHERE C.idUser=U.id 
                                                                           and PS.idParent=U.id 
                                                                           and U.nom like :nom
                                                                           and U.roles LIKE :roles
                                                                           and C.numComande LIKE :CommandeParent')
                    ->setParameter('nom', '%' . $nom . '%')
                    ->setParameter('roles', '%"'.$value.'"%')
                    ->setParameter('CommandeParent', '%' . $CommandeParent . '%');
            return $result->getResult();
        }
        if (($CommandeParent != '') && ($nom != '') && ($email != '')) {
            $entityManager = $this->getEntityManager();
            $result = $entityManager->createQuery('SELECT U FROM App\Entity\User U ,
                                                                          App\Entity\Commande C,
                                                                          App\Entity\ParentSejour PS 
                                                                           WHERE C.idUser=U.id 
                                                                           and PS.idParent=U.id 
                                                                           and U.email like :email
                                                                           and U.nom like :nom
                                                                           and U.roles LIKE :roles
                                                                           and C.numComande LIKE :CommandeParent')
                    ->setParameter('email', '%' . $email . '%')
                    ->setParameter('nom', '%' . $nom . '%')
                    ->setParameter('roles', '%"'.$value.'"%')
                    ->setParameter('CommandeParent', '%' . $CommandeParent . '%');
            return $result->getResult();
        }
        if (($CommandeParent != '') && ($nom != '') && ($email != '') && ($prenom != '')) {
            $entityManager = $this->getEntityManager();
            $result = $entityManager->createQuery('SELECT U FROM App\Entity\User U ,
                                                                          App\Entity\Commande C,
                                                                          App\Entity\ParentSejour PS 
                                                                           WHERE C.idUser=U.id 
                                                                           and PS.idParent=U.id 
                                                                           and U.email like :email
                                                                           and U.nom like :nom
                                                                           and U.prenom like :prenom
                                                                           and U.roles LIKE :roles
                                                                           and C.numComande LIKE :CommandeParent')
                    ->setParameter('email', '%' . $email . '%')
                    ->setParameter('nom', '%' . $nom . '%')
                    ->setParameter('prenom', '%' . $prenom . '%')
                    ->setParameter('roles', '%"'.$value.'"%')
                    ->setParameter('CommandeParent', '%' . $CommandeParent . '%');
            return $result->getResult();
        }
        if (($CommandeParent != '') && ($nom != '') && ($email != '') && ($prenom != '') && ($dateDebutCreationCode != '') && ($dateFinCreationCode != '')) {
            $entityManager = $this->getEntityManager();
            $result = $entityManager->createQuery('SELECT U FROM App\Entity\User U ,
                                                                          App\Entity\Commande C,
                                                                          App\Entity\ParentSejour PS 
                                                                           WHERE C.idUser=U.id 
                                                                           and PS.idParent=U.id 
                                                                           and U.email like :email
                                                                           and U.nom like :nom
                                                                           and U.prenom like :prenom
                                                                           and U.roles LIKE :roles
                                                                           and U.dateCreation BETWEEN :dateDebutCreationCode AND :dateFinCreationCode
                                                                           and C.numComande LIKE :CommandeParent')
                    ->setParameter('email', '%' . $email . '%')
                    ->setParameter('nom', '%' . $nom . '%')
                    ->setParameter('prenom', '%' . $prenom . '%')
                    ->setParameter('roles', '%"'.$value.'"%')
                    ->setParameter('dateDebutCreationCode', $dateOne, Types::DATETIME_MUTABLE)
                    ->setParameter('dateFinCreationCode', $dateTwo, Types::DATETIME_MUTABLE)
                    ->setParameter('CommandeParent', '%' . $CommandeParent . '%');
            return $result->getResult();
        }
        if (($CommandeParent != '') && ($dateDebutCreationCode != '') && ($dateFinCreationCode != '')) {
            $entityManager = $this->getEntityManager();
            $result = $entityManager->createQuery('SELECT U FROM App\Entity\User U ,
                                                                          App\Entity\Commande C,
                                                                          App\Entity\ParentSejour PS 
                                                                           WHERE C.idUser=U.id 
                                                                           and PS.idParent=U.id 
                                                                           and U.roles LIKE :roles
                                                                           and U.dateCreation BETWEEN :dateDebutCreationCode AND :dateFinCreationCode
                                                                           and C.numComande LIKE :CommandeParent')
                    ->setParameter('roles', '%"'.$value.'"%')
                    ->setParameter('dateDebutCreationCode', $dateOne, Types::DATETIME_MUTABLE)
                    ->setParameter('dateFinCreationCode', $dateTwo, Types::DATETIME_MUTABLE)
                    ->setParameter('CommandeParent', '%' . $CommandeParent . '%');
            return $result->getResult();
        }
        if (($CommandeParent != '') && ($nom != '') && ($email != '') && ($dateDebutCreationCode != '') && ($dateFinCreationCode != '')) {
            $entityManager = $this->getEntityManager();
            $result = $entityManager->createQuery('SELECT U FROM App\Entity\User U ,
                                                                          App\Entity\Commande C,
                                                                          App\Entity\ParentSejour PS 
                                                                           WHERE C.idUser=U.id 
                                                                           and PS.idParent=U.id 
                                                                           and U.email like :email
                                                                           and U.nom like :nom
                                                                           and U.roles LIKE :roles
                                                                           and U.dateCreation BETWEEN :dateDebutCreationCode AND :dateFinCreationCode
                                                                           and C.numComande LIKE :CommandeParent')
                    ->setParameter('email', '%' . $email . '%')
                    ->setParameter('nom', '%' . $nom . '%')
                    ->setParameter('roles', '%"'.$value.'"%')
                    ->setParameter('dateDebutCreationCode', $dateOne, Types::DATETIME_MUTABLE)
                    ->setParameter('dateFinCreationCode', $dateTwo, Types::DATETIME_MUTABLE)
                    ->setParameter('CommandeParent', '%' . $CommandeParent . '%');
            return $result->getResult();
        }
        if (($CommandeParent != '') && ($email != '') && ($dateDebutCreationCode != '') && ($dateFinCreationCode != '')) {
            $entityManager = $this->getEntityManager();
            $result = $entityManager->createQuery('SELECT U FROM App\Entity\User U ,
                                                                          App\Entity\Commande C,
                                                                          App\Entity\ParentSejour PS 
                                                                           WHERE C.idUser=U.id 
                                                                           and PS.idParent=U.id 
                                                                           and U.email like :email
                                                                           and U.roles LIKE :roles
                                                                           and U.dateCreation BETWEEN :dateDebutCreationCode AND :dateFinCreationCode
                                                                           and C.numComande LIKE :CommandeParent')
                    ->setParameter('email', '%' . $email . '%')
                    ->setParameter('roles', '%"'.$value.'"%')
                    ->setParameter('dateDebutCreationCode', $dateOne, Types::DATETIME_MUTABLE)
                    ->setParameter('dateFinCreationCode', $dateTwo, Types::DATETIME_MUTABLE)
                    ->setParameter('CommandeParent', '%' . $CommandeParent . '%');
            return $result->getResult();
        }
        if (($CommandeParent != '') && ($nom != '') && ($prenom != '') && ($dateDebutCreationCode != '') && ($dateFinCreationCode != '')) {
            $entityManager = $this->getEntityManager();
            $result = $entityManager->createQuery('SELECT U FROM App\Entity\User U ,
                                                                          App\Entity\Commande C,
                                                                          App\Entity\ParentSejour PS 
                                                                           WHERE C.idUser=U.id 
                                                                           and PS.idParent=U.id 
                                                                           and U.email like :email
                                                                           and U.prenom like :prenom
                                                                           and U.roles LIKE :roles
                                                                           and U.dateCreation BETWEEN :dateDebutCreationCode AND :dateFinCreationCode
                                                                           and C.numComande LIKE :CommandeParent')
                    ->setParameter('email', '%' . $email . '%')
                    ->setParameter('prenom', '%' . $prenom . '%')
                     ->setParameter('roles', '%"'.$value.'"%')
                    ->setParameter('dateDebutCreationCode', $dateOne, Types::DATETIME_MUTABLE)
                    ->setParameter('dateFinCreationCode', $dateTwo, Types::DATETIME_MUTABLE)
                    ->setParameter('CommandeParent', '%' . $CommandeParent . '%');
            return $result->getResult();
        }
        if (($CommandeParent != '') && ($codeSejour)) {
            $entityManager = $this->getEntityManager();
            $result = $entityManager->createQuery('SELECT U FROM App\Entity\User U ,
                                                                          App\Entity\ParentSejour PS ,
                                                                          App\Entity\Commande C ,
                                                                          App\Entity\Sejour S
                                                                           WHERE C.idUser=U.id 
                                                                           and U.id=PS.idParent
                                                                           and  PS.idSejour=S.id
                                                                           and U.roles LIKE :roles
                                                                           and C.numComande LIKE :CommandeParent
                                                                           and S.codeSejour LIKE :codeSejour')
                    ->setParameter('roles', '%"'.$value.'"%')
                    ->setParameter('CommandeParent', '%' . $CommandeParent . '%')
                    ->setParameter('codeSejour', '%' . $codeSejour . '%');
            return $result->getResult();
        }
        if ($activatemail != '') {
            $result = $this->_em->createQueryBuilder()
                    ->select('u')->from($this->_entityName, 'u')->where('u.id IS NOT NULL')
                    ->andWhere('u.activatemail = :activatemail')
                    ->andWhere('u.roles LIKE :roles')
                    ->setParameter('activatemail', $activatemail)
                    ->setParameter('roles', '%"'.$value.'"%');
            return $result->getQuery()->getResult();
        }
        if (($dateDebutCreationCode != '') && ($dateFinCreationCode != '')) {
            $result = $this->_em->createQueryBuilder()
                    ->select('u')->from($this->_entityName, 'u')->where('u.id IS NOT NULL')
                    ->andWhere('u.roles LIKE :roles')
                    ->andWhere('u.dateCreation  BETWEEN :dateDebutCreationCode AND :dateFinCreationCode')
                    ->setParameter('dateDebutCreationCode', $dateOne, Types::DATETIME_MUTABLE)
                    ->setParameter('dateFinCreationCode', $dateTwo, Types::DATETIME_MUTABLE)
                    ->setParameter('roles', '%"'.$value.'"%');
            return $result->getQuery()->getResult();
        }
    }
    public function listeConnexionUsersSejour($idSejour) {
      
        $value ="ROLE_PARENT";
        $activatemail = '1' ;
      
            $entityManager = $this->getEntityManager();
            $result = $entityManager->createQuery('SELECT U FROM App\Entity\ParentSejour  PS ,
                                                                        
                                                                          App\Entity\User U 
                                                                           WHERE PS.idSejour= idSejour
                                                                           and PS.idParent=U.id 
                                                                           and PS.activatemail= activatemail
                                                                           and U.roles LIKE :roles
                                                                           ')
                    ->setParameter('roles', '%"'.$value.'"%')
                    ->setParameter('activatemail', '%"'.$activatemail.'"%')
                    ->setParameter('idSejour', '%' . $idSejour . '%');
            return $result->getResult();
        
       
    
    }
    public function RechercheFiltreVERSION2($email, $nom, $prenom, $activatemail, $dateDebutCreationCode, $dateFinCreationCode , $codeSejour, $CommandeParent)
    {
        $dateOne = new \DateTime($dateDebutCreationCode);
        $dateTwo = new \DateTime($dateFinCreationCode);
        $value ="ROLE_PARENT";
        $result = $this->_em->createQueryBuilder()
            ->select('u')->from($this->_entityName, 'u')
            ->where('u.id IS NOT NULL')
            ->andWhere('u.roles LIKE :roles');
        if (($dateDebutCreationCode != '') && ($dateFinCreationCode != '')) {
            $result->andWhere('u.dateCreation  BETWEEN :dateDebutCreationCode AND :dateFinCreationCode');
        }
            $result->andWhere('u.activatemail = :activatemail');
             if($prenom!=""){ $result->andWhere('u.prenom LIKE :prenom');}
              if($nom!=""){$result->andWhere('u.nom LIKE :nom');}
               if($email!=""){$result->andWhere('u.email LIKE :email');}
        if (($dateDebutCreationCode != '') && ($dateFinCreationCode != '')) {
        $result->setParameter('dateDebutCreationCode', $dateOne, Types::DATETIME_MUTABLE)
            ->setParameter('dateFinCreationCode', $dateTwo, Types::DATETIME_MUTABLE);}
        if($activatemail==1){$result->setParameter('activatemail', 1);}else{$result->setParameter('activatemail', null);}
            if($email!=""){$result->setParameter('email', '%' . $email . '%');}
            if($nom!=""){$result->setParameter('nom', '%' . $nom . '%');}
          if($prenom!=""){ $result ->setParameter('prenom', '%' . $prenom . '%');}
        $result->setParameter('roles', '%"'.$value.'"%');
        return $result->getQuery()->getResult();
    }
    public function RechercheFiltreUsersSejour($codeSejour)
    {
        $value ="ROLE_PARENT";
        $activatemail =1;
        $entityManager = $this->getEntityManager();
        $requete=    'SELECT U FROM App\Entity\User U, App\Entity\ParentSejour PS ,App\Entity\Sejour S
          WHERE U.id=PS.idParent and  PS.idSejour=S.id and S.codeSejour LIKE :codeSejour  and U.roles LIKE :roles and U.activatemail = :activatemail
            ';
   
            $result = $entityManager->createQuery($requete);
    
        return $result->getResult();
    }
    public function RechercheFiltreVERSION3($email, $nom, $prenom, $activatemail, $dateDebutCreationCode, $dateFinCreationCode , $codeSejour, $CommandeParent)
    {
        $dateOne = new \DateTime($dateDebutCreationCode);
        $dateTwo = new \DateTime($dateFinCreationCode);
        $value ="ROLE_PARENT";
        $entityManager = $this->getEntityManager();
$requeteFinal="";
if($codeSejour!="" && $CommandeParent!="")
{   $requete=    'SELECT U FROM App\Entity\User U,
                                                                         App\Entity\ParentSejour PS ,
                                                                          App\Entity\Commande C ,
                                                                          App\Entity\Sejour S
                                                                           WHERE C.idUser=U.id 
                                                                           and U.id=PS.idParent
                                                                           and  PS.idSejour=S.id
                                                                         and U.roles LIKE :roles
                                                                       
                                                                             ';}
        if($codeSejour=="" && $CommandeParent!="")
        {   $requete=    'SELECT U FROM App\Entity\User U,
                                                                         App\Entity\ParentSejour PS ,
                                                                          App\Entity\Commande C 
                                                                      
                                                                           WHERE C.idUser=U.id 
                                                                          
                                                                         and U.roles LIKE :roles
                                                                       
                                                                             ';}
        if($codeSejour!="" && $CommandeParent=="")
        {   $requete=    'SELECT U FROM App\Entity\User U,
                                                                         App\Entity\ParentSejour PS ,
                                                                 
                                                                          App\Entity\Sejour S
                                                                         
                                                                          WHERE U.id=PS.idParent
                                                                           and  PS.idSejour=S.id
                                                                         and U.roles LIKE :roles
                                                                       
                                                                             ';}
        if($codeSejour=="" && $CommandeParent=="")
        {   $requete=    'SELECT U FROM App\Entity\User U
                                          
                                                                         WHERE U.roles LIKE :roles
                                                                       
                                                                             ';}
    if($activatemail==1)
    {
        $requete=$requete.' and U.activatemail = :activatemail ';
    }
        if($activatemail==2 )
        {
            $requete=$requete.' and U.activatemail IS NULL ';
        }
        if($nom!="")
        {
            $requete=$requete.' and U.nom LIKE :nom ';
        }
        if($prenom!="")
        {
            $requete=$requete.' and U.prenom LIKE :prenom ';
        }
        if($email!="")
        {
            $requete=$requete.' and U.email LIKE :email ';
        }
        if (($dateDebutCreationCode != '') && ($dateFinCreationCode != '')) {
            $requete=$requete.' and U.dateCreation  BETWEEN :dateDebutCreationCode AND :dateFinCreationCode ';
        }
        if($codeSejour!="")
        {
            $requete=$requete.' and S.codeSejour LIKE :codeSejour ';
        }
        if($CommandeParent!="")
        {
            $requete=$requete.' and C.numComande LIKE :CommandeParent ';
        }
//
//        var_dump($requete);
            $result = $entityManager->createQuery($requete);
        //Test sur nom
            $result->setParameter('roles', '%"'.$value.'"%');
           if($CommandeParent!=""){$result->setParameter('CommandeParent', '%' . $CommandeParent . '%');}
            if($codeSejour!=""){$result->setParameter('codeSejour', '%' . $codeSejour . '%');}
        if (($dateDebutCreationCode != '') && ($dateFinCreationCode != '')) {
            $result->setParameter('dateDebutCreationCode', $dateOne, Types::DATETIME_MUTABLE)
                ->setParameter('dateFinCreationCode', $dateTwo, Types::DATETIME_MUTABLE);}
        if($activatemail==1){$result->setParameter('activatemail', 1);}
        if($email!=""){$result->setParameter('email', '%' . $email . '%');}
        if($nom!=""){$result->setParameter('nom', '%' . $nom . '%');}
        if($prenom!=""){ $result ->setParameter('prenom', '%' . $prenom . '%');}
//$result->getMaxResults(5);
        return $result->getResult();
    }
    function RechercheFiltreAccompagnateurPlus($email, $nom, $prenom, $activatemail, $dateDebutCreationCode, $dateFinCreationCode)
    {
        $dateOne = new \DateTime($dateDebutCreationCode);
        $dateTwo = new \DateTime($dateFinCreationCode);
        $value ="ROLE_PARTENAIRE";
        $entityManager = $this->getEntityManager();
        $requeteFinal="";
   $requete=    'SELECT U FROM App\Entity\User U WHERE U.roles LIKE :roles and  U.accompaplus = :val3';
        if($activatemail!="")
        {
   $requete=$requete.' and U.nometablisment LIKE :activatemail ';
        }
        if($nom!="")
        {
            $requete=$requete.' and U.nom LIKE :nom ';
        }
        if($prenom!="")
        {
            $requete=$requete.' and U.prenom LIKE :prenom ';
        }
        if($email!="")
        {
            $requete=$requete.' and U.email LIKE :email ';
        }
        if (($dateDebutCreationCode != '') && ($dateFinCreationCode != '')) {
            $requete=$requete.' and U.dateCreation  BETWEEN :dateDebutCreationCode AND :dateFinCreationCode ';
        }
        
        $result = $entityManager->createQuery($requete);
        $result->setParameter('roles', '%"'.$value.'"%');
        $result->setParameter('val3', "oui");
        if (($dateDebutCreationCode != '') && ($dateFinCreationCode != '')) {
            $result->setParameter('dateDebutCreationCode', $dateOne, Types::DATETIME_MUTABLE)
                ->setParameter('dateFinCreationCode', $dateTwo, Types::DATETIME_MUTABLE);}
        if($activatemail!=""){$result->setParameter('activatemail', '%' . $activatemail . '%');}
        if($email!=""){$result->setParameter('email', '%' . $email . '%');}
        if($nom!=""){$result->setParameter('nom', '%' . $nom . '%');}
        if($prenom!=""){ $result ->setParameter('prenom', '%' . $prenom . '%');}
        return $result->getResult();
    }
    public function VerifierAddresseEmailPartenaire($email,$value)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
            ->from($this->_entityName, 'u')
            ->where('u.roles LIKE :roles')
            ->andWhere('u.email = :email')
            ->setParameter('roles', '%"'.$value.'"%')
            ->setParameter('email',$email);
        return $qb->getQuery()->getOneOrNullResult();
    }
    
    public function checkEmail($email){
         $mails = $this->_em->createQueryBuilder();
                  $mails ->select('u')
              ->from($this->_entityName, 'u')
              ->where("u.email = :email")
            ->setParameter('email',$email)
         ->getQuery()
         ->getResult();
          $nbmails=count($mails ->getQuery()->getResult());
          return($nbmails);
    }
    public function checkCompteToActivate(){
        $value="ROLE_PARENT";
        $date=new DateTime();
//        $dtoday = date('Y-m-d 00:00:00');
//        $dtoday   =(new \DateTime())->format('Y-m-d 00:00:00');
//        $ftoday =(new \DateTime())->format('Y-m-d 23:59:59');
        $dtoday   =(new \DateTime())->format('2021-08-16 00:00:00');
        $ftoday =(new \DateTime())->format('2021-08-16 23:59:59');
$dateD = strtotime($dtoday);
$dateDebut= date('Y-m-d h:i:s', $dateD);
        $dateF = strtotime($ftoday);
        $dateFin= date('Y-m-d h:i:s', $dateF);
        $mails = $this->_em->createQueryBuilder();
        $mails ->select('u')
            ->from($this->_entityName, 'u')
            ->where('u.activatemail IS  NULL')
            ->andWhere('u.roles LIKE :roles')
            ->andWhere('u.dateCreation BETWEEN :datedebut AND :datefin')
            ->setParameter('roles', '%"'.$value.'"%')
            ->setParameter('datedebut',$dtoday)
            ->setParameter('datefin', $ftoday);
        return $mails->getQuery()->getResult();
    }
    /*
    public function findUtilisatursForCodeSejour():array
    {
       $value="ROLE_PARENT";
      
            $entityManager = $this->getEntityManager();
    
            $query = $entityManager->createQuery(
                'SELECT s.id,s.nom,s.email,s.prenom FROM App\Entity\User s where s.activatemail =:activmail AND s.roles like :roles ORDER BY s.nom ASC'
            )->setParameter('roles', '%"'.$value.'"%')
            ->setParameter('activmail', 1);
         
    
            // returns an array of Product objects
            return $query->getResult();
        
    }
    */
    public function listeProfs()
    {
        $value="ROLE_ACC";
      
            $entityManager = $this->getEntityManager();
    
            $query = $entityManager->createQuery(
                'SELECT s FROM App\Entity\User s where s.idFonction =:idFonction AND s.roles like :roles'
            )->setParameter('roles', '%"'.$value.'"%')
            ->setParameter('idFonction', 3);
         
    
            // returns an array of Product objects
            return $query->getResult();
    }
    public function  allParentsActives(){
        $value="ROLE_PARENT";
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\User s  WHERE s.email is not null AND s.roles like :role and s.activatemail =:activmail'
        )   ->setParameter('activmail',1)
            ->setParameter('role','%"'.$value.'"%');
        // returns an array of Product objects
        return $query->getResult();
    }

}
