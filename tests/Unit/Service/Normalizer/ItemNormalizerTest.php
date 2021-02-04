<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Normalizer;

use App\Entity\Item;
use App\Service\Normalizer\ItemNormalizer;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ItemNormalizerTest extends TestCase
{
    private ItemNormalizer $itemNormalizer;

    public function setUp(): void
    {
        $this->itemNormalizer = new ItemNormalizer();
    }

    /**
     * @param Item $item
     * @param array $excepted
     * @dataProvider dataProviderForTestItemToArray
     */
    public function testItemToArray(Item $item, array $excepted): void
    {
        $actual = $this->itemNormalizer->itemToArray($item);

        $this->assertEquals($excepted, $actual);
    }

    public function dataProviderForTestItemToArray(): array
    {
        return [
            'test basic data' => [
                (new Item())
                    ->setData('important data')
                    ->setCreatedAt(new DateTimeImmutable('2020-01-01 01:02:03'))
                    ->setUpdatedAt(new DateTimeImmutable('2020-01-01 01:02:03')),
                [
                    'data' => 'important data',
                    'created_at' => new DateTimeImmutable('2020-01-01 01:02:03'),
                    'updated_at' => new DateTimeImmutable('2020-01-01 01:02:03'),
                ],
            ],
            'test missing created at' => [
                (new Item())
                    ->setData('other important data')
                    ->setUpdatedAt(new DateTimeImmutable('2021-01-01 01:02:03')),
                [
                    'data' => 'other important data',
                    'created_at' => null,
                    'updated_at' => new DateTimeImmutable('2021-01-01 01:02:03'),
                ],
            ],
            'test missing updated at' => [
                (new Item())
                    ->setData('super important data')
                    ->setCreatedAt(new DateTimeImmutable('2022-01-01 01:02:03')),
                [
                    'data' => 'super important data',
                    'created_at' => new DateTimeImmutable('2022-01-01 01:02:03'),
                    'updated_at' => null,
                ],
            ],
        ];
    }
}
