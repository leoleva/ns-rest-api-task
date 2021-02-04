<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture implements OrderedFixtureInterface
{
    public const USER_ONE_REFERENCE_NAME = 'user-user_1';

    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $userOne = (new User())
            ->setUsername('john')
        ;
        $userOne
            ->setPassword($this->encoder->encodePassword($userOne, 'maxsecure'))
        ;

        $userTwo = (new User())
            ->setUsername('thom')
        ;
        $userTwo
            ->setPassword($this->encoder->encodePassword($userTwo, 'superSecure'))
        ;

        $this->addReference(self::USER_ONE_REFERENCE_NAME, $userOne);

        $manager->persist($userOne);
        $manager->persist($userTwo);
        $manager->flush();
    }

    public function getOrder(): int
    {
        return 1;
    }
}
