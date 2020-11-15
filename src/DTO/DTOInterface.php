<?php
declare(strict_types=1);

namespace App\DTO;

interface DTOInterface
{
    public function toArray() : array;
}