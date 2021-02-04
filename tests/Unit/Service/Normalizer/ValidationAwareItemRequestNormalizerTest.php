<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Normalizer;

use App\Entity\Request\ItemRequest;
use App\Service\Normalizer\ValidationAwareItemRequestNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class ValidationAwareItemRequestNormalizerTest extends TestCase
{
    private ValidationAwareItemRequestNormalizer $validationAwareItemRequestNormalizer;

    public function setUp(): void
    {
        $this->validationAwareItemRequestNormalizer = new ValidationAwareItemRequestNormalizer();
    }

    /**
     * @param array $returnValueMap
     * @param ItemRequest $excepted
     * @dataProvider dataProviderForTestRequestToItemRequest
     */
    public function testRequestToItemRequest(array $returnValueMap, ItemRequest $excepted): void
    {
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($returnValueMap))
        ;

        $this->assertEquals($excepted, $this->validationAwareItemRequestNormalizer->requestToItemRequest($requestMock));
    }

    public function dataProviderForTestRequestToItemRequest(): array
    {
        return [
            'test valid data' => [
                [
                    ['id', null, '123'],
                    ['data', null, 'some data'],
                ],
                (new ItemRequest())
                    ->setId(123)
                    ->setData('some data'),
            ],
            'test invalid id' => [
                [
                    ['id', null, ['array at id']],
                    ['data', null, 'some data'],
                ],
                (new ItemRequest())
                    ->setData('some data'),
            ],
            'test invalid data' => [
                [
                    ['id', null, 456],
                    ['data', null, ['another array']],
                ],
                (new ItemRequest())
                    ->setId(456),
            ],
            'test id and data invalid' => [
                [
                    ['id', null, []],
                    ['data', null, []],
                ],
                new ItemRequest(),
            ],
        ];
    }
}
