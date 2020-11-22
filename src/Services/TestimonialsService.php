<?php
declare(strict_types=1);
namespace App\Services;

use App\Entity\Testimonials;
use App\Form\TestimonialType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class TestimonialsService
{
    /**
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $formFactory;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var FormErrors
     */
    private FormErrors $formErrorsService;

    /**
     * TestimonialsService constructor.
     * @param FormFactoryInterface $formFactory
     * @param EntityManagerInterface $entityManager
     * @param FormErrors $formErrorsService
     */
    public function __construct
    (
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        FormErrors $formErrorsService
    )
    {
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->formErrorsService = $formErrorsService;
    }

    /**
     * @param UserInterface $user
     * @param UserInterface $currentUser
     * @param Request $request
     */
    public function create(UserInterface $user, UserInterface $currentUser, Request $request) : void
    {
        if( !$user->getProfile()->isMentor() ) {
            throw new BadRequestException('Only mentor can have a review',403);
        }

        $review = $this->persistRequestToTestimonial( json_decode($request->getContent(),true) );

        $review->setUserFrom($currentUser);
        $review->setUserTo($user);
        $this->commit($review);
    }

    /**
     * @param Testimonials $testimonial
     * @param UserInterface $user
     * @param Request $request
     */
    public function update(Testimonials $testimonial, UserInterface $user, Request $request): void
    {
        if( $testimonial->getUserFrom() !== $user ) {
            throw new BadRequestException('You need to be testimonial maintainer to edit it',403);
        }

        $review = $this->persistRequestToTestimonial( json_decode($request->getContent(),true), $testimonial );
        $this->commit($review);
    }

    public function delete(Testimonials $testimonial, UserInterface $user) : void
    {
        if( $testimonial->getUserFrom() !== $user ) {
            throw new BadRequestException('You need to be testimonial maintainer to delete it',403);
        }
        $this->entityManager->remove($testimonial);
        $this->entityManager->flush();
    }

    /**
     * @param array $data
     * @param Testimonials|null $testimonial
     * @return Testimonials
     */
    private function persistRequestToTestimonial(array $data, ?Testimonials $testimonial = null) : Testimonials
    {
        if(!$testimonial) {
            $testimonial = new Testimonials();
        }
        $reviewForm = $this->formFactory->create(TestimonialType::class,$testimonial);

        $reviewForm->submit($data);

        if( $errors = $this->formErrorsService->getFormErrors($reviewForm) ) {
            throw new BadRequestException(json_encode($errors),400);
        }
        return $testimonial;
    }

    /**
     * @param Testimonials $testimonials
     */
    private function commit(Testimonials $testimonials): void
    {
        $this->entityManager->persist($testimonials);
        $this->entityManager->flush();
    }
}