<?php

namespace Hyqo\Collection;

/**
 * @template T
 * @implements \IteratorAggregate<int,T>
 *
 * @mixin Collection<T>
 */
class Reference implements \Countable, \IteratorAggregate, \JsonSerializable
{
    /** @var array<T> */
    protected $source;

    /** @var Collection<T> */
    protected $collection = null;

    /** @var int */
    protected $first;

    /** @var int|null */
    protected $length;

    /**
     * @param array<T> $source
     */
    public function __construct(array &$source, int $first, ?int $length = null)
    {
        $this->source = &$source;
        $this->first = $first;
        $this->length = $length;
    }

    /**
     * @return Collection<T>
     */
    private function collection(): Collection
    {
        if (null === $this->collection) {
            $this->collection = new Collection(array_slice($this->source, $this->first, $this->length));
        }

        return $this->collection;
    }

    /**
     * @param array<string,mixed> $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return $this->collection()->$name(...$arguments);
    }

    public function count(): int
    {
        return count($this->collection());
    }

    /**
     * @return \Traversable<int,T>
     */
    public function getIterator(): \Traversable
    {
        return $this->collection();
    }

    /**
     * @return array<int,mixed>
     */
    public function jsonSerialize(): array
    {
        return iterator_to_array($this->collection());
    }
}
