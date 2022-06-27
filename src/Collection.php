<?php

namespace Hyqo\Collection;

/**
 * @template T
 */
class Collection extends AbstractCollection
{
    public function __construct(...$items)
    {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    /** @param T $item */
    public function add(object $item): self
    {
        $this->list[] = $item;

        return $this;
    }

    public function each(\Closure $closure): self
    {
        foreach ($this->list as &$item) {
            $closure($item);
        }

        return $this;
    }

    public function map(\Closure $closure): array
    {
        $map = [];

        foreach ($this->list as $item) {
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

    public function reduce(\Closure $closure, $initial = null)
    {
        return array_reduce($this->list, $closure, $initial);
    }

    /** @return Collection<T> */
    public function slice(int $first, ?int $length = null): Reference
    {
        return Reference::create($this->list, $first, $length);
    }

    /** @return Chunks<T> */
    public function chunk(int $amount): Chunks
    {
        return new Chunks($this->list, $amount);
    }

    public function filter(\Closure $closure): self
    {
        $collection = new self();

        foreach ($this->list as $item) {
            if ($closure($item)) {
                $collection->add($item);
            }
        }

        return $collection;
    }
}
