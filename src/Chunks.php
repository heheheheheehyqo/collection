<?php

namespace Hyqo\Collection;

/**
 * @template T
 * @implements \IteratorAggregate<Reference<T>>
 */
class Chunks implements \IteratorAggregate, \Countable, \JsonSerializable
{
    /** @var array<T> */
    protected $source;

    /** @var int */
    protected $amount;

    /** @var int */
    protected $count;

    /**
     * @param array<T> $source
     * @param int $amount
     */
    public function __construct(array &$source, int $amount)
    {
        $this->source = &$source;
        $this->amount = $amount;

        $this->count = (int)ceil(count($this->source) / $this->amount);
    }

    public function count(): int
    {
        return $this->count;
    }

    /** @return \Generator<Reference<T>> */
    public function getIterator(): \Generator
    {
        $count = $this->count;
        $i = -1;

        while (++$i <= $count - 1) {
            yield new Reference($this->source, ($i * $this->amount), $this->amount);
        }
    }

    /**
     * @return array<int,mixed>
     */
    public function jsonSerialize(): array
    {
        return iterator_to_array($this);
    }
}
