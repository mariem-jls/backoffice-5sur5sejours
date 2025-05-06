<?php

namespace App\Repository;

use App\Entity\Etablisment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Etablisment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Etablisment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Etablisment[]    findAll()
 * @method Etablisment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtablismentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etablisment::class);
    }
    /*public function findPrtenaireNotNull():array
     {
         $qb = $this->_em->createQueryBuilder();
         $qb->select('u')
             ->from($this->_entityName, 'u')
             ->where('u.User_id != :null')->setParameter('NULL', 'N;');
         return $qb->getQuery()->getResult();
     }*/
    public function  findFiltredEtab()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT e FROM App\Entity\Etablisment e WHERE e.user  IS NOT NULL  and  e.nometab != \'\' and e.nometab IS NOT NULL and (e.delated!=:del OR e.delated IS NULL) '
        )->setParameter('del', 1);;
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findNotDeletedEtab()
    {
        return $this->createQueryBuilder('s')
            # ->innerJoin('s.user',"u")
            ->Where('s.delated != :val3 or s.delated is null')
            ->setParameter('val3', '1')
            ->getQuery()
            ->getResult()
        ;
    }
    public function findByEtablissementPartenaire()
    {
        $val = "oui";
        return $this->createQueryBuilder('s')
            ->innerJoin('s.user', "u")
            ->Where('u.accompaplus != :val3')
            ->setParameter('val3', $val)
            ->getQuery()
            ->getResult()
        ;
    }
    public function findByOrder()
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
    public function RechercheFiltreEtablisement($nomEtab, $idEtab, $typeEtab, $formeJurdique)
    {
        $entityManager = $this->getEntityManager();
        $requeteFinal = "";

        if ($formeJurdique != "") {
            $requete =    'SELECT U FROM App\Entity\Etablisment U, App\Entity\User AS  A where U.delated is NULL and U.user = A.id ';
        } else {
            $requete =    'SELECT U FROM App\Entity\Etablisment U where U.delated is NULL';
        }
        if ($nomEtab != "") {
            $requete = $requete . ' and U.nometab LIKE :nometab ';
        }
        if ($idEtab != "") {
            $requete = $requete . ' and U.id = :identifiant ';
        }
        if ($typeEtab != "") {
            $requete = $requete . ' and U.typeetablisment LIKE :typeEtab ';
        }

        if ($formeJurdique != "") {
            $requete = $requete . ' and  A.nom LIKE :formatEtablisement or A.prenom LIKE :formatEtablisement';
        }

        $result = $entityManager->createQuery($requete);

        if ($nomEtab != "") {
            $result->setParameter('nometab', '%' . $nomEtab . '%');
        }
        if ($formeJurdique != "") {
            $result->setParameter('formatEtablisement', '%' . $formeJurdique . '%');
        }
        if ($idEtab != "") {
            $result->setParameter('identifiant', $idEtab);
        }
        if ($typeEtab != "") {
            if ($typeEtab == 1) {
                $type = "ECOLES/AUTRES";
            }
            if ($typeEtab == 2) {
                $type = "PARTENAIRES/VOYAGISTES";
            }
            if ($typeEtab == 3) {
                $type = "CSE";
            }
            $result->setParameter('typeEtab', '%' . $type . '%');
        }
        //dd($result->getResult());
        //$result->getMaxResults(5)

        return $result->getResult();
    }
    public function listeEtablisementsOntSejour()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT DISTINCT(s.idEtablisment),e.nometab FROM  App\Entity\Sejour s,App\Entity\Etablisment e  WHERE   (s.idEtablisment=e.id)'
        );
        return $query->getResult();
    }
}
