<?php

declare(strict_types=1);

namespace App\Service\Normalizer;

use App\Entity\Item;

class ItemNormalizer
{
    public function itemToArray(Item $item): array
    {
        return [
            'data' => $item->getData(),
            'created_at' => $item->getCreatedAt(),
            'updated_at' => $item->getUpdatedAt(),
        ];
    }
}
