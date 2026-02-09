<?php

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
final readonly class GetCurrencyMessage
{
    public function __construct(
        public \DateTimeImmutable $date,
    ) {
    }
}
