<?php

namespace App\Repository;

use App\Entity\Advert;
use App\Entity\Thread;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Thread>
 *
 * @method Thread|null find($id, $lockMode = null, $lockVersion = null)
 * @method Thread|null findOneBy(array $criteria, array $orderBy = null)
 * @method Thread[]    findAll()
 * @method Thread[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ThreadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Thread::class);
    }

    public function add(Thread $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Thread $entity, bool $flush = false): void
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

    public function getParticipantThreads(User $user): array
    {
        $qb = $this->createQueryBuilder('t')
            ->innerJoin('t.metadata', 'tm')
            ->innerJoin('tm.participant', 'p')
            ->leftJoin('t.advert', 'advert')
            ->addSelect('advert')
            ->andWhere('p.id = :user_id')
            ->andWhere('tm.isDeleted = 0')
            ->setParameter('user_id', $user->getId())
            ->orderBy('t.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function getParticipantInboxThreads(User $user): array
    {
        $qb = $this->createQueryBuilder('t')
            ->innerJoin('t.metadata', 'tm')
            ->innerJoin('tm.participant', 'p')
            ->andWhere('p.id = :user_id')
            ->setParameter('user_id', $user->getId())
            ->andWhere('tm.isDeleted = 0')
            ->andWhere('tm.lastMessageDate IS NOT NULL')
            ->orderBy('tm.lastMessageDate', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function getParticipantDeletedThreads(User $user): array
    {
        $qb = $this->createQueryBuilder('t')
            ->innerJoin('t.metadata', 'tm')
            ->innerJoin('tm.participant', 'p')
            ->andWhere('p.id = :user_id')
            ->setParameter('user_id', $user->getId())
            ->andWhere('tm.isDeleted = 1')
            ->orderBy('tm.lastMessageDate', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function getThreadsCreatedBy(User $user): array
    {
        $qb = $this->createQueryBuilder('t')
            ->innerJoin('t.createdBy', 'p')
            ->where('p.id = :participant_id')
            ->setParameter('participant_id', $user->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findThreadsCreatedByAdvert(Advert $advert, User $user): ?Thread
    {
        $qb = $this->createQueryBuilder('t')
            ->leftJoin('t.createdBy', 'p')
            ->leftJoin('t.advert', 'a')
            ->where('p.id = :participant_id')
            ->andWhere('a.id = :advert_id')
            ->setParameter('participant_id', $user->getId())
            ->setParameter('advert_id', $advert->getId())
            ->setMaxResults(1)
            ->orderBy('t.createdAt', 'desc');

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getParticipantSentThreads(User $user): array
    {
        $qb = $this->createQueryBuilder('t')
            ->innerJoin('t.metadata', 'tm')
            ->innerJoin('tm.participant', 'p')
            ->andWhere('p.id = :user_id')
            ->setParameter('user_id', $user->getId())
            ->andWhere('tm.isDeleted = 0')
            ->andWhere('tm.lastParticipantMessageDate IS NOT NULL')
            ->orderBy('tm.lastParticipantMessageDate', 'DESC');

        return $qb->getQuery()->getResult();
    }
}
