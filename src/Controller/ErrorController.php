<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

// todo: consult if backwards-compatible before deploying
class ErrorController extends AbstractController
{
    public function handleError(HttpException $exception): JsonResponse
    {
        return $this->json(['error' => $exception->getStatusCode()], $exception->getStatusCode());
    }
}
