<?php
declare(strict_types=1);

namespace App\Services;

use App\Entity\Testimonials;
use App\Entity\User;
use App\Repository\TestimonialsRepository;
use Doctrine\ORM\EntityManagerInterface;

class UpdateRatingService
{

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var TestimonialsRepository
     */
    private TestimonialsRepository $testimonialsRepository;

    /**
     * UpdateRatingService constructor.
     * @param EntityManagerInterface $entityManager
     * @param TestimonialsRepository $testimonialsRepository
     */
    public function __construct
    (
        EntityManagerInterface $entityManager,
        TestimonialsRepository $testimonialsRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->testimonialsRepository = $testimonialsRepository;
    }

    /**
     * @param int $user_id
     * @param Testimonials|null $testimonials
     */
    public function updateRating(int $user_id, Testimonials $testimonials = null)
    {
        $user = $this->entityManager->find(User::class,$user_id);
        $rating = 0;
        $ratingData = $this->testimonialsRepository->getRating($user_id);

        if($testimonials) {
            $rating = ( (float)$ratingData['sum'] + $testimonials->getRating() ) / ( (int)$ratingData['count'] + 1);
        }else{
            if($ratingData['sum'] && $ratingData['count']) {
                $rating = ( (float)$ratingData['sum'] ) / ( (int)$ratingData['count'] );
            }
        }

        $user->setRating(round($rating,2));
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}