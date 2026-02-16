<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\DoctrineClockBundle\EventSubscriber;

use ChamberOrchestra\DoctrineClockBundle\Mapping\Configuration\TimestampConfiguration;
use ChamberOrchestra\MetadataBundle\EventSubscriber\AbstractDoctrineListener;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Clock\DatePoint;

#[AsDoctrineListener(event: Events::preUpdate)]
#[AsDoctrineListener(event: Events::prePersist)]
class TimestampSubscriber extends AbstractDoctrineListener
{
    public function prePersist(PrePersistEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getObject();
        $em = $eventArgs->getObjectManager();

        $config = $this->getTimestampConfiguration($em, $entity);
        if (null === $config) {
            return;
        }

        $classMetadata = $em->getClassMetadata(ClassUtils::getClass($entity));
        $now = new DatePoint();

        // On insert, only set timestamps that haven't been manually pre-populated
        $this->setTimestampFields($classMetadata, $entity, $config->getCreateFields(), $now, onlyIfNull: true);
        $this->setTimestampFields($classMetadata, $entity, $config->getUpdateFields(), $now, onlyIfNull: true);
    }

    public function preUpdate(PreUpdateEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getObject();
        $em = $eventArgs->getObjectManager();

        $config = $this->getTimestampConfiguration($em, $entity);
        if (null === $config) {
            return;
        }

        $updateFields = $config->getUpdateFields();
        if ([] !== $updateFields) {
            $classMetadata = $em->getClassMetadata(ClassUtils::getClass($entity));
            // On update, always overwrite to reflect the latest modification time
            $this->setTimestampFields($classMetadata, $entity, $updateFields, new DatePoint(), onlyIfNull: false);
            $em->getUnitOfWork()->recomputeSingleEntityChangeSet($classMetadata, $entity);
        }
    }

    /**
     * @param ClassMetadata<object> $classMetadata
     * @param list<string>          $fields
     */
    private function setTimestampFields(ClassMetadata $classMetadata, object $entity, array $fields, DatePoint $now, bool $onlyIfNull): void
    {
        foreach ($fields as $field) {
            if ($onlyIfNull && null !== $classMetadata->getFieldValue($entity, $field)) {
                continue;
            }
            $classMetadata->setFieldValue($entity, $field, $now);
        }
    }

    private function getTimestampConfiguration(EntityManagerInterface $em, object $entity): ?TimestampConfiguration
    {
        $config = $this->getConfiguration($em, $entity, TimestampConfiguration::class);

        if (!$config instanceof TimestampConfiguration) {
            return null;
        }

        return $config;
    }
}
