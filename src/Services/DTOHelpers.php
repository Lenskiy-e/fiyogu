<?php
declare(strict_types=1);

namespace App\Services;

use App\DTO\DTOException;

/**
 * Class AbstractDTO
 * @package App\DTO
 */
class DTOHelpers
{
    /**
     * @param $class
     * @param $entity
     * @throws DTOException
     */
    public static function support($class, $entity): void
    {
        if ( !$entity instanceof $class) {
            throw new DTOException("Wrong entity provided. Expected {$class}",500);
        }
    }

    public static function validRequired(array $request, array $required) : array
    {
        return array_diff($required, array_keys($request));
    }
}