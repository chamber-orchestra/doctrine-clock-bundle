<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\DoctrineClockBundle\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;

trait DateTimeTrait
{
    /**
     * Gets the format string, as accepted by the date() function, that describes
     * the format of a stored datetime value of this platform.
     */
    protected function getDateTimeFormatString(): string
    {
        return 'Y-m-d H:i:s.u';
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        if (!$platform instanceof PostgreSQLPlatform) {
            return $platform->getDateTimeTypeDeclarationSQL($column);
        }

        if (isset($column['scale'])) {
            if (!\is_int($column['scale']) || $column['scale'] < 0 || $column['scale'] > 6) {
                throw new \InvalidArgumentException(\sprintf('PostgreSQL TIMESTAMP scale must be an integer between 0 and 6, %s given.', \var_export($column['scale'], true)));
            }

            return 'TIMESTAMP('.$column['scale'].') WITHOUT TIME ZONE';
        }

        return 'TIMESTAMP(0) WITHOUT TIME ZONE';
    }
}
