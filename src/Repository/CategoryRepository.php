<?php

namespace App\Repository;

use App\Entity\Category;
use App\Model\Admin\CategorySearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function add(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Category $entity, bool $flush = false): void
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

    public function getAdminWithParent(CategorySearch $search, Category $parent = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('c');

        if ($parent) {
            $qb->where('c.parent = :parent')->setParameter('parent', $parent);
        } else {
            $qb->where($qb->expr()->isNull('c.parent'));
        }

        $qb->orderBy('c.position', 'asc');

        if ($search->isEnabled()) {
            $qb->andWhere('c.enabled = 1');
        }

        if ($search->getName()) {
            $qb->andWhere('c.name LIKE :name')->setParameter('name', '%'.$search->getName().'%');
        }

        return $qb;
    }

    public function getHasParent(): array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb->where($qb->expr()->isNotNull('c.parent'))
            ->andWhere('c.enabled = 1')
            ->orderBy('c.parent', 'asc')
            ->getQuery()
            ->getResult();
    }

    public function getHasNotParent(): array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb->where($qb->expr()->isNull('c.parent'))
            ->andWhere('c.enabled = 1')
            ->leftJoin('c.children', 'children')
            ->addSelect('children')
            ->orderBy('c.position', 'asc')
            ->getQuery()
            ->getResult();
    }

    public function getPartielHasNotParent(int $limit = 5)
    {
        $qb = $this->createQueryBuilder('c');

        return $qb->where($qb->expr()->isNull('c.parent'))
            ->andWhere('c.enabled = 1')
            ->orderBy('c.position', 'asc')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getByParentSlug(?string $slug = null): array
    {
         $qb = $this->createQueryBuilder('c')
                    ->leftJoin('c.parent', 'parent')
                    ->where('c.enabled = 1')
                    ->orderBy('c.position', 'asc');

         if ($slug) {
             $qb->andWhere('parent.slug = :slug')
                 ->setParameter('slug', $slug);
         } else {
             $qb->andWhere($qb->expr()->isNull('c.parent'));
         }

         return $qb->getQuery()->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getEnabledBySlug(string $slug): ?Category
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.parent', 'parent')
            ->addSelect('parent')
            ->where('c.enabled = 1')
            ->andWhere('c.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getBySlug(string $slug): ?Category
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.parent', 'parent')
            ->addSelect('parent')
            ->where('c.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
