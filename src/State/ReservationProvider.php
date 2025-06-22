<?php
// src/State/ReservationProvider.php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ReservationProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): iterable|object|null
    {
        $user = $this->security->getUser();

        if (!$user) {
            throw new AccessDeniedException("User not authenticated.");
        }

        if (isset($uriVariables['id'])) {
            $reservation = $this->em->getRepository(Reservation::class)->find($uriVariables['id']);

            if (!$reservation) {
                return null;
            }

            if (
                !$this->security->isGranted('ROLE_ADMIN') &&
                $reservation->getUser() !== $user
            ) {
                throw new AccessDeniedException("You are not allowed to access this reservation.");
            }

            return $reservation;
        }

        $qb = $this->em->getRepository(Reservation::class)->createQueryBuilder('r');

        if (!$this->security->isGranted('ROLE_ADMIN')) {
            $qb->where('r.user = :user')
               ->setParameter('user', $user);
        }

        return $qb->getQuery()->getResult();
    }
}
