<?php

namespace App\Controller;

use App\Form\UpdateProfileType;
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

    public function __construct(
        LoggerInterface $logger,
        EntityManagerInterface $manager
    )
    {
        $this->logger = $logger;
        $this->manager = $manager;
    }

    /**
     * @param Request $request
     * @return Response
     * @Route ("", name="update_profile", methods={"patch"})
     */
    public function update
    (
        Request $request
    ) : Response
    {
        $profile = $this->getUser()->getProfile();
        $data = json_decode($request->getContent(),true);
        $form = $this->createForm(UpdateProfileType::class, $profile);
        $form->submit($data);
    
        $this->manager->persist($profile);
        $this->manager->flush();
        return $this->json([
            'result' => 'success'
        ], 200);
    }
}
