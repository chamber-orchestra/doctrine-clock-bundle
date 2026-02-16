<?php

declare(strict_types=1);

namespace Tests\Fixtures\Entity;

use ChamberOrchestra\DoctrineClockBundle\Entity\TimestampCreateTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'create_only_entities')]
class CreateOnlyEntity
{
    use TimestampCreateTrait;

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
