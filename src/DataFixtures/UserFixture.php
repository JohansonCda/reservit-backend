<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class UserFixture extends Fixture
{

    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
         $faker = Factory::create();

        // Test users
        for ($i = 0; $i < 3; $i++) {
            $email = $faker->email();
            $user = new User();
            $user->setUsername($email);
            $user->setEmail($email);
            $user->setPassword($this->passwordHasher->hashPassword($user, '123456'));
            $user->setRole($this->getReference(RoleFixture::ROLE_USER_REF, Role::class));
            $user->setIsVerified(false);
            $manager->persist($user);
        }

        // Admin user
        $admin = new User();
        $admin->setUsername('admin@mail.com');
        $admin->setEmail('admin@mail.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setRole($this->getReference(RoleFixture::ROLE_ADMIN_REF, Role::class));
        $admin->setIsVerified(false);
        $manager->persist($admin);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RoleFixture::class,
        ];
    }
}
