<?php
declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\HttpFoundation\Request;
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
    abstract protected function setFields(array $data): DTOInterface;

    /**
     * @param array $request
     * @return DTOInterface
     */
    public function transform(array $request) : DTOInterface
    {
        $this->checkMissFields($request);
        $dto = $this->setFields($request);
        $this->validate($dto);
        return $dto;
    }

    /**
     * @param array $request
     * @throws \Exception
     */
    protected function checkMissFields(array $request) : void
    {
        if($fields = array_diff($this->required, array_keys($request))) {
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