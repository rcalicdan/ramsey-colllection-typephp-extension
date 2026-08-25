<?php

declare(strict_types=1);

use Ramsey\Collection\Queue;
use Tests\Fixtures\Domain\Car;
use Tests\Fixtures\Domain\Dog;
use TypePHP\Exception\TypeError;
use TypePHP\TypePHP;

describe('Ramsey\Collection\Queue<T> Reified Generics', function () {
    test('enforces generic type T on add(), offer(), and FIFO extraction', function () {
        /** @var Queue<Dog> $queue */
        $queue = new Queue(Dog::class);

        expect($queue->add(new Dog('FirstIn')))->toBeTrue();
        expect($queue->offer(new Dog('SecondIn')))->toBeTrue();

        expect(TypePHP::getGenericType($queue))->toBe(Dog::class);
        expect($queue->peek()->name)->toBe('FirstIn');
        expect($queue->poll()->name)->toBe('FirstIn');
        expect($queue->count())->toBe(1);

        expect(fn () => $queue->add(new Car()))
            ->toThrow(TypeError::class, 'must be of type ' . Dog::class);
    });
});