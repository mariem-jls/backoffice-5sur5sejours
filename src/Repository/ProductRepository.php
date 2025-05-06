<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Find all products ordered by a specific field
     *
     * @param string $field
     * @param string $order
     * @return Product[]
     */
    public function findAllOrderedBy(string $field, string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.' . $field, $order)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find products by a specific field and value
     *
     * @param string $field
     * @param mixed $value
     * @return Product[]
     */
    public function findByField(string $field, $value): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.' . $field . ' = :value')
            ->setParameter('value', $value)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find a product by its label
     *
     * @param string $label
     * @return Product|null
     */
    public function findOneByLabel(string $label): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.labele = :label')
            ->setParameter('label', $label)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Get products with pagination
     *
     * @param int $page
     * @param int $limit
     * @return Product[]
     */
    public function findAllPaginated(int $page, int $limit = 10): array
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Count all products
     *
     * @return int
     */
    public function countAll(): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Get products with a join on another table (example: User)
     *
     * @return Product[]
     */
    public function findAllWithUser(): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.iduser', 'u', Join::WITH, 'u.id = p.iduser')
            ->addSelect('u')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get products with advanced filtering and sorting
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return Product[]
     */
    public function findByAdvanced(array $criteria, array $orderBy = [], int $limit = null, int $offset = null): array
    {
        $queryBuilder = $this->createQueryBuilder('p');

        foreach ($criteria as $field => $value) {
            $queryBuilder->andWhere('p.' . $field . ' = :' . $field)
                         ->setParameter($field, $value);
        }

        foreach ($orderBy as $field => $order) {
            $queryBuilder->addOrderBy('p.' . $field, $order);
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

