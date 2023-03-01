<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
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

    public function add(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function getAdmins(string $category, ?int $option = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p')
                ->where('p.category = :category')
                ->setParameter('category', $category)
                ->orderBy('p.createdAt', 'desc');

        if ($option) {
            $qb->andWhere('p.options = :options')
                ->setParameter('options', $option);
        }

        return $qb;
    }

    public function findByArray(array $array): array
    {
        return $this->createQueryBuilder('p')
                ->Where('p.id IN (:array)')
                ->setParameter('array', $array)
                ->getQuery()->getResult();
    }

    public function getOptions(int $option): array
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.category = :category')
            ->andWhere('p.options = :options')
            ->andWhere('p.enabled = 1')
            ->setParameter('category', Product::CATEGORY_ADVERT_OPTION)
            ->setParameter('options', $option)
            ->orderBy('p.price', 'asc');

        return $qb->getQuery()->getResult();
    }

    public function getEnabledCredits(): array
    {
        return $this->createQueryBuilder('p')
                ->where('p.category = :category')
                ->andWhere('p.enabled = 1')
                ->setParameter('category', Product::CATEGORY_CREDIT)
                ->orderBy('p.price', 'desc')
                ->getQuery()
                ->getResult();
    }

}
