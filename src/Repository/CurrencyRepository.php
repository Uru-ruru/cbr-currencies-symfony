<?php

namespace App\Repository;

use App\Entity\Currency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

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

    public function add(\DateTimeImmutable $date, string $numCode, string $charCode, int $nominal, string $name, string $value, string $vunitRate): void
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

    private function createCurrency(\DateTimeImmutable $date, string $numCode, string $charCode, int $nominal, string $name, string $value, string $vunitRate): Currency
    {
        $rate = $this->findOneBy(['date' => $date, 'numCode' => $numCode]);
        if ($rate instanceof Currency) {
            $this->logger->info('Currency already exists');

            return $rate;
        }

        $rate = new Currency();
        $rate->setDate($date)
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
            'date' => $rate->getDate()?->format('Y-m-d'),
            'numCode' => $rate->getNumCode(),
            'charCode' => $rate->getCharCode(),
            'nominal' => $rate->getNominal(),
            'name' => $rate->getName(),
            'value' => $rate->getValue(),
            'vunitRate' => $rate->getVunitRate(),
        ]);
    }
}
