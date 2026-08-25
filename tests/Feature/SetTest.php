<?php

declare(strict_types=1);

use Ramsey\Collection\Set;
use Tests\Fixtures\Domain\Car;
use Tests\Fixtures\Domain\Dog;
use TypePHP\Exception\TypeError;
use TypePHP\TypePHP;

describe('Ramsey\Collection\Set<T> Reified Generics', function () {
    test('enforces generic type T on Set while preventing duplicate entries', function () {
        /** @var Set<Dog> $dogSet */
        $dogSet = new Set(Dog::class);
        $dog1 = new Dog('Rex');
        $dog2 = new Dog('Buddy');

        expect($dogSet->add($dog1))->toBeTrue();
        expect($dogSet->add($dog1))->toBeFalse(); 
        expect($dogSet->add($dog2))->toBeTrue();

        expect($dogSet->count())->toBe(2)
            ->and(TypePHP::getGenericType($dogSet))->toBe(Dog::class);

        expect(fn () => $dogSet->add(new Car()))
            ->toThrow(TypeError::class, 'must be of type ' . Dog::class);
    });
});