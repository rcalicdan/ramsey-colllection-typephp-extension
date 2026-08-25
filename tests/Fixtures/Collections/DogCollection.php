<?php

declare(strict_types=1);

namespace Tests\Fixtures\Collections;

use Ramsey\Collection\AbstractCollection;
use Tests\Fixtures\Domain\Dog;

/**
 * @extends AbstractCollection<Dog>
 */
class DogCollection extends AbstractCollection
{
    public function getType(): string
    {
        return Dog::class;
    }
}