<?php

declare(strict_types=1);

use Tests\Fixtures\Collections\DogCollection;
use Tests\Fixtures\Collections\DogSet;
use Tests\Fixtures\Collections\DogTypedMap;
use Tests\Fixtures\Domain\Car;
use Tests\Fixtures\Domain\Dog;
use TypePHP\Exception\TypeError;
use TypePHP\TypePHP;

describe('Custom Subclass Generic Inheritance (@extends AbstractCollection<Dog>)', function () {
    test('DogCollection inherits Dog generic parameter from class docblock', function () {
        $dogs = new DogCollection();
        $dogs->add(new Dog('CustomRex'));

        expect($dogs->count())->toBe(1)
            ->and(TypePHP::getGenericType($dogs))->toBe(Dog::class);

        expect(fn () => $dogs->add(new Car()))
            ->toThrow(TypeError::class, 'must be of type ' . Dog::class);
    });

    test('DogSet inherits Dog generic parameter from class docblock', function () {
        $dogSet = new DogSet();
        $dogSet->add(new Dog('UniqueDog'));

        expect($dogSet->count())->toBe(1)
            ->and(TypePHP::getGenericType($dogSet))->toBe(Dog::class);

        expect(fn () => $dogSet->add(new Car()))
            ->toThrow(TypeError::class, 'must be of type ' . Dog::class);
    });

    test('DogTypedMap inherits <string, Dog> generic parameters from class docblock', function () {
        $dogMap = new DogTypedMap();
        $dogMap->put('dog_key', new Dog('MappedDog'));

        expect($dogMap->count())->toBe(1)
            ->and(TypePHP::getGenericType($dogMap, 'K'))->toBe('string')
            ->and(TypePHP::getGenericType($dogMap, 'T'))->toBe(Dog::class);

        expect(fn () => $dogMap->put('dog_key', new Car()))
            ->toThrow(TypeError::class, 'must be of type ' . Dog::class);
    });
});