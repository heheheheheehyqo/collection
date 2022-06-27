<?php

namespace Hyqo\Collection;

/**
 * @template T
 */
class Chunks implements \IteratorAggregate, \Countable, \JsonSerializable
{
    /** @var array */
    protected $source;

    /** @var int */
    protected $amount;

    /** @var int */
    protected $count;

    public function __construct(array &$source, int $amount)
    {
        $this->source = &$source;
        $this->amount = $amount;

        $this->count = ceil(count($this->source) / $this->amount);
    }

    public function count(): int
    {
        return $this->count;
    }

    /** @return Collection<T>[] */
    public function getIterator(): \Generator
    {
        $count = $this->count;
        $i = -1;

        while (++$i <= $count - 1) {
            yield Reference::create($this->source, ($i * $this->amount), $this->amount);
        }
    }

    public function jsonSerialize(): array
    {
        return iterator_to_array($this);
    }
}
