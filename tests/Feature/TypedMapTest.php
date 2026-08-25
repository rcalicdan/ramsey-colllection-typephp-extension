<?php

declare(strict_types=1);

use Ramsey\Collection\Map\TypedMap;
use Tests\Fixtures\Domain\Car;
use Tests\Fixtures\Domain\Dog;
use TypePHP\Exception\TypeError;
use TypePHP\TypePHP;

describe('Ramsey\Collection\Map\TypedMap<K, T> Reified Generics', function () {
    test('enforces both Key (K) and Value (T) generic parameters on put() and offsetSet()', function () {
        /** @var TypedMap<string, Dog> $map */
        $map = new TypedMap('string', Dog::class);

        $map->put('primary', new Dog('AlphaDog'));
        $map['secondary'] = new Dog('BetaDog');

        expect($map->count())->toBe(2)
            ->and(TypePHP::getGenericType($map, 'K'))->toBe('string')
            ->and(TypePHP::getGenericType($map, 'T'))->toBe(Dog::class);

        expect(fn () => $map->put('error', new Car()))
            ->toThrow(TypeError::class, 'must be of type ' . Dog::class);

        expect(fn () => $map[123] = new Dog('NumericKeyDog'))
            ->toThrow(TypeError::class, 'must be of type string');
    });
});