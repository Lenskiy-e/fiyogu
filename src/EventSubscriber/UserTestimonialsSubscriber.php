<?php
declare(strict_types=1);
namespace App\EventSubscriber;

use App\Entity\Testimonials;
use App\Services\UpdateRatingService;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class UserTestimonialsSubscriber implements EventSubscriber
{

    /**
     * @var UpdateRatingService
     */
    private UpdateRatingService $ratingService;

    /**
     * UserTestimonialsSubscriber constructor.
     * @param UpdateRatingService $ratingService
     */
    public function __construct(UpdateRatingService $ratingService)
    {
        $this->ratingService = $ratingService;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        /** @var Testimonials $testimonial */
        $entity = $args->getEntity();

        if($entity instanceof Testimonials) {
            $this->ratingService->updateRating($entity->getUserTo()->getId(), $entity);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        /** @var Testimonials $testimonial */
        $entity = $args->getEntity();

        if($entity instanceof Testimonials) {
            $this->ratingService->updateRating($entity->getUserTo()->getId(), null);
        }
    }

    /**
     * @return array|string[]
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postUpdate,
            Events::postRemove
        ];
    }
}