<?php

namespace App\Service;

use App\Entity\Commande;
use App\Entity\Sejour;
use Doctrine\ORM\EntityManagerInterface;

class ComandeService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function findSejourListForSearch()
    {
        return $this->em->getRepository(Sejour::class)->findSejourListForSearch();
    }

    public function findCommandeListForSearchEspaceComptable()
    {
        return $this->em->getRepository(Commande::class)->findCommandeListForSearchEspaceComptable();
    }

    public function findAppelFacture()
    {
        return $this->em->getRepository(Commande::class)->findAppelFacture();
    }

    public function listeCommandesNonPayeeBetween(\DateTime $startDate, \DateTime $endDate)
    {
        return $this->em->getRepository(Commande::class)->listeCommandesNonPayeeBetween($startDate, $endDate);
    }

    public function ListDesCommande(\DateTime $startDate, \DateTime $endDate)
    {
        return $this->em->getRepository(Commande::class)->ListDesCommande($startDate, $endDate);
    }

    public function ListDesCommandeToday(\DateTime $dateToday)
    {
        return $this->em->getRepository(Commande::class)->ListDesCommandeToday($dateToday);
    }

    public function getSommeProduit(array $commandes)
    {
        $totalCaProduit = 0;
        foreach ($commandes as $commande) {
            $totalCaProduit += $commande->getTotal();
        }
        return ['totalCaProduit' => $totalCaProduit];
    }
}

