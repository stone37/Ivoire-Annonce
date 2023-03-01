<?php

namespace App\Repository;

use App\Entity\ThreadMessage;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ThreadMessage>
 *
 * @method ThreadMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method ThreadMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method ThreadMessage[]    findAll()
 * @method ThreadMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ThreadMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ThreadMessage::class);
    }

    public function add(ThreadMessage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ThreadMessage $entity, bool $flush = false): void
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
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function getNbUnreadMessageByParticipant(User $user): int
    {
        $qb = $this->createQueryBuilder('tm')
            ->select('count(tm.id)')
            ->innerJoin('tm.metadata', 'mm')
            ->innerJoin('mm.participant', 'p')
            ->andWhere('tm.sender != :sender')
            ->setParameter('sender', $user->getId())
            ->andWhere('mm.isRead = 0');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
