<?php

declare(strict_types=1);

namespace Tests\Fixtures\Collections;

use Ramsey\Collection\Map\AbstractTypedMap;
use Tests\Fixtures\Domain\Dog;

/**
 * @extends AbstractTypedMap<string, Dog>
 */
class DogTypedMap extends AbstractTypedMap
{
    public function getKeyType(): string
    {
        return 'string';
    }

    public function getValueType(): string
    {
        return Dog::class;
    }
}