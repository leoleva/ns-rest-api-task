<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

// todo: consult if backwards-compatible before deploying
class IndexController extends AbstractController
{
    /**
     * @Route("", name="index", methods={"GET|POST"})
     */
    public function index(): JsonResponse
    {
        return $this->json([]);
    }
}
