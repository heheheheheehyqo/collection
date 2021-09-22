<?php

namespace Hyqo\Collection;

/**
 * @template T
 */
abstract class BaseCollection implements \Iterator, \Countable, \JsonSerializable
{
    private int $position = 0;

    /** @var array<int, T> */
    protected array $list = [];

    public function rewind(): void
    {
        $this->position = 0;
    }

    /** @return T */
    public function current(): mixed
    {
        return $this->list[$this->position];
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function valid(): bool
    {
        return isset($this->list[$this->position]);
    }

    public function count(): int
    {
        return count($this->list);
    }

    public function jsonSerialize(): array
    {
        return $this->list;
    }
}
