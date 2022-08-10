<?php

namespace Hyqo\Collection;

/**
 * @template T
 * @implements \IteratorAggregate<int,T>
 */
class Collection implements \Countable, \IteratorAggregate, \JsonSerializable
{
    /** @var array<int,T> */
    protected $elements = [];

    /**
     * @param array<T> $items
     */
    final public function __construct(array $items = [])
    {
        $this->elements = array_values($items);
    }

    public function count(): int
    {
        return count($this->elements);
    }

    /**
     * @return \Traversable<int,T>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->elements);
    }

    /**
     * @return array<int,mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->elements;
    }

    /**
     * @param T $item
     * @return $this
     */
    public function add($item): self
    {
        $this->elements[] = $item;

        return $this;
    }

    /**
     * @return T|null
     */
    public function get(int $index)
    {
        return $this->elements[$index] ?? null;
    }

    /**
     * @param callable(T, int): void $closure
     * @return $this
     */
    public function each(callable $closure): self
    {
        foreach ($this->elements as $index => $item) {
            $closure($item, $index);
        }

        return $this;
    }

    /**
     * @param callable(T):(\Generator<int,T,mixed,void>|T) $closure
     * @return static<T>
     */
    public function map(callable $closure): self
    {
        $collection = new static();

        foreach ($this->elements as $item) {
            $result = $closure($item);

            if ($result instanceof \Generator) {
                if ($result->valid()) {
                    foreach ($result as $value) {
                        $collection->add($value);
                    }
                }
            } else {
                $collection->add($result);
            }
        }

        return $collection;
    }

    /**
     * @param callable(mixed,T): mixed $closure
     * @param mixed $initial
     * @return mixed|null
     */
    public function reduce(callable $closure, $initial = null)
    {
        return array_reduce($this->elements, $closure, $initial);
    }

    /** @return static<T> */
    public function slice(int $first, ?int $length = null): self
    {
        return new static(array_slice($this->elements, $first, $length));
    }

    /**
     * @return static<T>
     */
    public function copy(): self
    {
        return $this->slice(0);
    }

    /**
     * @param int $length
     * @return \Generator<static<T>>
     */
    public function chunk(int $length): \Generator
    {
        $count = ceil(count($this) / $length);
        $i = -1;

        while (++$i <= $count - 1) {
            yield $this->slice(($i * $length), $length);
        }
    }

    /**
     * @param callable(T): bool $closure
     * @return static<T>
     */
    public function filter(callable $closure): self
    {
        return new static(array_filter($this->elements, $closure));
    }

    /**
     * @param null|callable(T):(\Generator<array-key,T,mixed,void>|T) $closure
     * @return array<array-key,mixed>
     */
    public function toArray(?callable $closure = null): array
    {
        if (null === $closure) {
            return $this->elements;
        }

        $array = [];

        foreach ($this->elements as $item) {
            $result = $closure($item);

            if ($result instanceof \Generator) {
                if ($result->valid()) {
                    foreach ($result as $key => $value) {
                        $array[$key] = $value;
                    }
                }
            } else {
                $array[] = $result;
            }
        }

        return $array;
    }
}
