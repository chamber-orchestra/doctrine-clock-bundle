<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\DoctrineClockBundle\EventSubscriber;

use ChamberOrchestra\DoctrineClockBundle\Contracts\Entity\TimestampCreateInterface;
use ChamberOrchestra\DoctrineClockBundle\Contracts\Entity\TimestampUpdateInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Clock\DatePoint;

#[AsDoctrineListener(event: Events::preUpdate)]
#[AsDoctrineListener(event: Events::prePersist)]
readonly class TimestampSubscriber
{
    public function prePersist(PrePersistEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getObject();
        $em = $eventArgs->getObjectManager();
        if ($entity instanceof TimestampCreateInterface) {
            if (null === $this->getCreatedDatetime($em, $entity)) {
                $this->setCreatedDatetime($em, $entity);
            }
        }

        if ($entity instanceof TimestampUpdateInterface) {
            if (null === $this->getUpdatedDatetime($em, $entity)) {
                $this->setUpdatedDatetime($em, $entity);
            }
        }
    }

    public function preUpdate(PreUpdateEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getObject();
        if ($entity instanceof TimestampUpdateInterface) {
            $em = $eventArgs->getObjectManager();
            $this->setUpdatedDatetime($em, $entity);
            $classMetadata = $em->getClassMetadata(ClassUtils::getClass($entity));
            $em->getUnitOfWork()->recomputeSingleEntityChangeSet($classMetadata, $entity);
        }
    }

    private function getUpdatedDatetime(EntityManagerInterface $em, object $entity): DatePoint|null
    {
        return $em->getClassMetadata(ClassUtils::getClass($entity))->getFieldValue($entity, 'updatedDatetime');
    }

    private function setUpdatedDatetime(EntityManagerInterface $em, object $entity): void
    {
        $em->getClassMetadata(ClassUtils::getClass($entity))->setFieldValue($entity, 'updatedDatetime', new DatePoint());
    }

    private function getCreatedDatetime(EntityManagerInterface $em, object $entity): DatePoint|null
    {
        return $em->getClassMetadata(ClassUtils::getClass($entity))->getFieldValue($entity, 'createdDatetime');
    }

    private function setCreatedDatetime(EntityManagerInterface $em, object $entity): void
    {
        $em->getClassMetadata(ClassUtils::getClass($entity))->setFieldValue($entity, 'createdDatetime', new DatePoint());
    }
}
