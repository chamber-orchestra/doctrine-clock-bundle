<?php

declare(strict_types=1);

namespace Tests\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\DatePointType;
use Symfony\Component\Clock\DatePoint;

#[ORM\Entity]
#[ORM\Table(name: 'precised_manual_timestamp_entities')]
class PrecisedManualTimestampEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: DatePointType::NAME, scale: 6)]
    private DatePoint $createdDatetime;

    #[ORM\Column(type: DatePointType::NAME, scale: 6)]
    private DatePoint $updatedDatetime;

    public function __construct(DatePoint $createdDatetime, DatePoint $updatedDatetime)
    {
        $this->createdDatetime = $createdDatetime;
        $this->updatedDatetime = $updatedDatetime;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedDatetime(): DatePoint
    {
        return $this->createdDatetime;
    }

    public function getUpdatedDatetime(): DatePoint
    {
        return $this->updatedDatetime;
    }
}
