<?php

use Hyqo\Collection\Collection;
use JetBrains\PhpStorm\ArrayShape;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    #[ArrayShape(['1.1' => Item::class, '1.2' => Item::class, '2.1' => Item::class, '2.2' => Item::class])]
    private function mockProducts(): array
    {
        return [
            '1.1' => new Item('product 1.1', 1),
            '1.2' => new Item('product 1.2', 2),
            '2.1' => new Item('product 2.1', 3),
            '2.2' => new Item('product 2.2', 4),
        ];
    }

    /** @return Collection<Item> */
    private function mockCollection(): Collection
    {
        /** @var Collection<Item> */
        $collection = new Collection();

        $products = $this->mockProducts();

        foreach ($products as $product) {
            $collection->add($product);
        }

        return $collection;
    }

    public function test_create(): void
    {
        $this->assertInstanceOf(Collection::class, new Collection());
    }

    public function test_slice(): void
    {
        $products = $this->mockProducts();
        $collection = $this->mockCollection();

        $slice = $collection->slice(1, 1)->slice(0)->add($products['2.2']);

        $this->assertJsonStringEqualsJsonString(
            json_encode(new Collection($products['1.2'], $products['2.2'])),
            json_encode($slice)
        );
    }

    public function test_foreach_reference(): void
    {
        $products = $this->mockProducts();
        $collection = $this->mockCollection();

        foreach ($collection->slice(0) as $i => $item) {
            $this->assertEquals($products[array_keys($products)[$i]], $item);
        }
    }

    public function test_count_reference(): void
    {
        $collection = $this->mockCollection();

        $this->assertCount(4, $collection->slice(0));
    }

    public function test_filter_reference(): void
    {
        $products = $this->mockProducts();
        $collection = $this->mockCollection();

        $product1Collection = $collection->slice(0)->filter(
            fn(Item $item) => str_starts_with($item->title, 'product 1.')
        );
        $product2Collection = $collection->slice(0)->filter(
            fn(Item $item) => str_starts_with($item->title, 'product 2.')
        );

        $this->assertEquals(new Collection($products['1.1'], $products['1.2']), $product1Collection);
        $this->assertEquals(new Collection($products['2.1'], $products['2.2']), $product2Collection);
    }

    public function test_reduce_reference(): void
    {
        $collection = $this->mockCollection();

        $product1Collection = $collection->filter(
            fn(Item $item) => str_starts_with($item->title, 'product 1.')
        );
        $product2Collection = $collection->filter(
            fn(Item $item) => str_starts_with($item->title, 'product 2.')
        );

        $product1Amount = $product1Collection->slice(0)->reduce(
            fn($carry, Item $item) => $carry + $item->amount
        );
        $product2Amount = $product2Collection->slice(0)->reduce(
            fn($carry, Item $item) => $carry + $item->amount
        );

        $this->assertEquals(3, $product1Amount);
        $this->assertEquals(7, $product2Amount);
    }

    public function test_each_reference(): void
    {
        $collection = $this->mockCollection();

        $collection
            ->slice(0)
            ->each(fn(Item $item) => $this->assertInstanceOf(Item::class, $item));
    }

    public function test_map_reference(): void
    {
        $products = $this->mockProducts();
        $collection = $this->mockCollection();

        $this->assertEquals(
            array_combine(
                array_map(fn(Item $item) => $item->title, $products),
                array_map(fn(Item $item) => $item->amount, $products)
            ),
            $collection
                ->slice(0)
                ->map(fn(Item $item) => yield $item->title => $item->amount)
        );

        $this->assertEquals(
            array_fill(0, count($products), null),
            $collection
                ->slice(0)
                ->map(fn(Item $item) => null)
        );

        $this->assertEquals(
            array_fill(0, count($products), null),
            $collection
                ->slice(0)
                ->map(function (Item $item): \Generator {
                    if (0) {
                        yield;
                    }
                })
        );
    }

    public function test_chunk_reference(): void
    {
        /** @var Collection<Item> */
        $collection = new Collection();

        $products = $this->mockProducts();

        foreach ($products as $product) {
            $collection->add($product);
        }

        $chunks = $collection->slice(0)->chunk(3);

        $this->assertCount(2, $chunks);
    }

    public function test_chunk_json(): void
    {
        /** @var Collection<Item> */
        $collection = new Collection();

        $products = $this->mockProducts();

        foreach ($products as $product) {
            $collection->add($product);
        }

        $chunks = $collection->chunk(3);

        $this->assertEquals(
            json_encode([
                [
                    $products['1.1'],
                    $products['1.2'],
                    $products['2.1'],
                ],
                [
                    $products['2.2']
                ]
            ]),
            json_encode($chunks)
        );
    }

    public function test_big_chunk(): void
    {
        /** @var Collection<Item> */
        $collection = new Collection();

        $count = 100_000;
        $perChunk = 100;
        $pages = (int)ceil($count / $perChunk);

        for ($i = 1; $i <= $count; $i++) {
            $collection->add(new Item("title", $i));
        }

        $chunks = $collection->chunk($perChunk);

        $this->assertCount($pages, $chunks);
    }
}

class Item
{
    public function __construct(
        public string $title,
        public int $amount,
    ) {
    }
}
