<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\Request\ItemRequest;
use App\Exception\ValidationException;
use App\Service\ItemRequestValidator;
use PHPUnit\Framework\TestCase;

class ItemRequestValidatorTest extends TestCase
{
    private ItemRequestValidator $itemRequestValidator;

    public function setUp(): void
    {
        $this->itemRequestValidator = new ItemRequestValidator();
    }

    public function testInValidItemRequestOnValidateOnCreate(): void
    {
        $this->expectException(ValidationException::class);

        $this->itemRequestValidator->validateOnCreate(new ItemRequest());
    }

    public function testValidItemRequestOnValidateOnCreate(): void
    {
        $itemRequest = (new ItemRequest())
            ->setData('data')
        ;

        $this->itemRequestValidator->validateOnCreate($itemRequest);

        $this->assertTrue(true);
    }

    public function testInValidItemRequestOnValidateOnDelete(): void
    {
        $this->expectException(ValidationException::class);

        $this->itemRequestValidator->validateOnDelete(new ItemRequest());
    }

    public function testValidItemRequestOnValidateOnDelete(): void
    {
        $itemRequest = (new ItemRequest())
            ->setId(123)
        ;

        $this->itemRequestValidator->validateOnDelete($itemRequest);

        $this->assertTrue(true);
    }

    /**
     * @param ItemRequest $itemRequest
     * @dataProvider dataProviderForTestInValidItemRequestOnValidateOnUpdate
     */
    public function testInValidItemRequestOnValidateOnUpdate(ItemRequest $itemRequest): void
    {
        $this->expectException(ValidationException::class);

        $this->itemRequestValidator->validateOnUpdate($itemRequest);
    }

    public function dataProviderForTestInValidItemRequestOnValidateOnUpdate(): array
    {
        return [
            'test empty ItemRequest' => [
                new ItemRequest(),
            ],
            'test invalid id' => [
                (new ItemRequest())
                    ->setData('data'),
            ],
            'test invalid data' => [
                (new ItemRequest())
                    ->setId(456),
            ],
        ];
    }

    public function testValidItemRequestOnValidateOnUpdate(): void
    {
        $itemRequest = (new ItemRequest())
            ->setId(789)
            ->setData('secure data')
        ;

        $this->itemRequestValidator->validateOnUpdate($itemRequest);

        $this->assertTrue(true);
    }
}
