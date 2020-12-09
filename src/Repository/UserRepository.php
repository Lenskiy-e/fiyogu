<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param string $token
     * @return User|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findForActivation(string $token) : ?User
    {
        return $this->createQueryBuilder('u')
            ->where('u.activation_token = :token')
            ->andWhere('u.active = 0')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param int $skill_id
     * @return int|mixed|string
     */
    public function getUsersWithSkill(int $skill_id)
    {
        $query = $this->createQueryBuilder('u')
            ->innerJoin('u.skills', 's')
            ->where('s.id = :id')
            ->andWhere('u.active = 1')
            ->setParameter('id', $skill_id)
            ->getQuery()
            ->getResult();
        return $query;
    }
    
    /**
     * @param int $min_count
     * @param int $limit
     * @param int $offset
     * @return int|mixed|string
     */
    public function getUsersWithTestimonials(int $min_count = 1, int $limit = 20, int $offset = 0)
    {
        if($offset > 0) {
            $offset *= $limit;
        }

        return $this->createQueryBuilder('u')
            ->innerJoin('u.testimonials', 't')
            ->groupBy('u')
            ->having('count(t.id) > :count')
            ->setParameter('count', $min_count)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }
    
    public function getMentors(int $limit = 20, int $offset = 0)
    {
        if($offset > 0) {
            $offset *= $limit;
        }
        return $this->createQueryBuilder('u')
            ->innerJoin('u.profile', 'p')
            ->where('u.active = 1')
            ->where('p.mentor = 1')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }
}
