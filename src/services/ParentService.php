<?php

namespace App\Service;

use App\Entity\Commande;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\ParentSejour;
use App\Entity\Sejour;
use Doctrine\Persistence\ManagerRegistry;

class ParentService
{
    private $em;
    public function __construct(ManagerRegistry $em)
    {
        $this->em = $em;
    }
    function ListeDesParents()
    {
        $user = $this->em->getRepository(User::class)->findBy(array('roles' => '["ROLE_PARENT"]'));
        return $user;
    }
    function ListeDesCommandeParent($parent)
    {
        $Commandes = $this->em->getRepository(Commande::class)->findBy(array('idUser' => $parent));
        return $Commandes;
    }
    function ListeDesSejoursParent($parent)
    {
        $Sejours = $this->em->getRepository(Sejour::class)->findBy(array('idParent' => $parent));
        return $Sejours;
    }
    function ListeSejourDesParents()
    {
        $SejourDesParents = $this->em->getRepository(ParentSejour::class)->findAll();
        return $SejourDesParents;
    }
}
