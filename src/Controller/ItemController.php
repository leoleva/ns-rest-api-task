<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Request\ItemRequest;
use App\Entity\User;
use App\Exception\ApiException;
use App\Exception\ValidationException;
use App\Service\ItemManager;
use App\Service\Normalizer\ValidationAwareItemRequestNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ItemController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ItemManager $itemManager;
    private ValidationAwareItemRequestNormalizer $validationAwareItemRequestNormalizer;

    public function __construct(
        EntityManagerInterface $entityManager,
        ItemManager $itemManager,
        ValidationAwareItemRequestNormalizer $validationAwareItemRequestNormalizer
    ) {
        $this->entityManager = $entityManager;
        $this->itemManager = $itemManager;
        $this->validationAwareItemRequestNormalizer = $validationAwareItemRequestNormalizer;
    }

    /**
     * @Route("/item", name="item_list", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function list(): JsonResponse
    {
        $user = $this->getUserOrThrowException();

        return $this->json($this->itemManager->get($user));
    }

    /**
     * @Route("/item", name="item_create", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function create(Request $request): JsonResponse
    {
        $user = $this->getUserOrThrowException();
        $itemRequest = $this->getItemRequest($request);

        try {
            $this->itemManager->create($user, $itemRequest);
            $this->entityManager->flush();

            return $this->json([]);
        } catch (ValidationException $validationException) {
            return $this->formatJsonErrorResponse($validationException->getMessage());
        }
    }

    /**
     * @Route("/item/{id}", name="item_delete", methods={"DELETE"})
     * @IsGranted("ROLE_USER")
     */
    public function delete(Request $request): JsonResponse
    {
        $user = $this->getUserOrThrowException();
        $itemRequest = $this->getItemRequest($request);

        try {
            $this->itemManager->delete($user, $itemRequest);
            $this->entityManager->flush();

            return $this->json([]);
        } catch (ValidationException | ApiException $exception) {
            return $this->formatJsonErrorResponse($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/item", name="item_update", methods={"PUT"})
     * @IsGranted("ROLE_USER")
     */
    public function update(Request $request): JsonResponse
    {
        $user = $this->getUserOrThrowException();
        $itemRequest = $this->getItemRequest($request);

        try {
            $this->itemManager->update($user, $itemRequest);
            $this->entityManager->flush();

            return $this->json([]);
        } catch (ValidationException | ApiException $exception) {
            return $this->formatJsonErrorResponse($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    private function getUserOrThrowException(): User
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new HttpException(Response::HTTP_UNAUTHORIZED);
        }

        return $user;
    }

    private function getItemRequest(Request $request): ItemRequest
    {
        return $this->validationAwareItemRequestNormalizer->requestToItemRequest($request);
    }

    private function formatJsonErrorResponse(string $errorMessage, int $status = Response::HTTP_OK): JsonResponse
    {
        return $this->json(['error' => $errorMessage], $status);
    }
}
