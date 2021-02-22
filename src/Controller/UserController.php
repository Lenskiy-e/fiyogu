<?php

namespace App\Controller;

use App\DTO\User\GetUserFullPublicInfoDTO;
use App\Entity\Skill;
use App\Entity\User;
use App\Event\UserAddSkillEvent;
use App\Event\UserRemoveSkillEvent;
use App\Repository\TestimonialsRepository;
use App\Repository\UserRepository;
use App\Services\SkillService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class UserController
 * @package App\Controller
 * @Route ("/user")
 */
class UserController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private UserRepository $repository;
    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;
    /**
     * @var SkillService
     */
    private SkillService $skillService;
    
    /**
     * UserController constructor.
     * @param UserRepository $repository
     * @param ValidatorInterface $validator
     * @param LoggerInterface $logger
     * @param EntityManagerInterface $manager
     * @param SkillService $skillService
     */
    public function __construct
    (
        UserRepository $repository,
        ValidatorInterface $validator,
        LoggerInterface $logger,
        EntityManagerInterface $manager,
        SkillService $skillService
    )
    {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->logger = $logger;
        $this->manager = $manager;
        $this->skillService = $skillService;
    }
    /**
     * @param User $user
     * @return Response
     * @Route ("/{id}", name="user_get_public_info", methods={"get"})
     */
    public function getUserPublicInfo(User $user): Response
    {
        return $this->json( (new GetUserFullPublicInfoDTO($user))->toArray() );
    }

    /**
     * @param User $user
     * @param Request $request
     * @param TestimonialsRepository $testimonialsRepository
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/{id}/testimonials", name="user_get_testimonials", methods={"get"})
     */
    public function getTestimonials(User $user, Request $request,TestimonialsRepository $testimonialsRepository) : Response
    {
        $requestData = json_decode($request->getContent(),true);
        $limit = $requestData['limit'] ?? 20;
        $offset = $requestData['offset'] ?? 0;
    
        return $this->json([
            'result' => $testimonialsRepository->findByUserTo($user->getId(), $limit, $offset)
        ],200);
    }
    
    /**
     * @param Skill $skill
     * @param EventDispatcherInterface $dispatcher
     * @return JsonResponse
     * @Route("/skills/{id}/add", name="user_add_skill", methods={"post"})
     */
    public function addSkill(Skill $skill, EventDispatcherInterface $dispatcher) : JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $user->getSkills()->add($skill);
    
        $event = new UserAddSkillEvent($skill, $user);
        $dispatcher->dispatch($event, $event::NAME);
    
        return $this->json([
            'result' => 'success'
        ]);
    }
    
    /**
     * @param Skill $skill
     * @param EventDispatcherInterface $dispatcher
     * @return JsonResponse
     * @Route("/skills/{id}/remove", name="user_remove_skill", methods={"delete"})
     */
    public function removeSkill(Skill $skill, EventDispatcherInterface $dispatcher) : JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $user->getSkills()->removeElement($skill);
    
        $event = new UserRemoveSkillEvent($skill, $user);
        $dispatcher->dispatch($event, $event::NAME);
    
        return $this->json([
            'result' => 'success'
        ]);
    }
}
