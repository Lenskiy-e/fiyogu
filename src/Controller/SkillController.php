<?php
declare(strict_types=1);
namespace App\Controller;

use App\Entity\Skill;
use App\Repository\SkillRepository;
use App\Repository\UserRepository;
use App\Services\SkillService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use \Exception;

/**
 * Class SkillController
 * @package App\Controller
 * @Route("/skill")
 */
class SkillController extends AbstractController
{
    /**
     * @var SkillRepository
     */
    private SkillRepository $skillRepository;
    /**
     * @var SkillService
     */
    private SkillService $skillService;
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;
    
    public function __construct(
        SkillRepository $skillRepository,
        SkillService $skillService,
        UserRepository $userRepository
    )
    {
        $this->skillRepository = $skillRepository;
        $this->skillService = $skillService;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/add", name="skill_add", methods={"post"})
     */
    public function add(Request $request) : Response
    {
        try {
            $skill = $this->skillService->create( json_decode($request->getContent(),true) );
            $this->skillService->addSkillToUser($this->getUser(), $skill);
            
            return $this->json([
                'result' => 'success'
            ],201);
        }catch (BadRequestException $e) {
            return $this->json([
                'error' => $e->getMessage()
            ],400);
        }
    }
    
    /**
     * @param int $id
     * @param Request $request
     * @return Response
     * @Route("/{id}/users", name="skill_get_users", methods={"get"})
     */
    
    public function getSkillUsers(int $id, Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(),true);
            $limit = $data['limit'] ?? 20;
            $offset = $data['offset'] ?? 0;
            $mentor = $data['mentor'] ?? 0;
            
            $users = $this->skillService->getSkillUsers($id, (bool)$mentor, $limit, $offset);
            
            if(!$users) {
                $users = $this->skillRepository->getUsers($id, $limit, $offset, (bool)$mentor);
            }

            return $this->json([
                'users' => $users
            ]);
        }catch (Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ],400);
        }
    }
    
    /**
     * @param Skill $skill
     * @return JsonResponse
     * @Route("/{id}/verify", name="skill_verify", methods={"post"})
     */
    public function verify(Skill $skill): JsonResponse
    {
        try {
            $this->skillService->edit(['valid' => true], $skill);
            return $this->json([
                'result' => 'success'
            ]);
        }catch (Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ],$e->getCode());
        }
    }
    
    /**
     * @param Skill $skill
     * @param Request $request
     * @return JsonResponse
     * @Route("/{id}", name="skill_edit", methods={"patch"})
     */
    public function edit(Skill $skill, Request $request): JsonResponse
    {
        try {
            $this->skillService->edit( json_decode($request->getContent(),true), $skill );
            return $this->json([
                'result' => 'success'
            ]);
        }catch (Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ],$e->getCode());
        }
    }
}
