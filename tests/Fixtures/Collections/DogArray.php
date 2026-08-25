<?php

declare(strict_types=1);

namespace Tests\Fixtures\Collections;

use Ramsey\Collection\AbstractArray;
use Tests\Fixtures\Domain\Dog;

/**
 * @extends AbstractArray<Dog>
 */
class DogArray extends AbstractArray
{
}