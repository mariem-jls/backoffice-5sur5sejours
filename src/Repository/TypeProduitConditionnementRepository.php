<?php

namespace App\Repository;

use App\Entity\TypeProduitConditionnement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @method TypeProduitConditionnement|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeProduitConditionnement|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeProduitConditionnement[]    findAll()
 * @method TypeProduitConditionnement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeProduitConditionnementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeProduitConditionnement::class);
    }

    /**
     * Find all TypeProduitConditionnements ordered by a specific field
     *
     * @param string $field
     * @param string $order
     * @return TypeProduitConditionnement[]
     */
    public function findAllOrderedBy(string $field, string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.' . $field, $order)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find TypeProduitConditionnements by a specific field and value
     *
     * @param string $field
     * @param mixed $value
     * @return TypeProduitConditionnement[]
     */
    public function findByField(string $field, $value): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.' . $field . ' = :value')
            ->setParameter('value', $value)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find a TypeProduitConditionnement by its descriptionCommande
     *
     * @param string $descriptionCommande
     * @return TypeProduitConditionnement|null
     */
    public function findOneByDescriptionCommande(string $descriptionCommande): ?TypeProduitConditionnement
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.descriptionCommande = :descriptionCommande')
            ->setParameter('descriptionCommande', $descriptionCommande)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Get TypeProduitConditionnements with pagination
     *
     * @param int $page
     * @param int $limit
     * @return TypeProduitConditionnement[]
     */
    public function findAllPaginated(int $page, int $limit = 10): array
    {
        $queryBuilder = $this->createQueryBuilder('t')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Count all TypeProduitConditionnements
     *
     * @return int
     */
    public function countAll(): int
    {
        return (int) $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Get TypeProduitConditionnements with a join on another table (example: Typeproduit)
     *
     * @return TypeProduitConditionnement[]
     */
    public function findAllWithTypeproduit(): array
    {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.idTypeProduit', 'tp', Join::WITH, 'tp.id = t.idTypeProduit')
            ->addSelect('tp')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get TypeProduitConditionnements with advanced filtering and sorting
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return TypeProduitConditionnement[]
     */
    public function findByAdvanced(array $criteria, array $orderBy = [], int $limit = null, int $offset = null): array
    {
        $queryBuilder = $this->createQueryBuilder('t');

        foreach ($criteria as $field => $value) {
            $queryBuilder->andWhere('t.' . $field . ' = :' . $field)
                         ->setParameter($field, $value);
        }

        foreach ($orderBy as $field => $order) {
            $queryBuilder->addOrderBy('t.' . $field, $order);
        }

        if ($limit !== null) {
            $queryBuilder->setMaxResults($limit);
        }

        if ($offset !== null) {
            $queryBuilder->setFirstResult($offset);
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
