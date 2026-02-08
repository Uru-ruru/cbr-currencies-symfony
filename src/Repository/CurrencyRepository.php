<?php

namespace App\Repository;

use App\Entity\Currency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Clock\DatePoint;

/**
 * @extends ServiceEntityRepository<Currency>
 */
class CurrencyRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct($registry, Currency::class);
    }

    //    /**
    //     * @return Currency[] Returns an array of Currency objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Currency
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult();
    }

    public function add(\DateTime $date, string $numCode, string $charCode, int $nominal, string $name, string $value, string $vunitRate): void
    {
        try {
            $rate = $this->createCurrency($date, $numCode, $charCode, $nominal, $name, $value, $vunitRate);
            $this->logCurrencySave($rate);
            $this->getEntityManager()->persist($rate);
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            $this->logger->error('Error saving currency', [
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @throws \DateMalformedStringException
     */
    private function createCurrency(\DateTime $date, string $numCode, string $charCode, int $nominal, string $name, string $value, string $vunitRate): Currency
    {
        $rate = new Currency();
        $rate->setDate(new DatePoint($date->format('Y-m-d')))
            ->setNumCode($numCode)
            ->setCharCode($charCode)
            ->setNominal($nominal)
            ->setName($name)
            ->setValue($value)
            ->setVunitRate($vunitRate);

        return $rate;
    }

    private function logCurrencySave(Currency $rate): void
    {
        $this->logger->info('Saving currency', [
            'numCode' => $rate->getNumCode(),
            'charCode' => $rate->getCharCode(),
            'nominal' => $rate->getNominal(),
            'name' => $rate->getName(),
            'value' => $rate->getValue(),
            'vunitRate' => $rate->getVunitRate(),
        ]);
    }
}
