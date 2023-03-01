<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 *
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function add(Notification $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Notification $entity, bool $flush = false): void
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

    public function findRecentForUser(User $user, array $channels = ['public']): array
    {
        return array_map(fn ($n) => (clone $n)->setOwner($user), $this->createQueryBuilder('n')
            ->orderBy('n.createdAt', 'DESC')
            ->setMaxResults(10)
            ->where('n.owner = :user')
            ->orWhere('n.owner IS NULL AND n.channel IN (:channels)')
            ->setParameter('user', $user)
            ->setParameter('channels', $channels)
            ->getQuery()
            ->getResult());
    }

    public function findForUser(User $user, array $channels = ['public']): array
    {
        return array_map(fn ($n) => (clone $n)->setOwner($user), $this->createQueryBuilder('n')
            ->orderBy('n.createdAt', 'DESC')
            ->where('n.owner = :user')
            ->orWhere('n.user IS NULL AND n.channel IN (:channels)')
            ->setParameter('user', $user)
            ->setParameter('channels', $channels)
            ->getQuery()
            ->getResult());
    }

    public function findRecentForChannel(array $channels = ['public']): array
    {
        return $this->createQueryBuilder('n')
            ->orderBy('n.createdAt', 'DESC')
            ->where('n.owner IS NULL AND n.channel IN (:channels)')
            ->setParameter('channels', $channels)
            ->getQuery()
            ->getResult();
    }

    public function persistOrUpdate(Notification $notification): object
    {
        if (null === $notification->getOwner()) {
            $this->getEntityManager()->persist($notification);

            return $notification;
        }

        $oldNotification = $this->findOneBy([
            'user' => $notification->getOwner(),
            'target' => $notification->getTarget()
        ]);

        if ($oldNotification) {
            $oldNotification->setCreatedAt($notification->getCreatedAt());
            $oldNotification->setMessage($notification->getMessage());

            return $oldNotification;
        } else {
            $this->getEntityManager()->persist($notification);

            return $notification;
        }
    }

    public function clean(): int
    {
        return $this->createQueryBuilder('n')
            ->where('n.createdAt < :date')
            ->setParameter('date', new DateTime('-3 month'))
            ->delete(Notification::class, 'n')
            ->getQuery()
            ->execute();
    }
}
