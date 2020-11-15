<?php
declare(strict_types=1);
namespace App\Controller;

use App\Entity\Skill;
use App\Repository\SkillRepository;
use App\Repository\UserRepository;
use App\Services\AddSkill;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @var AddSkill
     */
    private AddSkill $addSkillService;
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    public function __construct(SkillRepository $skillRepository, AddSkill $addSkillService, UserRepository $userRepository)
    {
        $this->skillRepository = $skillRepository;
        $this->addSkillService = $addSkillService;
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
            $this->addSkillService->add( json_decode($request->getContent(),true), $this->getUser() );
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
     * @return Response
     * @Route("/{id}/users", name="skill_get_users", methods={"get"})
     */
    public function getSkillUsers(int $id, Request $request): Response
    {
        $data = json_decode($request->getContent(),true);
        $limit = $data['limit'] ?? 20;
        $offset = $data['offset'] ?? 0;

        $users = $this->skillRepository->getUsers($id,$limit,$offset);
        return $this->json([
            'users' => $users
        ],200);
    }
}
