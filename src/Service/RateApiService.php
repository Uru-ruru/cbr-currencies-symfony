<?php

namespace App\Service;

use App\Repository\CurrencyRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class RateApiService implements ApiServiceInterface
{
    public const string URL = 'https://www.cbr.ru/scripts/XML_daily.asp?date_req=';

    public function __construct(
        private CurrencyRepository $currencyRepository,
        private LoggerInterface $logger,
        private HttpClientInterface $httpClient,
    ) {
    }

    public function get(string $date): void
    {
        $xml = $this->loadXml($date);
        if ('' === $xml) {
            $this->logger->error('Failed to fetch currency data');
            throw new \RuntimeException('Failed to fetch currency data');
        }

        $crawler = new Crawler();
        $crawler->addXmlContent($xml);
        $documentDate = $crawler->filterXPath('//ValCurs')->attr('Date');

        $crawler->filterXPath('//Valute')->each(function (Crawler $node) use ($documentDate) {
            $this->currencyRepository->add(
                date: \DateTimeImmutable::createFromFormat('d.m.Y', $documentDate),
                numCode: $node->filter('NumCode')->text(),
                charCode: $node->filter('CharCode')->text(),
                nominal: (int) $node->filter('Nominal')->text(),
                name: $node->filter('Name')->text(),
                value: (float) str_replace(',', '.', $node->filter('Value')->text()),
                vunitRate: (float) str_replace(',', '.', $node->filter('VunitRate')->text()),
            );
        });
    }

    private function loadXml(string $date): string
    {
        try {
            $response = $this->httpClient->request(
                'GET',
                self::URL.$date,
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
