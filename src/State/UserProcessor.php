<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private RoleRepository $roleRepository
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User
    {

        if (!$data instanceof User) {
            throw new \InvalidArgumentException('Expected a User object');
        }

        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data->getEmail()]);

        if ($existingUser && $existingUser->getId() !== $data->getId()) {
            throw new \RuntimeException('Email already in use.');
        }

        if ($data->getPassword()) {
            $hashedPassword = $this->passwordHasher->hashPassword($data, $data->getPassword());
            $data->setPassword($hashedPassword);
        }

        if (!$data->getUsername()) {
            $data->setUsername($data->getEmail());
        }

        if (!$data->getRole()) {
            $defaultRole = $this->roleRepository->findOneBy(['name' => 'ROLE_USER']);
            if ($defaultRole) {
                $data->setRole($defaultRole);
            }
        }

        $data->setIsVerified(false);

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }
}
