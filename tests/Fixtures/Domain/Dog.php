<?php

declare(strict_types=1);

namespace Tests\Fixtures\Domain;

class Dog
{
    public function __construct(
        public string $name = 'Rex',
        public int $age = 3
    ) {}
}