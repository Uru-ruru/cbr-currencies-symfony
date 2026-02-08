<?php

namespace App\Controller;

use App\Repository\CurrencyRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class RatesController extends AbstractController
{
    public function __construct(
        private readonly CurrencyRepository $currencyRepository,
        private readonly LoggerInterface $logger,
    ) {
    }

    #[Route('/rates', name: 'app_rates')]
    public function index(): JsonResponse
    {
        $rates = $this->currencyRepository->findAll();
        $this->logger->info('Rates loaded: '.count($rates));

        return $this->json($rates);
    }
}
