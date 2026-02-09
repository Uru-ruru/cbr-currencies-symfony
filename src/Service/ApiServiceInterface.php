<?php

namespace App\Service;

interface ApiServiceInterface
{
    public function get(string $date): void;
}
