<?php
declare(strict_types=1);

namespace App\DTO\User;

use App\DTO\AbstractDTOTransformer;
use App\DTO\DTOException;
use App\DTO\DTOInterface;

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
     * @return DTOInterface
     * @throws DTOException
     */
    protected function setFields() : DTOInterface
    {
        $dto = new CreateUserDTO();

        $dto->setUsername( $this->request['username'] );
        $dto->setPassword( $this->request['password'] );
        $dto->setEmail( $this->request['email'] );
        $dto->setName( $this->request['name'] );

        return $dto;
    }

}