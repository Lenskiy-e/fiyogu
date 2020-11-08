<?php
declare(strict_types=1);

namespace App\DTO\Profile;

use App\DTO\AbstractDTOTransformer;
use App\DTO\DTOInterface;
use App\Entity\Profile;

class RequestToUpdateProfileDTO extends AbstractDTOTransformer
{
    /**
     * @var array
     */
    protected array $required = [];
    /**
     * @var Profile
     */
    private Profile $profile;

    protected function setFields(): DTOInterface
    {
        $dto = new UpdateProfileDTO();


        $dto->setName( $this->request['name'] ?? $this->profile->getName() );
        $dto->setSurname( $this->request['surname'] ?? $this->profile->getSurname() );
        $dto->setPhone( $this->request['phone'] ?? $this->profile->getPhone() );
        $dto->setMentor( $this->request['mentor'] ?? $this->profile->getMentor());

        return $dto;
    }

    public function setProfile(Profile $profile)
    {
        $this->profile = $profile;
    }

}