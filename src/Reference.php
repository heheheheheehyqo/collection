<?php

namespace Hyqo\Collection;

/**
 * @template T
 */
class Reference extends Collection
{
    private bool $initialized = false;

    private array $source;

    private int $first;

    private ?int $length;

    public static function create(array &$source, int $first, ?int $length = null): self
    {
        return (new self())->setReference($source, $first, $length);
    }

    public function setReference(array &$source, int $first, ?int $length): self
    {
        $this->source = &$source;
        $this->first = $first;
        $this->length = $length;

        return $this;
    }

    private function initialize(): void
    {
        $this->list = array_slice($this->source, $this->first, $this->length);
        $this->initialized = true;
    }

    public function rewind(): void
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        parent::rewind();
    }

    public function count(): int
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::count();
    }

    public function add(object $item): static
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::add($item);
    }

    public function each(\Closure $closure): static
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::each($closure);
    }

    public function reduce(\Closure $closure, mixed $initial = null): mixed
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::reduce($closure, $initial);
    }

    /** @inheritDoc */
    public function slice(int $first, ?int $length = null): self
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::slice($first, $length);
    }

    /** @inheritDoc */
    public function chunk(int $amount): Chunks
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::chunk($amount);
    }

    public function filter(\Closure $closure): Collection
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::filter($closure);
    }

    public function jsonSerialize(): array
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::jsonSerialize();
    }
}
