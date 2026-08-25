<?php

declare(strict_types=1);

use Ramsey\Collection\Collection;
use Tests\Fixtures\Domain\Car;
use Tests\Fixtures\Domain\Cat;
use Tests\Fixtures\Domain\Dog;
use TypePHP\Exception\TypeError;
use TypePHP\TypePHP;

describe('Advanced TypePHP Types & Cloning on Ramsey Collections', function () {
    test('enforces positive-int constraint on Collection<positive-int>', function () {
        /** @var Collection<positive-int> $scores */
        $scores = new Collection('int');
        $scores->add(100);

        expect($scores->count())->toBe(1);

        expect(fn () => $scores->add(-50))
            ->toThrow(TypeError::class, 'positive-int');
    });

    test('enforces complex array shapes on Collection<array{id: positive-int, name: non-empty-string}>', function () {
        /** @var Collection<array{id: positive-int, name: non-empty-string}> $users */
        $users = new Collection('array');
        $users->add(['id' => 10, 'name' => 'Alice']);

        expect($users->count())->toBe(1);

        expect(fn () => $users->add(['id' => -1, 'name' => 'Alice']))
            ->toThrow(TypeError::class, "['id'] must be of type positive-int");

        expect(fn () => $users->add(['id' => 10, 'name' => '']))
            ->toThrow(TypeError::class, "['name'] must be of type non-empty-string");
    });

    test('enforces union generics on Collection<Dog|Cat>', function () {
        /** @var Collection<Dog|Cat> $pets */
        $pets = new Collection(object::class);

        $pets->add(new Dog('Rover'));
        $pets->add(new Cat('Mittens'));

        expect($pets->count())->toBe(2);

        expect(fn () => $pets->add(new Car()))
            ->toThrow(TypeError::class);
    });

    test('preserves generic type bindings in WeakMap when collection is cloned', function () {
        /** @var Collection<Dog> $original */
        $original = new Collection(Dog::class);
        $original->add(new Dog('Alpha'));

        $cloned = clone $original;
        $cloned->add(new Dog('Beta'));

        expect($cloned->count())->toBe(2)
            ->and(TypePHP::getGenericType($cloned))->toBe(Dog::class);

        expect(fn () => $cloned->add(new Car()))
            ->toThrow(TypeError::class, 'must be of type ' . Dog::class);
    });
});