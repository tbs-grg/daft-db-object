<?php
/**
* Base daft objects.
*
* @author SignpostMarv
*/
declare(strict_types=1);

namespace SignpostMarv\DaftObject\EasyDB\Tests;

use ParagonIE\EasyDB\Factory;
use SignpostMarv\DaftObject\DaftObjectRepository;
use SignpostMarv\DaftObject\DefinesOwnIdPropertiesInterface;
use SignpostMarv\DaftObject\EasyDB\TestObjectRepository;
use SignpostMarv\DaftObject\Tests\DaftObjectRepositoryTest as Base;

class DaftObjectRepositoryTest extends Base
{
    public static function DaftObjectRepositoryByType(
        string $type
    ) : DaftObjectRepository {
        return TestObjectRepository::DaftObjectRepositoryByType(
            $type,
            Factory::create('sqlite::memory:')
        );
    }

    public static function DaftObjectRepositoryByDaftObject(
        DefinesOwnIdPropertiesInterface $object
    ) : DaftObjectRepository {
        return TestObjectRepository::DaftObjectRepositoryByDaftObject(
            $object,
            Factory::create('sqlite::memory:')
        );
    }
}
