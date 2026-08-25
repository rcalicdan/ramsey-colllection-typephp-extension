# Ramsey Collection TypePHP Extension

Enables TypePHP runtime type checking and reified generic enforcement for [ramsey/collection](https://github.com/ramsey/collection).

[![Latest Version](https://img.shields.io/packagist/v/rcalicdan/ramsey-collection-typephp-extension.svg?style=flat&color=blue)](https://packagist.org/packages/rcalicdan/ramsey-collection-typephp-extension)
[![License](https://img.shields.io/packagist/l/rcalicdan/ramsey-collection-typephp-extension.svg?style=flat)](https://github.com/rcalicdan/ramsey-collection-typephp-extension/blob/main/LICENCE.md)
[![PHP Version](https://img.shields.io/packagist/php-v/rcalicdan/ramsey-collection-typephp-extension.svg?style=flat)](https://php.net/)

---

## Overview

The `ramsey/collection` library provides typed collection structures in PHP. However, because its internal type checking relies on standard string comparisons and `instanceof` checks against basic class names or primitive types (`int`, `string`, `object`), it cannot enforce complex static analysis annotations at runtime.

This extension bridges **TypePHP** and **ramsey/collection**, allowing all collection classes (`Collection`, `Set`, `Queue`, `DoubleEndedQueue`, `TypedMap`, etc.) to enforce modern PHPDoc type contracts dynamically without changing their public API.

---

## Native Ramsey Collection Limitations vs. TypePHP Capabilities

| Feature | Native `ramsey/collection` | With TypePHP Extension |
| :--- | :--- | :--- |
| **Basic Class Checking** | Supported (`new Collection(User::class)`) | Supported and reified |
| **Scalar Refinements** | Unsupported (Only checks broad `int` or `string`) | Supported (`positive-int`, `non-empty-string`, `int<1, 100>`, `numeric-string`, etc.) |
| **Union Types** | Unsupported (Cannot accept `Dog \| Cat` without base class) | Supported (`Collection<Dog \| Cat>`) |
| **Array Shapes** | Unsupported (Only checks broad `array`) | Supported (`Collection<array{id: positive-int, name: non-empty-string}>`) |
| **Intersection & DNF Types** | Unsupported | Supported (`Collection<Countable & ArrayAccess>`) |
| **Closure Parameter Validation** | Unsupported (Closure arguments in `filter()` are not checked) | Supported (`filter(callable(T): bool)`) |
| **Runtime Generic Introspection** | Unsupported (Cannot inspect template `T` via Reflection) | Supported (`TypePHP::getGenericType($collection)`) |
| **Cloning State Preservation** | Partial | Fully preserved and isolated in memory via `\WeakMap` |

### Key Capabilities Added to Ramsey Collections

1. **Scalar Refinements within Generics**: Native collections can only restrict elements to basic primitives like `int` or `string`. With TypePHP, you can restrict collections to `positive-int`, `non-empty-string`, `int<min, max>`, `uppercase-string`, and bitmasks.
2. **Union Types within Generics**: Native collections require all elements to extend a single common ancestor or be marked as `object`. TypePHP allows heterogeneous union types such as `Collection<Dog|Cat>`.
3. **Array Shapes within Generics**: Validate structured associative arrays and tuples directly when inserted into a collection, ensuring nested keys and values adhere to schema rules.
4. **Higher-Order Closures**: Methods like `filter()` validate incoming elements against the declared callback signature, preventing runtime type mismatches inside closure bodies.
5. **Reified Generic Introspection**: Inspect the concrete generic type assigned to any collection instance at runtime using the `TypePHP::getGenericType()` API.

---

## Installation

### Development & Testing (Recommended)

This library is primarily intended as a development dependency for local development, testing suites (Pest, PHPUnit), and CI/CD pipelines:

```bash
composer require --dev rcalicdan/ramsey-collection-typephp-extension
```

### Production Runtime (Optional)

If your architecture requires strict runtime generic enforcement and input boundary validation in production environments, you may install it as a standard dependency:

```bash
composer require rcalicdan/ramsey-collection-typephp-extension
```

*Note: In production environments, it is recommended to enable OPcache, JIT, and execute `php bin/typephp cache:warm` during your deployment pipeline for zero-overhead AST execution.*

---

## Configuration

Register the extension in your project's `typephp.php` configuration file:

```php
// typephp.php
use Rcalicdan\RamseyExtension\Extension\RamseyCollectionExtension;

return [
    'enabled' => true,

    'extensions' => [
        RamseyCollectionExtension::class,
    ],

    'include' => [
        'src/**',
        'app/**',
    ],

    'exclude' => [
        'vendor/**',
    ],
];
```

---

## Usage Examples

### 1. Reified Class Generics (`Collection<T>`)

```php
use Ramsey\Collection\Collection;
use App\Domain\Dog;
use App\Domain\Car;

/** @var Collection<Dog> $dogs */
$dogs = new Collection(Dog::class);

$dogs->add(new Dog('Rex'));   // Valid
$dogs[] = new Dog('Max');     // Valid (ArrayAccess supported)

$dogs->add(new Car());        // Throws TypePHP\Exception\TypeError
```

---

### 2. Refined Scalar Constraints (`positive-int`, `non-empty-string`)

```php
use Ramsey\Collection\Collection;

/** @var Collection<positive-int> $scores */
$scores = new Collection('int');

$scores->add(100);  // Valid
$scores->add(42);   // Valid

$scores->add(-5);   // Throws TypeError: must be of type positive-int, negative int (-5) given
$scores->add(0);    // Throws TypeError: must be of type positive-int, zero int (0) given
```

---

### 3. Array Shapes inside Collections

```php
use Ramsey\Collection\Collection;

/** @var Collection<array{id: positive-int, name: non-empty-string}> $users */
$users = new Collection('array');

$users->add(['id' => 1, 'name' => 'Alice']); // Valid

// Fails if ID is negative or name is empty:
$users->add(['id' => -1, 'name' => 'Alice']); // Throws TypeError: ['id'] must be of type positive-int
$users->add(['id' => 2, 'name' => '']);       // Throws TypeError: ['name'] must be of type non-empty-string
```

---

### 4. Union Generics (`Collection<Dog|Cat>`)

```php
use Ramsey\Collection\Collection;
use App\Domain\Dog;
use App\Domain\Cat;
use App\Domain\Car;

/** @var Collection<Dog|Cat> $pets */
$pets = new Collection(object::class);

$pets->add(new Dog('Rex'));      // Valid
$pets->add(new Cat('Whiskers')); // Valid

$pets->add(new Car());           // Throws TypeError: must be of type (App\Domain\Dog | App\Domain\Cat)
```

---

### 5. Generic Typed Maps (`TypedMap<K, T>`)

Enforces both key (`K`) and value (`T`) constraints simultaneously:

```php
use Ramsey\Collection\Map\TypedMap;
use App\Domain\Dog;
use App\Domain\Car;

/** @var TypedMap<string, Dog> $map */
$map = new TypedMap('string', Dog::class);

$map->put('alpha', new Dog('AlphaDog')); // Valid
$map['beta'] = new Dog('BetaDog');       // Valid

// Invalid Value
$map->put('gamma', new Car());           // Throws TypeError: must be of type App\Domain\Dog

// Invalid Key (Integer key when string is required)
$map[123] = new Dog('Charlie');          // Throws TypeError: must be of type string
```

---

### 6. Queues & Double-Ended Queues (`Queue<T>` & `DoubleEndedQueue<T>`)

```php
use Ramsey\Collection\DoubleEndedQueue;
use App\Domain\Dog;
use App\Domain\Car;

/** @var DoubleEndedQueue<Dog> $deque */
$deque = new DoubleEndedQueue(Dog::class);

$deque->addFirst(new Dog('FrontDog')); // Valid
$deque->addLast(new Dog('BackDog'));   // Valid

$deque->addFirst(new Car());           // Throws TypeError
```

---

### 7. Custom Collection Subclassing (`@extends AbstractCollection<T>`)

When creating your own collection subclasses, TypePHP inherits the generic parameter directly from your class DocBlock:

```php
namespace App\Collections;

use Ramsey\Collection\AbstractCollection;
use App\Domain\Dog;
use App\Domain\Car;

/**
 * @extends AbstractCollection<Dog>
 */
class DogCollection extends AbstractCollection
{
    public function getType(): string
    {
        return Dog::class;
    }
}

$dogs = new DogCollection();
$dogs->add(new Dog('Rex')); // Valid
$dogs->add(new Car());       // Throws TypeError
```

---

### 8. Runtime Generic Introspection & Cloning

```php
use Ramsey\Collection\Collection;
use App\Domain\Dog;
use App\Domain\Car;
use TypePHP\TypePHP;

/** @var Collection<Dog> $dogs */
$dogs = new Collection(Dog::class);

// Inspect the bound generic type at runtime:
echo TypePHP::getGenericType($dogs); // Returns "App\Domain\Dog"

// Clones automatically inherit and isolate their bound generic state:
$cloned = clone $dogs;
$cloned->add(new Dog('ClonedDog')); // Valid
$cloned->add(new Car());            // Throws TypeError
```

---

## Supported Ramsey Collection Classes

| Class | Generic Parameters | Supported Operations |
| :--- | :--- | :--- |
| `Ramsey\Collection\Collection<T>` | `T` | `add()`, `offsetSet()`, `filter()`, `first()`, `last()`, `where()`, `merge()` |
| `Ramsey\Collection\Set<T>` | `T` | `add()`, `offsetSet()`, duplicate rejection with type check |
| `Ramsey\Collection\Queue<T>` | `T` | `add()`, `offer()`, `element()`, `peek()`, `poll()`, `remove()` |
| `Ramsey\Collection\DoubleEndedQueue<T>` | `T` | `addFirst()`, `addLast()`, `offerFirst()`, `offerLast()`, `firstElement()`, `lastElement()` |
| `Ramsey\Collection\AbstractArray<T>` | `T` | Array access, serialization, and iteration |
| `Ramsey\Collection\Map\TypedMap<K, T>` | `K of array-key`, `T` | `put()`, `putIfAbsent()`, `replace()`, `offsetSet()` |
| `Ramsey\Collection\Map\AbstractTypedMap<K, T>` | `K of array-key`, `T` | Subclasses inheriting custom typed maps |

---

## Testing

Run the test suite using Pest:

```bash
composer test
# or
./vendor/bin/pest
```

---

## License

This project is open-source software licensed under the [MIT License](LICENCE.md).
