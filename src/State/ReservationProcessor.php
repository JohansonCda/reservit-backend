<?php
namespace App\State;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ReservationProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Reservation
    {
        if (!$data instanceof Reservation) {
            throw new \InvalidArgumentException('Expected a Reservation object');
        }

        $user = $this->security->getUser();

        if (!$user) {
            throw new BadRequestHttpException("User not authenticated.");
        }

        //DELETE
        if ($operation instanceof Delete) {
            $this->em->remove($data);
            $this->em->flush();
            return $data;
        }

        //PATCH OWN RESERVATION VALIDATION
        if ($operation instanceof Patch) {
            if (
                !$this->security->isGranted('ROLE_ADMIN') &&
                $data->getUser() !== $user
            ) {
                throw new AccessDeniedHttpException("You can only edit your own reservations.");
            }
        }

        if ($data->getUser() === null) {
            $data->setUser($user);
        }

        /* Implementar lógica de validación para evitar la superposición de reservas en los mismos horarios. */
        $qb = $this->em->getRepository(Reservation::class)
            ->createQueryBuilder('r')
            ->where('r.space = :space')
            ->andWhere('r.startTime < :end')
            ->andWhere('r.endTime > :start')
            ->setParameter('space', $data->getSpace())
            ->setParameter('start', $data->getStartTime())
            ->setParameter('end', $data->getEndTime());

        if ($data->getId()) {
            $qb->andWhere('r.id != :id')
                ->setParameter('id', $data->getId());
        }

        $overlaps = $qb->getQuery()->getResult();

        if (!empty($overlaps)) {
            throw new BadRequestHttpException("Reservation already exists in that time range.");
        }

        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }
}
