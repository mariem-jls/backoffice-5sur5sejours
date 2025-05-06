<?php

namespace App\Repository;

use App\Entity\Typeproduit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @method Typeproduit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Typeproduit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Typeproduit[]    findAll()
 * @method Typeproduit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeproduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Typeproduit::class);
    }

    public function findAllWithRelations()
    {
        return $this->createQueryBuilder('tp')
            ->leftJoin('tp.typeProduitPhotos', 'tpp')
            ->addSelect('tpp')
            ->leftJoin('tpp.idAttachement', 'att')
            ->addSelect('att')
            ->leftJoin('tp.reversment', 'r')
            ->addSelect('r')
            ->leftJoin('tp.iduser', 'u')
            ->addSelect('u')
            ->leftJoin('tp.typeProduitConditionnements', 'tpc')
            ->addSelect('tpc')
            ->getQuery()
            ->getResult();
    }

    public function findBySearchQuery(string $query)
    {
        return $this->createQueryBuilder('p')
            ->where('p.labeletype LIKE :query')
            ->setParameter('query', '%'.$query.'%')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all Typeproduits ordered by a specific field
     *
     * @param string $field
     * @param string $order
     * @return Typeproduit[]
     */
    public function findAllOrderedBy(string $field, string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.' . $field, $order)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find Typeproduits by a specific field and value
     *
     * @param string $field
     * @param mixed $value
     * @return Typeproduit[]
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
     * Find a Typeproduit by its label
     *
     * @param string $label
     * @return Typeproduit|null
     */
    public function findOneByLabel(string $label): ?Typeproduit
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.labeletype = :label')
            ->setParameter('label', $label)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Get Typeproduits with pagination
     *
     * @param int $page
     * @param int $limit
     * @return Typeproduit[]
     */
    public function findAllPaginated(int $page, int $limit = 10): array
    {
        $queryBuilder = $this->createQueryBuilder('t')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Count all Typeproduits
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
     * Get Typeproduits with a join on another table (example: User)
     *
     * @return Typeproduit[]
     */
    public function findAllWithUser(): array
    {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.iduser', 'u', Join::WITH, 'u.id = t.iduser')
            ->addSelect('u')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get Typeproduits with advanced filtering and sorting
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return Typeproduit[]
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

