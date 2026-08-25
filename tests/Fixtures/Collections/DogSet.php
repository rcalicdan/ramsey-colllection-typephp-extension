<?php

declare(strict_types=1);

namespace Tests\Fixtures\Collections;

use Ramsey\Collection\AbstractSet;
use Tests\Fixtures\Domain\Dog;

/**
 * @extends AbstractSet<Dog>
 */
class DogSet extends AbstractSet
{
    public function getType(): string
    {
        return Dog::class;
    }
}