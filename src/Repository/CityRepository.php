<?php

namespace App\Repository;

use App\Entity\City;
use App\Model\Admin\CitySearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<City>
 *
 * @method City|null find($id, $lockMode = null, $lockVersion = null)
 * @method City|null findOneBy(array $criteria, array $orderBy = null)
 * @method City[]    findAll()
 * @method City[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class);
    }

    public function add(City $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(City $entity, bool $flush = false): void
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

    public function getAdmins(CitySearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.position', 'asc');

        if ($search->getName()) {
            $qb->andWhere('c.name LIKE :name')->setParameter('name', '%'.$search->getName().'%');
        }

        if ($search->getCountry()) {
            $qb->andWhere('c.country = :country')->setParameter('country', $search->getCountry());
        }

        return $qb;
    }

    public function getWithData(): array
    {
        $results = $this->createQueryBuilder('c')
            ->where('c.enabled = 1')
            ->orderBy('c.position', 'asc')
            ->getQuery()->getArrayResult();

        $data = [];

        foreach ($results as $result) {
            $data[$result['name']] = $result['name'];
        }

        return $data;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getByName(string $name): ?City
    {
        return $this->createQueryBuilder('c')
            ->where('c.enabled = 1')
            ->andWhere('c.name = :name')
            ->setParameter('name', $name)
            ->getQuery()->getOneOrNullResult();
    }

}
