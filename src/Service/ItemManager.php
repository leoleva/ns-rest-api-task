<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Item;
use App\Entity\Request\ItemRequest;
use App\Entity\User;
use App\Exception\ApiException;
use App\Exception\ValidationException;
use App\Repository\ItemRepository;
use App\Service\Normalizer\ItemNormalizer;
use Doctrine\ORM\EntityManagerInterface;

class ItemManager
{
    public const ERROR_NO_ITEM = 'No item';

    private EntityManagerInterface $entityManager;
    private ItemRepository $itemRepository;
    private ItemNormalizer $itemNormalizer;
    private ItemRequestValidator $itemRequestValidator;

    public function __construct(
        EntityManagerInterface $entityManager,
        ItemRepository $itemRepository,
        ItemNormalizer $itemNormalizer,
        ItemRequestValidator $itemRequestValidator
    ) {
        $this->entityManager = $entityManager;
        $this->itemRepository = $itemRepository;
        $this->itemNormalizer = $itemNormalizer;
        $this->itemRequestValidator = $itemRequestValidator;
    }

    /**
     * @param User $user
     * @param ItemRequest $itemRequest
     * @throws ValidationException
     */
    public function create(User $user, ItemRequest $itemRequest): void
    {
        $this->itemRequestValidator->validateOnCreate($itemRequest);

        $item = (new Item())
            ->setUser($user)
            ->setData($itemRequest->getData())
        ;

        $this->entityManager->persist($item);
    }

    public function get(User $user): array
    {
        $items = $this->itemRepository->findByUser($user);

        $mappedItems = [];

        foreach ($items as $item) {
            $mappedItems[] = $this->itemNormalizer->itemToArray($item);
        }

        return $mappedItems;
    }

    /**
     * @param User $user
     * @param ItemRequest $itemRequest
     * @throws ApiException|ValidationException
     */
    public function delete(User $user, ItemRequest $itemRequest): void
    {
        $this->itemRequestValidator->validateOnDelete($itemRequest);

        $item = $this->itemRepository->findOneById($itemRequest->getId());

        if ($item === null) {
            throw new ApiException(self::ERROR_NO_ITEM);
        }

        if ($item->getUser()->getId() !== $user->getId()) {
            throw new ApiException(self::ERROR_NO_ITEM);
        }

        $this->entityManager->remove($item);
    }

    /**
     * @param User $user
     * @param ItemRequest $itemRequest
     * @throws ApiException|ValidationException
     */
    public function update(User $user, ItemRequest $itemRequest): void
    {
        $this->itemRequestValidator->validateOnUpdate($itemRequest);

        $item = $this->itemRepository->findOneById($itemRequest->getId());

        if ($item === null) {
            throw new ApiException(self::ERROR_NO_ITEM);
        }

        if ($item->getUser()->getId() !== $user->getId()) {
            throw new ApiException(self::ERROR_NO_ITEM);
        }

        $item->setData($itemRequest->getData());

        $this->entityManager->persist($item);
    }
}
