<?php
declare(strict_types=1);
namespace App\Controller;

use App\Entity\Testimonials;
use App\Entity\User;
use App\Services\FormErrors;
use App\Services\TestimonialsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TestimonialsController
 * @package App\Controller
 * @Route("/testimonials")
 */
class TestimonialsController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;
    /**
     * @var TestimonialsService
     */
    private TestimonialsService $testimonialsService;

    public function __construct(EntityManagerInterface $manager, TestimonialsService $testimonialsService)
    {
        $this->manager = $manager;
        $this->testimonialsService = $testimonialsService;
    }

    /**
     * @param User $user
     * @param Request $request
     * @param FormErrors $formErrorsService
     * @return Response
     * @Route("/{id}", name="testimonails_add", methods={"post"})
     */
    public function add(User $user, Request $request) : Response
    {
        $this->testimonialsService->create($user,$this->getUser(),$request);
        return $this->json([
            'status' => 'success'
        ],201);
    }

    /**
     * @param Testimonials $testimonial
     * @param Request $request
     * @return Response
     * @Route("/{id}", name="testimonials_edit", methods={"patch"})
     */
    public function edit(Testimonials $testimonial, Request $request) : Response
    {
        $this->testimonialsService->update($testimonial,$this->getUser(),$request);
        return $this->json([
            'status' => 'success'
        ]);
    }

    /**
     * @param Testimonials $testimonial
     * @return Response
     * @Route ("/{id}", name="testimonials_delete", methods={"delete"})
     */
    public function delete(Testimonials $testimonial) : Response
    {
        $this->testimonialsService->delete($testimonial,$this->getUser());
        return $this->json([
            'status' => 'success'
        ]);
    }
}