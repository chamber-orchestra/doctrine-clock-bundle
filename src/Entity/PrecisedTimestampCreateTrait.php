<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\DoctrineClockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\DatePointType;
use Symfony\Component\Clock\DatePoint;

#[ORM\MappedSuperclass]
trait PrecisedTimestampCreateTrait
{
    #[ORM\Column(type: DatePointType::NAME, scale: 6, nullable: false)]
    protected DatePoint $createdDatetime;

    public function getCreatedDatetime(): DatePoint
    {
        return $this->createdDatetime;
    }
}