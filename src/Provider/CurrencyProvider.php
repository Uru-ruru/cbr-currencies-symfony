<?php

namespace App\Provider;

use App\Repository\CurrencyRepository;
use Psr\Log\LoggerInterface;

readonly class CurrencyProvider implements CurrencyProviderInterface
{
    public function __construct(
        private CurrencyRepository $currencyRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function provide(array $params, ?array $toParams = null): CurrencyResult
    {
        $this->logger->info('Finding currency rates', $params);

        $rates = $this->getRates($params);

        if ($toParams) {
            $baseRates = $this->getRates($toParams);

            if (null === $baseRates->getRate() || 0.0 === $baseRates->getRate()) {
                throw new \InvalidArgumentException('No data rate found for current currency');
            }

            if (null === $baseRates->getDayBeforeRate() || 0.0 === $baseRates->getDayBeforeRate()) {
                throw new \InvalidArgumentException('No day Before data rate found for current currency');
            }

            $rates = new CurrencyResult(
                rate: $rates->getRate() / $baseRates->getRate(),
                dayBeforeRate: $rates->getDayBeforeRate() / $baseRates->getDayBeforeRate(),
                currency: $rates->getCurrency(),
                baseCurrency: $baseRates->getCurrency(),
                dayBeforeDiff: round(($rates->getRate() / $baseRates->getRate()) - ($rates->getDayBeforeRate() / $baseRates->getDayBeforeRate()), 4)
            );
        }

        return $rates;
    }

    /**
     * @throws \DateMalformedStringException
     */
    private function getRates($params): CurrencyResult
    {
        $resultRate = $this->currencyRepository->findOneBy($params);
        $dayBeforeResultRate = null;
        $dayBefore = $resultRate?->getDate()?->modify('-1 day');

        if ($resultRate && $dayBefore) {
            $dayBeforeParams = [
                ...$params,
                'date' => $dayBefore,
            ];

            $this->logger->info('Finding dayBeforeParams rates', $dayBeforeParams);

            $dayBeforeResultRate = $this->currencyRepository->findOneBy($dayBeforeParams);

            if ($dayBeforeResultRate) {
                $diff = round($resultRate->getValue() - $dayBeforeResultRate->getValue(), 4);
            } else {
                $diff = null;
            }
        }

        return new CurrencyResult(
            rate: $resultRate?->getValue(),
            dayBeforeRate: $dayBeforeResultRate?->getValue(),
            currency: $params['charCode'] ?? null,
            dayBeforeDiff: $diff ?? null
        );
    }
}
