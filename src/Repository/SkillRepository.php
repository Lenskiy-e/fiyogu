<?php

namespace App\Repository;

use App\Entity\Skill;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Skill|null find($id, $lockMode = null, $lockVersion = null)
 * @method Skill|null findOneBy(array $criteria, array $orderBy = null)
 * @method Skill[]    findAll()
 * @method Skill[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SkillRepository extends ServiceEntityRepository
{
    /**
     * SkillRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Skill::class);
    }

//    public function getUsers(int $id, int $limit, int $offset)
//    {
//        if($offset > 0) {
//            $offset *= $limit;
//        }
//
//        $query = $this->createQueryBuilder('s')
//            ->innerJoin('s.users', 'u')
//            ->innerJoin('u.profile', 'p')
//            ->select('p.phone,p.surname,p.name,u.email,u.id')
//            ->where('s.id = :id')
//            ->andWhere('u.active = 1 and p.mentor = 0')
//            ->setParameter('id', $id)
//            ->setMaxResults($limit)
//            ->setFirstResult($offset)
//            ->orderBy('u.id')
//            ->getQuery()
//            ->getResult();
//        return $query;
//    }
    
    /**
     * @param int $id
     * @param int $limit
     * @param int $offset
     * @param bool $mentor
     * @return int|mixed|string
     */
    public function getUsers(int $id, int $limit, int $offset, bool $mentor)
    {
        sleep(5);
        if($offset > 0) {
            $offset *= $limit;
        }
        
        return $this->createQueryBuilder('s')
            ->from('App:User', 'u')
            ->innerJoin('u.profile', 'p')
            ->select('u')
            ->where('s.id = :id')
            ->andWhere('u.active = 1 and p.mentor = :mentor')
            ->setParameter('id', $id)
            ->setParameter('mentor', $mentor)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('u.id')
            ->getQuery()
            ->getResult();
    }
}
