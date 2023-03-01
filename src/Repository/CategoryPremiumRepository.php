<?php

namespace App\Repository;

use App\Entity\CategoryPremium;
use App\Model\Admin\CategorySearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CategoryPremium>
 *
 * @method CategoryPremium|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryPremium|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryPremium[]    findAll()
 * @method CategoryPremium[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryPremiumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryPremium::class);
    }

    public function add(CategoryPremium $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CategoryPremium $entity, bool $flush = false): void
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

    public function getAdmins(CategorySearch $search): QueryBuilder
    {
        $qb = $this->createQueryBuilder('cp')
            ->orderBy('cp.position', 'asc');

        if ($search->isEnabled()) {
            $qb->andWhere('cp.enabled = 1');
        }

        if ($search->getName()) {
            $qb->andWhere('cp.name LIKE :name')->setParameter('name', '%'.$search->getName().'%');
        }

        return $qb;
    }

    public function getEnabled(): array
    {
        return $this->createQueryBuilder('cp')
            ->where('cp.enabled = 1')
            ->orderBy('cp.position', 'asc')
            ->getQuery()->getResult();
    }

}
