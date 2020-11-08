<?php
declare(strict_types=1);

namespace App\DTO;

use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class AbstractDTOTransformer
 * @package App\DTO
 */
abstract class AbstractDTOTransformer
{

    /**
     * @var ValidatorInterface
     */
    protected ValidatorInterface $validator;

    /**
     * @var array
     */
    protected array $request;

    /**
     * AbstractDTOTransformer constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param array $data
     * @return DTOInterface
     */
    abstract protected function setFields(): DTOInterface;

    /**
     * @param array $request
     * @return DTOInterface
     */
    public function transform(array $request) : DTOInterface
    {
        $this->request = $request;
        $this->checkMissFields();
        $dto = $this->setFields();
        $this->validate($dto);
        return $dto;
    }

    /**
     * @param array $request
     * @throws \Exception
     */
    protected function checkMissFields() : void
    {
        if($fields = array_diff($this->required, array_keys($this->request))) {
            throw new DTOException('Request missing required fields: ' . implode(',', $fields),466);
        }
    }

    /**
     * @param DTOInterface $dto
     * @throws DTOException
     */
    protected function validate(DTOInterface $dto) : void
    {
        $errors = $this->validator->validate($dto);

        if($errors->count()) {
            $validationErrors = [];
            foreach ($errors as $error) {
                $validationErrors[$error->getPropertyPath()] = $error->getMessage();
            }
            throw new DTOException($validationErrors,477,true);
        }
    }
}