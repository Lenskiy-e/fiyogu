<?php

namespace App\Controller;

use App\DTO\DTOException;
use App\DTO\Profile\RequestToUpdateProfileDTO;
use App\DTO\Profile\UpdateProfileDTO;
use App\Entity\Profile;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProfileController
 * @package App\Controller
 * @Route ("/profile")
 */
class ProfileController extends AbstractController
{

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;

    public function __construct(LoggerInterface $logger, EntityManagerInterface $manager)
    {
        $this->logger = $logger;
        $this->manager = $manager;
    }

    /**
     * @param Profile $profile
     * @param Request $request
     * @param RequestToUpdateProfileDTO $transformer
     * @return Response
     * @Route ("/{id}/update", name="update_profile", methods={"patch"})
     */
    public function update
    (
        Profile $profile,
        Request $request,
        RequestToUpdateProfileDTO $transformer
    ) : Response
    {
        try {

            $transformer->setProfile($profile);
            /** @var UpdateProfileDTO $dto */
            $dto = $transformer->transform( json_decode( $request->getContent(), true ) );

            $profile->setName( $dto->getName() );
            $profile->setSurname( $dto->getSurname() );
            $profile->setPhone( $dto->getPhone() );
            $profile->setMentor( $dto->getMentor() );

            $this->manager->persist($profile);
            $this->manager->flush();

            return $this->json([
                'result' => 'success'
            ], 200);
        }catch (DTOException $e) {
            $this->logger->error($e->getTraceAsString());

            $message = $e->getMessage();

            if($e->getCode() === 477) {
                $message = $e->getArrayErrors();
            }

            return $this->json([
                'errors' => $message
            ],400);
        }
    }
}
