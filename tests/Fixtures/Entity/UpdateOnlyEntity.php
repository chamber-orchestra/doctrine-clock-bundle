<?php

declare(strict_types=1);

namespace Tests\Fixtures\Entity;

use ChamberOrchestra\DoctrineClockBundle\Contracts\Entity\TimestampUpdateInterface;
use ChamberOrchestra\DoctrineClockBundle\Entity\TimestampUpdateTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'update_only_entities')]
class UpdateOnlyEntity implements TimestampUpdateInterface
{
    use TimestampUpdateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
