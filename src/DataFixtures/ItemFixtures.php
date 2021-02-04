<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Item;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ItemFixtures extends Fixture implements OrderedFixtureInterface
{
    public const ITEM_ONE_FIXTURE_DATA = 'item data one from fixtures';
    public const ITEM_TWO_FIXTURE_DATA = 'item data two from fixtures';

    public function load(ObjectManager $manager): void
    {
        /** @var User $userReference */
        $userReference = $this->getReference(UserFixtures::USER_ONE_REFERENCE_NAME);

        $itemOne = (new Item())
            ->setData(self::ITEM_ONE_FIXTURE_DATA)
            ->setUser($userReference)
        ;

        $itemTwo = (new Item())
            ->setData(self::ITEM_TWO_FIXTURE_DATA)
            ->setUser($userReference)
        ;

        $manager->persist($itemOne);
        $manager->persist($itemTwo);

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 2;
    }
}
