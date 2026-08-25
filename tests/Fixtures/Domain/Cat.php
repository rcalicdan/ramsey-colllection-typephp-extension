<?php

declare(strict_types=1);

namespace Tests\Fixtures\Domain;

class Cat
{
    public function __construct(
        public string $name = 'Whiskers',
        public int $age = 2
    ) {}
}