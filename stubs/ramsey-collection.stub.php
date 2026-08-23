<?php

declare(strict_types=1);

namespace Ramsey\Collection;

/**
 * @template-covariant T
 */
interface ArrayInterface extends \ArrayAccess, \Countable, \IteratorAggregate
{
}

/**
 * @template-covariant T
 * @extends ArrayInterface<T>
 */
interface CollectionInterface extends ArrayInterface
{
}

/**
 * @template-covariant T
 * @implements ArrayInterface<T>
 */
abstract class AbstractArray implements ArrayInterface
{
}

/**
 * @template-covariant T
 * @extends AbstractArray<T>
 * @implements CollectionInterface<T>
 */
abstract class AbstractCollection extends AbstractArray implements CollectionInterface
{
}

/**
 * @template-covariant T
 * @extends AbstractCollection<T>
 */
class Collection extends AbstractCollection
{
}

/**
 * @template-covariant T
 * @extends AbstractCollection<T>
 */
abstract class AbstractSet extends AbstractCollection
{
}

/**
 * @template-covariant T
 * @extends AbstractSet<T>
 */
class Set extends AbstractSet
{
}

/**
 * @template-covariant T
 * @extends ArrayInterface<T>
 */
interface QueueInterface extends ArrayInterface
{
}

/**
 * @template-covariant T
 * @extends AbstractArray<T>
 * @implements QueueInterface<T>
 */
class Queue extends AbstractArray implements QueueInterface
{
}

/**
 * @template-covariant T
 * @extends QueueInterface<T>
 */
interface DoubleEndedQueueInterface extends QueueInterface
{
}

/**
 * @template-covariant T
 * @extends Queue<T>
 * @implements DoubleEndedQueueInterface<T>
 */
class DoubleEndedQueue extends Queue implements DoubleEndedQueueInterface
{
}

namespace Ramsey\Collection\Map;

use Ramsey\Collection\ArrayInterface;

/**
 * @template K of array-key
 * @template-covariant T
 * @extends ArrayInterface<T>
 */
interface MapInterface extends ArrayInterface
{
}

/**
 * @template K of array-key
 * @template-covariant T
 * @extends \Ramsey\Collection\AbstractArray<T>
 * @implements MapInterface<K, T>
 */
abstract class AbstractMap extends \Ramsey\Collection\AbstractArray implements MapInterface
{
}

/**
 * @template K of array-key
 * @template-covariant T
 * @extends AbstractMap<K, T>
 */
class TypedMap extends AbstractMap
{
}