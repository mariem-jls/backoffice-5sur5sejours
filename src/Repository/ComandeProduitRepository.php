<?php

namespace App\Repository;

use App\Entity\ComandeProduit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Doctrine\DBAL\Types\Types;

class ComandeProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ComandeProduit::class);
    }

    public function PRoduitsENOrDREdEvENTE($type): array
    {
        return $this->createQueryBuilder('cp')
            ->join('cp.idProduit', 'p')
            ->where('p.type = :type')
            ->setParameter('type', $type)
            ->orderBy('cp.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function PRoduitsENOrDREdEvENTEFiltrePardate($type, \DateTime $dateTimeDebut, \DateTime $dateTimeFin): array
    {
        return $this->createQueryBuilder('cp')
            ->join('cp.idProduit', 'p')
            ->where('p.type = :type')
            ->andWhere('cp.date BETWEEN :dateTimeDebut AND :dateTimeFin')
            ->setParameter('type', $type)
            ->setParameter('dateTimeDebut', $dateTimeDebut)
            ->setParameter('dateTimeFin', $dateTimeFin)
            ->orderBy('cp.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function LaSommeDesProduitsVendus(): int
    {
        $result = $this->createQueryBuilder('cp')
            ->select('SUM(cp.quantiter)')
            ->getQuery()
            ->getSingleScalarResult();

        return $result !== null ? (int)$result : 0;
    }

    public function LaSommeDesProduitsVendusParDate(\DateTime $dateTimeDebut, \DateTime $dateTimeFin): int
    {
        $result = $this->createQueryBuilder('cp')
            ->select('SUM(cp.quantiter)')
            ->where('cp.date BETWEEN :dateTimeDebut AND :dateTimeFin')
            ->setParameter('dateTimeDebut', $dateTimeDebut)
            ->setParameter('dateTimeFin', $dateTimeFin)
            ->getQuery()
            ->getSingleScalarResult();

        return $result !== null ? (int)$result : 0;
    }

    public function Nbpersone_commandepartypesejoursansdate($type): int
    {
        $result = $this->createQueryBuilder('cp')
            ->select('COUNT(cp.id)')
            ->join('cp.idProduit', 'p')
            ->where('p.type = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->getSingleScalarResult();

        return $result !== null ? (int)$result : 0;
    }

    public function Nbpersone_commandepartypesejourAvecdate($type, \DateTime $dateTimeDebut, \DateTime $dateTimeFin): int
    {
        $result = $this->createQueryBuilder('cp')
            ->select('COUNT(cp.id)')
            ->join('cp.idProduit', 'p')
            ->where('p.type = :type')
            ->andWhere('cp.date BETWEEN :dateTimeDebut AND :dateTimeFin')
            ->setParameter('type', $type)
            ->setParameter('dateTimeDebut', $dateTimeDebut)
            ->setParameter('dateTimeFin', $dateTimeFin)
            ->getQuery()
            ->getSingleScalarResult();

        return $result !== null ? (int)$result : 0;
    }

    public function rechercherAvanceCmdProduitEspaceComptable($datedebut, $datefin, $idCmdF, $idCmd, $idSejour, $idPartenaire, $produit)
    {
        $qb = $this->createQueryBuilder('cp')
            ->join('cp.idComande', 'c')
            ->join('cp.idProduit', 'p')
            ->where('cp.date BETWEEN :datedebut AND :datefin')
            ->setParameter('datedebut', $datedebut)
            ->setParameter('datefin', $datefin);

        // if ($idCmdF) {
        //     $qb->andWhere('c.id = :idCmdF')
        //        ->setParameter('idCmdF', $idCmdF);
        // }

        // if ($idCmd) {
        //     $qb->andWhere('cp.idComande = :idCmd')
        //        ->setParameter('idCmd', $idCmd);
        // }

        // if ($idSejour) {
        //     $qb->andWhere('c.idSejour = :idSejour')
        //        ->setParameter('idSejour', $idSejour);
        // }

        // if ($idPartenaire) {
        //     $qb->andWhere('c.idUser = :idPartenaire')
        //        ->setParameter('idPartenaire', $idPartenaire);
        // }

        // if ($produit) {
        //     $qb->andWhere('p.id = :produit')
        //        ->setParameter('produit', $produit);
        // }

        return $qb->getQuery()->getResult();
    }

    public function  ProduitCommandeAvecCmdPayExipBetween($datedebut ,$datefin){
        $result=  $this->createQueryBuilder('k')
            ->select('k')
            ->innerJoin('k.idComande', "commande")
            ->innerJoin('commande.idSejour', "sejour")
            ->andWhere('(commande.dateCreateCommande BETWEEN :datedebut AND :datefin)')
            ->andWhere('commande.statut IN(:paye,:expide)')
            ->andWhere('sejour.statut <>:statutSejour')
            ->setParameter('datefin',$datefin, Types::DATETIME_MUTABLE)
            ->setParameter('datedebut', $datedebut, Types::DATETIME_MUTABLE)
            ->setParameter('paye',33)
            ->setParameter('expide',38)
            ->setParameter('statutSejour',39)
            ->getQuery()
            ->getResult();
        return $result;
    }
}
