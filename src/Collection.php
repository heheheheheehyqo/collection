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
        foreach ($items as $item) {
            $this->add($item);
        }
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

    /** @return T */
    public function get(int $index)
    {
        return $this->elements[$index];
    }

    /**
     * @param \Closure(T): void $closure
     * @return $this
     */
    public function each(\Closure $closure): self
    {
        foreach ($this->elements as $item) {
            $closure($item);
        }

        return $this;
    }

    /**
     * @param \Closure(T):(\Generator<array-key,mixed,void>|mixed) $closure
     * @return array<array-key,mixed|null>
     */
    public function map(\Closure $closure): array
    {
        $map = [];

        foreach ($this->elements as $item) {
            $result = $closure($item);

            if ($result instanceof \Generator) {
                $array = iterator_to_array($result);

                if (!$array) {
                    return array_fill(0, $this->count(), null);
                }

                if (is_string(array_keys($array)[0])) {
                    foreach ($array as $key => $value) {
                        $map[$key] = $value;
                    }
                } else {
                    /** @noinspection SlowArrayOperationsInLoopInspection */
                    $map = array_merge($map, $array);
                }
            } else {
                $map[] = $result;
            }
        }

        return $map;
    }

    /**
     * @param \Closure(mixed,T): mixed $closure
     * @param mixed $initial
     * @return mixed|null
     */
    public function reduce(\Closure $closure, $initial = null)
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

    /** @return \Generator<static<T>> */
    public function chunk(int $amount): \Generator
    {
        $count = ceil(count($this) / $amount);
        $i = -1;

        while (++$i <= $count - 1) {
            yield $this->slice(($i * $amount), $amount);
        }
    }

    /**
     * @param \Closure(T): bool $closure
     * @return static<T>
     */
    public function filter(\Closure $closure): self
    {
        return new static(array_filter($this->elements, $closure));
    }
}
