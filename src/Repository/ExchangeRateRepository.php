<?php

namespace App\Repository;

use App\Entity\ExchangeRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExchangeRate>
 *
 * @method ExchangeRate|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExchangeRate|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExchangeRate[]    findAll()
 * @method ExchangeRate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExchangeRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExchangeRate::class);
    }

    public function add(ExchangeRate $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ExchangeRate $entity, bool $flush = false): void
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

    public function findOneWithCurrencyPair(string $firstCurrencyCode, string $secondCurrencyCode): ?ExchangeRate
    {
        $expr = $this->getEntityManager()->getExpressionBuilder();

        return $this->createQueryBuilder('o')
            ->addSelect('sourceCurrency')
            ->addSelect('targetCurrency')
            ->innerJoin('o.sourceCurrency', 'sourceCurrency')
            ->innerJoin('o.targetCurrency', 'targetCurrency')
            ->andWhere($expr->orX(
                'sourceCurrency.code = :firstCurrency AND targetCurrency.code = :secondCurrency',
                'targetCurrency.code = :firstCurrency AND sourceCurrency.code = :secondCurrency'
            ))
            ->setParameter('firstCurrency', $firstCurrencyCode)
            ->setParameter('secondCurrency', $secondCurrencyCode)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
