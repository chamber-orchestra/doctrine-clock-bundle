<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\DoctrineClockBundle\Entity;

use ChamberOrchestra\DoctrineClockBundle\Mapping\Attribute\CreateTimestamp;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\DatePointType;
use Symfony\Component\Clock\DatePoint;

#[ORM\MappedSuperclass]
trait TimestampCreateTrait
{
    #[CreateTimestamp]
    #[ORM\Column(type: DatePointType::NAME, nullable: false)]
    protected DatePoint $createdDatetime;

    public function getCreatedDatetime(): DatePoint
    {
        return $this->createdDatetime;
    }

    public function setCreatedDatetime(DatePoint $createdDatetime): void
    {
        $this->createdDatetime = $createdDatetime;
    }
}
