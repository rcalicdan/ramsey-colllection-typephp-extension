<?php

declare(strict_types=1);

namespace Tests\Fixtures\Domain;

class Car
{
    public function __construct(
        public string $model = 'Sedan'
    ) {}
}