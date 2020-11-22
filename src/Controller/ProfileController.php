<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Form\UpdateProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
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
     * @param Profile $profile
     * @param Request $request
     * @return Response
     * @Route ("/{id}", name="update_profile", methods={"patch"})
     */
    public function update
    (
        Profile $profile,
        Request $request
    ) : Response
    {
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
