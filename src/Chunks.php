<?php

namespace Hyqo\Collection;

use Exception;
use Traversable;

/**
 * @template T
 */
class Chunks implements \IteratorAggregate, \Countable, \JsonSerializable
{
    private int $count;

    public function __construct(private array &$source, private int $amount)
    {
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
