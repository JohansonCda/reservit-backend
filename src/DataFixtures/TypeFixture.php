<?php

namespace App\DataFixtures;

use App\Entity\Type;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TypeFixture extends Fixture
{
    public const TYPES = [
        'party_hall' => 'Salones de fiesta y centros de eventos',
        'hotel' => 'Hotel',
        'outdoor' => 'Espacio al aire libre',
        'community' => 'Centros comunitarios y parques',
        'coworking' => 'Espacios de coworking y estudios creativos',
        'bar_club' => 'Bares y clubes',
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::TYPES as $key => $label) {
            $type = new Type();
            $type->setName($label);
            $manager->persist($type);

            $this->addReference('type_' . $key, $type);
        }

        $manager->flush();
    }
}
