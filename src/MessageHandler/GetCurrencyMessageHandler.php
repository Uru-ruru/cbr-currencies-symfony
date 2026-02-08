<?php

namespace App\MessageHandler;

use App\Message\GetCurrencyMessage;
use App\Repository\CurrencyRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsMessageHandler]
final class GetCurrencyMessageHandler
{
    public function __construct(
        private readonly CurrencyRepository  $currencyRepository,
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface     $logger,
    )
    {
    }

    public const string URL = 'https://www.cbr.ru/scripts/XML_daily.asp?date_req=';

    /**
     * @throws \Exception|TransportExceptionInterface
     */
    public function __invoke(GetCurrencyMessage $message): void
    {
        $date = $message->date->format('d/m/Y');
        $this->logger->info('Fetched currency data for ' . $date);

        $xml = $this->loadXml($date);
        if ($xml === '') {
            $this->logger->error('Failed to fetch currency data');
            throw new \RuntimeException('Failed to fetch currency data');
        }
        $this->logger->debug('XML: ' . $xml);

        $crawler = new Crawler($xml);

        foreach ($crawler->filterXPath('/ValCurs/Valute') as $valute) {
            $valuteNode = new Crawler($valute);
            $this->logger->info('Adding currency ' . $valuteNode->filterXPath('//CharCode')->text());
            $this->currencyRepository->add(
                date: $message->date,
                numCode: $valuteNode->filterXPath('//NumCode')->text(),
                charCode: $valuteNode->filterXPath('//CharCode')->text(),
                nominal: (int)$valuteNode->filterXPath('//Nominal')->text(),
                name: $valuteNode->filterXPath('//Name')->text(),
                value: $valuteNode->filterXPath('//Value')->text(),
                vunitRate: $valuteNode->filterXPath('//VunitRate')->text()
            );
        }

        $this->logger->info('End Fetch currency data for ' . $date);
    }

    private function loadXml(string $date): string
    {
        try {
            $response = $this->httpClient->request(
                'GET',
                self::URL . $date,
                [
                    'timeout' => 10,
                ]
            );
            return $response->getContent();
        } catch (\Throwable $e) {
            $this->logger->error('Failed to fetch currency data', [
                'exception' => $e->getMessage(),
            ]);
            return '';
        }
    }
}
