<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Request\ItemRequest;
use App\Exception\ValidationException;

class ItemRequestValidator
{
    private const ERROR_NO_DATA_PARAMETER = 'No data parameter';

    /**
     * @param ItemRequest $itemRequest
     * @throws ValidationException
     */
    public function validateOnCreate(ItemRequest $itemRequest): void
    {
        if ($itemRequest->getData() === null) {
            throw new ValidationException(self::ERROR_NO_DATA_PARAMETER);
        }
    }

    /**
     * @param ItemRequest $itemRequest
     * @throws ValidationException
     */
    public function validateOnDelete(ItemRequest $itemRequest): void
    {
        if ($itemRequest->getId() === null) {
            throw new ValidationException(self::ERROR_NO_DATA_PARAMETER);
        }
    }

    /**
     * @param ItemRequest $itemRequest
     * @throws ValidationException
     */
    public function validateOnUpdate(ItemRequest $itemRequest): void
    {
        if ($itemRequest->getId() === null) {
            throw new ValidationException(self::ERROR_NO_DATA_PARAMETER);
        }

        if ($itemRequest->getData() === null) {
            throw new ValidationException(self::ERROR_NO_DATA_PARAMETER);
        }
    }
}
