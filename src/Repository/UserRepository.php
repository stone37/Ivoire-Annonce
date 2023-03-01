<?php

namespace App\Repository;

use App\Entity\User;
use App\Model\Admin\UserSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends AbstractRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function add(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
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
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->add($user, true);
    }

    /**
     * Requête permettant de récupérer un utilisateur pour le login.
     *
     * @throws NonUniqueResultException
     */
    public function findForAuth(string $username): ?User
    {
        return $this->createQueryBuilder('u')
            ->where('LOWER(u.email) = :username')
            ->orWhere('LOWER(u.username) = :username')
            ->andWhere('u.isVerified = 1')
            ->setMaxResults(1)
            ->setParameter('username', mb_strtolower($username))
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Cherche un utilisateur pour l'oauth.
     *
     * @throws NonUniqueResultException
     */
    public function findForOauth(string $service, ?string $serviceId, ?string $email): ?User
    {
        if (null === $serviceId || null === $email) {
            return null;
        }

        return $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->orWhere("u.{$service}Id = :serviceId")
            ->andWhere('u.isVerified = 1')
            ->setMaxResults(1)
            ->setParameters(['email' => $email, 'serviceId' => $serviceId])
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return User[]
     */
    public function clean(): array
    {
        $query = $this->createQueryBuilder('u')
            ->where('u.deleteAt IS NOT NULL')
            ->andWhere('u.deleteAt < NOW()');

        /** @var User[] $users */
        $users = $query->getQuery()->getResult();
        $query->delete(User::class, 'u')->getQuery()->execute();

        return $users;
    }

    public function getAdmins(UserSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('u');

        $qb->where($qb->expr()->isNull('u.deleteAt'))
            ->andWhere('u.roles LIKE :roles')
            ->setParameter('roles', '%'."ROLE_ADMIN".'%')
            ->orderBy('u.createdAt', 'desc');

        return $this->adminSearchData($search, $qb);
    }

    public function getAdminUsers(UserSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.deleteAt IS NULL')
            ->andWhere('u.confirmationToken IS NULL')
            ->andWhere('u.roles LIKE :roles')
            ->andWhere('u.roles NOT LIKE :roleP')
            ->andWhere('u.roles NOT LIKE :roleA')
            ->andWhere('u.roles NOT LIKE :roleSA')
            ->setParameter('roles', '%'."".'%')
            ->setParameter('roleP', '%'."ROLE_USER_PRO".'%')
            ->setParameter('roleA', '%'."ROLE_ADMIN".'%')
            ->setParameter('roleSA', '%'."ROLE_SUPER_ADMIN".'%')
            ->orderBy('u.createdAt', 'desc');

        return $this->adminSearchData($search, $qb);
    }

    public function getUserNoConfirmed(UserSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.deleteAt IS NULL')
            ->andWhere('u.confirmationToken IS NOT NULL')
            ->andWhere('u.roles LIKE :roles')
            ->andWhere('u.roles NOT LIKE :roleP')
            ->andWhere('u.roles NOT LIKE :roleA')
            ->andWhere('u.roles NOT LIKE :roleSA')
            ->setParameter('roles', '%'."".'%')
            ->setParameter('roleP', '%'."ROLE_USER_PRO".'%')
            ->setParameter('roleA', '%'."ROLE_ADMIN".'%')
            ->setParameter('roleSA', '%'."ROLE_SUPER_ADMIN".'%')
            ->orderBy('u.createdAt', 'desc');

        return $this->adminSearchData($search, $qb);
    }

    public function getUserDeleted(UserSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.deleteAt IS NOT NULL')
            ->andWhere('u.confirmationToken IS NULL')
            ->andWhere('u.roles LIKE :roles')
            ->andWhere('u.roles NOT LIKE :roleP')
            ->andWhere('u.roles NOT LIKE :roleA')
            ->andWhere('u.roles NOT LIKE :roleSA')
            ->setParameter('roles', '%'."".'%')
            ->setParameter('roleP', '%'."ROLE_USER_PRO".'%')
            ->setParameter('roleA', '%'."ROLE_ADMIN".'%')
            ->setParameter('roleSA', '%'."ROLE_SUPER_ADMIN".'%')
            ->orderBy('u.createdAt', 'desc');

        return $this->adminSearchData($search, $qb);
    }

    public function findAllUsers(): array
    {
        $qb = $this->createQueryBuilder('u');

        $qb->where($qb->expr()->isNull('u.deleteAt'))
            ->andWhere('u.deleteAt IS NULL')
            ->andWhere('u.confirmationToken IS NOT NULL')
            ->andWhere('u.roles LIKE :roles')
            ->andWhere('u.roles NOT LIKE :roleP')
            ->andWhere('u.roles NOT LIKE :roleA')
            ->andWhere('u.roles NOT LIKE :roleSA')
            ->setParameter('roles', '%'."".'%')
            ->setParameter('roleP', '%'."ROLE_USER_PRO".'%')
            ->setParameter('roleA', '%'."ROLE_ADMIN".'%')
            ->setParameter('roleSA', '%'."ROLE_SUPER_ADMIN".'%')
            ->orderBy('u.createdAt', 'desc');

        return $qb->getQuery()->getResult();
    }

    public function findAllProUsers(): array
    {
        $qb = $this->createQueryBuilder('u');

        $qb->where($qb->expr()->isNull('u.deleteAt'))
            ->andWhere('u.deleteAt IS NULL')
            ->andWhere('u.confirmationToken IS NOT NULL')
            ->andWhere('u.roles LIKE :roles')
            ->setParameter('roles', '%'."ROLE_USER_PRO".'%')
            ->orderBy('u.createdAt', 'desc');

        return $qb->getQuery()->getResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getUserNumber(): int
    {
        $qb = $this->createQueryBuilder('u')
            ->select('count(u.id)');

        $qb->where($qb->expr()->isNull('u.deleteAt'))
            ->andWhere('u.roles LIKE :roles')
            ->andWhere('u.roles NOT LIKE :roleA')
            ->andWhere('u.roles NOT LIKE :roleSA')
            ->setParameter('roles', '%'."".'%')
            ->setParameter('roleA', '%'."ROLE_ADMIN".'%')
            ->setParameter('roleSA', '%'."ROLE_SUPER_ADMIN".'%');

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getLastClients(): array
    {
        $qb = $this->createQueryBuilder('u');

        $qb->where($qb->expr()->isNull('u.deleteAt'))
            ->andWhere('u.roles LIKE :roles')
            ->andWhere('u.roles NOT LIKE :roleA')
            ->andWhere('u.roles NOT LIKE :roleSA')
            ->setParameter('roles', '%'."".'%')
            ->setParameter('roleA', '%'."ROLE_ADMIN".'%')
            ->setParameter('roleSA', '%'."ROLE_SUPER_ADMIN".'%')
            ->orderBy('u.createdAt', 'desc')
            ->setMaxResults(5);

        return $qb->getQuery()->getResult();
    }












    private function adminSearchData(UserSearch $search, QueryBuilder $qb): QueryBuilder
    {
        if ($search->getEmail()) {
            $qb->andWhere('u.email LIKE :email')->setParameter('email', '%'.$search->getEmail().'%');
        }

        if ($search->getPhone()) {
            $qb->andWhere('u.phone LIKE :phone')->setParameter('phone', '%'.$search->getPhone().'%');
        }

        if ($search->getCity()) {
            $qb->andWhere('u.city = :city')->setParameter('city', $search->getCity());
        }

        return $qb;
    }
}
