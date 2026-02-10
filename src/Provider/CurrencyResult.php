<?php

namespace App\Provider;

readonly class CurrencyResult
{
    public function __construct(
        private ?string $rate = null,
        private ?string $dayBeforeRate = null,
        private ?string $currency = null,
        private ?string $baseCurrency = null,
        private ?string $dayBeforeDiff = null,
    ) {
    }

    public function getRate(): ?string
    {
        return $this->rate;
    }

    public function getDayBeforeRate(): ?string
    {
        return $this->dayBeforeRate;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function getBaseCurrency(): ?string
    {
        return $this->baseCurrency;
    }

    public function getDayBeforeDiff(): ?string
    {
        return $this->dayBeforeDiff;
    }
}
