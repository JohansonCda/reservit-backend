<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoleFixture extends Fixture
{

    public const ROLE_USER_REF = 'user';
    public const ROLE_ADMIN_REF = 'admin';

    public function load(ObjectManager $manager): void
    {
        $userRole = new Role();
        $userRole->setName('ROLE_USER');
        $manager->persist($userRole);
        $this->addReference(self::ROLE_USER_REF, $userRole);

        $adminRole = new Role();
        $adminRole->setName('ROLE_ADMIN');
        $manager->persist($adminRole);
        $this->addReference(self::ROLE_ADMIN_REF, $adminRole);

        $manager->flush();
    }
}
