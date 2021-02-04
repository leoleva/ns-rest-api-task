<?php

declare(strict_types=1);

namespace App\Service\Normalizer;

use App\Entity\Request\ItemRequest;
use Symfony\Component\HttpFoundation\Request;

class ValidationAwareItemRequestNormalizer
{
    public function requestToItemRequest(Request $request): ItemRequest
    {
        $itemRequest = new ItemRequest();

        if ($request->get('id') !== null && filter_var($request->get('id'), FILTER_VALIDATE_INT)) {
            $itemRequest->setId((int) $request->get('id'));
        }

        if ($request->get('data') !== null && is_string($request->get('data'))) {
            $itemRequest->setData($request->get('data'));
        }

        return $itemRequest;
    }
}
