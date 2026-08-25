<?php

declare(strict_types=1);

use Ramsey\Collection\Collection;
use Tests\Fixtures\Domain\Car;
use Tests\Fixtures\Domain\Cat;
use Tests\Fixtures\Domain\Dog;
use TypePHP\Exception\TypeError;
use TypePHP\TypePHP;

describe('Ramsey\Collection\Collection<T> Reified Generics', function () {
    test('enforces generic type T on add() and array append []', function () {
        /** @var Collection<Dog> $dogs */
        $dogs = new Collection(Dog::class);

        $dogs->add(new Dog('Rex'));
        $dogs[] = new Dog('Buddy');

        expect($dogs->count())->toBe(2)
            ->and(TypePHP::getGenericType($dogs))->toBe(Dog::class);

        expect(fn () => $dogs->add(new Car()))
            ->toThrow(TypeError::class, 'must be of type ' . Dog::class);

        expect(fn () => $dogs[] = new Cat())
            ->toThrow(TypeError::class, 'must be of type ' . Dog::class);
    });

    test('reifies return types on first() and last()', function () {
        /** @var Collection<Dog> $dogs */
        $dogs = new Collection(Dog::class);
        $dogs->add(new Dog('FirstDog'));
        $dogs->add(new Dog('LastDog'));

        expect($dogs->first())->toBeInstanceOf(Dog::class)
            ->and($dogs->first()->name)->toBe('FirstDog')
            ->and($dogs->last()->name)->toBe('LastDog');
    });

    test('enforces generic type inside filter() callbacks', function () {
        /** @var Collection<Dog> $dogs */
        $dogs = new Collection(Dog::class);
        $dogs->add(new Dog('Rex', 5));
        $dogs->add(new Dog('Pup', 1));

        $filtered = $dogs->filter(function (Dog $dog): bool {
            return $dog->age > 2;
        });

        expect($filtered->count())->toBe(1)
            ->and($filtered->first()->name)->toBe('Rex');
    });
});