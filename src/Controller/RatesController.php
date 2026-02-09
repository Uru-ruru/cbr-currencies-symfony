<?php

namespace App\Controller;

use App\Provider\CurrencyProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class RatesController extends AbstractController
{
    public function __construct(
        private readonly CurrencyProviderInterface $currencyProvider,
    ) {
    }

    #[Route('/{currency}/{date?}',
        name: 'app_rates_base',
        requirements: ['currency' => '[A-Za-z]{3}', 'date' => '\d{4}-\d{2}-\d{2}'],
        methods: ['GET']
    )]
    public function index(string $currency, ?string $date = null): JsonResponse
    {
        $date = $date ?? new \DateTimeImmutable('today')->format('Y-m-d');
        $rates = $this->currencyProvider->provide([
            'date' => \DateTimeImmutable::createFromFormat('Y-m-d', $date),
            'charCode' => strtoupper($currency),
        ]);

        return $this->json($rates);
    }

    #[Route('/{currency}/{baseCurrency}/{date?}',
        name: 'app_rates',
        requirements: ['currency' => '[A-Za-z]{3}', 'baseCurrency' => '[A-Za-z]{3}', 'date' => '\d{4}-\d{2}-\d{2}'],
        methods: ['GET'],
        priority: 2
    )]
    public function exchange(string $currency, string $baseCurrency, ?string $date = null): JsonResponse
    {
        $date = $date ?? new \DateTimeImmutable('today')->format('Y-m-d');
        $rates = $this->currencyProvider->provide(
            [
                'date' => \DateTimeImmutable::createFromFormat('Y-m-d', $date),
                'charCode' => strtoupper($currency),
            ],
            [
                'date' => \DateTimeImmutable::createFromFormat('Y-m-d', $date),
                'charCode' => strtoupper($baseCurrency),
            ]);

        return $this->json($rates);
    }
}
