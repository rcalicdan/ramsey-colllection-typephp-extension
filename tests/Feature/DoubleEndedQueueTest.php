<?php

declare(strict_types=1);

use Ramsey\Collection\DoubleEndedQueue;
use Tests\Fixtures\Domain\Car;
use Tests\Fixtures\Domain\Dog;
use TypePHP\Exception\TypeError;
use TypePHP\TypePHP;

describe('Ramsey\Collection\DoubleEndedQueue<T> Reified Generics', function () {
    test('enforces generic type T on addFirst(), addLast(), offerFirst(), and offerLast()', function () {
        /** @var DoubleEndedQueue<Dog> $deque */
        $deque = new DoubleEndedQueue(Dog::class);

        $deque->addFirst(new Dog('FrontDog'));
        $deque->addLast(new Dog('BackDog'));

        expect(TypePHP::getGenericType($deque))->toBe(Dog::class)
            ->and($deque->firstElement()->name)->toBe('FrontDog')
            ->and($deque->lastElement()->name)->toBe('BackDog');

        expect(fn () => $deque->addFirst(new Car()))
            ->toThrow(TypeError::class, 'must be of type ' . Dog::class);

        expect(fn () => $deque->addLast(new Car()))
            ->toThrow(TypeError::class, 'must be of type ' . Dog::class);
    });
});