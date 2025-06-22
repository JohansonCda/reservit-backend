<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use App\Repository\ReservationRepository;
use App\State\ReservationProcessor;
use App\State\ReservationProvider;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/* CRUD para la gestión de las reservas (crear, ver, editar y eliminar reservas). Solo para el usuario actual. */
#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ApiResource(
    processor: ReservationProcessor::class,
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_USER')",
            provider: ReservationProvider::class
        ),
        new Get(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_USER')",
                provider: ReservationProvider::class
        ),
        new Post(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_USER')"),
        new Patch(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_USER')"),
        new Delete(security: "is_granted('ROLE_ADMIN')")
    ]
)]


#[Assert\Callback([Reservation::class, 'validateDates'])]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Space $space = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "The Start Time is required.")]
    #[Assert\GreaterThan("now", message: "The start time must be in the future.")]
    private ?\DateTime $startTime = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "The End Time is required.")]
    private ?\DateTime $endTime = null;

    #[ORM\Column(options: ["default" => true])]
    #[Assert\NotNull(message: "The confirm status cannot be null.")]
    private ?bool $isConfirmed = true;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getSpace(): ?Space
    {
        return $this->space;
    }

    public function setSpace(?Space $space): static
    {
        $this->space = $space;

        return $this;
    }

    public function getStartTime(): ?\DateTime
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTime $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTime
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTime $endTime): static
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function isConfirmed(): ?bool
    {
        return $this->isConfirmed;
    }

    public function setIsConfirmed(bool $isConfirmed): static
    {
        $this->isConfirmed = $isConfirmed;

        return $this;
    }

    public static function validateDates(self $reservation, ExecutionContextInterface $context): void
    {
        if ($reservation->getStartTime() && $reservation->getEndTime()) {
            if ($reservation->getEndTime() <= $reservation->getStartTime()) {
                $context->buildViolation("The End Time must be greater than Start Time.")
                    ->atPath('endTime')
                    ->addViolation();
            }
        }
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
}
