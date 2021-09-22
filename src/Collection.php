<?php

namespace Hyqo\Collection;

/**
 * @template T
 */
class Collection extends BaseCollection
{
    public function __construct(...$items)
    {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

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

    public function reduce(\Closure $closure, mixed $initial = null): mixed
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
