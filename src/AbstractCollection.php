<?php

namespace Hyqo\Collection;

/**
 * @template T
 */
abstract class AbstractCollection implements \Iterator, \Countable, \JsonSerializable
{
    /** @var int */
    private $position = 0;

    /** @var array<int, T> */
    protected $list = [];

    public function rewind(): void
    {
        $this->position = 0;
    }

    /** @return T */
    public function current()
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
