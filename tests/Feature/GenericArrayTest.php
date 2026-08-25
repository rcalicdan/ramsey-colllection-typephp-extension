<?php

declare(strict_types=1);

use Ramsey\Collection\GenericArray;
use Tests\Fixtures\Collections\DogArray;
use Tests\Fixtures\Domain\Car;
use Tests\Fixtures\Domain\Dog;
use TypePHP\Exception\TypeError;
use TypePHP\TypePHP;

describe('Ramsey\Collection\GenericArray & AbstractArray<T> Reified Generics', function () {
    test('GenericArray is typed to mixed by definition in ramsey/collection', function () {
        $array = new GenericArray();

        $array[0] = new Dog('Rex');
        $array[1] = new Car('Sedan');
        $array[2] = 'plain string';

        expect($array->count())->toBe(3);
    });

    test('custom subclass extending AbstractArray<Dog> enforces generic type T on array index assignments', function () {
        $dogArray = new DogArray();

        $dogArray[0] = new Dog('Rex');
        $dogArray[1] = new Dog('Max');

        expect($dogArray->count())->toBe(2)
            ->and(TypePHP::getGenericType($dogArray))->toBe(Dog::class);

        expect(fn () => $dogArray[2] = new Car())
            ->toThrow(TypeError::class, 'must be of type ' . Dog::class);
    });
});