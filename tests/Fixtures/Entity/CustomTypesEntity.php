<?php

declare(strict_types=1);

namespace Tests\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'custom_types_entities')]
class CustomTypesEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $dateMutable;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $dateImmutable;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $amount;

    public function __construct(\DateTime $dateMutable, \DateTimeImmutable $dateImmutable, string $amount)
    {
        $this->dateMutable = $dateMutable;
        $this->dateImmutable = $dateImmutable;
        $this->amount = $amount;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateMutable(): \DateTime
    {
        return $this->dateMutable;
    }

    public function getDateImmutable(): \DateTimeImmutable
    {
        return $this->dateImmutable;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }
}
