<?php

namespace App\MessageHandler;

use App\Message\GetCurrencyMessage;
use App\Service\ApiServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetCurrencyMessageHandler
{
    public function __construct(
        private ApiServiceInterface $rateApiService,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(GetCurrencyMessage $message): void
    {
        $date = $message->date->format('d/m/Y');
        $this->logger->info('Fetched currency data for '.$date);

        $this->rateApiService->get($date);

        $this->logger->info('End Fetch currency data for '.$date);
    }
}
