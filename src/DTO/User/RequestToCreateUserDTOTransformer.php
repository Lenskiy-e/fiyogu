<?php
declare(strict_types=1);

namespace App\DTO\User;

use App\DTO\AbstractDTOTransformer;
use App\DTO\DTOException;
use App\DTO\DTOInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RequestToCreateUserDTOTransformer
 * @package App\DTO\User
 */
class RequestToCreateUserDTOTransformer extends AbstractDTOTransformer
{
    /**
     * @var array|string[]
     */
    protected array $required = ['email', 'password', 'username', 'name'];

    /**
     * @param array $request
     * @return DTOInterface
     * @throws DTOException
     */
    protected function setFields(array $request) : DTOInterface
    {
        $dto = new CreateUserDTO();

        $dto->setUsername( $request['username']);
        $dto->setPassword( $request['password']);
        $dto->setEmail( $request['email']);
        $dto->setName( $request['name'] );

        return $dto;
    }

}