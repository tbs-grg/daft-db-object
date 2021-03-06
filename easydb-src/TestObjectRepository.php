<?php
/**
* Base daft objects.
*
* @author SignpostMarv
*/
declare(strict_types=1);

namespace SignpostMarv\DaftObject\EasyDB;

use ParagonIE\EasyDB\EasyDB;
use ReflectionClass;
use ReflectionType;
use RuntimeException;
use SignpostMarv\DaftObject\AbstractDaftObjectEasyDBRepository;

class TestObjectRepository extends AbstractDaftObjectEasyDBRepository
{
    protected function __construct(string $type, EasyDB $db)
    {
        parent::__construct($type, $db);
        $query =
            'CREATE TABLE ' .
            $db->escapeIdentifier($this->DaftObjectDatabaseTable()) .
            ' (';

        $queryParts = [];

        $ref = new ReflectionClass($type);
        $nullables = $type::DaftObjectNullableProperties();

        foreach ($type::DaftObjectProperties() as $i => $prop) {
            $methodName = 'Get' . ucfirst($prop);
            if ($ref->hasMethod($methodName) === true) {
                $refReturn = $ref->getMethod($methodName)->getReturnType();

                if (
                    ($refReturn instanceof ReflectionType) &&
                    $refReturn->isBuiltin()
                ) {
                    $queryPart = $db->escapeIdentifier($prop);
                    switch ($refReturn->__toString()) {
                        case 'string':
                            $queryPart .= ' VARCHAR(255)';
                        break;
                        case 'float':
                            $queryPart .= ' REAL';
                        break;
                        case 'int':
                        case 'bool':
                            $queryPart .= ' INTEGER';
                        break;
                        default:
                            throw new RuntimeException(
                                sprintf(
                                    'Unsupported data type! (%s)',
                                    $refReturn->__toString()
                                )
                            );
                    }
                    if (in_array($prop, $nullables, true) === false) {
                        $queryPart .= ' NOT NULL';
                    }

                    $queryParts[] = $queryPart;
                } else {
                    throw new RuntimeException('Only supports builtins');
                }
            }
        }

        $primaryKeyCols = [];
        foreach ($type::DaftObjectIdProperties() as $col) {
            $primaryKeyCols[] = $db->escapeIdentifier($col);
        }

        if (count($primaryKeyCols) > 0) {
            $queryParts[] =
                'PRIMARY KEY (' .
                implode(',', $primaryKeyCols) .
                ')';
        }

        $query .=
            implode(',', $queryParts) .
            ');';

        $db->safeQuery($query);
    }

    protected function DaftObjectDatabaseTable() : string
    {
        return preg_replace('/[^a-z]+/', '_', mb_strtolower($this->type));
    }
}
