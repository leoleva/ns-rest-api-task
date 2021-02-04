<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\DataFixtures\ItemFixtures;
use App\Entity\Item;
use App\Repository\ItemRepository;
use App\Service\ItemManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;

class ItemControllerTest extends WebTestCase
{
    public function testCreateValidRequest(): void
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneByUsername('john');

        $client->loginUser($user);
        
        $data = 'very secure new item data';

        $newItemData = ['data' => $data];

        $client->request('POST', '/item', $newItemData);
        $client->request('GET', '/item');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('very secure new item data', $client->getResponse()->getContent());
    }

    public function testCreateWhenNotLoggedIn(): void
    {
        $client = static::createClient();

        $client->request('POST', '/item', ['data' => 'new data']);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testCreateWithInvalidRequest(): void
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneByUsername('john');

        $client->loginUser($user);

        $client->request('POST', '/item', []);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('error', $client->getResponse()->getContent());
    }

    public function testListWhenNotLoggedIn(): void
    {
        $client = static::createClient();

        $client->request('GET', '/item');

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testListValidRequest(): void
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneByUsername('john');

        $client->loginUser($user);

        $client->request('GET', '/item');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString(ItemFixtures::ITEM_ONE_FIXTURE_DATA, $client->getResponse()->getContent());
        $this->assertStringContainsString(ItemFixtures::ITEM_TWO_FIXTURE_DATA, $client->getResponse()->getContent());
    }

    public function testDeleteWhenNotLoggedIn(): void
    {
        $client = static::createClient();

        $client->request('DELETE', '/item/1');

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testDeleteWithInvalidRequest(): void
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneByUsername('john');

        $client->loginUser($user);

        $client->request('DELETE', '/item/');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testDeleteWhenItemDoesntExists(): void
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneByUsername('john');

        $client->loginUser($user);

        $client->request('DELETE', '/item/1234599');

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertStringContainsString(ItemManager::ERROR_NO_ITEM, $client->getResponse()->getContent());
    }

    public function testDeleteWhenClientIsNotOwner(): void
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $itemRepository = static::$container->get(ItemRepository::class);

        $requestClient  = $userRepository->findOneByUsername('thom');
        $owner = $userRepository->findOneByUsername('john');

        /** @var Item[] $items */
        $items = $itemRepository->findByUser($owner);

        $client->loginUser($requestClient);

        $client->request('DELETE', '/item/' . $items[0]->getId());

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertStringContainsString(ItemManager::ERROR_NO_ITEM, $client->getResponse()->getContent());
    }

    public function testDeleteWithValidRequest(): void
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $itemRepository = static::$container->get(ItemRepository::class);

        $user = $userRepository->findOneByUsername('john');
        /** @var Item[] $items */
        $items = $itemRepository->findByUser($user);
        $itemId = $items[array_key_first($items)]->getId();

        $client->loginUser($user);

        $client->request('DELETE', '/item/' . $itemId);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('[]', $client->getResponse()->getContent());

        $this->assertNull($itemRepository->findOneById($itemId));
    }

   public function testUpdateWhenNotLoggedIn(): void
    {
        $client = static::createClient();

        $client->request('PUT', '/item', ['id' => 1, 'data' => 'data update']);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testUpdateWithInvalidRequest(): void
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneByUsername('john');

        $client->loginUser($user);

        $client->request('PUT', '/item', []);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testUpdateWhenItemDoesntExists(): void
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneByUsername('john');

        $client->loginUser($user);

        $client->request('PUT', '/item', ['id' => 11223444, 'data' => 'new updated data']);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertStringContainsString(ItemManager::ERROR_NO_ITEM, $client->getResponse()->getContent());
    }

    public function testUpdateWhenClientIsNotOwner(): void
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $itemRepository = static::$container->get(ItemRepository::class);

        $requestClient  = $userRepository->findOneByUsername('thom');
        $owner = $userRepository->findOneByUsername('john');

        /** @var Item[] $items */
        $items = $itemRepository->findByUser($owner);

        $client->loginUser($requestClient);

        $client->request('PUT', '/item', ['id' => $items[0]->getId(), 'data' => 'new very updated data']);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertStringContainsString(ItemManager::ERROR_NO_ITEM, $client->getResponse()->getContent());
    }

    public function testUpdateWithValidRequest(): void
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $itemRepository = static::$container->get(ItemRepository::class);

        $user = $userRepository->findOneByUsername('john');
        /** @var Item[] $items */
        $items = $itemRepository->findByUser($user);
        $itemId = $items[array_key_first($items)]->getId();

        $client->loginUser($user);

        $client->request('PUT', '/item', ['id' => $itemId, 'data' => 'fresh data']);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('[]', $client->getResponse()->getContent());

        /** @var Item $updatedItem */
        $updatedItem = $itemRepository->findOneById($itemId);

        $this->assertEquals('fresh data', $updatedItem->getData());
    }
}
