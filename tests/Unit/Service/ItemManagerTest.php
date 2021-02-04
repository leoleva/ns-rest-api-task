<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\Item;
use App\Entity\Request\ItemRequest;
use App\Entity\User;
use App\Exception\ApiException;
use App\Exception\ValidationException;
use App\Repository\ItemRepository;
use App\Service\ItemManager;
use App\Service\ItemRequestValidator;
use App\Service\Normalizer\ItemNormalizer;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ItemManagerTest extends TestCase
{
    /**
     * @var EntityManagerInterface|MockObject
     */
    private $entityManager;

    /**
     * @var ItemRepository|MockObject
     */
    private $itemRepository;
    private ItemManager $itemManager;
    private ItemNormalizer $itemNormalizer;

    public function setUp()
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->itemRepository = $this->createMock(ItemRepository::class);
        $this->itemNormalizer = new ItemNormalizer();

        $this->itemManager = new ItemManager(
            $this->entityManager,
            $this->itemRepository,
            $this->itemNormalizer,
            new ItemRequestValidator()
        );
    }

    public function testValidCreate()
    {
        $itemRequest = (new ItemRequest())
            ->setData('data')
        ;

        $user = new User();

        $item = (new Item())
            ->setUser($user)
            ->setData($itemRequest->getData())
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($item)
        ;

        $this->itemManager->create($user, $itemRequest);
    }

    public function testInValidRequestCreate()
    {
        $this->expectException(ValidationException::class);

        $this->itemManager->create(new User(), new ItemRequest());
    }

    public function testGetWithResults()
    {
        $this->itemRepository
            ->expects($this->once())
            ->method('findByUser')
            ->willReturn(
                [
                    (new Item())
                        ->setData('data')
                        ->setCreatedAt(new DateTimeImmutable('2020-01-01'))
                        ->setUpdatedAt(new DateTimeImmutable('2020-02-02')),
                    (new Item())
                        ->setData('more data')
                        ->setCreatedAt(new DateTimeImmutable('2020-03-03'))
                        ->setUpdatedAt(new DateTimeImmutable('2020-04-04')),
                ]
            )
        ;

        $excepted = [
            [
                'data' => 'data',
                'created_at' => new DateTimeImmutable('2020-01-01'),
                'updated_at' => new DateTimeImmutable('2020-02-02'),
            ],
            [
                'data' => 'more data',
                'created_at' => new DateTimeImmutable('2020-03-03'),
                'updated_at' => new DateTimeImmutable('2020-04-04'),
            ],
        ];

        $this->assertEquals($excepted, $this->itemManager->get(new User()));
    }

    public function testGetWithNoResults()
    {
        $this->itemRepository
            ->expects($this->once())
            ->method('findByUser')
            ->willReturn([])
        ;

        $this->assertEquals([], $this->itemManager->get(new User()));
    }

    public function testDeleteWithInvalidRequest()
    {
        $this->expectException(ValidationException::class);

        $this->itemManager->delete(new User(), new ItemRequest());
    }

    public function testDeleteWhenItemDoesntExits()
    {
        $this->expectException(ApiException::class);

        $this->itemRepository
            ->expects($this->once())
            ->method('findOneById')
            ->willReturn(null)
        ;

        $itemRequest = (new ItemRequest())
            ->setId(777)
        ;

        $this->itemManager->delete(new User(), $itemRequest);
    }

    public function testDeleteWhenUserIsNotOwner()
    {
        $this->expectException(ApiException::class);

        $owner = $this->createMock(User::class);
        $owner->expects($this->once())
            ->method('getId')
            ->willReturn(1)
        ;

        $user = $this->createMock(User::class);
        $user->expects($this->once())
            ->method('getId')
            ->willReturn(2)
        ;

        $this->itemRepository
            ->expects($this->once())
            ->method('findOneById')
            ->willReturn((new Item())->setUser($owner))
        ;

        $itemRequest = (new ItemRequest())
            ->setId(888)
        ;

        $this->itemManager->delete($user, $itemRequest);
    }

    public function testValidDelete()
    {
        $owner = $this->createMock(User::class);
        $owner->expects($this->once())
            ->method('getId')
            ->willReturn(1)
        ;

        $user = $this->createMock(User::class);
        $user->expects($this->once())
            ->method('getId')
            ->willReturn(1)
        ;

        $item = (new Item())
            ->setUser($owner)
        ;

        $this->itemRepository
            ->expects($this->once())
            ->method('findOneById')
            ->willReturn($item)
        ;

        $itemRequest = (new ItemRequest())
            ->setId(999)
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($item)
        ;

        $this->itemManager->delete($user, $itemRequest);
    }

    public function testUpdateWithInvalidRequest()
    {
        $this->expectException(ValidationException::class);

        $this->itemManager->update(new User(), new ItemRequest());
    }

    public function testUpdateWhenItemDoesntExits()
    {
        $this->expectException(ApiException::class);

        $this->itemRepository
            ->expects($this->once())
            ->method('findOneById')
            ->willReturn(null)
        ;

        $itemRequest = (new ItemRequest())
            ->setId(111)
            ->setData('data')
        ;

        $this->itemManager->update(new User(), $itemRequest);
    }

    public function testUpdateWhenUserIsNotOwner()
    {
        $this->expectException(ApiException::class);

        $owner = $this->createMock(User::class);
        $owner->expects($this->once())
            ->method('getId')
            ->willReturn(5)
        ;

        $user = $this->createMock(User::class);
        $user->expects($this->once())
            ->method('getId')
            ->willReturn(6)
        ;

        $this->itemRepository
            ->expects($this->once())
            ->method('findOneById')
            ->willReturn((new Item())->setUser($owner))
        ;

        $itemRequest = (new ItemRequest())
            ->setId(888)
            ->setData('more data')
        ;

        $this->itemManager->update($user, $itemRequest);
    }

    public function testValidUpdate()
    {
        $owner = $this->createMock(User::class);
        $owner->expects($this->once())
            ->method('getId')
            ->willReturn(9)
        ;

        $user = $this->createMock(User::class);
        $user->expects($this->once())
            ->method('getId')
            ->willReturn(9)
        ;

        $item = (new Item())
            ->setUser($owner)
        ;

        $this->itemRepository
            ->expects($this->once())
            ->method('findOneById')
            ->willReturn($item)
        ;

        $itemRequest = (new ItemRequest())
            ->setId(222)
            ->setData('updated data')
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($item)
        ;

        $this->itemManager->update($user, $itemRequest);
    }
}
