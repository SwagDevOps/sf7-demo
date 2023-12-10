<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Carbon\Carbon;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private ?DateTimeImmutable $begin_date = null;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private ?DateTimeImmutable $end_date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getBeginDate(): ?DateTimeImmutable
    {
        return $this->begin_date;
    }

    public function setBeginDate(string|DateTimeImmutable $begin_date): static
    {
        $this->begin_date = $this->parseDateToImmutable($begin_date);

        return $this;
    }

    public function getEndDate(): ?DateTimeImmutable
    {
        return $this->end_date;
    }

    public function setEndDate(string|DateTimeImmutable $end_date): static
    {
        $this->end_date = $this->parseDateToImmutable($end_date);

        return $this;
    }

    /**
     * Parse given string to transform to an immutable date object.
     */
    protected function parseDateToImmutable(string|DateTimeImmutable $date): DateTimeImmutable
    {
        return is_a($date, DateTimeImmutable::class) ? $date : call_user_func(function() use ($date): DateTimeImmutable {
            $date = Carbon::parse($date);

            return DateTimeImmutable::createFromMutable($date);
        });
    }
}
