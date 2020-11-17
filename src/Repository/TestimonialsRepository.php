<?php

namespace App\Repository;

use App\Entity\Profile;
use App\Entity\Testimonials;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Testimonials|null find($id, $lockMode = null, $lockVersion = null)
 * @method Testimonials|null findOneBy(array $criteria, array $orderBy = null)
 * @method Testimonials[]    findAll()
 * @method Testimonials[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TestimonialsRepository extends ServiceEntityRepository
{
    /**
     * TestimonialsRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Testimonials::class);
    }

    /**
     * @param int $user_id
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function findByUserTo(int $user_id, int $limit, int $offset) : array
    {
        if($offset > 0) {
            $offset *= $limit;
        }
        $query = $this->createQueryBuilder('t')
            ->innerJoin(Profile::class,'p', Join::WITH, 'p.user = t.user_from')
            ->select('identity(t.user_from), (p.name) as from_name, t.text, t.rating')
            ->where('t.user_to = :user_id')
            ->andWhere('t.verified = 1')
            ->setParameter('user_id', $user_id)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery();

        return $query->getArrayResult();
    }
}
