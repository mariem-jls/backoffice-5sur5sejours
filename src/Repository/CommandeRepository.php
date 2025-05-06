<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use \DateTime;
use App\Repository\SejourRepository;
use App\Entity\Sejour;
use Doctrine\DBAL\Types\Types;

/**
 * @method Commande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commande[]    findAll()
 * @method Commande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }
    public function searshTypeEmth()
    {
        $value = "payer";
        return $this->createQueryBuilder('t')
            ->select('SUM(t.montantht)')
            ->innerJoin('t.statut', "ref")
            ->Where('ref.libiller LIKE :word')
            ->setParameter('word', $value)
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getSingleScalarResult();
    }
    public function searshTypeEmthByDate($date)
    {
        $value = "payer";
        $result = $this->createQueryBuilder('t')
            ->select('SUM(t.montantht)')
            ->innerJoin('t.statut', "ref")
            ->Where('ref.libiller LIKE :word')
            ->Where('t.dateCreateCommande >= :val')
            //->innerJoin('t.statut',"ref")

            //->setParameter('val', $value)
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
    function GetCommandeExcel()
    {
        return $this->createQueryBuilder('s')
            ->Where('s.statut =:val1 OR s.statut=:val2')
            ->andWhere('s.montantrth != :val3')
            ->setParameter('val1', 33)
            ->setParameter('val2', 38)
            ->setParameter('val3', 2.9)
            ->getQuery()
            ->getResult();
    }
    function getCommandeFromSejour($idSejour)
    {
        return $this->createQueryBuilder('s')
            ->Where('s.idSejour =:val3')
            ->andWhere('s.statut =:val1 OR s.statut=:val2')

            ->setParameter('val1', 33)
            ->setParameter('val2', 38)
            ->setParameter('val3', $idSejour)
            ->getQuery()
            ->getResult();
    }
    function findCommandeQuiOntDateCmdNull()
    {
        return $this->createQueryBuilder('s')
            ->Where('s.statut =:val1 OR s.statut=:val2')
            ->andWhere('s.dateCreateCommande IS NULL')
            ->andWhere('s.montantrth = :val3')
            ->setParameter('val1', 33)
            ->setParameter('val2', 38)
            ->setParameter('val3', 2.9)
            ->getQuery()
            ->getResult();
    }
    public function searshTypeEmthh()
    {
        $value = "payer";
        return $this->createQueryBuilder('f')
            ->select('SUM(f.montantrth)')
            ->innerJoin('f.statut', "ref")
            ->Where('ref.libiller LIKE :word')
            ->setParameter('word', $value)
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getSingleScalarResult();
    }
    public function searshTypeEmthhByDate($date)
    {
        $value = "payer";
        $result = $this->createQueryBuilder('f')
            ->select('SUM(f.montantrth)')
            ->innerJoin('f.statut', "ref")
            ->Where('f.dateCreateCommande >= :val')
            ->andWhere('ref.libiller LIKE :word')
            ->setParameter('word', $value)

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

    public function searshTypeEmthreversment()
    {
        //88
        $value = "payer";
        return $this->createQueryBuilder('com')
            ->select('SUM(com.revesmentpart)')
            ->innerJoin('com.statut', "ref")
            ->Where('ref.libiller LIKE :word')
            ->setParameter('word', $value)
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getSingleScalarResult();
    }
    public function searshTypeEmthreversmentByDate($date)
    {
        $value = "payer";
        $result = $this->createQueryBuilder('com')
            ->select('SUM(com.revesmentpart)')
            ->innerJoin('com.statut', "ref")
            ->Where('ref.libiller LIKE :word')

            ->andWhere('com.dateCreateCommande >= :val')
            ->setParameter('val', $date)
            ->setParameter('word', $value)
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
    public function searshTypeEmthreversmentuse($user)
    {
        return $this->createQueryBuilder('com')
            ->select('SUM(com.moantantTtcregl)')
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getSingleScalarResult();
    }
    public function searshTypeEmthreversmentByDateanduser($date, $user)
    {
        $result = $this->createQueryBuilder('com')
            ->select('SUM(com.moantantTtcregl)')
            ->Where('com.dateCreateCommande >= :val')
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
    public function searshNbpersoneparNbcnnx($value)
    {
        $typ = "Connexion";
        return $this->createQueryBuilder('ComandeProduit')
            ->select('COUNT(ComandeProduit.idComande)')
            ->innerJoin('ComandeProduit.idComande_', "command")
            ->innerJoin('ComandeProduit.idProduit', "produit")
            ->innerJoin('produit.type', "typeproduit")
            ->andWhere('typeproduit.labeletype = :typ')
            ->andWhere('comand.id_sejour = :val')
            ->setParameter('val', $value)
            ->setParameter('typ', $typ)
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getSingleScalarResult();
    }
    public function NbcommandePartenaire($value)
    {

        return $this->createQueryBuilder('C')
            ->select('COUNT(C.id)')
            ->innerJoin('C.idSejour', "sejour")
            ->innerJoin('C.idUser', "user")
            ->Where('sejour.idPartenaire =:val')
            // ->andWhere('k.dateDebutSejour >=:val1')

            ->setParameter('val', $value)
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getSingleScalarResult();
    }
    public function NbcommandePartenaireBydate($value, $datedebut, $datefin)
    {

        return $this->createQueryBuilder('C')
            ->select('COUNT(C.id)')
            ->innerJoin('C.idSejour', "sejour")
            ->innerJoin('C.idUser', "user")
            ->Where('sejour.idPartenaire =:val')
            ->andWhere('(C.dateCreateCommande BETWEEN :datedebut AND :datefin)  ')
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('val', $value)
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getSingleScalarResult();
    }
    public function searshNBcommandeParSejour($word)
    {


        $conn = $this->getEntityManager()->getConnection();

        $sql = '
      SELECT COUNT( DISTINCT c.id )
      FROM commande AS c, sejour AS s, etablisment AS et
      WHERE c.id_sejour = s.id
      AND s.id_etablisment = et.id
      AND et.typeetablisment LIKE :word';

        $stmt = $conn->executeQuery($sql, ['word' => '%' . $word . '%']);
        return $stmt->fetchOne();
    }
    public function searshNBcommandeParSejourByDate($word, $date)
    {
        $result =  $this->createQueryBuilder('c')
            ->select('COUNT( DISTINCT c.id )')
            ->innerJoin('c.idSejour', "Sejour")
            ->innerJoin('Sejour.idEtablisment', "Etab")
            ->andWhere('Etab.typeetablisment LIKE :word')
            ->andWhere('c.dateCreateCommande >= :date')
            ->setParameter('word', '%' . $word . '%')
            ->setParameter('date', $date)
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

    public function searshMttc($id)
    {

        return $this->createQueryBuilder('com')
            ->select('SUM(com.montantht)')
            ->innerJoin('com.idSejour', "Sejour")
            ->andWhere('Sejour.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getSingleScalarResult();
    }
    public function  SreachComandespart($id)
    {
        $val2 = "partenaire_Facture";
        return $this->createQueryBuilder('com')
            ->select('com')
            ->innerJoin('com.idSejour', "Sejour")
            ->innerJoin('com.statut', "ref")
            ->andWhere('ref.libiller  LIKE :val2')
            ->andWhere('Sejour.idEtablisment = :val')
            ->setParameter('val2', '%' . $val2 . '%')
            ->setParameter('val', $id)
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getResult();
    }
    public function rechercheMyCommandesSaufCnx($id)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Commande s  WHERE (s.idSejour=:id) and s.montantrth!= :cnx  and (s.statut IN(:paye,:expide))'
        )
            ->setParameter('cnx', 2.9)
            ->setParameter('paye', 33)
            ->setParameter('expide', 38)
            ->setParameter(':id', $id);;
        // returns an array of Product objects
        return $query->getResult();
    }
    public function rechercheMyCommandes($id)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Commande s  WHERE (s.idSejour=:id)and  (s.statut IN(:paye,:expide))'
        )
            ->setParameter('paye', 33)
            ->setParameter('expide', 38)
            ->setParameter(':id', $id);;
        // returns an array of Product objects
        return $query->getResult();
    }
    public function rechercheMyCommandesYeayMonth($id, $ym)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Commande s  WHERE (s.idSejour=:id)and  (s.statut IN(:paye,:expide) and s.dateCreateCommande LIKE :ym)'
        )
            ->setParameter('paye', 33)
            ->setParameter('expide', 38)
            ->setParameter('ym', '%' . $ym . '%')
            ->setParameter('id', $id);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function  SerachReversmentPartenaireproduits($id)
    {
        $val2 = "payer";
        $val3 = 1;
        return $this->createQueryBuilder('com')
            ->select('com')
            ->innerJoin('com.idSejour', "Sejour")
            ->innerJoin('com.statut', "ref")
            ->innerJoin('com.idUser', "user")
            ->andWhere('ref.libiller  LIKE :val2')
            ->andWhere('Sejour.id = :val')
            ->setParameter('val2', '%' . $val2 . '%')
            ->setParameter('val', $id)
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getResult();
    }
    public function   Sreachreversment_Connextion($id)
    {
        $val2 = "Reversement_partenaire_connx";
        return $this->createQueryBuilder('com')
            ->select('com')
            ->innerJoin('com.idSejour', "Sejour")
            ->innerJoin('com.statut', "ref")
            ->andWhere('ref.libiller  LIKE :val2')
            ->andWhere('Sejour.idEtablisment = :val')
            ->setParameter('val2', '%' . $val2 . '%')
            ->setParameter('val', $id)
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getResult();
    }
    public function  Sreachreversment_Produits($id)
    {
        $val2 = 0;
        return $this->createQueryBuilder('com')
            ->select('com')
            ->innerJoin('com.idSejour', "Sejour")
            ->innerJoin('com.idUser', "user")
            ->andWhere('Sejour.idEtablisment = :val')
            ->andWhere('com.rev = :val2')
            ->setParameter('val2', $val2)
            ->setParameter('val', $id)
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getResult();
    }
    public function serachNombreFacture()
    {

        return $this->createQueryBuilder('C')
            ->select('COUNT(C.id)')
            ->innerJoin('C.idSejour', "sejour")
            ->innerJoin('C.idUser', "user")
            ->Where('C.numfacture IS NOT NULL ')
            // ->andWhere('k.dateDebutSejour >=:val1')

            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getSingleScalarResult();
    }
    function caProduit()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT SUM(s.montantrth),COUNT(s),AVG(s.montantrth) FROM App\Entity\Commande s WHERE (s.montantrth!= :cnx)and (s.statut IN(:paye,:expide)) '
        )->setParameter('cnx', 2.9)
            ->setParameter('paye', 33)
            ->setParameter('expide', 38);;
        // returns an array of Product objects
        return $query->getResult();
    }
    function caProduitParPart($part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT SUM(s.montantrth),COUNT(s),AVG(s.montantrth) FROM App\Entity\Commande s ,App\Entity\Sejour se WHERE (s.idSejour=se.id)and (s.montantrth!= :cnx)and (s.statut IN(:paye,:expide)) and se.idEtablisment=:etab '
        )->setParameter('cnx', 2.9)
            ->setParameter('paye', 33)
            ->setParameter('expide', 38)
            ->setParameter(':etab', $part);;
        // returns an array of Product objects
        return $query->getResult();
    }
    function nbrProduitCommande()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(p) FROM App\Entity\Commande s , App\Entity\ComandeProduit p WHERE (s.montantrth!= :cnx)and (s.statut IN(:paye,:expide))and p.idComande=s.id '
        )->setParameter('cnx', 2.9)
            ->setParameter('paye', 33)
            ->setParameter('expide', 38);;
        // returns an array of Product objects
        return $query->getResult();
    }
    function nbrProduitCommandeParPart($part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(p) FROM App\Entity\Commande s , App\Entity\ComandeProduit p ,App\Entity\Sejour se WHERE (s.idSejour=se.id)and (s.montantrth!= :cnx)and (s.statut IN(:paye,:expide))and p.idComande=s.id  and se.idEtablisment=:etab '
        )->setParameter('cnx', 2.9)
            ->setParameter('paye', 33)
            ->setParameter('expide', 38)
            ->setParameter(':etab', $part);;
        // returns an array of Product objects
        return $query->getResult();
    }
    function caProduitPaye()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT SUM(s.montantrth) FROM App\Entity\Commande s,App\Entity\Sejour se WHERE (s.idSejour=se.id)and (s.montantrth!= :cnx)and (s.statut IN(:paye,:expide)) and se.codeSejour LIKE \'_P%\' '
        )->setParameter('cnx', 2.9)
            ->setParameter('paye', 33)
            ->setParameter('expide', 38);;
        // returns an array of Product objects
        return $query->getResult();
    }
    function caProduitPayeParPart($part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT SUM(s.montantrth) FROM App\Entity\Commande s,App\Entity\Sejour se WHERE (s.idSejour=se.id)and (s.montantrth!= :cnx)and (s.statut IN(:paye,:expide)) and se.codeSejour LIKE \'_P%\' and se.idEtablisment=:etab '
        )->setParameter('cnx', 2.9)
            ->setParameter('paye', 33)
            ->setParameter('expide', 38)
            ->setParameter(':etab', $part);
        // returns an array of Product objects
        return $query->getResult();
    }
    function caProduitFree()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT SUM(s.montantrth) FROM App\Entity\Commande s,App\Entity\Sejour se WHERE (s.idSejour=se.id)and (s.montantrth!= :cnx)and (s.statut IN(:paye,:expide)) and se.codeSejour LIKE \'_F%\' '
        )->setParameter('cnx', 2.9)
            ->setParameter('paye', 33)
            ->setParameter('expide', 38);;
        // returns an array of Product objects
        return $query->getResult();
    }
    function caProduitFreeParPart($part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT SUM(s.montantrth) FROM App\Entity\Commande s,App\Entity\Sejour se WHERE (s.idSejour=se.id)and (s.montantrth!= :cnx)and (s.statut IN(:paye,:expide)) and se.codeSejour LIKE \'_F%\' and se.idEtablisment=:etab'
        )->setParameter('cnx', 2.9)
            ->setParameter('paye', 33)
            ->setParameter('expide', 38)
            ->setParameter(':etab', $part);
        // returns an array of Product objects
        return $query->getResult();
    }
    function caProduitDujour()
    {
        $entityManager = $this->getEntityManager();
        $dateJ = new DateTime();
        $query = $entityManager->createQuery(
            'SELECT SUM(s.montantrth) FROM App\Entity\Commande s WHERE (s.montantrth!= :cnx)and (s.statut IN(:paye,:expide)) and(s.dateCreateCommande=:datedujour) '
        )->setParameter('cnx', 2.9)
            ->setParameter('paye', 33)
            ->setParameter('expide', 38)
            ->setParameter('datedujour', $dateJ, Types::DATE_MUTABLE);;
        // returns an array of Product objects
        return $query->getResult();
    }
    function caProduitDujourParPart($part)
    {
        $entityManager = $this->getEntityManager();
        $dateJ = new DateTime();
        $query = $entityManager->createQuery(
            'SELECT SUM(s.montantrth) FROM App\Entity\Commande s,App\Entity\Sejour se WHERE (s.idSejour=se.id) and (s.montantrth!= :cnx)and (s.statut IN(:paye,:expide)) and(s.dateCreateCommande=:datedujour) and se.idEtablisment=:etab '
        )->setParameter('cnx', 2.9)
            ->setParameter('paye', 33)
            ->setParameter('expide', 38)
            ->setParameter('datedujour', $dateJ, Types::DATE_MUTABLE)
            ->setParameter(':etab', $part);
        // returns an array of Product objects
        return $query->getResult();
    }
    function reversementProduitDujour()
    {
        $entityManager = $this->getEntityManager();
        $dateJ = new DateTime();
        $query = $entityManager->createQuery(
            'SELECT SUM(s.revesmentpart) FROM App\Entity\Commande s WHERE (s.montantrth!= :cnx)and (s.statut IN(:paye,:expide)) and(s.dateCreateCommande=:datedujour) '
        )->setParameter('cnx', 2.9)
            ->setParameter('paye', 33)
            ->setParameter('expide', 38)
            ->setParameter('datedujour', $dateJ, Types::DATE_MUTABLE);;
        // returns an array of Product objects
        return $query->getResult();
    }
    function reversementProduitDujourParPart($part)
    {
        $entityManager = $this->getEntityManager();
        $dateJ = new DateTime();
        $query = $entityManager->createQuery(
            'SELECT SUM(s.revesmentpart) FROM App\Entity\Commande s ,App\Entity\Sejour se WHERE (s.idSejour=se.id) and (s.montantrth!= :cnx)and (s.statut IN(:paye,:expide)) and(s.dateCreateCommande=:datedujour) and se.idEtablisment=:etab '
        )->setParameter('cnx', 2.9)
            ->setParameter('paye', 33)
            ->setParameter('expide', 38)
            ->setParameter('datedujour', $dateJ, Types::DATE_MUTABLE)
            ->setParameter(':etab', $part);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function sumProduitsdesSejOURdEpARTENAIRE($user)
    {
        return $this->createQueryBuilder('com')
            ->select('SUM(com.montantrth)')
            ->innerJoin('com.idSejour', "s")
            ->andWhere('com.statut = :payer')
            ->andWhere('com.montantrth != :cnx')
            ->andWhere('s.idPartenaire = :use')
            ->setParameter('cnx', 2.9)
            ->setParameter('use', $user)
            ->setParameter('payer', 33)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function sumConnexiondesSejOURdEpARTENAIRE($user)
    {
        return $this->createQueryBuilder('com')
            ->select('SUM(com.montantrth)')
            ->innerJoin('com.idSejour', "s")
            ->andWhere('com.statut = :payer')
            ->andWhere('com.montantrth = :cnx')
            ->andWhere('s.idPartenaire = :use')
            ->setParameter('cnx', 2.9)
            ->setParameter('use', $user)
            ->setParameter('payer', 33)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function ListDesCommandeToday($date)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT  s FROM App\Entity\Commande s ,App\Entity\Sejour se WHERE (s.idSejour=se.id)and s.statut<>:statut and(s.montantrth!= :cnx)and (s.statut IN(:paye,:expide) and(s.dateCreateCommande=:dateDeb)) '
        )->setParameter('cnx', 2.9)
            ->setParameter('paye', 33)
            ->setParameter('expide', 38)
            ->setParameter('statut', 39)
            ->setParameter('dateDeb', $date, Types::DATETIME_MUTABLE);

        return $query->getResult();
    }
    public function ListDesCommande($dateDebut, $dateFin)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT  s FROM App\Entity\Commande s ,App\Entity\Sejour se WHERE (s.idSejour=se.id)and s.statut<>:statut and(s.montantrth!= :cnx)and (s.statut IN(:paye,:expide) and(s.dateCreateCommande<=:dateFin and s.dateCreateCommande>=:dateDeb)) '
        )->setParameter('cnx', 2.9)
            ->setParameter('paye', 33)
            ->setParameter('expide', 38)
            ->setParameter('statut', 39)
            ->setParameter('dateDeb', $dateDebut, Types::DATETIME_MUTABLE)
            ->setParameter('dateFin', $dateFin, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function nbrDesProduit($dateDebut, $dateFin)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(p) FROM App\Entity\Commande s ,App\Entity\Sejour se , App\Entity\ComandeProduit p   WHERE (s.idSejour=se.id)and se.statut<>:statut and(s.montantrth!= :cnx)and (s.statut IN(:paye,:expide) and(s.dateCreateCommande<=:dateFin and s.dateCreateCommande>=:dateDeb)and (p.idComande=s.id ) )'
        )->setParameter('cnx', 2.9)
            ->setParameter('paye', 33)
            ->setParameter('statut', 39)
            ->setParameter('expide', 38)
            ->setParameter('dateDeb', $dateDebut, Types::DATETIME_MUTABLE)
            ->setParameter('dateFin', $dateFin, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function ListDesCommandeParPart($dateDebut, $dateFin, $part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT  s FROM App\Entity\Commande s ,App\Entity\Sejour se WHERE (s.montantrth!= :cnx)and (s.statut IN(:paye,:expide) and(s.dateCreateCommande<=:dateFin and s.dateCreateCommande>=:dateDeb)and (s.idSejour=se.id and se.statut<>:statut)and (se.idEtablisment=:etab)) '
        )->setParameter('cnx', 2.9)
            ->setParameter('paye', 33)
            ->setParameter('statut', 39)
            ->setParameter('expide', 38)
            ->setParameter('etab', $part)
            ->setParameter('dateDeb', $dateDebut, Types::DATETIME_MUTABLE)
            ->setParameter('dateFin', $dateFin, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function nbrDesProduitParPart($dateDebut, $dateFin, $part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(p) FROM App\Entity\Commande s , App\Entity\ComandeProduit p ,App\Entity\Sejour se  WHERE (s.montantrth!= :cnx)and (s.statut IN(:paye,:expide) and(s.dateCreateCommande<=:dateFin and s.dateCreateCommande>=:dateDeb)and (p.idComande=s.id )and (s.idSejour=se.id) and (se.statut<>:statut)and (se.idEtablisment=:etab)  )'
        )->setParameter('cnx', 2.9)
            ->setParameter('paye', 33)
            ->setParameter('expide', 38)
            ->setParameter('statut', 39)
            ->setParameter('etab', $part)
            ->setParameter('dateDeb', $dateDebut, Types::DATETIME_MUTABLE)
            ->setParameter('dateFin', $dateFin, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function ListDesCommandeParTypePart($dateDebut, $dateFin, $Type)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT  s FROM App\Entity\Commande s ,App\Entity\Sejour se,App\Entity\Etablisment e WHERE (s.montantrth!= :cnx)and (s.statut IN(:paye,:expide) and(s.dateCreateCommande<=:dateFin and s.dateCreateCommande>=:dateDeb)and (s.idSejour=se.id)and (se.statut<>:statut) and (se.idEtablisment=e.id)and (e.typeetablisment=:type)) '
        )->setParameter('cnx', 2.9)
            ->setParameter('paye', 33)
            ->setParameter('expide', 38)
            ->setParameter('statut', 39)
            ->setParameter('type', $Type)
            ->setParameter('dateDeb', $dateDebut, Types::DATETIME_MUTABLE)
            ->setParameter('dateFin', $dateFin, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function nbrDesProduitParTypePart($dateDebut, $dateFin, $Type)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(p) FROM App\Entity\Commande s , App\Entity\ComandeProduit p ,App\Entity\Sejour se ,App\Entity\Etablisment e WHERE (s.montantrth!= :cnx)and (s.statut IN(:paye,:expide) and(s.dateCreateCommande<=:dateFin and s.dateCreateCommande>=:dateDeb)and (p.idComande=s.id )and (s.idSejour=se.id)and (se.statut<>:statut)and(se.idEtablisment=e.id)and e.typeetablisment=:type  )'
        )->setParameter('cnx', 2.9)
            ->setParameter('paye', 33)
            ->setParameter('expide', 38)
            ->setParameter('statut', 39)
            ->setParameter('type', $Type)
            ->setParameter('dateDeb', $dateDebut, Types::DATETIME_MUTABLE)
            ->setParameter('dateFin', $dateFin, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function caConexxion($dateDebut, $dateFin)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT SUM(s.montantrth) FROM App\Entity\Commande s , App\Entity\ComandeProduit p ,App\Entity\Sejour se ,App\Entity\Etablisment e WHERE (s.montantrth= :cnx)and (s.statut IN(:paye,:expide) and(s.dateCreateCommande<=:dateFin and s.dateCreateCommande>=:dateDeb)and (p.idComande=s.id )  )'
        )->setParameter('cnx', 2.9)
            ->setParameter('paye', 33)
            ->setParameter('expide', 38)
            ->setParameter('dateDeb', $dateDebut, Types::DATETIME_MUTABLE)
            ->setParameter('dateFin', $dateFin, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function caConexxionParPart($dateDebut, $dateFin, $part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT SUM(s.montantrth) FROM App\Entity\Commande s ,App\Entity\Sejour se ,App\Entity\Etablisment e WHERE (s.montantrth= :cnx)and (s.statut IN(:paye,:expide) and(s.dateCreateCommande<=:dateFin and s.dateCreateCommande>=:dateDeb)and (p.idComande=s.id )and (s.idSejour=se.id) and (se.idEtablisment=:etab)    )'
        )->setParameter('cnx', 2.9)
            ->setParameter('paye', 33)
            ->setParameter('expide', 38)
            ->setParameter('etab', $part)
            ->setParameter('dateDeb', $dateDebut, Types::DATETIME_MUTABLE)
            ->setParameter('dateFin', $dateFin, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function caConexxionParType($dateDebut, $dateFin, $Type)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT SUM(s.montantrth) FROM App\Entity\Commande s , App\Entity\ComandeProduit p ,App\Entity\Sejour se ,App\Entity\Etablisment e WHERE (s.montantrth= :cnx)and (s.statut IN(:paye,:expide) and(s.dateCreateCommande<=:dateFin and s.dateCreateCommande>=:dateDeb)and (p.idComande=s.id )and (s.idSejour=se.id)and (se.idEtablisment=e.id)and e.typeetablisment=:type  )'
        )->setParameter('cnx', 2.9)
            ->setParameter('paye', 33)
            ->setParameter('expide', 38)
            ->setParameter('type', $Type)
            ->setParameter('dateDeb', $dateDebut, Types::DATETIME_MUTABLE)
            ->setParameter('dateFin', $dateFin, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    function reversementProduitDash($dateDebut, $dateFin)
    {
        $entityManager = $this->getEntityManager();
        $dateJ = new DateTime();
        $query = $entityManager->createQuery(
            'SELECT SUM(s.revesmentpart) FROM App\Entity\Commande s WHERE (s.montantrth!= :cnx)and (s.statut IN(:paye,:expide)) and(s.dateCreateCommande<=:dateFin and s.dateCreateCommande>=:dateDeb) '
        )->setParameter('cnx', 2.9)
            ->setParameter('paye', 33)
            ->setParameter('expide', 38)
            ->setParameter('dateDeb', $dateDebut, Types::DATETIME_MUTABLE)
            ->setParameter('dateFin', $dateFin, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    function ProduitCommandeTotal($dateFin)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Commande s,App\Entity\Sejour se WHERE s.idSejour=se.id and se.statut<>:statut and (s.montantrth!= :cnx)and  (s.statut IN(:paye,:expide)) and(s.dateCreateCommande<=:dateFin)'
        )
            ->setParameter('cnx', 2.9)
            ->setParameter('statut', 39)
            ->setParameter('paye', 33)
            ->setParameter('expide', 38)
            ->setParameter('dateFin', $dateFin, Types::DATETIME_MUTABLE);
        return $query->getResult();
    }
    function ProduitCommandeTotalParPArt($dateFin, $Part)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Commande s,App\Entity\Sejour se WHERE (s.montantrth!= :cnx)and  (s.statut IN(:paye,:expide)) and(s.dateCreateCommande<=:dateFin) and (s.idSejour=se.id)and (se.statut<>:statut) and (se.idEtablisment = :etab )'
        )
            ->setParameter('cnx', 2.9)
            ->setParameter('paye', 33)
            ->setParameter('expide', 38)
            ->setParameter('statut', 39)
            ->setParameter('dateFin', $dateFin, Types::DATETIME_MUTABLE)
            ->setParameter(':etab', $Part);
        return $query->getResult();
    }
    function ProduitCommandeTotalPArTypePart($dateFin, $typePart)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(s) FROM App\Entity\Commande s,App\Entity\Sejour se,App\Entity\Etablisment e WHERE (s.montantrth!= :cnx)and  (s.statut IN(:paye,:expide)) and(s.dateCreateCommande<=:dateFin)and (s.idSejour=se.id) and (se.statut<>:statut) and (se.idEtablisment=e.id)and (e.typeetablisment=:type)'
        )
            ->setParameter('cnx', 2.9)
            ->setParameter('paye', 33)
            ->setParameter('expide', 38)
            ->setParameter('statut', 39)
            ->setParameter('dateFin', $dateFin, Types::DATETIME_MUTABLE)
            ->setParameter('type', $typePart);
        return $query->getResult();
    }
    function GetCommandeDupliget()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Commande s WHERE (s.montantrth!= :cnx)and  (s.statut =:paye) '
        )
            ->setParameter('cnx', 2.9)
            ->setParameter('paye', 33);
        return $query->getResult();
    }
    function CmdPayExip()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT c FROM App\Entity\Commande c WHERE  c.statut IN(:paye,:expide)'
        )
            ->setParameter('paye', 33)
            ->setParameter('expide', 38);
        return $query->getResult();
    }
    function nbTotalDesCmd()
    {
        return $this->createQueryBuilder('C')
            ->select('COUNT(C.id)')
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getSingleScalarResult();
    }

    public function rechercherCmdReverPourFacture($iduser, $ym)
    {
        return $this->createQueryBuilder('s')
            ->select('s')
            ->WHERE('s.idUser=:id')
            ->andWhere('s.statut=:statut')
            ->andWhere('s.periode LIKE :ym')
            ->setParameter('statut', 32)
            ->setParameter('ym', '%' . $ym . '%')
            ->setParameter('id', $iduser)
            ->groupBy('s.idSejour')
            ->getQuery()
            ->getResult();
    }
    public function rechercherNbSejourFacture($iduser, $ym)
    {
        return $this->createQueryBuilder('s')
            ->select('s')
            ->WHERE('s.idUser=:id')
            ->andWhere('s.statut=:statut')
            ->andWhere('s.periode LIKE :ym')
            ->setParameter('statut', 31)
            ->setParameter('ym', '%' . $ym . '%')
            ->setParameter('id', $iduser)
            ->groupBy('s.idSejour')
            ->getQuery()
            ->getResult();
    }

    public function listeCommandeCnx(array $liste)
    {
        if (empty($liste)) {
            return "";
        }

        $entityManager = $this->getEntityManager();

        // Process in batches of 1000
        $batchSize = 1000;
        $results = [];
        $total = count($liste);
        for ($offset = 0; $offset < $total; $offset += $batchSize) {
            $batch = array_slice($liste, $offset, $batchSize);

            $ids = [];
            $periodes = [];
            $sejours = [];

            foreach ($batch as $item) {
                $ids[] = $item[0]->getIdSejour()->getIdPartenaire();
                $periodes[] = $item[0]->getDateCreation()->format('Y-m');
                $sejours[] = $item[0]->getIdSejour()->getId();
            }

            $query = $this->createQueryBuilder('s')
                ->select('s')
                ->where('s.statut = :statut')
                ->andWhere('s.idUser IN (:ids)')
                ->andWhere('s.periode IN (:periodes)')
                ->andWhere('s.idSejour IN (:sejours)')
                ->setParameter('statut', 31)
                ->setParameter('ids', $ids)
                ->setParameter('periodes', $periodes)
                ->setParameter('sejours', $sejours)
                ->getQuery();

            $batchResults = $query->getResult();
            $results = array_merge($results, $batchResults);
        }

        return $results;
    }


    function listeCommandeCnxAvancer($idCmdF, $idCmd)
    {
        $entityManager = $this->getEntityManager();
        //    var_dump($liste);
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->Where('s.statut=:statut');
        if ($idCmd != "" || $idCmdF != "") {
            if ($idCmdF != "" && $idCmd == "") {
                $cmdCmd = 's.id =:idCmdF ';
            } elseif ($idCmdF == "" && $idCmd != "") {
                $cmdCmd = 's.id =:idCmd ';
            } else {
                $cmdCmd = 's.id =:idCmdF OR s.id =:idCmd ';
            }
            $query->andWhere($cmdCmd);
        }
        if ($idCmd != "" || $idCmdF != "") {
            if ($idCmdF != "" && $idCmd == "") {
                $query->setParameter('idCmdF', $idCmdF);
            } elseif ($idCmdF == "" && $idCmd != "") {
                $query->setParameter('idCmd', $idCmd);
            } else {
                $query->setParameter('idCmdF', $idCmdF);
                $query->setParameter('idCmd', $idCmd);
            }
        }
        $query->setParameter('statut', 31);
        return  $query->getQuery()->getResult();
    }
    public function rechercherCmdFcatureCnx()
    {
        return $this->createQueryBuilder('s')
            ->select('s')
            ->innerJoin('s.idSejour', "sejour")
            ->Where('s.statut=:statut')
            ->andWhere('sejour.statut<>:statutSejour')
            ->setParameter('statut', 31)
            ->setParameter('statutSejour', 39)
            ->getQuery()
            ->getResult();
    }
    public function findCommandeListForSearch()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT c.id,c.numComande,s.codeSejour,r.libiller FROM App\Entity\Commande c ,App\Entity\Sejour s ,App\Entity\Ref r WHERE c.idSejour=s.id and c.statut=r.id and c.statut IN(:paye,:expide)'
        )
            ->setParameter('paye', 33)
            ->setParameter('expide', 38);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function  PanierAbondonneesBetween($datedebut, $datefin)
    {
        //        $entityManager = $this->getEntityManager();
        //        $query = $entityManager->createQuery(
        //            'SELECT s FROM App\Entity\ComandeProduit s ,App\Entity\Commande  c WHERE c.dateCreateCommande BETWEEN :datedebut AND :datefin'
        //        )->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
        //            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE);;
        //        // returns an array of Product objects
        //        return $query->getResult();


        $result =  $this->createQueryBuilder('k')
            ->select('k')
            ->innerJoin('k.idComande', "commande")
            ->innerJoin('commande.idSejour', "sejour")
            ->andWhere('(commande.dateCreateCommande BETWEEN :datedebut AND :datefin)')
            ->andWhere('commande.statut IN(:paye,:expide)')
            ->andWhere('sejour.statut <>:statutSejour')
            ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('paye', 15)
            ->setParameter('expide', 38)
            ->setParameter('statutSejour', 39)
            ->getQuery()
            ->getResult();
        return $result;
    }

    public function listeCommandesNonPayeeBetween($dateDebut, $dateFin)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(

            'SELECT c.id,c.montantht,c.numComande,c.dateCreateCommande,c.numfacture,s.codeSejour,r.libiller FROM App\Entity\Commande c ,App\Entity\Sejour s ,App\Entity\Ref r WHERE c.idSejour=s.id and c.statut=r.id and c.statut IN(:paye,:expide,:cnx)'
        )
            ->setParameter('paye', 15)
            ->setParameter('expide', 38)
            ->setParameter('cnx', 31);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function findCommandeListForSearchEspaceComptable()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT c.id,c.montantht,c.numComande,c.dateCreateCommande,c.numfacture,s.codeSejour,r.libiller FROM App\Entity\Commande c ,App\Entity\Sejour s ,App\Entity\Ref r WHERE c.idSejour=s.id and c.statut=r.id and c.statut IN(:paye,:expide,:cnx)'
        )
            ->setParameter('paye', 33)
            ->setParameter('expide', 38)
            ->setParameter('cnx', 31);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function verifierAppelFacture($iduser, $ym)
    {
        return $this->createQueryBuilder('s')
            ->select('s')
            ->WHERE('s.idUser=:id')
            ->andWhere('s.statut=:statut')
            ->andWhere('s.periode LIKE :ym')
            ->setParameter('statut', 32)
            ->setParameter('ym', '%' . $ym . '%')
            ->setParameter('id', $iduser)
            ->getQuery()
            ->getResult();
    }
    public function verifierFactureCnx($iduser, $ym)
    {
        return $this->createQueryBuilder('s')
            ->select('s')
            ->WHERE('s.idUser=:id')
            ->andWhere('s.statut=:statut')
            ->andWhere('s.periode LIKE :ym')
            ->setParameter('statut', 31)
            ->setParameter('ym', '%' . $ym . '%')
            ->setParameter('id', $iduser)
            ->getQuery()
            ->getResult();
    }
    public function findAppelFacture()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT  s.periode,u.id,s.numfacture FROM App\Entity\Commande s, App\Entity\User u WHERE s.idUser=u.id and s.statut=:appelFact '
        )->setParameter('appelFact', 32);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function rechercheSumMontantTTC()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT  SUM(s.moantantTtcregl),s.numfacture,s.periode FROM App\Entity\Commande s WHERE  s.statut=:appelFact'
        )->setParameter('appelFact', 32);
        // returns an array of Product objects
        return $query->getResult();
    }
    function hasCmdPay($parent)
    {
        return $this->createQueryBuilder('s')
            ->Where('s.statut =:val1 OR s.statut=:val2')
            ->andWhere('s.idUser =:parent')
            ->setParameter('parent', $parent)
            ->setParameter('val1', 33)
            ->setParameter('val2', 38)
            ->getQuery()
            ->getResult();
    }
    function getAllCommandes()
    {
        return $this->createQueryBuilder('s')
            ->Where('s.statut =:val1 OR s.statut=:val2')
            ->setParameter('val1', 33)
            ->setParameter('val2', 38)
            ->getQuery()
            ->getResult();
    }
    function getCommandesDuJour($jour)
    {
        return $this->createQueryBuilder('s')
            ->Where('s.statut =:val1 OR s.statut=:val2')
            ->andWhere('s.dateCreateCommande LIKE :ym')
            ->setParameter('val1', 33)
            ->setParameter('val2', 38)
            ->setParameter('ym', $jour)
            ->getQuery()
            ->getResult();
    }
    public function getCommandeEntreDeuxDate($datedebut, $dateFin)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT c FROM App\Entity\Commande c WHERE  :datedeb<=c.dateCreateCommande and :datefin>=c.dateCreateCommande    and c.statut=:statut'
        )
            ->setParameter('statut', 38)
            ->setParameter('datefin', $dateFin, Types::DATETIME_MUTABLE)
            ->setParameter('datedeb', $datedebut, Types::DATETIME_MUTABLE);
        // returns an array of Product objects
        return $query->getResult();
    }
    public function commandeparent($years, $idParent)
    {
        $value = "ROLE_PARENT";
        $entityManager = $this->getEntityManager();
        $result = [];
        foreach ($years as $year) {
            $datedebut = date('01/01/' . $year);
            // Last day of the month.
            $datefin = date('31/12/' . $year);

            $datedebut = \DateTime::createFromFormat('d/m/Y', $datedebut)->setTime(0, 0);
            $datefin = \DateTime::createFromFormat('d/m/Y', $datefin)->setTime(0, 0);
            $listeCommandes =   $this->createQueryBuilder('commande')
                ->where('commande.idUser = :idParent')
                ->andwhere('commande.dateCreateCommande BETWEEN :datedebut AND :datefin')
                ->andwhere('commande.statut IN (33,38)')
                ->setParameter('idParent', $idParent)
                ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
                ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
                ->getQuery()
                ->getResult();
            $result[$year]['listeCommandes'] = $listeCommandes;

            $totalCommande = $this->createQueryBuilder('commande')
                ->select('SUM(commande.montantht)')
                ->where('commande.idUser = :idParent')
                ->andwhere('commande.dateCreateCommande BETWEEN :datedebut AND :datefin')
                ->andwhere('commande.statut IN (33,38)')
                ->setParameter('idParent', $idParent)
                ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
                ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
                ->getQuery()
                ->getSingleScalarResult();
            $result[$year]['totalCommande'] = $totalCommande;
            $nbreCmd = $this->createQueryBuilder('commande')
                ->select('COUNT(commande.id)')
                ->where('commande.idUser = :idParent')
                ->andwhere('commande.dateCreateCommande BETWEEN :datedebut AND :datefin')
                ->andwhere('commande.statut IN (33,38)')
                ->setParameter('idParent', $idParent)
                ->setParameter('datefin', $datefin, Types::DATETIME_MUTABLE)
                ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
                ->getQuery()
                ->getSingleScalarResult();
            $result[$year]['nbreCmd'] = $nbreCmd;
        }
        return $result;
    }
    public function findByYearMonth($year_month)
    {
        $cmds = $this->createQueryBuilder('commande')
            ->select('commande')
            ->where("commande.dateCreateCommande LIKE '%" . $year_month . "%'")
            ->getQuery()
            ->getResult();
        return $cmds;
    }
}
