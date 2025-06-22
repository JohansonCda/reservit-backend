<?php

namespace App\DataFixtures;

use App\Entity\Space;
use App\Entity\Type;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class SpaceFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $space = new Space();
        $space->setName('Espacio de Prueba');
        $space->setDescription('Un espacio de prueba para eventos');
        $space->setPricePerHour(20000);
        $space->setCapacity(100);
        $space->setIsAvailable(true);

        $space->setType($this->getReference('type_party_hall', Type::class));

        $manager->persist($space);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TypeFixture::class,
        ];
    }
}
