<?php

namespace App\Repository;

use App\Entity\Suggestion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Suggestion>
 *
 * @method Suggestion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Suggestion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Suggestion[]    findAll()
 * @method Suggestion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SuggestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Suggestion::class);
    }

    public function add(Suggestion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Suggestion $entity, bool $flush = false): void
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

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getNumber(): int
    {
        return (int) $this->createQueryBuilder('s')
            ->select('count(s.id)')
            ->getQuery()->getSingleScalarResult();
    }
}
