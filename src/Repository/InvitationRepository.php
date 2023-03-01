<?php

namespace App\Repository;

use App\Entity\Invitation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Invitation>
 *
 * @method Invitation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invitation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invitation[]    findAll()
 * @method Invitation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvitationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invitation::class);
    }

    public function add(Invitation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Invitation $entity, bool $flush = false): void
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
     */
    public function findByUser(User $user): ?Invitation
    {
        $qb = $this->createQueryBuilder('i')
            ->where('i.owner = :user')
            ->setParameter('user', $user);

        return $qb->getQuery()->getOneOrNullResult();
    }


    /**
     * @throws NonUniqueResultException
     */
    public function findByCode(string $code)
    {
        $qb = $this->createQueryBuilder('i')
            ->where('i.code = :code')
            ->setParameter('code', $code);

        return $qb->getQuery()->getOneOrNullResult();
    }

}
